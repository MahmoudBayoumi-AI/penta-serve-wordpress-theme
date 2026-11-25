<?php
/**
 * Helper functions: options, language, UI strings, page lookup, image slots.
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default company data (editable from Appearance > Customize > Company Info).
 *
 * @return array
 */
function ps_defaults() {
	return array(
		'email'               => 'info@pentaserveksa.com',
		'phone'               => '+966506005453',
		'phone_label'         => '+966 50 600 5453',
		'whatsapp'            => '966506005453',
		'address_ar'          => '',
		'address_en'          => '',
		'video_ar'            => 'https://youtube.com/shorts/GckiRiFoHQ8?si=LF1EGeQ4_Yc0LSsn',
		'video_en'            => 'https://youtube.com/shorts/LIjirD-6C7A?si=n7p3rjtg8stfroNw',
		'video_invest_ar'     => '',
		'video_invest_en'     => '',
		'hero_image'          => '',
		'footer_logo'         => '',
		'social_linkedin'     => '',
		// Image slots (Media Library attachment IDs or URLs via Customizer).
		'img_sector_1'        => '',
		'img_sector_1_alt_en' => 'Commercial cold storage warehouse',
		'img_sector_1_alt_ar' => 'مستودع تبريد تجاري',
		'img_sector_2'        => '',
		'img_sector_2_alt_en' => 'Freezer warehouse with insulated freezing rooms',
		'img_sector_2_alt_ar' => 'مستودع تجميد بغرف تجميد معزولة',
		'img_sector_3'        => '',
		'img_sector_3_alt_en' => 'Chilled and frozen food storage warehouse',
		'img_sector_3_alt_ar' => 'مستودع لتخزين الأغذية المبردة والمجمدة',
		'img_sector_4'        => '',
		'img_sector_4_alt_en' => 'Integrated cold storage facility with loading and unloading areas',
		'img_sector_4_alt_ar' => 'مرفق تخزين بارد متكامل بمناطق تحميل وتفريغ',
		'img_investment'      => '',
		'img_investment_alt_en' => 'Large-scale commercial cold storage warehouse investment project in Saudi Arabia',
		'img_investment_alt_ar' => 'مشروع استثماري لمستودع تبريد وتجميد تجاري في المملكة العربية السعودية',
		'img_service_1'       => '',
		'img_service_1_alt_en' => 'Commercial cold storage project planning and assessment',
		'img_service_1_alt_ar' => 'تقييم وتطوير مشاريع مستودعات التبريد والتجميد',
		'img_service_2'       => '',
		'img_service_2_alt_en' => 'Cold storage warehouse layout and loading bay planning',
		'img_service_2_alt_ar' => 'تصميم وتخطيط مستودعات التبريد ومناطق التحميل والتفريغ',
		'img_service_3'       => '',
		'img_service_3_alt_en' => 'Thermal insulated sandwich panels and cold room doors',
		'img_service_3_alt_ar' => 'ألواح الساندويتش بانل المعزولة وأبواب غرف التبريد والتجميد',
		'img_service_4'       => '',
		'img_service_4_alt_en' => 'Industrial refrigeration compressors and cooling plant systems',
		'img_service_4_alt_ar' => 'أنظمة ووحدات التبريد والتجميد الصناعية',
		'img_service_5'       => '',
		'img_service_5_alt_en' => 'Cold storage warehouse forklift and pallet handling operations',
		'img_service_5_alt_ar' => 'عمليات المناولة والتشغيل اليومي داخل مستودع التبريد والتجميد',
		'img_service_6'       => '',
		'img_service_6_alt_en' => 'Cold storage operational supervision and temperature monitoring',
		'img_service_6_alt_ar' => 'الإدارة والإشراف ومتابعة الأداء في مستودعات التبريد',
	);
}

/**
 * Get a company option.
 *
 * @param string $key Option key.
 * @return string
 */
function ps_opt( $key ) {
	$defaults = ps_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	$value    = get_theme_mod( 'ps_' . $key, $default );
	return is_scalar( $value ) ? (string) $value : $default;
}

/**
 * Current language slug: "en" (default) or "ar".
 *
 * @return string
 */
function ps_lang() {
	if ( function_exists( 'pll_current_language' ) ) {
		$slug = pll_current_language( 'slug' );
		if ( $slug ) {
			return ( 0 === strpos( $slug, 'ar' ) ) ? 'ar' : 'en';
		}
	}
	return ( 0 === strpos( get_locale(), 'ar' ) ) ? 'ar' : 'en';
}

