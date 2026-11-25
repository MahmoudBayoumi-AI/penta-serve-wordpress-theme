<?php
/**
 * Footer template.
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ps_email       = ps_opt( 'email' );
$ps_phone       = ps_opt( 'phone_label' );
$ps_address     = ps_opt( 'ar' === ps_lang() ? 'address_ar' : 'address_en' );
$ps_wa          = ps_whatsapp_url();
$ps_tel         = ps_tel_url();
$ps_footer_logo = ps_opt( 'footer_logo' );
$ps_logo_id     = (int) get_theme_mod( 'custom_logo' );
$ps_home        = function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
$ps_linkedin    = trim( (string) ps_opt( 'social_linkedin' ) );
$ps_privacy_url = ps_find_page( 'privacy' ) ? ps_page_url( 'privacy' ) : '';
$ps_terms_url   = ps_find_page( 'terms' ) ? ps_page_url( 'terms' ) : '';
?>
</main>

<footer class="ps-footer">
	<div class="ps-footer__grid">
		<div class="ps-footer__brand">
			<?php if ( $ps_footer_logo ) : ?>
				<a class="ps-footer__logo" href="<?php echo esc_url( $ps_home ); ?>">
					<img class="ps-footer__logo-img" src="<?php echo esc_url( $ps_footer_logo ); ?>" alt="PENTA SERVE KSA" loading="lazy" decoding="async">
				</a>
			<?php elseif ( $ps_logo_id ) : ?>
				<a class="ps-footer__logo ps-footer__logo--plate" href="<?php echo esc_url( $ps_home ); ?>">
					<?php
					echo wp_get_attachment_image( $ps_logo_id, 'full', false, array(
						'class'    => 'ps-footer__logo-img',
						'alt'      => 'PENTA SERVE KSA',
						'loading'  => 'lazy',
						'decoding' => 'async',
						'sizes'    => '240px',
					) );
					?>
				</a>
			<?php else : ?>
				<a class="ps-brand" href="<?php echo esc_url( $ps_home ); ?>">PENTA <span>SERVE</span></a>
			<?php endif; ?>
			<p><?php echo ps_nowrap_brand( ps_t( 'footer_about' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php if ( $ps_linkedin ) : ?>
				<ul class="ps-footer__social">
					<li><a href="<?php echo esc_url( $ps_linkedin ); ?>" target="_blank" rel="noopener">LinkedIn</a></li>
				</ul>
			<?php endif; ?>
		</div>

		<div>
			<h3><?php echo esc_html( ps_t( 'quick_links' ) ); ?></h3>
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'ps-footer-menu',
					'fallback_cb'    => false,
					'depth'          => 1,
				) );
			} else {
				echo '<ul class="ps-footer-menu">';
				foreach ( array( 'home', 'about', 'services', 'contact' ) as $k ) {
					$pid = ps_find_page( $k );
					if ( $pid ) {
						printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $pid ) ), esc_html( 'home' === $k ? ps_t( 'menu_home' ) : get_the_title( $pid ) ) );
					}
				}
				echo '</ul>';
			}
			?>
		</div>

		<div>
			<h3><?php echo esc_html( ps_t( 'contact_us' ) ); ?></h3>
			<ul>
				<?php if ( $ps_email ) : ?>
					<li><a href="mailto:<?php echo esc_attr( antispambot( $ps_email ) ); ?>"><?php echo esc_html( antispambot( $ps_email ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( $ps_phone ) : ?>
					<li><a href="<?php echo esc_url( $ps_tel ); ?>" dir="ltr"><?php echo esc_html( $ps_phone ); ?></a></li>
				<?php endif; ?>
				<?php if ( $ps_wa ) : ?>
					<li><a href="<?php echo esc_url( $ps_wa ); ?>" target="_blank" rel="noopener"><?php echo esc_html( ps_t( 'whatsapp' ) ); ?>: <span dir="ltr"><?php echo esc_html( $ps_phone ? $ps_phone : '+966 50 600 5453' ); ?></span></a></li>
				<?php endif; ?>
				<?php if ( $ps_address ) : ?>
					<li><?php echo esc_html( $ps_address ); ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	<div class="ps-footer__bar">
		<p class="ps-footer__disclaimer"><?php echo ps_nowrap_brand( ps_t( 'visual_disclaimer' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
		<div class="ps-footer__legal">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <span class="ps-nowrap">PENTA SERVE KSA.</span> <?php echo esc_html( ps_t( 'rights' ) ); ?></span>
			<?php if ( $ps_privacy_url || $ps_terms_url ) : ?>
				<nav class="ps-footer__legal-links" aria-label="<?php echo esc_attr( ps_t( 'legal_nav' ) ); ?>">
					<?php if ( $ps_privacy_url ) : ?>
						<a href="<?php echo esc_url( $ps_privacy_url ); ?>"><?php echo esc_html( ps_t( 'privacy_label' ) ); ?></a>
					<?php endif; ?>
					<?php if ( $ps_terms_url ) : ?>
						<a href="<?php echo esc_url( $ps_terms_url ); ?>"><?php echo esc_html( ps_t( 'terms_label' ) ); ?></a>
					<?php endif; ?>
				</nav>
			<?php endif; ?>
		</div>
	</div>
</footer>

<button type="button" class="ps-top" id="ps-top" aria-label="<?php echo esc_attr( ps_t( 'top' ) ); ?>" title="<?php echo esc_attr( ps_t( 'top' ) ); ?>">
	<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</button>

<div class="ps-float">
	<?php if ( $ps_wa ) : ?>
		<a class="ps-float__wa" href="<?php echo esc_url( $ps_wa ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ps_t( 'whatsapp' ) ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2a9.9 9.9 0 0 0-8.5 14.95L2 22l5.2-1.36A9.9 9.9 0 1 0 12.04 2Zm0 1.8a8.1 8.1 0 1 1-4.23 15.02l-.3-.18-3.08.8.82-3-.2-.31A8.1 8.1 0 0 1 12.04 3.8Zm-3.2 3.9c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.06 3.3 5.1 4.5 2.52 1 3.03.8 3.58.75.55-.05 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35-.3-.15-1.77-.87-2.04-.97-.28-.1-.48-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5-.9-.8-1.5-1.78-1.67-2.08-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.2-.24-.58-.49-.5-.67-.5h-.57Z"/></svg>
		</a>
	<?php endif; ?>
	<?php if ( $ps_tel ) : ?>
		<a class="ps-float__call" href="<?php echo esc_url( $ps_tel ); ?>" aria-label="<?php echo esc_attr( ps_t( 'call' ) ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25c1.1.37 2.3.57 3.6.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1l-2.22 2.23Z"/></svg>
		</a>
	<?php endif; ?>
</div>

<?php wp_footer(); ?>
</body>
</html>