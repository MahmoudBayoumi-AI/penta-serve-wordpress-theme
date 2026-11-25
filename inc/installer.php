<?php
/**
 * Idempotent one-click setup: creates/updates the 4 core pages + 2 legal pages
 * (English default + Arabic), links Polylang translations, builds menus,
 * cleans up legacy standalone sectors/investment pages, and sets front page.
 *
 * Location: Appearance > PENTA SERVE Setup.
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu order and which menu each page belongs to.
 * Primary menu has 4 items only: Home | About Us | Services & Investment Opportunities | Contact Us.
 * Privacy Policy and Terms & Conditions are rendered in the footer bottom legal bar.
 */
function ps_page_map() {
	return array(
		'home'     => array( 'menu' => 'primary', 'order' => 1 ),
		'services' => array( 'menu' => 'primary', 'order' => 2 ),
		'about'    => array( 'menu' => 'primary', 'order' => 3 ),
		'contact'  => array( 'menu' => 'primary', 'order' => 4 ),
		'privacy'  => array( 'menu' => 'none',    'order' => 5 ),
		'terms'    => array( 'menu' => 'none',    'order' => 6 ),
	);
}

function ps_page_slugs() {
	return array(
		'home'     => 'home',
		'about'    => 'about-us',
		'services' => 'services-investment-opportunities',
		'contact'  => 'contact',
		'privacy'  => 'privacy-policy',
		'terms'    => 'terms-conditions',
	);
}

/**
 * Languages the setup can build, with English ('en') first as default.
 */
function ps_setup_languages() {
	$notes = array();
	if ( ! function_exists( 'pll_languages_list' ) ) {
		$notes[] = 'Polylang is not active. Only English pages will be created. Install and activate Polylang, add English (default) and Arabic, then run this setup again.';
		return array( 'langs' => array( 'en' ), 'notes' => $notes );
	}
	$available = pll_languages_list( array( 'fields' => 'slug' ) );
	$langs     = array();
	foreach ( array( 'en', 'ar' ) as $slug ) {
		if ( in_array( $slug, $available, true ) ) {
			$langs[] = $slug;
		} else {
			$notes[] = sprintf( 'Language "%s" is not added in Polylang (Languages > Languages). Add it, then run the setup again.', $slug );
		}
	}
	return array( 'langs' => $langs, 'notes' => $notes );
}

function ps_resolve_placeholders( $content, $lang, $ids ) {
	$content = preg_replace_callback(
		'/\{url:([a-z_-]+)\}/',
		static function ( $m ) use ( $lang, $ids ) {
			$key    = $m[1];
			$anchor = '';

			// توجيه الروابط القديمة المدمجة إلى الأقسام المقابلة داخل صفحة الخدمات والاستثمار
			if ( 'investment' === $key || 'investment-opportunities' === $key ) {
				$key    = 'services';
				$anchor = '#investment';
			} elseif ( 'sectors' === $key ) {
				$key    = 'services';
				$anchor = '#sectors';
			}

			if ( isset( $ids[ $lang ][ $key ] ) ) {
				$url = get_permalink( $ids[ $lang ][ $key ] );
				if ( $url ) {
					return $url . $anchor;
				}
			}
			return home_url( '/' );
		},
		$content
	);
	$wa  = ps_whatsapp_url();
	$tel = ps_tel_url();
	return str_replace(
		array( '{wa}', '{tel}', '{mail}', '{date}' ),
		array( $wa, $tel, ps_opt( 'email' ), wp_date( 'Y-m-d' ) ),
		$content
	);
}

/**
 * Run the full idempotent setup.
 */
