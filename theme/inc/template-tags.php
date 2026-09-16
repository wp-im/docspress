<?php
/**
 * Theme display helpers.
 *
 * @package Docspress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add contextual body classes.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function docspress_body_classes( $classes ) {
	if ( is_page() && docspress_is_docs_page() ) {
		$classes[] = 'docspress-docs-page';
	}
	return $classes;
}
add_filter( 'body_class', 'docspress_body_classes' );

/**
 * Get a short docs eyebrow from the closest parent or docs root.
 *
 * @param int $page_id Current Page ID.
 * @return string
 */
function docspress_get_page_eyebrow( $page_id ) {
	$parent_id = wp_get_post_parent_id( $page_id );
	$root_id   = docspress_get_docs_root_id();
	$label_id  = $parent_id && $parent_id !== $root_id ? $parent_id : $root_id;

	return $label_id ? get_the_title( $label_id ) : '';
}

/**
 * Render a content excerpt for directory listings.
 *
 * @param WP_Post $page Page object.
 * @return string
 */
function docspress_get_directory_description( $page ) {
	if ( ! empty( $page->post_excerpt ) ) {
		return wp_strip_all_tags( $page->post_excerpt );
	}

	return wp_trim_words( wp_strip_all_tags( strip_shortcodes( $page->post_content ) ), 24 );
}
