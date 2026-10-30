<?php
/**
 * Page content. Only documentation Pages use the optional editorial rail.
 *
 * @package Docspress
 */

$parsed  = isset( $args['parsed'] ) ? $args['parsed'] : docspress_get_parsed_content();
$is_docs = isset( $args['is_docs'] ) ? (bool) $args['is_docs'] : true;
$eyebrow = $is_docs ? docspress_get_page_eyebrow( get_the_ID() ) : '';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $is_docs ? 'docs-article docspress-article' : 'standard-article' ); ?>>
	<header class="entry-header">
		<?php
		if ( $eyebrow ) :
			?>
			<div class="docspress-eyebrow"><?php echo esc_html( $eyebrow ); ?></div><?php endif; ?>
		<h1><?php echo wp_kses_post( docspress_get_title() ); ?></h1>
		<?php if ( has_excerpt() && ! post_password_required() ) : ?>
			<div class="docspress-deck"><?php echo wp_kses_post( get_the_excerpt() ); ?></div>
		<?php endif; ?>
		<?php docspress_entry_meta(); ?>
	</header>
	<?php get_template_part( 'template-parts/navigation/page-toc', null, array( 'toc' => $parsed['toc'] ) ); ?>
	<div class="entry-content docspress-prose">
		<?php
		echo $parsed['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core the_content filters have run.
		wp_link_pages();
		?>
	</div>
	<?php
	if ( $is_docs ) :
		?>
		<?php get_template_part( 'template-parts/navigation/prev-next' ); ?><?php endif; ?>
</article>