function ps_run_setup( $overwrite = false, $cleanup = true ) {
	$log   = array();
	$info  = ps_setup_languages();
	$langs = $info['langs'];
	$log   = array_merge( $log, array_map( static function ( $n ) {
		return 'NOTE: ' . $n;
	}, $info['notes'] ) );

	if ( ! $langs ) {
		$log[] = 'ERROR: no language available. Nothing was created.';
		return $log;
	}

	// Archive (draft) legacy standalone 'sectors' and 'investment' pages so they no longer appear publicly.
	$legacy_pages = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => array( 'publish', 'private' ),
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'     => '_ps_key',
				'value'   => array( 'sectors', 'investment' ),
				'compare' => 'IN',
			),
		),
		'suppress_filters' => true,
	) );
	foreach ( $legacy_pages as $lp ) {
		wp_update_post( array(
			'ID'          => $lp->ID,
			'post_status' => 'draft',
		) );
		$log[] = sprintf( 'Set legacy standalone page #%d (%s) to draft (301 redirects remain active).', $lp->ID, $lp->post_name );
	}

	$map   = ps_page_map();
	$slugs = ps_page_slugs();
	$data  = array();
	foreach ( $langs as $lang ) {
		$file          = PS_DIR . '/inc/content/' . $lang . '.php';
		$data[ $lang ] = file_exists( $file ) ? include $file : array();
	}

	$ids     = array();
	$created = array();

	// Pass 1: find or create pages without duplicating.
	foreach ( $langs as $lang ) {
		foreach ( $map as $key => $meta ) {
			if ( empty( $data[ $lang ][ $key ] ) ) {
				continue;
			}
			$page     = $data[ $lang ][ $key ];
			$existing = ps_find_page_uncached( $key, $lang );

			if ( $existing ) {
				$ids[ $lang ][ $key ] = $existing;
				if ( 'publish' !== get_post_status( $existing ) ) {
					wp_update_post( array( 'ID' => $existing, 'post_status' => 'publish' ) );
				}
				$log[] = sprintf( 'Found existing page [%s/%s] #%d', $lang, $key, $existing );
				continue;
			}

			$post_id = wp_insert_post( array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page['title'],
				'post_name'    => $slugs[ $key ],
				'post_excerpt' => $page['excerpt'],
				'post_content' => '',
				'menu_order'   => $meta['order'],
			), true );

			if ( is_wp_error( $post_id ) ) {
				$log[] = sprintf( 'ERROR creating [%s/%s]: %s', $lang, $key, $post_id->get_error_message() );
				continue;
			}

			update_post_meta( $post_id, '_ps_key', $key );

			if ( function_exists( 'pll_set_post_language' ) ) {
				pll_set_post_language( $post_id, $lang );
				$current = get_post_field( 'post_name', $post_id );
				if ( $current !== $slugs[ $key ] ) {
					wp_update_post( array( 'ID' => $post_id, 'post_name' => $slugs[ $key ] ) );
				}
			}

			$ids[ $lang ][ $key ]     = $post_id;
			$created[ $lang ][ $key ] = true;
			$log[] = sprintf( 'Created page [%s/%s] #%d', $lang, $key, $post_id );
		}
	}

	// Link translations between English and Arabic.
	if ( function_exists( 'pll_save_post_translations' ) && count( $langs ) > 1 ) {
		foreach ( $map as $key => $meta ) {
			$group = array();
			foreach ( $langs as $lang ) {
				if ( isset( $ids[ $lang ][ $key ] ) ) {
					$group[ $lang ] = $ids[ $lang ][ $key ];
				}
			}
			if ( count( $group ) > 1 ) {
				pll_save_post_translations( $group );
			}
		}
		$log[] = 'Linked English and Arabic pages as translations.';
	}

	// Pass 2: write content.
	foreach ( $langs as $lang ) {
		foreach ( $map as $key => $meta ) {
			if ( empty( $ids[ $lang ][ $key ] ) ) {
				continue;
			}
			$is_new = ! empty( $created[ $lang ][ $key ] );
			if ( ! $is_new && ! $overwrite ) {
				continue;
			}
			$page = $data[ $lang ][ $key ];
			wp_update_post( array(
				'ID'           => $ids[ $lang ][ $key ],
				'post_title'   => $page['title'],
				'post_name'    => $slugs[ $key ],
				'post_excerpt' => $page['excerpt'],
				'post_content' => wp_slash( ps_resolve_placeholders( $page['content'], $lang, $ids ) ),
				'menu_order'   => $meta['order'],
			) );
			$log[] = sprintf( 'Wrote content [%s/%s]', $lang, $key );
		}
	}

	// Rebuild primary and footer menus (4 items each) cleanly without duplicates.
	$menu_ids = ps_build_menus( $langs, $ids, $data, true, $log );
	ps_assign_menus( $menu_ids, $langs, $log );

	// Front page (English default).
	$default_lang = in_array( 'en', $langs, true ) ? 'en' : $langs[0];
	if ( ! empty( $ids[ $default_lang ]['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids[ $default_lang ]['home'] );
		$log[] = sprintf( 'Front page set to [%s/home] #%d.', $default_lang, $ids[ $default_lang ]['home'] );
	}

	update_option( 'blogname', 'PENTA SERVE KSA' );
	update_option( 'blogdescription', 'Commercial Refrigeration & Freezing Warehouse Project Development' );

	if ( '/%postname%/' !== get_option( 'permalink_structure' ) ) {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$log[] = 'Permalinks set to "Post name".';
	}

	if ( $cleanup ) {
		$sample = get_page_by_path( 'sample-page' );
		if ( $sample && 'Sample Page' === $sample->post_title ) {
			wp_delete_post( $sample->ID, true );
			$log[] = 'Deleted the "Sample Page".';
		}
		$hello = get_posts( array( 'name' => 'hello-world', 'post_type' => 'post', 'post_status' => 'any', 'numberposts' => 1, 'suppress_filters' => true ) );
		if ( $hello && 'Hello world!' === $hello[0]->post_title ) {
			wp_delete_post( $hello[0]->ID, true );
			$log[] = 'Deleted the "Hello world!" post.';
		}
	}

	flush_rewrite_rules();
	update_option( 'ps_setup_done', time() );
	$log[] = 'DONE.';
	return $log;
}

function ps_find_page_uncached( $key, $lang ) {
	$args = array(
		'post_type'        => 'page',
		'post_status'      => array( 'publish', 'draft', 'private' ),
		'posts_per_page'   => 1,
		'fields'           => 'ids',
		'meta_key'         => '_ps_key',
		'meta_value'       => $key,
		'no_found_rows'    => true,
		'suppress_filters' => false,
	);
	if ( function_exists( 'pll_languages_list' ) ) {
		$args['lang'] = $lang;
	}
	$found = get_posts( $args );
	return $found ? (int) $found[0] : 0;
}

function ps_build_menus( $langs, $ids, $data, $overwrite, &$log ) {
	$map      = ps_page_map();
	$menu_ids = array();

	foreach ( $langs as $lang ) {
		foreach ( array( 'primary' => 'Main Menu', 'footer' => 'Footer Menu' ) as $loc => $label ) {
			$name  = 'PENTA ' . $label . ' - ' . strtoupper( $lang );
			$menu  = wp_get_nav_menu_object( $name );
			$fresh = false;
			if ( ! $menu ) {
				$menu_id = wp_create_nav_menu( $name );
				if ( is_wp_error( $menu_id ) ) {
					$log[] = 'ERROR creating menu ' . $name . ': ' . $menu_id->get_error_message();
					continue;
				}
				$fresh = true;
			} else {
				$menu_id = (int) $menu->term_id;
			}
			$menu_ids[ $lang ][ $loc ] = $menu_id;

			if ( ! $fresh && ! $overwrite ) {
				continue;
			}
			if ( ! $fresh ) {
				foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $old ) {
					wp_delete_post( $old->ID, true );
				}
			}

			// Both primary and footer menus contain the 4 main website pages in exact order.
			foreach ( $map as $key => $meta ) {
				if ( 'primary' !== $meta['menu'] || empty( $ids[ $lang ][ $key ] ) ) {
					continue;
				}
				wp_update_nav_menu_item( $menu_id, 0, array(
					'menu-item-title'     => $data[ $lang ][ $key ]['title'],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $ids[ $lang ][ $key ],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
					'menu-item-position'  => $meta['order'],
				) );
			}
			$log[] = sprintf( 'Built menu "%s" (4 pages).', $name );
		}
	}
	return $menu_ids;
}