/**
 * Hard-coded interface strings used by the theme (header, footer, forms, sub-nav).
 *
 * @param string $key String key.
 * @return string
 */
function ps_t( $key ) {
	static $strings = null;
	if ( null === $strings ) {
		$strings = array(
			'en' => array(
				'skip'              => 'Skip to content',
				'menu'              => 'Menu',
				'contact_cta'       => 'Contact Us',
				'switch'            => 'العربية',
				'footer_about'      => 'PENTA SERVE KSA provides integrated services for developing refrigeration and freezing warehouse projects, including planning, design, coordination of equipment and implementation, operations, management and supervision, in cooperation with specialized technical entities.',
				'quick_links'       => 'Quick Links',
				'contact_us'        => 'Contact Us',
				'rights'            => 'All rights reserved.',
				'privacy_label'     => 'Privacy Policy',
				'terms_label'       => 'Terms & Conditions',
				'legal_nav'         => 'Legal links',
				'visual_disclaimer' => 'The images displayed on this website are for illustrative purposes only and represent examples of facilities, equipment and technical solutions related to the company\'s activities. They do not necessarily represent projects executed or owned by PENTA SERVE, unless expressly stated otherwise.',
				'email'             => 'Email',
				'phone'             => 'Phone',
				'address'           => 'Address',
				'whatsapp'          => 'WhatsApp',
				'call'              => 'Call Us',
				'contact_title'     => 'Contact Details',
				'f_name'            => 'Full Name',
				'f_email'           => 'Email Address',
				'f_phone'           => 'Phone Number',
				'f_subject'         => 'Subject',
				'f_message'         => 'Message',
				'f_send'            => 'Send',
				// Investment form fields.
				'inv_location'      => 'Proposed Project Location',
				'inv_type'          => 'Project Type',
				'inv_capacity'      => 'Expected Storage Capacity',
				'inv_stage'         => 'Project Stage',
				'inv_details'       => 'Additional Details',
				'inv_submit'        => 'Submit Project Inquiry',
				'stage_idea'        => 'Initial Idea',
				'stage_study'       => 'Under Study',
				'stage_ready'       => 'Ready for Implementation',
				'select'            => 'Select...',
				'ok'                => 'Thank you for contacting PENTA SERVE. We have received your message and will reply as soon as possible.',
				'inv_ok'            => 'Thank you for submitting your project details. Our team will review your inquiry and contact you soon.',
				'err'               => 'Your message could not be sent. Please try again or email us directly.',
				'invalid'           => 'Please check the required fields and make sure the data is valid.',
				'wait'              => 'Several messages were sent in a short time. Please try again later.',
				'play'              => 'Play video',
				'not_found'         => 'Page not found',
				'not_found_txt'     => 'The page you requested could not be found.',
				'home'              => 'Back to home',
				'menu_home'         => 'Home',
				'top'               => 'Back to top',
				'video_title'       => 'PENTA SERVE introduction video',
				'video_invest_title' => 'The investment opportunity in Saudi Arabia - PENTA SERVE',
				// Services & Investment sticky sub-nav.
				'subnav_label'      => 'Page sections',
				'subnav_services'   => 'Our Services',
				'subnav_sectors'    => 'Our Solution Areas',
				'subnav_investment' => 'Investment Opportunities',
				'subnav_request'    => 'Request Discussion',
			),
			'ar' => array(
				'skip'              => 'تخطي إلى المحتوى',
				'menu'              => 'القائمة',
				'contact_cta'       => 'تواصل معنا',
				'switch'            => 'English',
				'footer_about'      => 'تقدم PENTA SERVE KSA خدمات متكاملة لتطوير مشاريع مستودعات التبريد والتجميد، تشمل التخطيط والتصميم وتنسيق التجهيز والتنفيذ والتشغيل والإدارة والإشراف، بالتعاون مع الجهات الفنية المتخصصة.',
				'quick_links'       => 'روابط سريعة',
				'contact_us'        => 'تواصل معنا',
				'rights'            => 'جميع الحقوق محفوظة.',
				'privacy_label'     => 'سياسة الخصوصية',
				'terms_label'       => 'الشروط والأحكام',
				'legal_nav'         => 'روابط قانونية',
				'visual_disclaimer' => 'الصور المعروضة على هذا الموقع لأغراض توضيحية، وتمثل نماذج للمرافق والمعدات والحلول الفنية المرتبطة بنشاط الشركة، ولا تمثل بالضرورة مشاريع منفذة أو مملوكة لشركة PENTA SERVE، ما لم يُذكر خلاف ذلك صراحةً.',
				'email'             => 'البريد الإلكتروني',
				'phone'             => 'الهاتف',
				'address'           => 'العنوان',
				'whatsapp'          => 'واتساب',
				'call'              => 'اتصل بنا',
				'contact_title'     => 'بيانات التواصل',
				'f_name'            => 'الاسم الكامل',
				'f_email'           => 'البريد الإلكتروني',
				'f_phone'           => 'رقم الهاتف',
				'f_subject'         => 'الموضوع',
				'f_message'         => 'الرسالة',
				'f_send'            => 'إرسال',
				// Investment form fields.
				'inv_location'      => 'موقع المشروع المقترح',
				'inv_type'          => 'نوع المشروع',
				'inv_capacity'      => 'السعة التخزينية المتوقعة',
				'inv_stage'         => 'مرحلة المشروع',
				'inv_details'       => 'تفاصيل إضافية',
				'inv_submit'        => 'إرسال طلب مناقشة المشروع',
				'stage_idea'        => 'فكرة أولية',
				'stage_study'       => 'قيد الدراسة',
				'stage_ready'       => 'جاهز للتنفيذ',
				'select'            => 'اختر...',
				'ok'                => 'شكراً لتواصلك مع PENTA SERVE. استلمنا رسالتك وسنرد عليك في أقرب وقت.',
				'inv_ok'            => 'شكراً لإرسال بيانات مشروعك. سيقوم فريقنا بمراجعة طلبك والتواصل معك في أقرب وقت.',
				'err'               => 'تعذّر إرسال الرسالة. يرجى المحاولة مرة أخرى أو مراسلتنا على البريد الإلكتروني.',
				'invalid'           => 'يرجى مراجعة الحقول المطلوبة والتأكد من صحة البيانات.',
				'wait'              => 'تم إرسال عدة رسائل خلال وقت قصير. يرجى المحاولة لاحقاً.',
				'play'              => 'تشغيل الفيديو',
				'not_found'         => 'الصفحة غير موجودة',
				'not_found_txt'     => 'تعذّر العثور على الصفحة المطلوبة.',
				'home'              => 'العودة للرئيسية',
				'menu_home'         => 'الرئيسية',
				'top'               => 'العودة إلى أعلى الصفحة',
				'video_title'       => 'الفيديو التعريفي لشركة PENTA SERVE',
				'video_invest_title' => 'الفرصة الاستثمارية في المملكة - PENTA SERVE',
				// Services & Investment sticky sub-nav.
				'subnav_label'      => 'أقسام الصفحة',
				'subnav_services'   => 'خدماتنا',
				'subnav_sectors'    => 'مجالات حلولنا',
				'subnav_investment' => 'فرص الاستثمار',
				'subnav_request'    => 'طلب مناقشة',
			),
		);
	}
	$lang = ps_lang();
	return isset( $strings[ $lang ][ $key ] ) ? $strings[ $lang ][ $key ] : '';
}

