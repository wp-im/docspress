<?php
/**
 * Disposable-site asset failure fixture. Never include in a theme package.
 *
 * @package Docspress
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_action(
	'wp_enqueue_scripts',
	static function () {
		if ( in_array( wp_parse_url( home_url(), PHP_URL_HOST ), array( 'localhost', '127.0.0.1' ), true ) && isset( $_GET['docspress-qa-nojs'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only local QA flag.
			wp_dequeue_script( 'docspress-navigation' );
		}
	},
	100
);
