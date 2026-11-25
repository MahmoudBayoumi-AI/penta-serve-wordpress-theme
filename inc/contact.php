<?php
/**
 * Contact & Investment Project Discussion form handling:
 * validation, anti-spam, email, saved inquiries.
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Private post type that keeps a copy of every inquiry in the dashboard.
 */
function ps_register_inquiry_cpt() {
	register_post_type( 'ps_inquiry', array(
		'labels'          => array(
			'name'          => __( 'Inquiries / الاستفسارات', 'penta-serve' ),
			'singular_name' => __( 'Inquiry', 'penta-serve' ),
			'menu_name'     => __( 'Inquiries / الاستفسارات', 'penta-serve' ),
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_icon'       => 'dashicons-email-alt',
		'menu_position'   => 26,
		'supports'        => array( 'title', 'editor' ),
		'capability_type' => 'post',
		'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'    => true,
	) );
}
add_action( 'init', 'ps_register_inquiry_cpt' );

/**
 * Show inquiry details in the editor screen (supports both general and investment forms).
 */
function ps_inquiry_metabox() {
	add_meta_box( 'ps_inquiry_details', __( 'Details / التفاصيل', 'penta-serve' ), 'ps_inquiry_metabox_html', 'ps_inquiry', 'side' );
}
add_action( 'add_meta_boxes', 'ps_inquiry_metabox' );

function ps_inquiry_metabox_html( $post ) {
	$rows = array(
		'Form'              => get_post_meta( $post->ID, '_ps_form_kind', true ),
		'Name'              => get_post_meta( $post->ID, '_ps_name', true ),
		'Email'             => get_post_meta( $post->ID, '_ps_email', true ),
		'Phone'             => get_post_meta( $post->ID, '_ps_phone', true ),
		'Subject'           => get_post_meta( $post->ID, '_ps_subject', true ),
		'Project Location'  => get_post_meta( $post->ID, '_ps_project_location', true ),
		'Project Type'      => get_post_meta( $post->ID, '_ps_project_type', true ),
		'Storage Capacity'  => get_post_meta( $post->ID, '_ps_storage_capacity', true ),
		'Project Stage'     => get_post_meta( $post->ID, '_ps_project_stage', true ),
		'Language'          => get_post_meta( $post->ID, '_ps_lang', true ),
	);
	echo '<ul>';
	foreach ( $rows as $label => $value ) {
		if ( '' !== (string) $value ) {
			printf( '<li><strong>%s:</strong> %s</li>', esc_html( $label ), esc_html( $value ) );
		}
	}
	echo '</ul>';
}

/**
 * Allowed project stages for the Investment Project Discussion Form.
 *
 * @return array value => label key.
 */
function ps_project_stages() {
	return array(
		'initial_idea' => 'stage_idea',
		'under_study'  => 'stage_study',
		'ready'        => 'stage_ready',
	);
}

/**
 * Shared anti-abuse checks for both forms: nonce, honeypot, minimum fill time
 * and a per-form, per-IP hourly limit. Ends the request through $finish() when
 * a check fails.
 *
 * @param string   $kind         Form id used in the rate-limit key.
 * @param string   $nonce_field  POST field holding the nonce.
 * @param string   $nonce_action Nonce action.
 * @param callable $finish       Redirect callback receiving a status string.
 * @return string Transient key used to count this visitor's submissions.
 */
function ps_form_guard( $kind, $nonce_field, $nonce_action, $finish ) {
	if ( ! isset( $_POST[ $nonce_field ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_field ] ) ), $nonce_action ) ) {
		$finish( 'error' );
	}

	// Honeypot: bots fill the hidden field. Pretend success.
	if ( ! empty( $_POST['ps_website'] ) ) {
		$finish( 'sent' );
	}

	// Submitted faster than a human could fill the form.
	$ts = isset( $_POST['ps_ts'] ) ? absint( $_POST['ps_ts'] ) : 0;
	if ( $ts && ( time() - $ts ) < 3 ) {
		$finish( 'sent' );
	}

	$tkey = 'ps_rl_' . md5( $kind . '|' . ps_client_ip() );
	if ( (int) get_transient( $tkey ) >= 5 ) {
		$finish( 'wait' );
	}
	return $tkey;
}

