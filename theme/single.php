<?php
/**
 * Native single Post template.
 *
 * @package Docspress
 */

get_header();
while ( have_posts() ) :
	the_post();
	docspress_breadcrumbs();
	get_template_part( 'template-parts/content/content-post' );
	the_post_navigation();
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
endwhile;
get_footer();