function ps_assign_menus( $menu_ids, $langs, &$log ) {
	$default_lang = in_array( 'en', $langs, true ) ? 'en' : $langs[0];

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	foreach ( array( 'primary', 'footer' ) as $loc ) {
		if ( ! empty( $menu_ids[ $default_lang ][ $loc ] ) ) {
			$locations[ $loc ] = $menu_ids[ $default_lang ][ $loc ];
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	if ( function_exists( 'pll_languages_list' ) ) {
		$options = get_option( 'polylang' );
		if ( is_array( $options ) ) {
			$theme = get_option( 'stylesheet' );
			foreach ( $langs as $lang ) {
				foreach ( array( 'primary', 'footer' ) as $loc ) {
					if ( ! empty( $menu_ids[ $lang ][ $loc ] ) ) {
						$options['nav_menus'][ $theme ][ $loc ][ $lang ] = $menu_ids[ $lang ][ $loc ];
					}
				}
			}
			update_option( 'polylang', $options );
			$log[] = 'Menus assigned per language (Polylang).';
			return;
		}
	}
	$log[] = 'Menus assigned to theme locations.';
}

/* ------------------------------------------------------------------ ADMIN */

function ps_setup_menu() {
	add_theme_page(
		'PENTA SERVE Setup',
		'PENTA SERVE Setup',
		'manage_options',
		'penta-setup',
		'ps_setup_page'
	);
}
add_action( 'admin_menu', 'ps_setup_menu' );

function ps_setup_notice() {
	if ( ! current_user_can( 'manage_options' ) || get_option( 'ps_setup_done' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_penta-setup' === $screen->id ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p><strong>PENTA SERVE:</strong> %s <a href="%s">%s</a></p></div>',
		esc_html( 'Theme activated. Run the one-click setup to create/update all pages and menus.' ),
		esc_url( admin_url( 'themes.php?page=penta-setup' ) ),
		esc_html( 'Open PENTA SERVE Setup' )
	);
}
add_action( 'admin_notices', 'ps_setup_notice' );

function ps_handle_setup_post() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'penta-serve' ), 403 );
	}
	check_admin_referer( 'ps_run_setup' );

	$overwrite = ! empty( $_POST['ps_overwrite'] );
	$cleanup   = ! empty( $_POST['ps_cleanup'] );

	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 120 ); // phpcs:ignore Squiz.PHP.DiscouragedFunctions
	}
	$log = ps_run_setup( $overwrite, $cleanup );
	ps_flush_caches();
	set_transient( 'ps_setup_log_' . get_current_user_id(), $log, 10 * MINUTE_IN_SECONDS );

	wp_safe_redirect( admin_url( 'themes.php?page=penta-setup&done=1' ) );
	exit;
}
add_action( 'admin_post_ps_run_setup', 'ps_handle_setup_post' );

