<?php
/**
 * Seed the reviewed Docspress demo content into a disposable WordPress install.
 *
 * Run with WP-CLI from the project root. This is not part of the theme ZIP and
 * must not be run against production without a reviewed backup and confirmation.
 *
 * @package Docspress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( "Run this file with WP-CLI's eval-file command.\n" );
}

$content_root = dirname( __DIR__ ) . '/site-content';
$manifest     = $content_root . '/manifest.tsv';

if ( ! file_exists( $manifest ) ) {
	exit( "Missing site-content/manifest.tsv.\n" );
}

/**
 * Parse one front-matter content source.
 *
 * @param string $relative_file Relative source path.
 * @return array{metadata:array,body:string}
 */
function docspress_seed_source( $relative_file ) {
	$source = file_get_contents( dirname( __DIR__ ) . '/site-content/' . $relative_file );
	$parts  = preg_split( '/^---\s*$/m', (string) $source );

	if ( count( $parts ) < 3 ) {
		WP_CLI::error( 'Missing front matter: ' . $relative_file );
	}

	$metadata = array();
	foreach ( preg_split( '/\r?\n/', trim( $parts[1] ) ) as $line ) {
		$separator = strpos( $line, ':' );
		if ( false === $separator ) {
			continue;
		}
		$key              = trim( substr( $line, 0, $separator ) );
		$metadata[ $key ] = trim( substr( $line, $separator + 1 ) );
	}

	return array(
		'metadata' => $metadata,
		'body'     => trim( implode( '---', array_slice( $parts, 2 ) ) ),
	);
}

$lines   = file( $manifest, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
$entries = array();

foreach ( array_slice( $lines, 1 ) as $line ) {
	$fields = str_getcsv( $line, "\t" );
	if ( 8 !== count( $fields ) ) {
		WP_CLI::error( 'Invalid manifest row: ' . $line );
	}
	$entries[] = array_combine( array( 'type', 'title', 'slug', 'parent', 'menu_order', 'template', 'excerpt', 'file' ), $fields );
}

$ids = array();
foreach ( $entries as $entry ) {
	$source = docspress_seed_source( $entry['file'] );
	$type   = 'post' === $entry['type'] ? 'post' : 'page';
	$parent = 0;

	if ( 'page' === $type && '' !== $entry['parent'] && isset( $ids[ $entry['parent'] ] ) ) {
		$parent = $ids[ $entry['parent'] ];
	}

	$existing = get_page_by_path( $entry['slug'], OBJECT, $type );
	$post      = array(
		'post_type'      => $type,
		'post_status'    => 'publish',
		'post_title'     => $entry['title'],
		'post_name'      => $entry['slug'],
		'post_content'   => $source['body'],
		'post_excerpt'   => $entry['excerpt'],
		'post_parent'    => $parent,
		'menu_order'     => absint( $entry['menu_order'] ),
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
	);

	if ( $existing ) {
		$post['ID'] = $existing->ID;
		$id         = wp_update_post( wp_slash( $post ), true );
	} else {
		$id = wp_insert_post( wp_slash( $post ), true );
	}

	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}

	$ids[ $entry['slug'] ] = (int) $id;
	if ( 'page' === $type ) {
		if ( 'Docs Section Index' === $entry['template'] ) {
			update_post_meta( $id, '_wp_page_template', 'page-templates/docs-section.php' );
		} else {
			delete_post_meta( $id, '_wp_page_template' );
		}
	}
}

$root_id  = $ids['docspress'];
$notes_id = $ids['notes'];
update_option( 'blogname', 'Docspress' );
update_option( 'blogdescription', 'A quiet Classic Theme for documents, reference material, and long-form reading.' );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $root_id );
update_option( 'page_for_posts', $notes_id );
set_theme_mod( 'docspress_root_page', $root_id );
set_theme_mod( 'docspress_footer_text', 'Docspress · A WordPress Classic Theme for documents.' );

/**
 * Add a menu item if a menu with the target title exists.
 *
 * @param int    $menu_id Menu ID.
 * @param string $title   Item title.
 * @param int    $page_id Page ID.
 */
function docspress_seed_menu_item( $menu_id, $title, $page_id ) {
	$items = wp_get_nav_menu_items( $menu_id );
	foreach ( (array) $items as $item ) {
		if ( (int) $item->object_id === (int) $page_id ) {
			return;
		}
	}
	wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'     => $title,
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $page_id,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
		)
	);
}

$primary_menu = wp_get_nav_menu_object( 'Docspress Global' );
$primary_id   = $primary_menu ? $primary_menu->term_id : wp_create_nav_menu( 'Docspress Global' );
docspress_seed_menu_item( $primary_id, 'Guide', $ids['start-here'] );
docspress_seed_menu_item( $primary_id, 'Reference', $ids['content-model'] );
docspress_seed_menu_item( $primary_id, 'About', $ids['what-docspress-is'] );
docspress_seed_menu_item( $primary_id, 'Notes', $notes_id );

$footer_menu = wp_get_nav_menu_object( 'Docspress Footer' );
$footer_id   = $footer_menu ? $footer_menu->term_id : wp_create_nav_menu( 'Docspress Footer' );
docspress_seed_menu_item( $footer_id, 'Release notes', $ids['release-notes'] );
docspress_seed_menu_item( $footer_id, 'Notes', $notes_id );

$locations = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary'] = $primary_id;
$locations['footer']  = $footer_id;
set_theme_mod( 'nav_menu_locations', $locations );

WP_CLI::success( sprintf( 'Seeded %d Pages/Posts and configured Docspress.', count( $entries ) ) );
