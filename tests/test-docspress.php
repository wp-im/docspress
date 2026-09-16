<?php
/**
 * Small dependency-free regression tests for Docspress pure helpers.
 *
 * @package Docspress
 */

define( 'ABSPATH', __DIR__ );

function esc_attr( $value ) {
	return htmlspecialchars( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}

function sanitize_title( $value ) {
	$value = strtolower( trim( wp_strip_all_tags( (string) $value ) ) );
	$value = preg_replace( '/[^a-z0-9]+/', '-', $value );
	return trim( (string) $value, '-' );
}

function wp_strip_all_tags( $value ) {
	return strip_tags( (string) $value );
}

require_once __DIR__ . '/../theme/inc/docs.php';
require_once __DIR__ . '/../theme/inc/toc.php';

function docspress_test_assert( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

$pages = array(
	(object) array( 'ID' => 3, 'post_parent' => 10, 'menu_order' => 2, 'post_title' => 'Zeta' ),
	(object) array( 'ID' => 2, 'post_parent' => 10, 'menu_order' => 1, 'post_title' => 'Beta' ),
	(object) array( 'ID' => 1, 'post_parent' => 10, 'menu_order' => 1, 'post_title' => 'Alpha' ),
	(object) array( 'ID' => 4, 'post_parent' => 2, 'menu_order' => 1, 'post_title' => 'Beta child' ),
);

$tree = docspress_build_page_tree( $pages, 10 );
docspress_test_assert( 3 === count( $tree ), 'Expected three direct children.' );
docspress_test_assert( 'Alpha' === $tree[0]['page']->post_title, 'Title is the tie-break after menu order.' );
docspress_test_assert( 'Beta' === $tree[1]['page']->post_title, 'Second ordered child is Beta.' );
docspress_test_assert( 'Beta child' === $tree[1]['children'][0]['page']->post_title, 'Nested child is retained.' );

$flat = docspress_flatten_page_tree( $tree );
$flat_ids = array_map(
	static function ( $page ) {
		return (int) $page->ID;
	},
	$flat
);
docspress_test_assert( array( 1, 2, 4, 3 ) === $flat_ids, 'Depth-first flatten order is stable.' );

$parsed = docspress_parse_article_content(
	'<h2 class="editorial" id="given" onclick="alert(1)" data-kind="section"><em>Title</em></h2>' .
	'<h3><a href="#sub">Sub heading</a></h3>' .
	'<h2>Title</h2><h2>Title</h2>'
);

docspress_test_assert( 4 === count( $parsed['toc'] ), 'Four H2/H3 headings should enter the TOC.' );
docspress_test_assert( 'given' === $parsed['toc'][0]['id'], 'Existing heading IDs are preserved.' );
docspress_test_assert( 'sub-heading' === $parsed['toc'][1]['id'], 'Missing IDs are generated from heading text.' );
docspress_test_assert( 'title-2' === $parsed['toc'][3]['id'], 'Duplicate IDs receive a suffix.' );
docspress_test_assert( false === strpos( $parsed['html'], 'onclick' ), 'Event-handler attributes are removed.' );
docspress_test_assert( false !== strpos( $parsed['html'], 'data-kind="section"' ), 'Safe data attributes are preserved.' );
docspress_test_assert( false !== strpos( $parsed['html'], 'class="editorial docspress-section"' ), 'H2 headings receive a namespaced section class.' );
docspress_test_assert( false !== strpos( $parsed['html'], 'class="docspress-subsection"' ), 'H3 headings receive a namespaced subsection class.' );

echo "Docspress helper tests: PASS\n";