function ps_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$info    = ps_setup_languages();
	$has_pll = function_exists( 'pll_languages_list' );
	$log     = get_transient( 'ps_setup_log_' . get_current_user_id() );
	$show    = isset( $_GET['done'] ) && is_array( $log ); // phpcs:ignore WordPress.Security.NonceVerification
	?>
	<div class="wrap">
		<h1>PENTA SERVE Setup</h1>

		<?php if ( $show ) : ?>
			<div class="notice notice-success"><p><strong>Setup finished.</strong> Review the log below, then open the site.</p></div>
			<pre style="background:#fff;border:1px solid #ccd0d4;padding:14px;max-height:340px;overflow:auto;direction:ltr;text-align:left"><?php echo esc_html( implode( "\n", $log ) ); ?></pre>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">View site</a>
				<a class="button" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>">Customize (logo, phone, video, images)</a>
			</p>
			<hr>
		<?php endif; ?>

		<h2>1. Check</h2>
		<table class="widefat striped" style="max-width:720px">
			<tbody>
				<tr><td>Polylang</td><td><?php echo $has_pll ? '&#9989; active' : '&#10060; not active (only English pages will be created)'; ?></td></tr>
				<tr><td>Languages that will be built</td><td><?php echo esc_html( implode( ', ', $info['langs'] ) ); ?></td></tr>
			</tbody>
		</table>
		<?php foreach ( $info['notes'] as $note ) : ?>
			<div class="notice notice-warning inline"><p><?php echo esc_html( $note ); ?></p></div>
		<?php endforeach; ?>

		<h2>2. Run</h2>
		<p>Creates/updates the 4 main pages (Home, About Us, Services &amp; Investment Opportunities, Contact Us) plus Privacy Policy and Terms &amp; Conditions per language, archives legacy standalone Sectors/Investment pages, links English and Arabic translations, and builds the 4-item menus.</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="ps_run_setup">
			<?php wp_nonce_field( 'ps_run_setup' ); ?>
			<p><label><input type="checkbox" name="ps_cleanup" value="1" checked> Delete WordPress sample content ("Hello world!" post and "Sample Page")</label></p>
			<p><label><input type="checkbox" name="ps_overwrite" value="1"> Update existing pages and menus with the latest brief content <strong>(warning: replaces any manual edits made to those pages)</strong></label></p>
			<?php submit_button( 'Run setup', 'primary large' ); ?>
		</form>
	</div>
	<?php
}