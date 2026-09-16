<?php
/**
 * Docs article content.
 *
 * @package Docspress
 */

$parsed  = isset( $args['parsed'] ) && is_array( $args['parsed'] ) ? $args['parsed'] : array(
	'html' => '',
	'toc'  => array(),
);
$eyebrow = docspress_get_page_eyebrow( get_the_ID() );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'docs-article docspress-article' ); ?>>
	<?php if ( $eyebrow ) : ?>
		<div class="docspress-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
	<?php endif; ?>

	<?php the_title( '<h1>', '</h1>' ); ?>

	<?php if ( has_excerpt() ) : ?>
		<div class="docspress-deck"><?php echo wp_kses_post( get_the_excerpt() ); ?></div>
	<?php endif; ?>

	<div class="docspress-meta">
		<?php
		printf(
			/* translators: %s: modified date. */
			esc_html__( 'Updated %s', 'docspress' ),
			esc_html( get_the_modified_date() )
		);
		?>
	</div>

	<?php
	// The HTML has already passed through WordPress the_content filters; this parser only decorates H2/H3 markup.
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $parsed['html'];

	wp_link_pages(
		array(
			'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'docspress' ) . '">',
			'after'  => '</nav>',
		)
	);
	?>

	<?php get_template_part( 'template-parts/navigation/prev-next' ); ?>
</article>
