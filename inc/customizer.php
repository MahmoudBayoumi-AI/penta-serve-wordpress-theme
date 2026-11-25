<?php
/**
 * Customizer: company info, video links, hero image, and image slots.
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize a phone number (digits, plus, spaces, dashes, brackets).
 */
function ps_sanitize_phone( $value ) {
	return preg_replace( '/[^0-9+\-\s()]/', '', (string) $value );
}

/**
 * Register Customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function ps_customize_register( $wp_customize ) {
	$d = ps_defaults();

	$wp_customize->add_section( 'ps_company', array(
		'title'    => __( 'Company Info / بيانات الشركة', 'penta-serve' ),
		'priority' => 30,
	) );

	$fields = array(
		'email'           => array( 'Email / البريد (يستقبل رسائل النماذج)', 'sanitize_email', 'email' ),
		'phone'           => array( 'Phone for call button, e.g. +966506005453', 'ps_sanitize_phone', 'text' ),
		'phone_label'     => array( 'Phone as displayed on the site', 'ps_sanitize_phone', 'text' ),
		'whatsapp'        => array( 'WhatsApp number, digits only, e.g. 966506005453', 'ps_sanitize_phone', 'text' ),
		'address_ar'      => array( 'العنوان بالعربية (يظهر فقط عند تعبئته)', 'sanitize_text_field', 'text' ),
		'address_en'      => array( 'Address in English (shown only when filled)', 'sanitize_text_field', 'text' ),
		'social_linkedin' => array( 'LinkedIn URL (shown in footer when filled)', 'esc_url_raw', 'url' ),
	);

	foreach ( $fields as $key => $f ) {
		$wp_customize->add_setting( 'ps_' . $key, array(
			'default'           => isset( $d[ $key ] ) ? $d[ $key ] : '',
			'sanitize_callback' => $f[1],
		) );
		$wp_customize->add_control( 'ps_' . $key, array(
			'label'   => $f[0],
			'section' => 'ps_company',
			'type'    => $f[2],
		) );
	}

	$wp_customize->add_section( 'ps_media', array(
		'title'    => __( 'Video & Images / الفيديو والصور', 'penta-serve' ),
		'priority' => 31,
	) );

	foreach ( array(
		'video_ar' => 'رابط الفيديو العربي (YouTube / Vimeo / ملف mp4)',
		'video_en' => 'English video URL (YouTube / Vimeo / mp4 file)',
	) as $key => $label ) {
		$wp_customize->add_setting( 'ps_' . $key, array(
			'default'           => isset( $d[ $key ] ) ? $d[ $key ] : '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'ps_' . $key, array(
			'label'       => $label,
			'section'     => 'ps_media',
			'type'        => 'url',
			'description' => __( 'Shown on the Home page only. Leave empty to hide the video section.', 'penta-serve' ),
		) );
	}

	$wp_customize->add_setting( 'ps_video_poster', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ps_video_poster', array(
		'label'       => __( 'Video Cover Image / صورة غلاف الفيديو (اختياري)', 'penta-serve' ),
		'description' => __( 'تظهر قبل الضغط على زر التشغيل. إذا تُركت فارغة ستظهر صورة يوتيوب الافتراضية.', 'penta-serve' ),
		'section'     => 'ps_media',
	) ) );

	// Investment-opportunity video (Services & Investment Opportunities page).
	foreach ( array(
		'video_invest_ar' => 'رابط فيديو الفرصة الاستثمارية - العربي (YouTube / Vimeo / mp4)',
		'video_invest_en' => 'Investment opportunity video URL - English (YouTube / Vimeo / mp4)',
	) as $key => $label ) {
		$wp_customize->add_setting( 'ps_' . $key, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( 'ps_' . $key, array(
			'label'       => $label,
			'section'     => 'ps_media',
			'type'        => 'url',
			'description' => __( 'Shown on the Services & Investment Opportunities page only. Leave empty to hide the video section.', 'penta-serve' ),
		) );
	}

	$wp_customize->add_setting( 'ps_video_invest_poster', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ps_video_invest_poster', array(
		'label'       => __( 'Investment video cover image / غلاف فيديو الفرصة الاستثمارية (اختياري)', 'penta-serve' ),
		'description' => __( 'تظهر قبل الضغط على زر التشغيل. إذا تُركت فارغة ستظهر صورة يوتيوب الافتراضية.', 'penta-serve' ),
		'section'     => 'ps_media',
	) ) );

	$wp_customize->add_setting( 'ps_footer_logo', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ps_footer_logo', array(
		'label'       => __( 'Footer logo (optional) / شعار التذييل', 'penta-serve' ),
		'description' => __( 'Upload a light/white version of the logo for the dark footer. If empty, the main logo is shown on a white plate.', 'penta-serve' ),
		'section'     => 'ps_media',
	) ) );

	$wp_customize->add_setting( 'ps_hero_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ps_hero_image', array(
		'label'       => __( 'Home hero background image / صورة خلفية الواجهة', 'penta-serve' ),
		'description' => __( 'Large commercial refrigeration & freezing warehouse (min 1920x1080px). Leave empty for default navy gradient.', 'penta-serve' ),
		'section'     => 'ps_media',
	) ) );

	// Image slots for Solution Areas (4), Investment (1), and optional Services (6).
	$image_slots = array(
		'sector_1'   => 'Solution Area 01 Image: Commercial Cold Storage Warehouses (1200x750px)',
		'sector_2'   => 'Solution Area 02 Image: Freezer Warehouses (1200x750px)',
		'sector_3'   => 'Solution Area 03 Image: Chilled & Frozen Food Storage Warehouses (1200x750px)',
		'sector_4'   => 'Solution Area 04 Image: Cold Storage Facilities (1200x750px)',
		'investment' => 'Investment Section Image (1200x750px)',
		'service_1'  => 'Service 01 Image (Optional): Project Assessment & Development',
		'service_2'  => 'Service 02 Image (Optional): Design & Planning',
		'service_3'  => 'Service 03 Image (Optional): Thermal Insulation & Insulated Panels',
		'service_4'  => 'Service 04 Image (Optional): Refrigeration & Freezing Systems',
		'service_5'  => 'Service 05 Image (Optional): Operational Processes',
		'service_6'  => 'Service 06 Image (Optional): Management & Supervision',
	);

	foreach ( $image_slots as $slot => $label ) {
		$wp_customize->add_setting( 'ps_img_' . $slot, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ps_img_' . $slot, array(
			'label'   => $label,
			'section' => 'ps_media',
		) ) );

		$wp_customize->add_setting( 'ps_img_' . $slot . '_alt_en', array(
			'default'           => isset( $d[ 'img_' . $slot . '_alt_en' ] ) ? $d[ 'img_' . $slot . '_alt_en' ] : '',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'ps_img_' . $slot . '_alt_en', array(
			'label'   => $label . ' — Alt Text (EN)',
			'section' => 'ps_media',
			'type'    => 'text',
		) );

		$wp_customize->add_setting( 'ps_img_' . $slot . '_alt_ar', array(
			'default'           => isset( $d[ 'img_' . $slot . '_alt_ar' ] ) ? $d[ 'img_' . $slot . '_alt_ar' ] : '',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'ps_img_' . $slot . '_alt_ar', array(
			'label'   => $label . ' — النص البديل (AR)',
			'section' => 'ps_media',
			'type'    => 'text',
		) );
	}
}
add_action( 'customize_register', 'ps_customize_register' );


/**
 * Print the hero image as a CSS variable. The hero exists on the Home page
 * only, so the high-priority preload is limited to it, and phones get a
 * smaller file than desktops.
 */
