<?php
/**
 * Customizer settings.
 *
 * @package Docspress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Docspress theme settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function docspress_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'docspress_settings',
		array(
			'title'       => __( 'Docspress', 'docspress' ),
			'description' => __( 'Choose the root Page whose descendants form the documentation tree.', 'docspress' ),
			'priority'    => 35,
		)
	);

	$wp_customize->add_setting(
		'docspress_root_page',
		array(
			'default'           => 0,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'docspress_root_page',
		array(
			'type'        => 'dropdown-pages',
			'section'     => 'docspress_settings',
			'label'       => __( 'Docs Root Page', 'docspress' ),
			'description' => __( 'Pages below this Page are treated as Docspress content.', 'docspress' ),
		)
	);

	$wp_customize->add_setting(
		'docspress_footer_text',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'docspress_footer_text',
		array(
			'type'        => 'text',
			'section'     => 'docspress_settings',
			'label'       => __( 'Footer Text', 'docspress' ),
			'description' => __( 'Optional plain-text footer line. Leave empty to use the default copyright.', 'docspress' ),
		)
	);
}
add_action( 'customize_register', 'docspress_customize_register' );
