<?php
/**
 * WordPress integration assertions for configuration and content portability.
 *
 * @package Docspress
 */

if ( ! defined( 'ABSPATH' ) || ! in_array( wp_parse_url( home_url(), PHP_URL_HOST ), array( 'localhost', '127.0.0.1' ), true ) ) {
	throw new RuntimeException( 'Only run on the disposable QA site.' );
}
$docspress_assert = static function ( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
};
global $wp_registered_sidebars;
foreach ( array( 'sidebar-1', 'docspress-left-sidebar', 'docspress-footer' ) as $area ) {
	$docspress_assert( isset( $wp_registered_sidebars[ $area ] ), 'Missing native widget area: ' . $area );
}
$docspress_assert( docspress_sidebar_enabled( 'left' ) && docspress_sidebar_enabled( 'right' ), 'Both sidebars must be enabled by default.' );
$docspress_saved = get_theme_mod( 'docspress_show_right_sidebar', true );
set_theme_mod( 'docspress_show_right_sidebar', false );
$docspress_assert( ! docspress_sidebar_enabled( 'right' ), 'The explicit right sidebar setting must work.' );
set_theme_mod( 'docspress_show_right_sidebar', $docspress_saved );

$docspress_widgets = wp_get_sidebars_widgets();
$docspress_existing_right = $docspress_widgets['sidebar-1'];
$docspress_widgets['sidebar-1'] = array();
wp_set_sidebars_widgets( $docspress_widgets );
unset( $GLOBALS['_wp_sidebars_widgets'] );
ob_start();
get_sidebar();
$docspress_fallback = ob_get_clean();
$docspress_assert( false !== strpos( $docspress_fallback, 'docspress-rightbar' ) && false !== strpos( $docspress_fallback, 'Subscribe to updates' ), 'An empty widget area must keep a useful right sidebar.' );
$docspress_assert( false === strpos( $docspress_fallback, 'docspress-toc' ), 'Article TOC must not enter the right sidebar.' );
$docspress_widgets['sidebar-1'] = $docspress_existing_right;
wp_set_sidebars_widgets( $docspress_widgets );
unset( $GLOBALS['_wp_sidebars_widgets'] );

$docspress_before = get_posts( array( 'post_type' => array( 'page', 'post' ), 'posts_per_page' => -1, 'post_status' => array( 'publish', 'private' ), 'fields' => 'ids' ) );
$docspress_content_hash = static function ( $ids ) {
	$all = array();
	foreach ( $ids as $id ) {
		$all[ $id ] = array( get_post_field( 'post_content', $id ), get_post_field( 'post_name', $id ), get_post_field( 'post_parent', $id ) );
	}
	return hash( 'sha256', wp_json_encode( $all ) );
};
$docspress_hash = $docspress_content_hash( $docspress_before );
$docspress_stylesheet = get_stylesheet();
if ( wp_get_theme( 'twentytwentyfive' )->exists() ) {
	switch_theme( 'twentytwentyfive' );
	$docspress_assert( $docspress_hash === $docspress_content_hash( $docspress_before ), 'Native content changed after switching themes.' );
	switch_theme( $docspress_stylesheet );
	$docspress_assert( get_option( 'sidebars_widgets' )['sidebar-1'] === $docspress_existing_right, 'Existing widget instances were not restored.' );
}
echo "Docspress runtime settings, widgets and portability: PASS\n";
