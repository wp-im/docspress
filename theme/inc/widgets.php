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
 * Register stable native widget areas. Keep sidebar-1 for existing sites.
 */
function docspress_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Right Sidebar', 'docspress' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Site-wide right sidebar. On smaller screens it opens from the right. Article contents stay inside the article.', 'docspress' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	foreach ( array(
		'docspress-left-sidebar' => array( __( 'Left Sidebar', 'docspress' ), __( 'Site-wide widgets below the documentation tree or sidebar menu. Opens from the left on smaller screens.', 'docspress' ) ),
		'docspress-footer'       => array( __( 'Footer Widgets', 'docspress' ), __( 'Optional widgets above the footer copyright and links.', 'docspress' ) ),
	) as $id => $area ) {
		register_sidebar(
			array(
				'name'          => $area[0],
				'id'            => $id,
				'description'   => $area[1],
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'docspress_widgets_init' );
