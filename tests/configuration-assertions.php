<?php
/** Configuration edge cases in the disposable native WordPress runtime. */
if ( ! defined( 'ABSPATH' ) || ! in_array( wp_parse_url( home_url(), PHP_URL_HOST ), array( 'localhost', '127.0.0.1' ), true ) ) {
	throw new RuntimeException( 'Disposable QA only.' );
}
if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
	$_SERVER['SERVER_NAME'] = 'localhost';
}
$qa_assert = static function ( $value, $message ) {
	if ( ! $value ) {
		throw new RuntimeException( $message );
	}
};
$qa_mods = get_theme_mods();
$qa_widgets = get_option( 'sidebars_widgets' );
$qa_front = get_option( 'show_on_front' );
$qa_stylesheet = get_stylesheet();
$qa_original_query = $GLOBALS['wp_query'];
$qa_original_main_query = $GLOBALS['wp_the_query'];
$qa_ids = array();
$qa_results = array();
$qa_capture = static function ( $template, $query = array() ) {
	$GLOBALS['wp_query'] = new WP_Query( $query );
	$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
	ob_start();
	include get_theme_file_path( $template );
	return ob_get_clean();
};
try {
	foreach ( array( 0, 999999999 ) as $root ) {
		set_theme_mod( 'docspress_root_page', $root );
		$qa_assert( 0 === docspress_get_docs_root_id() && array() === docspress_get_docs_tree(), 'Unset/deleted root fallback.' );
	}
	$qa_results[] = 'Unset/deleted Docs Root';
	$qa_root = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'QA configuration root', 'post_content' => '<p>QA root.</p>' ) );
	$qa_ids[] = $qa_root;
	$qa_empty = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_parent' => $qa_root, 'post_title' => '', 'post_content' => '<p>QA body.</p>' ) );
	$qa_ids[] = $qa_empty;
	$qa_grandchild = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_parent' => $qa_empty, 'post_title' => 'QA grandchild', 'post_content' => '<p>QA child.</p>' ) );
	$qa_ids[] = $qa_grandchild;
	set_theme_mod( 'docspress_root_page', $qa_root );
	ob_start();
	docspress_render_tree_nodes( docspress_get_docs_tree(), $qa_grandchild, docspress_get_open_path_ids( $qa_grandchild ) );
	$qa_tree_html = ob_get_clean();
	$qa_assert( false !== strpos( $qa_tree_html, '>Untitled</a>' ) && false !== strpos( $qa_tree_html, 'Toggle Untitled' ), 'Untitled tree links and toggles need names.' );
	$qa_section_html = $qa_capture( 'page-templates/docs-section.php', array( 'page_id' => $qa_root ) );
	$qa_assert( false !== strpos( $qa_section_html, '>Untitled</span>' ), 'Untitled section index entry.' );
	$qa_results[] = 'Untitled tree, disclosure and section index';
	$qa_excerpt_filter = static function () { return 'Filtered directory description.'; };
	add_filter( 'get_the_excerpt', $qa_excerpt_filter, 99 );
	$qa_assert( 'Filtered directory description.' === docspress_get_directory_description( get_post( $qa_empty ) ), 'Directory must honor native excerpt filters.' );
	remove_filter( 'get_the_excerpt', $qa_excerpt_filter, 99 );
	$qa_results[] = 'Native excerpt filter honored';
	set_theme_mod( 'docspress_reading_width', 'compact' );
	set_theme_mod( 'docspress_text_size', 'large' );
	$qa_classes = docspress_body_classes( array() );
	$qa_assert( in_array( 'docspress-reading-compact', $qa_classes, true ) && in_array( 'docspress-text-large', $qa_classes, true ), 'Reading settings apply.' );
	foreach ( array( 'left', 'right' ) as $side ) {
		set_theme_mod( 'docspress_show_' . $side . '_sidebar', false );
		$qa_assert( ! docspress_sidebar_enabled( $side ), 'Explicit sidebar disable.' );
		set_theme_mod( 'docspress_show_' . $side . '_sidebar', true );
	}
	$qa_results[] = 'Compact/large reading and explicit sidebar toggles';
	$toc = docspress_parse_article_content( '<h2>First</h2><p>A.</p><h3>Second</h3><p>B.</p>' )['toc'];
	foreach ( array( 'off' => false, 'h2' => false, 'h2-h3' => true ) as $depth => $expected ) {
		set_theme_mod( 'docspress_toc_depth', $depth );
		ob_start();
		get_template_part( 'template-parts/navigation/page-toc', null, array( 'toc' => $toc ) );
		$qa_toc_html = ob_get_clean();
		$qa_assert( ( false !== strpos( $qa_toc_html, 'docspress-toc' ) ) === $expected, 'TOC depth or minimum-two-items rule.' );
	}
	$qa_results[] = 'TOC off/H2/H2-H3 and zero/one item behavior';
	require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';
	$qa_manager = new WP_Customize_Manager( array( 'settings_previewed' => false ) );
	docspress_customize_register( $qa_manager );
	foreach ( array( 'docspress_toc_depth', 'docspress_reading_width', 'docspress_text_size' ) as $key ) {
		$setting = $qa_manager->get_setting( $key );
		$qa_assert( 'edit_theme_options' === $setting->capability && $setting->default === $setting->sanitize( '<script>invalid</script>' ), 'Customizer capability/choice sanitization.' );
	}
	$qa_results[] = 'Customizer controls, native capability and whitelist';
	update_option( 'show_on_front', 'posts' );
	$qa_latest = $qa_capture( 'front-page.php', array( 'post_type' => 'post', 'posts_per_page' => 2 ) );
	$qa_assert( false !== strpos( $qa_latest, 'post-summary' ) && false === strpos( $qa_latest, 'directory-block' ), 'Latest-posts front page uses the native query instead of the Docs directory.' );
	$qa_results[] = 'Reading setting: latest posts front page';
	wp_clean_themes_cache();
	if ( wp_get_theme( 'docspress-qa-child' )->exists() ) {
		switch_theme( 'docspress-qa-child' );
		$qa_assert( 'docspress' === get_template() && 'docspress-qa-child' === get_stylesheet(), 'Native child theme parent.' );
		$qa_assert( false !== strpos( get_theme_file_path( '/assets/css/main.css' ), 'docspress-qa-child' ), 'Child asset override.' );
		$qa_assert( false !== strpos( locate_template( 'header.php' ), 'docspress-qa-child' ), 'Child PHP template override.' );
		$qa_results[] = 'Native child template and asset resolution';
	}
} finally {
	switch_theme( $qa_stylesheet );
	update_option( 'theme_mods_' . $qa_stylesheet, $qa_mods );
	update_option( 'sidebars_widgets', $qa_widgets );
	update_option( 'show_on_front', $qa_front );
	$GLOBALS['wp_query'] = $qa_original_query;
	$GLOBALS['wp_the_query'] = $qa_original_main_query;
	wp_reset_postdata();
	foreach ( $qa_ids as $qa_id ) {
		wp_trash_post( $qa_id );
	}
}
$qa_assert( get_theme_mods() === $qa_mods && get_option( 'sidebars_widgets' ) === $qa_widgets && get_option( 'show_on_front' ) === $qa_front, 'Restore original QA configuration.' );
echo wp_json_encode( array( 'result' => 'PASS', 'checks' => $qa_results, 'fixtures' => 'Moved to local Trash; no production changes.' ), JSON_PRETTY_PRINT ) . "\n";
