<?php
/**
 * Front-end assets.
 *
 * @package Docspress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a cache-busting version for a theme asset.
 *
 * @param string $relative_path Relative theme path.
 * @return string
 */
function docspress_asset_version( $relative_path ) {
	$path = get_theme_file_path( $relative_path );

	if ( file_exists( $path ) ) {
		return (string) filemtime( $path );
	}

	return (string) wp_get_theme()->get( 'Version' );
}

/**
 * Enqueue theme assets.
 */
function docspress_enqueue_assets() {
	wp_enqueue_style(
		'docspress-style',
		get_stylesheet_uri(),
		array(),
		docspress_asset_version( '/style.css' )
	);

	wp_enqueue_style(
		'docspress-main',
		get_theme_file_uri( '/assets/css/main.css' ),
		array( 'docspress-style' ),
		docspress_asset_version( '/assets/css/main.css' )
	);

	wp_enqueue_script(
		'docspress-navigation',
		get_theme_file_uri( '/assets/js/navigation.js' ),
		array(),
		docspress_asset_version( '/assets/js/navigation.js' ),
		true
	);
	wp_localize_script( 'docspress-navigation', 'docspressNavigation', array( 'scrollRegion' => __( 'Scrollable content. Use the arrow keys to read more.', 'docspress' ) ) );
	if ( is_rtl() ) {
		wp_enqueue_style( 'docspress-rtl', get_theme_file_uri( '/rtl.css' ), array( 'docspress-main' ), docspress_asset_version( '/rtl.css' ) );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'docspress_enqueue_assets' );
