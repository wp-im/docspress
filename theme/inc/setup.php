<?php
/**
 * Theme setup.
 *
 * @package Docspress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'docspress_setup' ) ) {
	/**
	 * Register theme supports and menus.
	 */
	function docspress_setup() {
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 924;
		}

		load_theme_textdomain( 'docspress', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_post_type_support( 'page', 'excerpt' );
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 64,
				'width'       => 240,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		add_editor_style( 'assets/css/editor.css' );

		register_nav_menus(
			array(
				'primary' => __( 'Primary Menu', 'docspress' ),
				'footer'  => __( 'Footer Menu', 'docspress' ),
			)
		);
	}
}
add_action( 'after_setup_theme', 'docspress_setup' );
