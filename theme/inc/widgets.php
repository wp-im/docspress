<?php
/**
 * Widget areas.
 *
 * @package Docspress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the standard blog sidebar.
 */
function docspress_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'docspress' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Widgets shown on non-document blog surfaces.', 'docspress' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'docspress_widgets_init' );
