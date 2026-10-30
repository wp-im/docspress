<?php
/**
 * Small dependency-free regression tests for Docspress pure helpers.
 *
 * @package Docspress
 */

if ( getenv( 'DOCSPRESS_WP_PATH' ) ) {
	require_once rtrim( getenv( 'DOCSPRESS_WP_PATH' ), '/' ) . '/wp-load.php';
}
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ );
}

if ( ! function_exists( 'esc_attr' ) ) {
function esc_attr( $value ) {
	return htmlspecialchars( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}
}

if ( ! function_exists( 'sanitize_title' ) ) {
function sanitize_title( $value ) {
	$value = strtolower( trim( wp_strip_all_tags( (string) $value ) ) );
	$value = preg_replace( '/[^a-z0-9]+/', '-', $value );
	return trim( (string) $value, '-' );
}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
function wp_strip_all_tags( $value ) {
	return strip_tags( (string) $value );
}
}

if ( ! function_exists( 'docspress_build_page_tree' ) ) {
	require_once __DIR__ . '/../theme/inc/docs.php';
}
if ( ! function_exists( 'docspress_parse_article_content' ) ) {
	require_once __DIR__ . '/../theme/inc/toc.php';
}

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

$ties = array(
	(object) array( 'ID' => 8, 'post_parent' => 0, 'menu_order' => 0, 'post_title' => 'Same' ),
	(object) array( 'ID' => 7, 'post_parent' => 0, 'menu_order' => 0, 'post_title' => 'Same' ),
	(object) array( 'ID' => 9, 'post_parent' => 999, 'menu_order' => 0, 'post_title' => 'Orphan' ),
);
docspress_test_assert( 7 === docspress_build_page_tree( $ties, 0 )[0]['page']->ID, 'Equal titles use a stable ID tie-break.' );
docspress_test_assert( 2 === count( docspress_flatten_page_tree( docspress_build_page_tree( $ties, 0 ) ) ), 'Orphans do not enter the root tree.' );
$cycle = array(
	(object) array( 'ID' => 2, 'post_parent' => 1, 'menu_order' => 0, 'post_title' => 'Child' ),
	(object) array( 'ID' => 1, 'post_parent' => 2, 'menu_order' => 0, 'post_title' => 'Cycle root' ),
);
docspress_test_assert( 1 === count( docspress_flatten_page_tree( docspress_build_page_tree( $cycle, 1 ) ) ), 'Cycle guards do not emit ancestors twice.' );
docspress_test_assert( 3 === count( docspress_flatten_page_tree( docspress_build_page_tree( $pages, 10, 1 ) ) ), 'Depth limits preserve only the requested levels.' );

if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
	echo "Docspress tree tests: PASS; HTML tests require DOCSPRESS_WP_PATH or WP-CLI.\n";
	return;
}

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

$complex = docspress_parse_article_content( '<div id="reserved"></div><h2>Reserved</h2><h2 id="stable" lang="ja" data-key="x"><em>日本語</em> &amp; 中文</h2><div class="wp-block-group"><h3>Nested <code>code</code></h3></div><h2> </h2><h2 id="stable">Duplicate</h2><script>var x = "<h2>Not a heading</h2>";</script>' );
docspress_test_assert( 4 === count( $complex['toc'] ), 'Ignore empty headings and script contents, retain grouped headings.' );
docspress_test_assert( 'reserved-2' === $complex['toc'][0]['id'], 'Generated IDs reserve non-heading IDs.' );
docspress_test_assert( 'stable' === $complex['toc'][1]['id'] && 'stable-2' === $complex['toc'][3]['id'], 'Preserve explicit IDs and suffix duplicates.' );
docspress_test_assert( '日本語 & 中文' === $complex['toc'][1]['title'], 'Unicode and entities form a readable title.' );
docspress_test_assert( false !== strpos( $complex['html'], '<em>日本語</em> &amp; 中文' ), 'Inline markup survives decoration.' );
docspress_test_assert( false !== strpos( $complex['html'], 'lang="ja" data-key="x"' ), 'Semantic and data attributes survive.' );

foreach ( array( 30, 300, 1000 ) as $size ) {
	$sample = array();
	for ( $i = 1; $i <= $size; ++$i ) {
		$sample[] = (object) array( 'ID' => $i, 'post_parent' => $i > 10 ? (int) floor( ( $i - 1 ) / 10 ) : 0, 'menu_order' => $i % 10, 'post_title' => 'Page ' . $i );
	}
	$start = microtime( true );
	$tree = docspress_build_page_tree( $sample, 0 );
	docspress_test_assert( $size === count( docspress_flatten_page_tree( $tree ) ), 'Scale test retains every reachable Page.' );
	printf( "Tree %d Pages: %.2f ms\n", $size, ( microtime( true ) - $start ) * 1000 );
}

echo "Docspress helper tests: PASS\n";