function ps_hero_css() {
	$img = trim( ps_opt( 'hero_image' ) );
	if ( ! $img ) {
		return;
	}

	$desktop = $img;
	$mobile  = $img;
	$att_id  = ps_image_id( $img );
	if ( $att_id ) {
		$d = wp_get_attachment_image_url( $att_id, '1536x1536' );
		$m = wp_get_attachment_image_url( $att_id, 'large' );
		if ( $d ) {
			$desktop = $d;
			$mobile  = $d;
		}
		if ( $m ) {
			$mobile = $m;
		}
	}

	$split = ( $desktop !== $mobile );

	if ( is_front_page() ) {
		if ( $split ) {
			printf(
				'<link rel="preload" as="image" href="%1$s" media="(max-width: 768px)" fetchpriority="high">' . "\n" .
				'<link rel="preload" as="image" href="%2$s" media="(min-width: 769px)" fetchpriority="high">' . "\n",
				esc_url( $mobile ),
				esc_url( $desktop )
			);
		} else {
			printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n", esc_url( $desktop ) );
		}
	}

	$css = sprintf( ':root{--ps-hero-img:url("%s")}', esc_url( $desktop ) );
	if ( $split ) {
		$css .= sprintf( '@media (max-width:768px){:root{--ps-hero-img:url("%s")}}', esc_url( $mobile ) );
	}
	echo '<style id="ps-hero-var">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'wp_head', 'ps_hero_css', 20 );