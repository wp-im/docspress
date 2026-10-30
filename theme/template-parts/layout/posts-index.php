<?php
/**
 * Native posts index within the shared site shell.
 *
 * @package Docspress
 */

$page_for_posts = absint( get_option( 'page_for_posts' ) );
?>
<section class="docspress-listing">
	<h1><?php echo esc_html( $page_for_posts ? docspress_get_title( $page_for_posts ) : get_bloginfo( 'name' ) ); ?></h1>
	<div class="post-list">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/content/content-summary' );
			}
			the_posts_pagination();
		} else {
			get_template_part( 'template-parts/content/content-none' );
		}
		?>
	</div>
</section>