/**
 * Current page internal key (_ps_key meta), if on a singular page.
 *
 * @return string
 */
function ps_current_page_key() {
	if ( ! is_page() ) {
		return '';
	}
	$id  = (int) get_queried_object_id();
	$key = (string) get_post_meta( $id, '_ps_key', true );
	if ( ! $key ) {
		$post = get_post( $id );
		if ( $post ) {
			if ( 0 === strpos( $post->post_name, 'about-us' ) ) {
				$key = 'about';
			} elseif ( 0 === strpos( $post->post_name, 'services' ) ) {
				$key = 'services';
			} elseif ( 0 === strpos( $post->post_name, 'contact' ) ) {
				$key = 'contact';
			} elseif ( 0 === strpos( $post->post_name, 'privacy' ) ) {
				$key = 'privacy';
			} elseif ( 0 === strpos( $post->post_name, 'terms' ) ) {
				$key = 'terms';
			}
		}
	}
	return $key;
}

/**
 * Cache version: bumped whenever pages or media change, which invalidates
 * every lookup cache below without having to know individual keys.
 *
 * @return string
 */
function ps_cache_version() {
	$v = get_option( 'ps_cache_ver' );
	if ( ! $v ) {
		$v = (string) time();
		update_option( 'ps_cache_ver', $v, false );
	}
	return (string) $v;
}

