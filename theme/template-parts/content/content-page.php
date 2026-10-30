<?php
/**
 * Standard Page content.
 *
 * @package Docspress
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'standard-article' ); ?>>
	<h1><?php echo wp_kses_post( docspress_get_title() ); ?></h1>
	<div class="entry-content">
		<?php
		the_content();
		wp_link_pages();
		?>
	</div>
</article>
