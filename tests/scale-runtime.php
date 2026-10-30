<?php
/** Native WordPress tree/query/render scale acceptance. Local disposable site only. */
if ( ! defined( 'ABSPATH' ) || ! in_array( wp_parse_url( home_url(), PHP_URL_HOST ), array( 'localhost', '127.0.0.1' ), true ) ) {
	throw new RuntimeException( 'Disposable QA site only.' );
}
$qa_root = get_page_by_path( 'qa-scale-root' );
$qa_root_id = $qa_root ? $qa_root->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => 'qa-scale-root', 'post_title' => 'QA scale library' ) );
$qa_ids = get_option( 'docspress_qa_scale_ids', array() );
wp_defer_term_counting( true );
for ( $i = count( $qa_ids ) + 1; $i <= 1000; ++$i ) {
	$parent_key = $i <= 10 ? 0 : ( $i <= 110 ? (int) floor( ( $i - 11 ) / 10 ) + 1 : (int) floor( ( $i - 111 ) / 10 ) + 11 );
	if ( $i > 994 ) {
		$parent_key = $i - 1;
	}
	$qa_ids[ $i ] = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => 'record-' . $i, 'post_title' => 'Reference 資料 ' . ( $i % 10 ), 'post_parent' => $parent_key ? $qa_ids[ $parent_key ] : $qa_root_id, 'menu_order' => $i % 10, 'post_content' => '<p>Native scale fixture.</p>' ) );
	update_option( 'docspress_qa_scale_ids', $qa_ids, false );
}
wp_defer_term_counting( false );
$qa_previous_root = get_theme_mod( 'docspress_root_page', 0 );
try {
	set_theme_mod( 'docspress_root_page', $qa_root_id );
	global $wpdb;
	$qa_start = microtime( true );
	$qa_queries = $wpdb->num_queries;
	$qa_pages = docspress_get_docs_pages();
	$qa_tree = docspress_get_docs_tree();
	$qa_first_queries = $wpdb->num_queries - $qa_queries;
	$qa_queries = $wpdb->num_queries;
	docspress_get_docs_pages();
	docspress_get_docs_tree();
	$qa_repeat_queries = $wpdb->num_queries - $qa_queries;
	ob_start();
	docspress_render_tree_nodes( $qa_tree, $qa_ids[1000], docspress_get_open_path_ids( $qa_ids[1000] ), 1, 0, 'qa-scale' );
	$qa_html = ob_get_clean();
	if ( 1000 !== count( $qa_pages ) || 1000 !== count( docspress_flatten_page_tree( $qa_tree ) ) || $qa_repeat_queries > 0 || false === strpos( $qa_html, 'data-tree-node="' . $qa_ids[1000] . '"' ) || substr_count( $qa_html, 'class="tree-node' ) > 400 ) {
		throw new RuntimeException( 'Native scale or current-path assertion failed.' );
	}
	echo wp_json_encode( array( 'native_pages' => count( $qa_pages ), 'initial_queries' => $qa_first_queries, 'repeat_queries' => $qa_repeat_queries, 'rendered_nodes' => substr_count( $qa_html, 'class="tree-node' ), 'html_bytes' => strlen( $qa_html ), 'query_tree_render_ms' => round( ( microtime( true ) - $qa_start ) * 1000, 2 ), 'current_path_retained' => true, 'result' => 'PASS' ), JSON_PRETTY_PRINT ) . "\n";
} finally {
	set_theme_mod( 'docspress_root_page', $qa_previous_root );
}
