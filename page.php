<?php
/**
 * Template for pages (all site pages use this one).
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	// The home page carries its own hero section inside the content.
	if ( ! is_front_page() ) :
		?>
		<section class="ps-page-hero">
			<h1><?php echo esc_html( ps_page_heading() ); ?></h1>
		</section>
	<?php endif; ?>

	<article <?php post_class( 'ps-entry' ); ?>>
		<?php the_content(); ?>
	</article>
	<?php
endwhile;

get_footer();
