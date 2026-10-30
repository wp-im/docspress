<?php
/**
 * Isolated-site fixtures, never included in the install ZIP.
 *
 * @package Docspress
 */

if ( ! defined( 'ABSPATH' ) || ! in_array( wp_parse_url( home_url(), PHP_URL_HOST ), array( 'localhost', '127.0.0.1' ), true ) ) {
	throw new RuntimeException( 'Only run these fixtures on an isolated loopback site.' );
}

$docspress_fixture_languages = array(
	'zh' => array( '长期资料库：阅读、检索与维护', '资料库不仅保存内容，也要保存读者找到它的路径。稳定的章节、完整的引用和清楚的更新时间，让过去的资料仍然能够被理解。阅读时应能自然移动，而不需要不断调整屏幕。', '资料的组织', '检索与引用', '维护与回顾' ),
	'ja' => array( '長期資料庫を読み続けるための設計', '資料を保存することと、後から読み返せることは同じではありません。見出し、出典、更新日、関連するページへのリンクがそろっていると、時間が経っても内容を理解できます。読みやすい余白と自然な行間を保ちます。', '資料の整理', '検索と引用', '保守と見直し' ),
	'en' => array( 'A durable reference library: reading, finding, and maintaining long articles', 'A useful library preserves the path to a document as carefully as the document itself. Clear headings, durable links, explicit sources, and readable paragraphs help a future reader understand why a record mattered and whether it is still current.', 'Organizing the collection', 'Finding and citing', 'Review and maintenance' ),
);
$docspress_fixture_ids = array();
foreach ( $docspress_fixture_languages as $language => $copy ) {
	$html = '<div lang="' . $language . '">';
	for ( $section = 2; $section <= 4; ++$section ) {
		$html .= '<h2 id="reference-' . $language . '-' . $section . '">' . $copy[ $section ] . '</h2>';
		for ( $paragraph = 0; $paragraph < 8; ++$paragraph ) {
			$html .= '<p>' . $copy[1] . ' <a href="#reference-' . $language . '-4">' . $copy[4] . '</a></p>';
		}
		$html .= '<div class="wp-block-group"><h3>Reference ' . $section . ' <em>details</em></h3><blockquote><p>' . $copy[1] . '</p><cite>Docspress QA fixture</cite></blockquote></div>';
	}
	$html .= '<h2>Tables and code</h2><figure class="wp-block-table"><table><caption>Reference records</caption><thead><tr><th>Record</th><th>Source</th><th>Year</th><th>Notes</th></tr></thead><tbody><tr><td>Archive 001</td><td>' . str_repeat( 'source.example/', 12 ) . '</td><td>2026</td><td>' . $copy[1] . '</td></tr></tbody></table></figure>';
	$html .= '<pre class="wp-block-code"><code>' . esc_html( 'reference --source=' . str_repeat( 'long-path/', 24 ) ) . '</code></pre><ul><li>' . $copy[1] . '</li><li>https://example.test/' . str_repeat( 'unbroken', 25 ) . '</li></ul>';
	$html .= '<figure><img src="' . esc_url( get_theme_file_uri( '/screenshot.png' ) ) . '" width="1200" height="900" alt="Theme screenshot used only for image ratio testing"><figcaption>' . $copy[1] . '</figcaption></figure>';
	$html .= '<p id="footnote-1">Reference note. <a href="#reference-' . $language . '-2">Return to the document</a></p></div>';
	$old = get_page_by_path( 'reading-fixture-' . $language );
	$data = array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => 'reading-fixture-' . $language, 'post_title' => $copy[0], 'post_content' => $html, 'post_excerpt' => $copy[1], 'comment_status' => 'open' );
	if ( $old ) {
		$data['ID'] = $old->ID;
	}
	$id = wp_insert_post( $data, true );
	if ( is_wp_error( $id ) ) {
		throw new RuntimeException( $id->get_error_message() );
	}
	$docspress_fixture_ids[ $language ] = $id;
}

$docspress_private = get_page_by_path( 'private-fixture' );
if ( ! $docspress_private ) {
	wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'private', 'post_name' => 'private-fixture', 'post_title' => 'PRIVATE_FIXTURE_DO_NOT_LEAK', 'post_content' => '<h2>PRIVATE_SECRET_HEADING</h2><p>Private body.</p>', 'post_parent' => docspress_get_docs_root_id() ) );
}
if ( ! get_page_by_path( 'protected-fixture' ) ) {
	wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => 'protected-fixture', 'post_title' => 'Protected reference', 'post_content' => '<h2>PROTECTED_SECRET_HEADING</h2><p>PROTECTED_SECRET_BODY</p><h2>Another secret</h2>', 'post_password' => wp_generate_password( 24 ), 'post_parent' => docspress_get_docs_root_id() ) );
}
if ( ! get_page_by_path( 'reading-post', OBJECT, 'post' ) ) {
	wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_name' => 'reading-post', 'post_title' => 'A complete native Post with headings and comments', 'post_content' => get_post_field( 'post_content', $docspress_fixture_ids['en'] ), 'comment_status' => 'open' ) );
}
update_option( 'docspress_qa_fixtures', $docspress_fixture_ids );
echo "Docspress isolated content fixtures: PASS\n";
