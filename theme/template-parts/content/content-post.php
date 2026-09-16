<?php
/**
 * Single Post content.
 *
 * @package Docspress
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'standard-article' ); ?>>
	<?php the_title( '<h1>', '</h1>' ); ?>
	<div class="entry-meta"><?php echo esc_html( get_the_date() ); ?> · <?php echo esc_html( get_the_author() ); ?></div>
	<div class="entry-content">
		<?php
		the_content();
		wp_link_pages();
		?>
	</div>
</article>