function ps_flush_caches() {
	update_option( 'ps_cache_ver', (string) microtime( true ), false );
}
add_action( 'save_post_page', 'ps_flush_caches' );
add_action( 'deleted_post', 'ps_flush_caches' );
add_action( 'trashed_post', 'ps_flush_caches' );
add_action( 'untrashed_post', 'ps_flush_caches' );
add_action( 'pll_save_post', 'ps_flush_caches' );
add_action( 'add_attachment', 'ps_flush_caches' );
add_action( 'edit_attachment', 'ps_flush_caches' );
add_action( 'delete_attachment', 'ps_flush_caches' );
add_action( 'customize_save_after', 'ps_flush_caches' );

/**
 * Find a page created by the setup tool, by its internal key and language.
 * The result is cached (request + transient) so the header, footer and menu
 * do not repeat the same meta query on every page load.
 *
 * @param string      $key  Page key (home, about, services, contact, privacy, terms).
 * @param string|null $lang Language slug; defaults to the current one.
 * @return int Post ID or 0.
 */
function ps_find_page( $key, $lang = null ) {
	static $cache = array();
	$lang = $lang ? $lang : ps_lang();
	$ck   = $key . '|' . $lang;
	if ( isset( $cache[ $ck ] ) ) {
		return $cache[ $ck ];
	}

	$tkey   = 'ps_pg_' . md5( ps_cache_version() . $ck );
	$stored = get_transient( $tkey );
	if ( false !== $stored ) {
		$cache[ $ck ] = (int) $stored;
		return $cache[ $ck ];
	}

	$args = array(
		'post_type'              => 'page',
		'post_status'            => array( 'publish', 'draft', 'private' ),
		'posts_per_page'         => 1,
		'fields'                 => 'ids',
		'meta_key'               => '_ps_key',
		'meta_value'             => $key,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'suppress_filters'       => false,
	);
	if ( function_exists( 'pll_languages_list' ) ) {
		$args['lang'] = $lang;
	}
	$ids = get_posts( $args );
	$id  = $ids ? (int) $ids[0] : 0;

	// Only cache hits, so a page created later is never hidden by a stale "0".
	if ( $id ) {
		set_transient( $tkey, $id, DAY_IN_SECONDS );
	}
	$cache[ $ck ] = $id;
	return $id;
}

/**
 * URL of a page by key in the current language, with a safe fallback.
 *
 * @param string      $key  Page key.
 * @param string|null $lang Language slug.
 * @return string
 */
function ps_page_url( $key, $lang = null ) {
	$id = ps_find_page( $key, $lang );
	if ( $id ) {
		$url = get_permalink( $id );
		if ( $url ) {
			return $url;
		}
	}
	return function_exists( 'pll_home_url' ) ? pll_home_url( $lang ? $lang : null ) : home_url( '/' );
}

/**
 * WhatsApp and phone links.
 */
function ps_whatsapp_url() {
	$number = preg_replace( '/\D+/', '', ps_opt( 'whatsapp' ) );
	return $number ? 'https://wa.me/' . $number : '';
}

function ps_tel_url() {
	$number = preg_replace( '/[^0-9+]/', '', ps_opt( 'phone' ) );
	return $number ? 'tel:' . $number : '';
}

/**
 * Resolve an image value (attachment ID or URL) to an attachment ID.
 * attachment_url_to_postid() runs a database query on every call, so the
 * answer is cached. It also copes with WebP URLs and resized (-WxH) URLs
 * created by optimisation plugins, which core cannot match on its own.
 *
 * @param string|int $val Attachment ID or image URL.
 * @return int Attachment ID or 0.
 */
function ps_image_id( $val ) {
	static $local = array();

	$val = trim( (string) $val );
	if ( '' === $val ) {
		return 0;
	}
	if ( is_numeric( $val ) ) {
		return (int) $val;
	}
	if ( isset( $local[ $val ] ) ) {
		return $local[ $val ];
	}

	$tkey   = 'ps_img_' . md5( ps_cache_version() . $val );
	$stored = get_transient( $tkey );
	if ( false !== $stored ) {
		$local[ $val ] = (int) $stored;
		return $local[ $val ];
	}

	$id = (int) attachment_url_to_postid( $val );

	if ( ! $id ) {
		$candidates = array();
		// Drop the "-300x200" size suffix.
		$no_size = preg_replace( '/-\d+x\d+(\.[A-Za-z0-9]+)(\?.*)?$/', '$1', $val );
		if ( $no_size !== $val ) {
			$candidates[] = $no_size;
		}
		// WebP copies: image.webp or image.jpg.webp -> original format.
		foreach ( array_merge( array( $val ), $candidates ) as $u ) {
			if ( preg_match( '/\.webp(\?.*)?$/i', $u ) ) {
				$base = preg_replace( '/\.webp(\?.*)?$/i', '', $u );
				if ( preg_match( '/\.(jpe?g|png|gif)$/i', $base ) ) {
					$candidates[] = $base;
				} else {
					foreach ( array( 'jpg', 'jpeg', 'png' ) as $ext ) {
						$candidates[] = $base . '.' . $ext;
					}
				}
			}
		}
		foreach ( array_unique( $candidates ) as $u ) {
			$id = (int) attachment_url_to_postid( $u );
			if ( $id ) {
				break;
			}
		}
	}

	if ( $id ) {
		set_transient( $tkey, $id, DAY_IN_SECONDS );
	}
	$local[ $val ] = $id;
	return $id;
}

