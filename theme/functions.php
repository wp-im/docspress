<?php
/**
 * Docspress theme bootstrap.
 *
 * @package Docspress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$docspress_files = array(
	'/inc/setup.php',
	'/inc/enqueue.php',
	'/inc/widgets.php',
	'/inc/customizer.php',
	'/inc/docs.php',
	'/inc/toc.php',
	'/inc/template-tags.php',
);

foreach ( $docspress_files as $docspress_file ) {
	require_once get_template_directory() . $docspress_file;
}
