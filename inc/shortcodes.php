<?php
/**
 * Shortcodes: [ps_video], [ps_contact_info], [ps_contact_form],
 * [ps_investment_form], [ps_services_subnav], [ps_image_slot].
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ps_parse_video( $url ) {
	$url = trim( $url );
	if ( ! $url ) {
		return null;
	}
	if ( preg_match( '~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $m ) ) {
		return array(
			'type'     => 'youtube',
			'id'       => $m[1],
			'src'      => 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0&playsinline=1&modestbranding=1',
			'vertical' => (bool) preg_match( '~youtube\.com/shorts/~', $url ),
		);
	}
	if ( preg_match( '~vimeo\.com/(?:video/)?(\d+)~', $url, $m ) ) {
		return array(
			'type'     => 'vimeo',
			'src'      => 'https://player.vimeo.com/video/' . $m[1],
			'vertical' => false,
		);
	}
	if ( preg_match( '~\.(mp4|webm|ogg|mov)(\?.*)?$~i', $url ) ) {
		return array(
			'type'     => 'file',
			'src'      => $url,
			'vertical' => true,
		);
	}
	return null;
}

/**
 * [ps_video]             - intro video, rendered on the Home page only.
 * [ps_video slot="invest"] - investment-opportunity video, rendered on the Services page only.
 */
function ps_sc_video( $atts = array() ) {
	$atts   = shortcode_atts( array( 'slot' => '' ), $atts, 'ps_video' );
	$invest = 'invest' === $atts['slot'];
	if ( $invest ) {
		if ( 'services' !== ps_current_page_key() ) {
			return '';
		}
		$prefix    = 'video_invest';
		$title_key = 'video_invest_title';
	} else {
		if ( ! is_front_page() && 'home' !== ps_current_page_key() ) {
			return '';
		}
		$prefix    = 'video';
		$title_key = 'video_title';
	}
	$url   = ps_opt( $prefix . ( 'ar' === ps_lang() ? '_ar' : '_en' ) );
	$video = ps_parse_video( $url );
	if ( ! $video ) {
		return '';
	}
	$custom_poster = trim( (string) ps_opt( $prefix . '_poster' ) );
	$class         = 'ps-video' . ( $video['vertical'] ? ' ps-video--vertical' : '' );
	ob_start();
	?>
	<div class="<?php echo esc_attr( $class ); ?>">
		<?php if ( 'youtube' === $video['type'] ) : ?>
			<?php
			$poster_src = $custom_poster ? $custom_poster : 'https://i.ytimg.com/vi/' . $video['id'] . '/hqdefault.jpg';
			?>
			<button type="button" class="ps-video__poster" data-src="<?php echo esc_url( $video['src'] . '&autoplay=1' ); ?>" data-title="<?php echo esc_attr( ps_t( $title_key ) ); ?>" aria-label="<?php echo esc_attr( ps_t( 'play' ) ); ?>">
				<img src="<?php echo esc_url( $poster_src ); ?>" alt="" loading="lazy" decoding="async" width="480" height="360">
				<span class="ps-video__play" aria-hidden="true"></span>
			</button>
			<noscript>
				<iframe src="<?php echo esc_url( $video['src'] ); ?>" title="<?php echo esc_attr( ps_t( $title_key ) ); ?>" allowfullscreen></iframe>
			</noscript>
		<?php elseif ( 'file' === $video['type'] ) : ?>
			<video src="<?php echo esc_url( $video['src'] ); ?>" <?php echo $custom_poster ? 'poster="' . esc_url( $custom_poster ) . '"' : ''; ?> controls playsinline preload="metadata"></video>
		<?php else : ?>
			<iframe
				src="<?php echo esc_url( $video['src'] ); ?>"
				title="<?php echo esc_attr( ps_t( $title_key ) ); ?>"
				loading="lazy"
				allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen"
				allowfullscreen
				referrerpolicy="strict-origin-when-cross-origin"></iframe>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'ps_video', 'ps_sc_video' );

/**
 * [ps_image_slot slot="sector_1" variant=""]
 */
function ps_sc_image_slot( $atts ) {
	$atts = shortcode_atts( array(
		'slot'    => '',
		'variant' => '',
	), $atts, 'ps_image_slot' );

	$slot = sanitize_key( $atts['slot'] );
	if ( ! $slot ) {
		return '';
	}
	return ps_render_image_slot( $slot, $atts['variant'] );
}
add_shortcode( 'ps_image_slot', 'ps_sc_image_slot' );

/**
 * [ps_services_subnav] - sticky internal jump navigation on Services & Investment Opportunities.
 */