/**
 * Handle the general Contact Us form post.
 */
function ps_handle_contact() {
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$redirect = remove_query_arg( array( 'ps_status', 'ps_inv_status' ), $redirect );

	$finish = static function ( $status ) use ( $redirect ) {
		wp_safe_redirect( add_query_arg( 'ps_status', $status, $redirect ) . '#ps-contact-form' );
		exit;
	};

	$tkey = ps_form_guard( 'contact', 'ps_nonce', 'ps_contact', $finish );
	$hits = (int) get_transient( $tkey );

	$name    = isset( $_POST['ps_name'] ) ? sanitize_text_field( wp_unslash( $_POST['ps_name'] ) ) : '';
	$email   = isset( $_POST['ps_email'] ) ? sanitize_email( wp_unslash( $_POST['ps_email'] ) ) : '';
	$phone   = isset( $_POST['ps_phone'] ) ? ps_sanitize_phone( wp_unslash( $_POST['ps_phone'] ) ) : '';
	$subject = isset( $_POST['ps_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['ps_subject'] ) ) : '';
	$message = isset( $_POST['ps_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ps_message'] ) ) : '';
	$lang    = isset( $_POST['ps_lang'] ) && 'ar' === $_POST['ps_lang'] ? 'ar' : 'en';

	if (
		mb_strlen( $name ) < 2 || mb_strlen( $name ) > 100 ||
		! is_email( $email ) ||
		mb_strlen( preg_replace( '/\D/', '', $phone ) ) < 7 ||
		mb_strlen( $subject ) < 2 || mb_strlen( $subject ) > 150 ||
		mb_strlen( $message ) < 10 || mb_strlen( $message ) > 5000
	) {
		$finish( 'invalid' );
	}

	set_transient( $tkey, $hits + 1, HOUR_IN_SECONDS );

	$post_id = wp_insert_post( array(
		'post_type'    => 'ps_inquiry',
		'post_status'  => 'private',
		'post_title'   => sprintf( '[Contact] %s - %s', $name, $subject ),
		'post_content' => $message,
		'meta_input'   => array(
			'_ps_form_kind' => 'Contact Us',
			'_ps_name'      => $name,
			'_ps_email'     => $email,
			'_ps_phone'     => $phone,
			'_ps_subject'   => $subject,
			'_ps_lang'      => $lang,
		),
	) );

	$to           = sanitize_email( ps_opt( 'email' ) );
	$mail_subject = sprintf( '[PENTA SERVE KSA] Contact Inquiry - %s', $subject );
	$body         = "Form: Contact Us\nName: {$name}\nEmail: {$email}\nPhone: {$phone}\nSubject: {$subject}\nLanguage: {$lang}\n\nMessage:\n{$message}\n";
	$headers      = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);
	$sent = $to ? wp_mail( $to, $mail_subject, $body, $headers ) : false;

	if ( $sent || ( $post_id && ! is_wp_error( $post_id ) ) ) {
		$finish( 'sent' );
	}
	$finish( 'error' );
}
add_action( 'admin_post_nopriv_ps_contact', 'ps_handle_contact' );
add_action( 'admin_post_ps_contact', 'ps_handle_contact' );

/**
 * Handle the Investment Project Discussion Form post (#request).
 */
