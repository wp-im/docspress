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
 * Keep untitled native content reachable without changing its stored title.
 *
 * @param int|WP_Post $post Optional post object or ID.
 * @return string
 */
function docspress_get_title( $post = 0 ) {
	$title = get_the_title( $post );
	return '' !== trim( wp_strip_all_tags( $title ) ) ? $title : __( 'Untitled', 'docspress' );
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
	foreach ( array( 'left', 'right' ) as $side ) {
		if ( docspress_sidebar_enabled( $side ) ) {
			$classes[] = 'docspress-has-' . $side;
		}
	}
	if ( 'compact' === get_theme_mod( 'docspress_reading_width', 'comfortable' ) ) {
		$classes[] = 'docspress-reading-compact';
	}
	if ( 'large' === get_theme_mod( 'docspress_text_size', 'standard' ) ) {
		$classes[] = 'docspress-text-large';
	}
	return $classes;
}
add_filter( 'body_class', 'docspress_body_classes' );

/**
 * A useful header menu when no menu has been assigned.
 */
function docspress_primary_menu_fallback() {
	echo '<ul class="global-nav-list"><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'docspress' ) . '</a></li>';
	$posts_page = absint( get_option( 'page_for_posts' ) );
	if ( $posts_page ) {
		echo '<li><a href="' . esc_url( get_permalink( $posts_page ) ) . '">' . esc_html( docspress_get_title( $posts_page ) ) . '</a></li>';
	}
	echo '</ul>';
}

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

	return $label_id ? docspress_get_title( $label_id ) : '';
}

/**
 * Render a content excerpt for directory listings.
 *
 * @param WP_Post $page Page object.
 * @return string
 */
function docspress_get_directory_description( $page ) {
	if ( ! empty( $page->post_password ) || ! is_post_publicly_viewable( $page ) ) {
		return '';
	}
	return wp_trim_words( wp_strip_all_tags( get_the_excerpt( $page ) ), 24 );
}

/**
 * Sidebars are enabled on every template unless explicitly disabled.
 *
 * @param string $side Left or right.
 * @return bool
 */
function docspress_sidebar_enabled( $side ) {
	return (bool) get_theme_mod( 'docspress_show_' . $side . '_sidebar', true );
}

/**
 * Breadcrumbs use native Page ancestry without changing content or URLs.
 */
function docspress_breadcrumbs() {
	if ( ! is_singular() || is_front_page() ) {
		return;
	}
	echo '<nav class="docspress-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'docspress' ) . '"><ol>';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'docspress' ) . '</a></li>';
	foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor_id ) {
		$ancestor = get_post( $ancestor_id );
		if ( $ancestor && is_post_publicly_viewable( $ancestor ) && get_permalink( $ancestor_id ) !== home_url( '/' ) ) {
			echo '<li><a href="' . esc_url( get_permalink( $ancestor_id ) ) . '">' . esc_html( docspress_get_title( $ancestor_id ) ) . '</a></li>';
		}
	}
	echo '<li aria-current="page">' . esc_html( docspress_get_title() ) . '</li></ol></nav>';
}

/**
 * Native publish/modified dates; a modification is not an editorial review.
 *
 * @param bool $include_author Show the native author archive link.
 */
function docspress_entry_meta( $include_author = false ) {
	echo '<div class="entry-meta">';
	if ( $include_author ) {
		echo '<time datetime="' . esc_attr( get_the_date( DATE_W3C ) ) . '">' . esc_html( get_the_date() ) . '</time>';
		if ( get_the_author_meta( 'ID' ) && get_the_author() ) {
			echo ' <span aria-hidden="true">·</span> <a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a> ';
		}
	}
	if ( get_theme_mod( 'docspress_show_modified', true ) ) {
		echo '<span class="docspress-modified">';
		printf(
			/* translators: %s: modification date in a time element. */
			esc_html__( 'Updated %s', 'docspress' ),
			'<time datetime="' . esc_attr( get_the_modified_date( DATE_W3C ) ) . '">' . esc_html( get_the_modified_date() ) . '</time>'
		);
		echo '</span>';
	}
	echo '</div>';
}
