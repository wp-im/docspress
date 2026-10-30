<?php
/**
 * Native Page template with article-owned contents.
 *
 * @package Docspress
 */

get_header();
while ( have_posts() ) :
	the_post();
	docspress_breadcrumbs();
	$parsed_content = docspress_get_parsed_content();
	get_template_part(
		'template-parts/content/content-docs',
		null,
		array(
			'parsed'  => $parsed_content,
			'is_docs' => docspress_is_docs_page( get_the_ID() ),
		)
	);
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
endwhile;
get_footer();