function ps_sc_services_subnav() {
	$items = array(
		'#services'   => ps_t( 'subnav_services' ),
		'#sectors'    => ps_t( 'subnav_sectors' ),
		'#investment' => ps_t( 'subnav_investment' ),
		'#request'    => ps_t( 'subnav_request' ),
	);
	ob_start();
	?>
	<nav class="ps-subnav" id="ps-subnav" aria-label="<?php echo esc_attr( ps_t( 'subnav_label' ) ); ?>">
		<div class="ps-subnav__inner">
			<?php foreach ( $items as $href => $label ) : ?>
				<a class="ps-subnav__link" href="<?php echo esc_attr( $href ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</div>
	</nav>
	<?php
	return ob_get_clean();
}
add_shortcode( 'ps_services_subnav', 'ps_sc_services_subnav' );

/**
 * [ps_contact_info] - contact card (email, phone, WhatsApp, optional address).
 */
function ps_sc_contact_info() {
	$email   = ps_opt( 'email' );
	$phone   = ps_opt( 'phone_label' );
	$address = ps_opt( 'ar' === ps_lang() ? 'address_ar' : 'address_en' );
	$wa      = ps_whatsapp_url();
	$tel     = ps_tel_url();

	ob_start();
	?>
	<div class="ps-contact-card">
		<h3><?php echo esc_html( ps_t( 'contact_title' ) ); ?></h3>
		<p>PENTA SERVE KSA</p>
		<ul class="ps-contactlist">
			<?php if ( $email ) : ?>
				<li><small><?php echo esc_html( ps_t( 'email' ) ); ?></small><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li>
			<?php endif; ?>
			<?php if ( $phone ) : ?>
				<li><small><?php echo esc_html( ps_t( 'phone' ) ); ?> &amp; <?php echo esc_html( ps_t( 'whatsapp' ) ); ?></small><a href="<?php echo esc_url( $tel ); ?>" dir="ltr"><?php echo esc_html( $phone ); ?></a></li>
			<?php endif; ?>
			<?php if ( $address ) : ?>
				<li><small><?php echo esc_html( ps_t( 'address' ) ); ?></small><?php echo esc_html( $address ); ?></li>
			<?php endif; ?>
		</ul>
		<div class="ps-pills">
			<?php if ( $wa ) : ?>
				<a class="ps-pill ps-pill--wa" href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener"><?php echo esc_html( ps_t( 'whatsapp' ) ); ?></a>
			<?php endif; ?>
			<?php if ( $tel ) : ?>
				<a class="ps-pill ps-pill--call" href="<?php echo esc_url( $tel ); ?>"><?php echo esc_html( ps_t( 'call' ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'ps_contact_info', 'ps_sc_contact_info' );

/**
 * [ps_contact_form] - Contact Us page form (Name, Email, Phone, Subject, Message).
 */
function ps_sc_contact_form() {
	$status = isset( $_GET['ps_status'] ) ? sanitize_key( wp_unslash( $_GET['ps_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$notice = '';
	if ( 'sent' === $status ) {
		$notice = '<div class="ps-alert ps-alert--ok" role="status">' . esc_html( ps_t( 'ok' ) ) . '</div>';
	} elseif ( in_array( $status, array( 'error', 'invalid', 'wait' ), true ) ) {
		$msg    = 'error' === $status ? 'err' : $status;
		$notice = '<div class="ps-alert ps-alert--err" role="alert">' . esc_html( ps_t( $msg ) ) . '</div>';
	}

	ob_start();
	?>
	<form class="ps-form" id="ps-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<input type="hidden" name="action" value="ps_contact">
		<input type="hidden" name="ps_lang" value="<?php echo esc_attr( ps_lang() ); ?>">
		<input type="hidden" name="ps_ts" value="<?php echo esc_attr( time() ); ?>">
		<?php wp_nonce_field( 'ps_contact', 'ps_nonce' ); ?>
		<div class="ps-form__hp" aria-hidden="true">
			<label>Website <input type="text" name="ps_website" tabindex="-1" autocomplete="off"></label>
		</div>

		<div class="ps-form__grid">
			<div>
				<label for="ps_name"><?php echo esc_html( ps_t( 'f_name' ) ); ?> <i>*</i></label>
				<input type="text" id="ps_name" name="ps_name" required maxlength="100" autocomplete="name">
			</div>
			<div>
				<label for="ps_email"><?php echo esc_html( ps_t( 'f_email' ) ); ?> <i>*</i></label>
				<input type="email" id="ps_email" name="ps_email" required autocomplete="email" dir="ltr">
			</div>
			<div>
				<label for="ps_phone"><?php echo esc_html( ps_t( 'f_phone' ) ); ?> <i>*</i></label>
				<input type="tel" id="ps_phone" name="ps_phone" required autocomplete="tel" dir="ltr">
			</div>
			<div>
				<label for="ps_subject"><?php echo esc_html( ps_t( 'f_subject' ) ); ?> <i>*</i></label>
				<input type="text" id="ps_subject" name="ps_subject" required maxlength="150">
			</div>
			<div class="ps-form__full">
				<label for="ps_message"><?php echo esc_html( ps_t( 'f_message' ) ); ?> <i>*</i></label>
				<textarea id="ps_message" name="ps_message" required minlength="10" maxlength="5000"></textarea>
			</div>
			<div class="ps-form__full">
				<button type="submit"><?php echo esc_html( ps_t( 'f_send' ) ); ?></button>
			</div>
		</div>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'ps_contact_form', 'ps_sc_contact_form' );

/**
 * [ps_investment_form] - Investment Project Discussion Form in #request.
 */
function ps_sc_investment_form() {
	$status = isset( $_GET['ps_inv_status'] ) ? sanitize_key( wp_unslash( $_GET['ps_inv_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$notice = '';
	if ( 'sent' === $status ) {
		$notice = '<div class="ps-alert ps-alert--ok" role="status">' . esc_html( ps_t( 'inv_ok' ) ) . '</div>';
	} elseif ( in_array( $status, array( 'error', 'invalid', 'wait' ), true ) ) {
		$msg    = 'error' === $status ? 'err' : $status;
		$notice = '<div class="ps-alert ps-alert--err" role="alert">' . esc_html( ps_t( $msg ) ) . '</div>';
	}

	ob_start();
	?>
	<form class="ps-form" id="ps-investment-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<input type="hidden" name="action" value="ps_investment">
		<input type="hidden" name="ps_lang" value="<?php echo esc_attr( ps_lang() ); ?>">
		<input type="hidden" name="ps_ts" value="<?php echo esc_attr( time() ); ?>">
		<?php wp_nonce_field( 'ps_investment', 'ps_inv_nonce' ); ?>
		<div class="ps-form__hp" aria-hidden="true">
			<label>Website <input type="text" name="ps_website" tabindex="-1" autocomplete="off"></label>
		</div>

		<div class="ps-form__grid">
			<div>
				<label for="ps_inv_name"><?php echo esc_html( ps_t( 'f_name' ) ); ?> <i>*</i></label>
				<input type="text" id="ps_inv_name" name="ps_inv_name" required maxlength="100" autocomplete="name">
			</div>
			<div>
				<label for="ps_inv_email"><?php echo esc_html( ps_t( 'f_email' ) ); ?> <i>*</i></label>
				<input type="email" id="ps_inv_email" name="ps_inv_email" required autocomplete="email" dir="ltr">
			</div>
			<div>
				<label for="ps_inv_phone"><?php echo esc_html( ps_t( 'f_phone' ) ); ?> <i>*</i></label>
				<input type="tel" id="ps_inv_phone" name="ps_inv_phone" required autocomplete="tel" dir="ltr">
			</div>
			<div>
				<label for="ps_inv_location"><?php echo esc_html( ps_t( 'inv_location' ) ); ?> <i>*</i></label>
				<input type="text" id="ps_inv_location" name="ps_inv_location" required maxlength="150">
			</div>
			<div>
				<label for="ps_inv_type"><?php echo esc_html( ps_t( 'inv_type' ) ); ?> <i>*</i></label>
				<input type="text" id="ps_inv_type" name="ps_inv_type" required maxlength="150">
			</div>
			<div>
				<label for="ps_inv_capacity"><?php echo esc_html( ps_t( 'inv_capacity' ) ); ?> <i>*</i></label>
				<input type="text" id="ps_inv_capacity" name="ps_inv_capacity" required maxlength="120">
			</div>
			<div class="ps-form__full">
				<label for="ps_inv_stage"><?php echo esc_html( ps_t( 'inv_stage' ) ); ?> <i>*</i></label>
				<select id="ps_inv_stage" name="ps_inv_stage" required>
					<option value=""><?php echo esc_html( ps_t( 'select' ) ); ?></option>
					<?php foreach ( ps_project_stages() as $value => $label_key ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( ps_t( $label_key ) ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="ps-form__full">
				<label for="ps_inv_details"><?php echo esc_html( ps_t( 'inv_details' ) ); ?></label>
				<textarea id="ps_inv_details" name="ps_inv_details" maxlength="5000"></textarea>
			</div>
			<div class="ps-form__full">
				<button type="submit"><?php echo esc_html( ps_t( 'inv_submit' ) ); ?></button>
			</div>
		</div>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'ps_investment_form', 'ps_sc_investment_form' );