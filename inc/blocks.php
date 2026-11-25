<?php
/**
 * Block markup builders.
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ps_blk_heading( $text, $level = 2 ) {
	$level = max( 1, min( 4, (int) $level ) );
	return sprintf(
		"<!-- wp:heading {\"level\":%1\$d} -->\n<h%1\$d class=\"wp-block-heading\">%2\$s</h%1\$d>\n<!-- /wp:heading -->\n\n",
		$level,
		wp_kses_post( $text )
	);
}

function ps_blk_p( $text, $class = '' ) {
	if ( $class ) {
		return sprintf(
			"<!-- wp:paragraph {\"className\":\"%1\$s\"} -->\n<p class=\"%1\$s\">%2\$s</p>\n<!-- /wp:paragraph -->\n\n",
			esc_attr( $class ),
			wp_kses_post( $text )
		);
	}
	return sprintf( "<!-- wp:paragraph -->\n<p>%s</p>\n<!-- /wp:paragraph -->\n\n", wp_kses_post( $text ) );
}

function ps_blk_eyebrow( $text ) {
	return ps_blk_p( $text, 'ps-eyebrow' );
}

function ps_blk_list( array $items, $class = '', $ordered = false ) {
	$tag   = $ordered ? 'ol' : 'ul';
	$attrs = array();
	if ( $ordered ) {
		$attrs['ordered'] = true;
	}
	if ( $class ) {
		$attrs['className'] = $class;
	}
	$json  = $attrs ? ' ' . wp_json_encode( $attrs ) : '';
	$cls   = $class ? ' class="' . esc_attr( $class ) . '"' : '';
	$out   = "<!-- wp:list{$json} -->\n<{$tag}{$cls}>";
	foreach ( $items as $item ) {
		$out .= "<!-- wp:list-item -->\n<li>" . wp_kses_post( $item ) . "</li>\n<!-- /wp:list-item -->";
	}
	$out .= "</{$tag}>\n<!-- /wp:list -->\n\n";
	return $out;
}

function ps_blk_buttons( array $buttons ) {
	$out = "<!-- wp:buttons {\"className\":\"ps-btns\"} -->\n<div class=\"wp-block-buttons ps-btns\">";
	foreach ( $buttons as $b ) {
		$style = isset( $b['style'] ) ? $b['style'] : 'primary';
		$cls   = 'ps-btn--' . $style;
		$out  .= sprintf(
			"<!-- wp:button {\"className\":\"%1\$s\"} -->\n<div class=\"wp-block-button %1\$s\"><a class=\"wp-block-button__link wp-element-button\" href=\"%2\$s\">%3\$s</a></div>\n<!-- /wp:button -->",
			esc_attr( $cls ),
			esc_attr( $b['url'] ),
			esc_html( $b['label'] )
		);
	}
	$out .= "</div>\n<!-- /wp:buttons -->\n\n";
	return $out;
}

function ps_blk_group( $inner, $class = '', $anchor = '' ) {
	$attrs = array();
	if ( $class ) {
		$attrs['className'] = $class;
	}
	if ( $anchor ) {
		$attrs['anchor'] = $anchor;
	}
	$json    = $attrs ? ' ' . wp_json_encode( $attrs ) : '';
	$cls     = 'wp-block-group' . ( $class ? ' ' . esc_attr( $class ) : '' );
	$id_attr = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
	return "<!-- wp:group{$json} -->\n<div{$id_attr} class=\"{$cls}\">\n" . $inner . "</div>\n<!-- /wp:group -->\n\n";
}

function ps_blk_section( $inner, $variant = '', $anchor = '' ) {
	$classes = 'ps-section';
	foreach ( array_filter( explode( ' ', $variant ) ) as $v ) {
		$classes .= ' ps-section--' . $v;
	}
	return ps_blk_group( $inner, $classes, $anchor );
}

/**
 * Grid of cards, with optional image slot shortcode per card.
 */
function ps_blk_cards( array $cards, $cols = 3, $numbered = false, $extra = '' ) {
	$inner = '';
	foreach ( $cards as $card ) {
		$card_inner = '';
		if ( ! empty( $card['slot'] ) ) {
			$variant     = isset( $card['slot_variant'] ) ? $card['slot_variant'] : '';
			$sc          = $variant ? sprintf( '[ps_image_slot slot="%s" variant="%s"]', esc_attr( $card['slot'] ), esc_attr( $variant ) ) : sprintf( '[ps_image_slot slot="%s"]', esc_attr( $card['slot'] ) );
			$card_inner .= ps_blk_shortcode( $sc );
		}
		$card_inner .= ps_blk_heading( $card['title'], 3 ) . ps_blk_p( $card['text'] );
		$inner      .= ps_blk_group( $card_inner, 'ps-card' );
	}
	$class = 'ps-grid ps-grid--' . (int) $cols . ( $numbered ? ' ps-grid--num' : '' ) . ( $extra ? ' ' . $extra : '' );
	return ps_blk_group( $inner, $class );
}

function ps_blk_shortcode( $code ) {
	return "<!-- wp:shortcode -->\n" . $code . "\n<!-- /wp:shortcode -->\n\n";
}

function ps_blk_video_split( $copy, $shortcode = '[ps_video]' ) {
	return ps_blk_group(
		ps_blk_group( $copy, 'ps-video-copy' ) . ps_blk_shortcode( $shortcode ),
		'ps-video-split'
	);
}