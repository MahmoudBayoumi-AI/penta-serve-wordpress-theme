<?php
/**
 * Fallback template (posts, archives, search).
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="ps-page-hero">
	<h1><?php echo is_search() ? esc_html( get_search_query() ) : esc_html( get_the_archive_title() ? get_the_archive_title() : get_bloginfo( 'name' ) ); ?></h1>
</section>

<div class="ps-plain">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php echo esc_html( ps_t( 'not_found_txt' ) ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
