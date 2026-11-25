<?php
/**
 * SEO: unique keyword-aware title & meta description per page/language,
 * canonical URL, hreflang alternates (when Polylang does not output them),
 * Open Graph locale tags, and Organization + WebSite JSON-LD schemas.
 * Skipped automatically when a dedicated SEO plugin is active.
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ps_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * Custom SEO titles per page key and language.
 */
function ps_document_title_parts( $parts ) {
	if ( ps_seo_plugin_active() ) {
		return $parts;
	}
	$key  = ps_current_page_key();
	$lang = ps_lang();

	$titles = array(
		'en' => array(
			'home'     => 'PENTA SERVE KSA | Cold Storage & Freezing Warehouse Investment in Saudi Arabia',
			'about'    => 'About Us | PENTA SERVE KSA – Refrigerated & Frozen Storage Project Partner',
			'services' => 'Services & Investment Opportunities | PENTA SERVE KSA Cold Storage Solutions',
			'contact'  => 'Contact Us | PENTA SERVE KSA – Discuss Your Cold Storage Project',
			'privacy'  => 'Privacy Policy | PENTA SERVE KSA',
			'terms'    => 'Terms & Conditions | PENTA SERVE KSA',
		),
		'ar' => array(
			'home'     => 'PENTA SERVE KSA | الاستثمار في مستودعات التبريد والتجميد في السعودية',
			'about'    => 'من نحن | PENTA SERVE KSA – شريك تطوير مشاريع التخزين المبرد والمجمد',
			'services' => 'الخدمات وفرص الاستثمار | PENTA SERVE KSA لحلول مستودعات التبريد والتجميد',
			'contact'  => 'تواصل معنا | PENTA SERVE KSA – ناقش مشروعك في التخزين المبرد',
			'privacy'  => 'سياسة الخصوصية | PENTA SERVE KSA',
			'terms'    => 'الشروط والأحكام | PENTA SERVE KSA',
		),
	);

	if ( $key && isset( $titles[ $lang ][ $key ] ) ) {
		return array( 'title' => $titles[ $lang ][ $key ] );
	}
	return $parts;
}
add_filter( 'document_title_parts', 'ps_document_title_parts', 20 );

function ps_meta_description() {
	// Unique description per page and language (editable here or via an SEO plugin).
	$descriptions = array(
		'en' => array(
			'home'     => 'PENTA SERVE KSA helps investors develop commercial refrigeration and freezing warehouse projects in Saudi Arabia, from initial assessment and design to operations and management.',
			'about'    => 'Learn about PENTA SERVE KSA, a project partner for refrigerated and frozen storage developments, and how we support investors from concept to operation.',
			'services' => 'Explore PENTA SERVE KSA services and cold storage investment opportunities: assessment, design, insulated panels, refrigeration systems, operations and management.',
			'contact'  => 'Contact PENTA SERVE KSA to discuss your refrigeration and freezing warehouse project. Send an inquiry by form, email, phone or WhatsApp.',
			'privacy'  => 'Privacy Policy of PENTA SERVE KSA: how we collect, use and protect information submitted through this website.',
			'terms'    => 'Terms and Conditions for using the PENTA SERVE KSA website.',
		),
		'ar' => array(
			'home'     => 'تساعد PENTA SERVE KSA المستثمرين على تطوير مشاريع مستودعات التبريد والتجميد التجارية في المملكة العربية السعودية، من التقييم الأولي والتصميم حتى التشغيل والإدارة.',
			'about'    => 'تعرّف على PENTA SERVE KSA، شريك تطوير مشاريع التخزين المبرد والمجمد، وكيف ندعم المستثمرين من الفكرة حتى التشغيل.',
			'services' => 'اكتشف خدمات PENTA SERVE KSA وفرص الاستثمار في التخزين المبرد: التقييم والتصميم والألواح المعزولة وأنظمة التبريد والتشغيل والإدارة.',
			'contact'  => 'تواصل مع PENTA SERVE KSA لمناقشة مشروع مستودع التبريد والتجميد الخاص بك عبر النموذج أو البريد أو الهاتف أو واتساب.',
			'privacy'  => 'سياسة الخصوصية لدى PENTA SERVE KSA: كيف نجمع المعلومات المرسلة عبر الموقع ونستخدمها ونحميها.',
			'terms'    => 'الشروط والأحكام الخاصة باستخدام موقع PENTA SERVE KSA.',
		),
	);

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post && has_excerpt( $post ) ) {
			return wp_strip_all_tags( get_the_excerpt( $post ) );
		}
		$key  = ps_current_page_key();
		$lang = ps_lang();
		if ( $key && isset( $descriptions[ $lang ][ $key ] ) ) {
			return $descriptions[ $lang ][ $key ];
		}
	}
	return get_bloginfo( 'description' );
}

