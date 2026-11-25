<?php
/**
 * PENTA SERVE theme bootstrap.
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PS_VERSION', '24.0.0' );
define( 'PS_DIR', get_template_directory() );
define( 'PS_URI', get_template_directory_uri() );

require_once PS_DIR . '/inc/helpers.php';
require_once PS_DIR . '/inc/customizer.php';
require_once PS_DIR . '/inc/seo.php';
require_once PS_DIR . '/inc/blocks.php';
require_once PS_DIR . '/inc/contact.php';
require_once PS_DIR . '/inc/shortcodes.php';
require_once PS_DIR . '/inc/installer.php';

/**
 * Theme supports and menu locations.
 */
function ps_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo', array(
		'height'      => 140,
		'width'       => 420,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	register_nav_menus( array(
		'primary' => __( 'Primary menu / القائمة الرئيسية', 'penta-serve' ),
		'footer'  => __( 'Footer menu / قائمة التذييل', 'penta-serve' ),
	) );
}
add_action( 'after_setup_theme', 'ps_setup' );

/**
 * Front-end assets.
 */
function ps_assets() {
	wp_enqueue_style(
		'ps-fonts',
		'https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;800&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'penta-serve', get_stylesheet_uri(), array( 'ps-fonts' ), PS_VERSION );

	wp_enqueue_script(
		'penta-serve',
		PS_URI . '/assets/js/main.js',
		array(),
		PS_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ps_assets' );

/**
 * Speed up the font request.
 */
function ps_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
		);
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'ps_resource_hints', 10, 2 );

/**
 * Remove emoji scripts (not needed, saves requests).
 */
function ps_trim_head() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
}
add_action( 'init', 'ps_trim_head' );

/**
 * Body classes: language and page type.
 */
function ps_body_class( $classes ) {
	$classes[] = 'ps-lang-' . ps_lang();
	$key       = ps_current_page_key();
	if ( $key ) {
		$classes[] = 'ps-page-' . $key;
	}
	return $classes;
}
add_filter( 'body_class', 'ps_body_class' );

/**
 * Style the Contact Us primary menu item as the single header CTA button.
 */
function ps_primary_menu_item_classes( $classes, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
		$contact_id = ps_find_page( 'contact' );
		$is_contact = ( $contact_id && (int) $item->object_id === (int) $contact_id );
		if ( ! $is_contact && ! empty( $item->url ) ) {
			$path = trim( (string) wp_parse_url( $item->url, PHP_URL_PATH ), '/' );
			if ( preg_match( '~(?:^|/)contact(?:-us)?$~i', $path ) ) {
				$is_contact = true;
			}
		}
		if ( $is_contact ) {
			$classes[] = 'ps-menu-cta';
		}
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'ps_primary_menu_item_classes', 10, 3 );

/**
 * Is this primary-menu item the Home page?
 *
 * @param object $item Menu item.
 * @return bool
 */
function ps_menu_item_is_home( $item ) {
	$home_id = ps_find_page( 'home' );
	if ( $home_id && 'page' === $item->object && (int) $item->object_id === (int) $home_id ) {
		return true;
	}
	if ( ! empty( $item->url ) ) {
		$home_url = function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
		if ( untrailingslashit( $item->url ) === untrailingslashit( $home_url ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Always show the correct label for the Home item ("Home" / "الرئيسية"),
 * whatever title was saved in the menu or machine-translated.
 */
function ps_menu_home_label( $title, $item, $args ) {
	if ( isset( $args->theme_location ) && in_array( $args->theme_location, array( 'primary', 'footer' ), true ) && ps_menu_item_is_home( $item ) ) {
		return ps_t( 'menu_home' );
	}
	return $title;
}
add_filter( 'nav_menu_item_title', 'ps_menu_home_label', 10, 3 );

/**
 * Does this menu item point to the page being viewed?
 *
 * @param object $item Menu item.
 * @return bool
 */
function ps_menu_item_is_current( $item ) {
	if ( is_front_page() && ps_menu_item_is_home( $item ) ) {
		return true;
	}
	if ( is_page() && 'page' === $item->object && (int) $item->object_id === (int) get_queried_object_id() ) {
		return true;
	}
	if ( ! empty( $item->url ) && is_singular() ) {
		return untrailingslashit( $item->url ) === untrailingslashit( get_permalink() );
	}
	return false;
}

/**
 * Highlight the active page in the header menu (also for custom-link items,
 * which WordPress does not mark as current by itself).
 */
function ps_menu_current_class( $classes, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location && ps_menu_item_is_current( $item ) ) {
		$classes[] = 'current-menu-item';
		$classes[] = 'ps-is-active';
	}
	return array_unique( $classes );
}
add_filter( 'nav_menu_css_class', 'ps_menu_current_class', 20, 3 );

function ps_menu_current_aria( $atts, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location && ps_menu_item_is_current( $item ) ) {
		$atts['aria-current'] = 'page';
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'ps_menu_current_aria', 20, 3 );

/**
 * 301 redirects from legacy /sectors and /investment-opportunities URLs
 * (both languages) to Services & Investment Opportunities with section anchors.
 */
function ps_legacy_page_redirects() {
	if ( is_admin() ) {
		return;
	}
	$req_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$req_path = trim( (string) wp_parse_url( $req_uri, PHP_URL_PATH ), '/' );

	$resolved_key = '';
	if ( is_page() ) {
		$resolved_key = (string) get_post_meta( get_queried_object_id(), '_ps_key', true );
	}

	$target_anchor = '';
	$target_lang   = ps_lang();

	if ( 'sectors' === $resolved_key || preg_match( '~^(?:(ar|en)/)?sectors/?$~i', $req_path, $m ) ) {
		$target_anchor = '#sectors';
		if ( ! empty( $m[1] ) ) {
			$target_lang = strtolower( $m[1] );
		}
	} elseif ( 'investment' === $resolved_key || preg_match( '~^(?:(ar|en)/)?investment(?:-opportunities)?/?$~i', $req_path, $m ) ) {
		$target_anchor = '#investment';
		if ( ! empty( $m[1] ) ) {
			$target_lang = strtolower( $m[1] );
		}
	}

	if ( $target_anchor ) {
		$services_url = ps_page_url( 'services', $target_lang );
		if ( $services_url ) {
			wp_safe_redirect( $services_url . $target_anchor, 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'ps_legacy_page_redirects', 1 );

/**
 * Menu fallback when no menu is assigned yet (4 primary pages only).
 */
function ps_menu_fallback() {
	$keys = array( 'home', 'services', 'about', 'contact' );
	echo '<ul class="ps-menu">';
	foreach ( $keys as $key ) {
		$id = ps_find_page( $key );
		if ( ! $id ) {
			continue;
		}
		$classes = array();
		if ( 'contact' === $key ) {
			$classes[] = 'ps-menu-cta';
		}
		if ( is_page( $id ) ) {
			$classes[] = 'current-menu-item';
			$classes[] = 'ps-is-active';
		}
		printf(
			'<li%1$s><a href="%2$s"%4$s>%3$s</a></li>',
			$classes ? ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_url( get_permalink( $id ) ),
			esc_html( 'home' === $key ? ps_t( 'menu_home' ) : get_the_title( $id ) ),
			is_page( $id ) ? ' aria-current="page"' : '' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
	echo '</ul>';
}

