<?php
/**
 * Router for an isolated WordPress PHP development server.
 *
 * @package Docspress
 */

if ( 'cli-server' !== PHP_SAPI ) {
	exit;
}
$docspress_path = rawurldecode( (string) parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
if ( '/' !== $docspress_path && is_file( $_SERVER['DOCUMENT_ROOT'] . $docspress_path ) ) {
	return false;
}
require $_SERVER['DOCUMENT_ROOT'] . '/index.php';
