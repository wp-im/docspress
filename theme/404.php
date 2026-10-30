<?php
/**
 * 404 template.
 *
 * @package Docspress
 */

get_header();
?>
	<section class="standard-article">
		<div class="page-eyebrow">404</div>
		<h1><?php esc_html_e( 'Page not found', 'docspress' ); ?></h1>
		<p><?php esc_html_e( 'The page may have moved. Try searching the documentation or return to the home page.', 'docspress' ); ?></p>
		<?php get_search_form(); ?>
	</section>
<?php
get_footer();
