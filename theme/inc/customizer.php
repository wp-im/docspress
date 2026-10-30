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
	foreach ( array(
		'docspress_show_left_sidebar'  => __( 'Show the left sidebar on all pages', 'docspress' ),
		'docspress_show_right_sidebar' => __( 'Show the right sidebar on all pages', 'docspress' ),
		'docspress_show_modified'      => __( 'Show the last modified date', 'docspress' ),
	) as $key => $label ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => true,
				'sanitize_callback' => 'docspress_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'type'    => 'checkbox',
				'section' => 'docspress_settings',
				'label'   => $label,
			)
		);
	}
	$choices = array(
		'docspress_toc_depth'     => array(
			__( 'Article table of contents', 'docspress' ),
			'h2-h3',
			array(
				'off'   => __( 'Off', 'docspress' ),
				'h2'    => __( 'H2 headings', 'docspress' ),
				'h2-h3' => __( 'H2 and H3 headings', 'docspress' ),
			),
		),
		'docspress_reading_width' => array(
			__( 'Reading width', 'docspress' ),
			'comfortable',
			array(
				'comfortable' => __( 'Comfortable', 'docspress' ),
				'compact'     => __( 'Compact', 'docspress' ),
			),
		),
		'docspress_text_size'     => array(
			__( 'Body text size', 'docspress' ),
			'standard',
			array(
				'standard' => __( 'Standard', 'docspress' ),
				'large'    => __( 'Large', 'docspress' ),
			),
		),
	);
	foreach ( $choices as $key => $choice ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $choice[1],
				'sanitize_callback' => 'docspress_sanitize_choice',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'type'    => 'select',
				'section' => 'docspress_settings',
				'label'   => $choice[0],
				'choices' => $choice[2],
			)
		);
	}
}
add_action( 'customize_register', 'docspress_customize_register' );

/**
 * Sanitize a boolean control.
 *
 * @param mixed $value Input.
 * @return bool
 */
function docspress_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Keep select values within the registered control choices.
 *
 * @param string               $value   Input.
 * @param WP_Customize_Setting $setting Setting.
 * @return string
 */
function docspress_sanitize_choice( $value, $setting ) {
	$control = $setting->manager->get_control( $setting->id );
	return $control && isset( $control->choices[ $value ] ) ? $value : $setting->default;
}
