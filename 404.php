<?php
/**
 * 404 template.
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="ps-page-hero">
	<h1><?php echo esc_html( ps_t( 'not_found' ) ); ?></h1>
</section>
<div class="ps-plain">
	<p><?php echo esc_html( ps_t( 'not_found_txt' ) ); ?></p>
	<p><a class="ps-cta" href="<?php echo esc_url( function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' ) ); ?>"><?php echo esc_html( ps_t( 'home' ) ); ?></a></p>
</div>
<?php
get_footer();
