<?php
/**
 * Single Post content with native metadata and in-article TOC.
 *
 * @package Docspress
 */

$parsed = docspress_get_parsed_content();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'standard-article' ); ?>>
	<header class="entry-header">
		<h1><?php echo wp_kses_post( docspress_get_title() ); ?></h1>
		<?php docspress_entry_meta( true ); ?>
		<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
			<figure class="docspress-featured-image"><?php the_post_thumbnail( 'large' ); ?></figure>
		<?php endif; ?>
	</header>
	<?php get_template_part( 'template-parts/navigation/page-toc', null, array( 'toc' => $parsed['toc'] ) ); ?>
	<div class="entry-content">
		<?php
		echo $parsed['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core the_content filters have run.
		wp_link_pages();
		the_tags( '<p class="docspress-entry-tags">', ', ', '</p>' );
		?>
	</div>
</article>