/**
 * Render an image slot with lazy loading, responsive srcset/sizes,
 * editable alt text, and a graceful branded gradient fallback when unset.
 *
 * @param string $slot    Slot key (e.g. 'sector_1', 'investment', 'service_1').
 * @param string $variant Optional modifier ('service', 'invest').
 * @return string HTML markup.
 */
function ps_render_image_slot( $slot, $variant = '' ) {
	$val = trim( ps_opt( 'img_' . $slot ) );
	$alt = trim( ps_opt( 'img_' . $slot . '_alt_' . ps_lang() ) );
	$cls = 'ps-slot-img-wrap' . ( $variant ? ' ps-slot-img-wrap--' . sanitize_html_class( $variant ) : '' );

	// For optional service slots, render nothing if no image is configured.
	if ( 0 === strpos( $slot, 'service_' ) && ! $val ) {
		return '';
	}

	if ( ! $val ) {
		return sprintf(
			'<div class="%s" role="img" aria-label="%s"></div>',
			esc_attr( $cls ),
			esc_attr( $alt )
		);
	}

	// Card width: full width on phones, 2 columns on tablets, up to 3 on desktop.
	if ( 'invest' === $variant ) {
		$sizes = '(max-width: 900px) 100vw, 560px';
	} elseif ( 0 === strpos( $slot, 'sector_' ) ) {
		$sizes = '(max-width: 640px) 100vw, 560px'; // Solution areas: 2 columns on desktop.
	} else {
		$sizes = '(max-width: 640px) 100vw, (max-width: 960px) 50vw, 400px';
	}

	$att_id = ps_image_id( $val );

	if ( $att_id ) {
		$img = wp_get_attachment_image(
			$att_id,
			'large',
			false,
			array(
				'class'    => 'ps-slot-img',
				'alt'      => $alt,
				'sizes'    => $sizes,
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		);
		if ( $img ) {
			return sprintf( '<div class="%s">%s</div>', esc_attr( $cls ), $img );
		}
	}

	return sprintf(
		'<div class="%1$s"><img class="ps-slot-img" src="%2$s" alt="%3$s" width="1200" height="750" loading="lazy" decoding="async"></div>',
		esc_attr( $cls ),
		esc_url( $val ),
		esc_attr( $alt )
	);
}

/**
 * Escape text and keep the company name on a single line (never split
 * "PENTA SERVE" / "PENTA SERVE KSA" across two lines).
 *
 * @param string $text Plain text.
 * @return string Safe HTML.
 */
function ps_nowrap_brand( $text ) {
	return preg_replace( '/PENTA SERVE(?: KSA)?/', '<span class="ps-nowrap">$0</span>', esc_html( $text ) );
}

/**
 * Best-effort real visitor IP (used only for the form rate limit).
 * Honours Cloudflare's header only when the request really comes through it.
 *
 * @return string
 */
function ps_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) && ! empty( $_SERVER['HTTP_CF_RAY'] ) ) {
		$cf = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) );
		if ( filter_var( $cf, FILTER_VALIDATE_IP ) ) {
			$ip = $cf;
		}
	}
	return apply_filters( 'ps_client_ip', $ip );
}

/**
 * Heading shown in the title band of a page.
 *
 * @param int $post_id Page ID (defaults to the current page).
 * @return string
 */
function ps_page_heading( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$title   = get_the_title( $post_id );
	$key     = get_post_meta( $post_id, '_ps_key', true );

	if ( ! $key ) {
		$post = get_post( $post_id );
		if ( $post && 0 === strpos( $post->post_name, 'about-us' ) ) {
			$key = 'about';
		}
	}

	if ( 'about' === $key && ! preg_match( '/[?\x{061F}]\s*$/u', $title ) ) {
		$title .= ( 'ar' === ps_lang() ) ? "\u{061F}" : '?';
	}
	return $title;
}