/**
 * Check if Polylang is already printing hreflang link tags in wp_head.
 */
function ps_polylang_prints_hreflang() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return false;
	}
	return ( false !== has_action( 'wp_head', 'pll_the_languages' ) ) || class_exists( 'PLL_Frontend_Links' );
}

function ps_head_meta() {
	if ( ps_seo_plugin_active() ) {
		return;
	}
	$desc   = trim( ps_meta_description() );
	$url    = is_singular() ? get_permalink() : ( function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' ) );
	$lang   = ps_lang();
	$locale = ( 'ar' === $lang ) ? 'ar_SA' : 'en_US';
	$alt    = ( 'ar' === $lang ) ? 'en_US' : 'ar_SA';

	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );

	if ( ! ps_polylang_prints_hreflang() && is_page() ) {
		$key = ps_current_page_key();
		if ( $key ) {
			$en_url = ps_page_url( $key, 'en' );
			$ar_url = ps_page_url( $key, 'ar' );
			if ( $en_url ) {
				printf( '<link rel="alternate" hreflang="en" href="%s">' . "\n", esc_url( $en_url ) );
				printf( '<link rel="alternate" hreflang="x-default" href="%s">' . "\n", esc_url( $en_url ) );
			}
			if ( $ar_url ) {
				printf( '<link rel="alternate" hreflang="ar" href="%s">' . "\n", esc_url( $ar_url ) );
			}
		}
	}

	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	printf( '<meta property="og:type" content="website">' . "\n" );
	printf( '<meta property="og:site_name" content="PENTA SERVE KSA">' . "\n" );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( wp_get_document_title() ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( $locale ) );
	printf( '<meta property="og:locale:alternate" content="%s">' . "\n", esc_attr( $alt ) );

	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$src = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $src ) {
			printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $src ) );
		}
	}
}
add_action( 'wp_head', 'ps_head_meta', 5 );

/**
 * Organization + WebSite structured data (using only facts from the brief).
 */
function ps_head_schema() {
	if ( ps_seo_plugin_active() || ! is_front_page() ) {
		return;
	}
	$lang = ps_lang();
	$org  = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => 'PENTA SERVE KSA',
		'url'      => home_url( '/' ),
		'email'    => ps_opt( 'email' ),
	);
	$tel = preg_replace( '/[^0-9+]/', '', ps_opt( 'phone' ) );
	if ( $tel ) {
		$org['telephone'] = $tel;
	}
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$src = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $src ) {
			$org['logo'] = $src;
		}
	}
	$linkedin = trim( (string) ps_opt( 'social_linkedin' ) );
	if ( $linkedin ) {
		$org['sameAs'] = array( $linkedin );
	}

	$website = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'WebSite',
		'name'       => 'PENTA SERVE KSA',
		'url'        => home_url( '/' ),
		'inLanguage' => ( 'ar' === $lang ) ? 'ar' : 'en',
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	echo '<script type="application/ld+json">' . wp_json_encode( $website, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'ps_head_schema', 6 );