<?php
/**
 * Docs article heading parser and table of contents.
 *
 * @package Docspress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Keep only safe non-layout attributes when rebuilding editorial headings.
 *
 * @param string $attributes Raw attributes after class/id removal.
 * @return string
 */
function docspress_sanitize_heading_extra_attributes( $attributes ) {
	$allowed_exact = array( 'title', 'role', 'dir', 'lang' );
	$safe          = array();

	if ( preg_match_all( '/\s([A-Za-z_:][A-Za-z0-9_:.\-]*)\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/', (string) $attributes, $matches, PREG_SET_ORDER ) ) {
		foreach ( $matches as $match ) {
			$name  = strtolower( (string) $match[1] );
			$value = isset( $match[2] ) && '' !== $match[2] ? $match[2] : ( $match[3] ?? '' );

			if ( ! in_array( $name, $allowed_exact, true ) && 0 !== strpos( $name, 'aria-' ) && 0 !== strpos( $name, 'data-' ) ) {
				continue;
			}

			$safe[] = $name . '="' . esc_attr( $value ) . '"';
		}
	}

	return implode( ' ', $safe );
}

/**
 * Merge a required class with any existing class attribute.
 *
 * @param string $attributes Raw heading attributes.
 * @param string $required   Required class.
 * @return array{attributes:string,classes:string,id:string}
 */
function docspress_extract_heading_attributes( $attributes, $required ) {
	$classes = array( $required );
	$id      = '';

	if ( preg_match( '/\sclass=(?:"([^"]*)"|\'([^\']*)\')/i', $attributes, $match ) ) {
		$existing = ! empty( $match[1] ) ? $match[1] : $match[2];
		$classes  = array_merge( preg_split( '/\s+/', trim( $existing ) ), $classes );
	}

	if ( preg_match( '/\sid=(?:"([^"]*)"|\'([^\']*)\')/i', $attributes, $match ) ) {
		$id = ! empty( $match[1] ) ? $match[1] : $match[2];
	}

	$attributes = preg_replace( '/\s(?:class|id)=(?:"[^"]*"|\'[^\']*\')/i', '', $attributes );
	$attributes = docspress_sanitize_heading_extra_attributes( (string) $attributes );
	$classes    = array_values( array_unique( array_filter( $classes ) ) );

	return array(
		'attributes' => trim( (string) $attributes ),
		'classes'    => implode( ' ', $classes ),
		'id'         => $id,
	);
}

/**
 * Decorate H2/H3 headings, inject section dividers, and build TOC data.
 *
 * @param string $content Filtered WordPress content HTML.
 * @return array{html:string,toc:array}
 */
function docspress_parse_article_content( $content ) {
	$toc           = array();
	$used_ids      = array();
	$h2_count      = 0;
	$heading_count = 0;

	$html = preg_replace_callback(
		'/<h([23])([^>]*)>(.*?)<\/h\1>/is',
		static function ( $heading_match ) use ( &$toc, &$used_ids, &$h2_count, &$heading_count ) {
			$level      = (int) $heading_match[1];
			$raw_attrs  = (string) $heading_match[2];
			$inner_html = (string) $heading_match[3];
			$title      = trim( wp_strip_all_tags( $inner_html ) );
			$required   = 2 === $level ? 'docspress-section' : 'docspress-subsection';
			$parsed     = docspress_extract_heading_attributes( $raw_attrs, $required );
			$base_id    = '' !== trim( (string) $parsed['id'] ) ? trim( (string) $parsed['id'] ) : sanitize_title( $title );
			$heading_count++;
			if ( '' === $base_id ) {
				$base_id = 'section-' . $heading_count;
			}

			$id     = $base_id;
			$suffix = 2;
			while ( isset( $used_ids[ $id ] ) ) {
				$id = $base_id . '-' . $suffix;
				$suffix++;
			}
			$used_ids[ $id ] = true;

			$toc[] = array(
				'level' => $level,
				'id'    => $id,
				'title' => $title,
			);

			$prefix = '';
			if ( 2 === $level ) {
				if ( $h2_count > 0 ) {
					$prefix = '<hr class="docspress-section-rule" aria-hidden="true" />';
				}
				$h2_count++;
			}

			$extra_attributes = '' !== $parsed['attributes'] ? ' ' . $parsed['attributes'] : '';

			return sprintf(
				'%1$s<h%2$d id="%3$s" class="%4$s"%5$s>%6$s</h%2$d>',
				$prefix,
				$level,
				esc_attr( $id ),
				esc_attr( $parsed['classes'] ),
				$extra_attributes,
				$inner_html
			);
		},
		(string) $content
	);

	return array(
		'html' => is_string( $html ) ? $html : (string) $content,
		'toc'  => $toc,
	);
}