function ps_handle_investment() {
	$redirect = wp_get_referer() ? wp_get_referer() : ps_page_url( 'services' );
	$redirect = remove_query_arg( array( 'ps_status', 'ps_inv_status' ), $redirect );

	$finish = static function ( $status ) use ( $redirect ) {
		wp_safe_redirect( add_query_arg( 'ps_inv_status', $status, $redirect ) . '#request' );
		exit;
	};

	$tkey = ps_form_guard( 'investment', 'ps_inv_nonce', 'ps_investment', $finish );
	$hits = (int) get_transient( $tkey );

	$name     = isset( $_POST['ps_inv_name'] ) ? sanitize_text_field( wp_unslash( $_POST['ps_inv_name'] ) ) : '';
	$email    = isset( $_POST['ps_inv_email'] ) ? sanitize_email( wp_unslash( $_POST['ps_inv_email'] ) ) : '';
	$phone    = isset( $_POST['ps_inv_phone'] ) ? ps_sanitize_phone( wp_unslash( $_POST['ps_inv_phone'] ) ) : '';
	$location = isset( $_POST['ps_inv_location'] ) ? sanitize_text_field( wp_unslash( $_POST['ps_inv_location'] ) ) : '';
	$ptype    = isset( $_POST['ps_inv_type'] ) ? sanitize_text_field( wp_unslash( $_POST['ps_inv_type'] ) ) : '';
	$capacity = isset( $_POST['ps_inv_capacity'] ) ? sanitize_text_field( wp_unslash( $_POST['ps_inv_capacity'] ) ) : '';
	$stage    = isset( $_POST['ps_inv_stage'] ) ? sanitize_key( wp_unslash( $_POST['ps_inv_stage'] ) ) : '';
	$details  = isset( $_POST['ps_inv_details'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ps_inv_details'] ) ) : '';
	$lang     = isset( $_POST['ps_lang'] ) && 'ar' === $_POST['ps_lang'] ? 'ar' : 'en';

	$stages = ps_project_stages();
	if (
		mb_strlen( $name ) < 2 || mb_strlen( $name ) > 100 ||
		! is_email( $email ) ||
		mb_strlen( preg_replace( '/\D/', '', $phone ) ) < 7 ||
		mb_strlen( $location ) < 2 || mb_strlen( $location ) > 150 ||
		mb_strlen( $ptype ) < 2 || mb_strlen( $ptype ) > 150 ||
		mb_strlen( $capacity ) < 1 || mb_strlen( $capacity ) > 120 ||
		! isset( $stages[ $stage ] ) ||
		mb_strlen( $details ) > 5000
	) {
		$finish( 'invalid' );
	}

	$stage_labels = array(
		'initial_idea' => 'Initial Idea',
		'under_study'  => 'Under Study',
		'ready'        => 'Ready for Implementation',
	);
	$stage_label = $stage_labels[ $stage ];

	set_transient( $tkey, $hits + 1, HOUR_IN_SECONDS );

	$post_id = wp_insert_post( array(
		'post_type'    => 'ps_inquiry',
		'post_status'  => 'private',
		'post_title'   => sprintf( '[Investment] %s - %s (%s)', $name, $location, $stage_label ),
		'post_content' => $details,
		'meta_input'   => array(
			'_ps_form_kind'        => 'Investment Project Discussion',
			'_ps_name'             => $name,
			'_ps_email'            => $email,
			'_ps_phone'            => $phone,
			'_ps_project_location' => $location,
			'_ps_project_type'     => $ptype,
			'_ps_storage_capacity' => $capacity,
			'_ps_project_stage'    => $stage_label,
			'_ps_lang'             => $lang,
		),
	) );

	$to           = sanitize_email( ps_opt( 'email' ) );
	$mail_subject = sprintf( '[PENTA SERVE KSA] Investment Discussion - %s (%s)', $name, $location );
	$body         = "Form: Investment Project Discussion\n"
		. "Full Name: {$name}\n"
		. "Email Address: {$email}\n"
		. "Phone Number: {$phone}\n"
		. "Proposed Project Location: {$location}\n"
		. "Project Type: {$ptype}\n"
		. "Expected Storage Capacity: {$capacity}\n"
		. "Project Stage: {$stage_label}\n"
		. "Language: {$lang}\n\n"
		. "Additional Details:\n{$details}\n";
	$headers      = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);
	$sent = $to ? wp_mail( $to, $mail_subject, $body, $headers ) : false;

	if ( $sent || ( $post_id && ! is_wp_error( $post_id ) ) ) {
		$finish( 'sent' );
	}
	$finish( 'error' );
}
add_action( 'admin_post_nopriv_ps_investment', 'ps_handle_investment' );
add_action( 'admin_post_ps_investment', 'ps_handle_investment' );