<?php
/**
 * Header template.
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Language switcher target (Polylang): points to the translated version of the current page.
$ps_switch_url  = '';
$ps_switch_lang = '';
$ps_target_lang = ( 'ar' === ps_lang() ) ? 'en' : 'ar';

if ( function_exists( 'pll_the_languages' ) ) {
	$langs = pll_the_languages( array( 'raw' => 1, 'hide_if_no_translation' => 0 ) );
	if ( is_array( $langs ) ) {
		foreach ( $langs as $l ) {
			if ( empty( $l['current_lang'] ) ) {
				$ps_switch_url  = $l['url'];
				$ps_switch_lang = $l['slug'];
				break;
			}
		}
	}
}

if ( is_page() ) {
	$ps_cur_key = ps_current_page_key();
	if ( $ps_cur_key ) {
		$ps_tr_id = ps_find_page( $ps_cur_key, $ps_target_lang );
		if ( $ps_tr_id ) {
			$ps_tr_url = get_permalink( $ps_tr_id );
			if ( $ps_tr_url && ( ! $ps_switch_url || untrailingslashit( $ps_switch_url ) === untrailingslashit( get_permalink() ) ) ) {
				$ps_switch_url  = $ps_tr_url;
				$ps_switch_lang = $ps_target_lang;
			}
		}
	}
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#102B49">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="ps-skip" href="#content"><?php echo esc_html( ps_t( 'skip' ) ); ?></a>

<header class="ps-header" id="ps-header">
	<div class="ps-header__inner">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a class="ps-brand" href="<?php echo esc_url( function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' ) ); ?>">PENTA <span>SERVE</span></a>
		<?php endif; ?>

		<button class="ps-nav-toggle" type="button" aria-expanded="false" aria-controls="ps-nav">
			<span class="ps-burger"></span>
			<span class="screen-reader-text"><?php echo esc_html( ps_t( 'menu' ) ); ?></span>
		</button>

		<nav class="ps-nav" id="ps-nav" aria-label="<?php echo esc_attr( ps_t( 'menu' ) ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'ps-menu',
				'fallback_cb'    => 'ps_menu_fallback',
				'depth'          => 1,
			) );
			?>
			<?php if ( $ps_switch_url ) : ?>
				<div class="ps-nav__actions">
					<a class="ps-lang" href="<?php echo esc_url( $ps_switch_url ); ?>" hreflang="<?php echo esc_attr( $ps_switch_lang ); ?>" lang="<?php echo esc_attr( $ps_switch_lang ); ?>"><?php echo esc_html( ps_t( 'switch' ) ); ?></a>
				</div>
			<?php endif; ?>
		</nav>
	</div>
</header>

<main class="ps-main" id="content">