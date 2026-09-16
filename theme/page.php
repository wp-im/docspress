<?php
/**
 * Page template.
 *
 * @package Docspress
 */

get_header();

while ( have_posts() ) :
	the_post();

	if ( docspress_is_docs_page( get_the_ID() ) ) {
		$filtered_content = apply_filters( 'the_content', get_the_content() );
		$parsed_content   = docspress_parse_article_content( $filtered_content );
		get_template_part( 'template-parts/layout/mobile-local', null, array( 'show_toc' => ! empty( $parsed_content['toc'] ) ) );
		?>
		<main id="primary" class="doc-frame">
			<?php get_template_part( 'template-parts/layout/docs-sidebar' ); ?>
			<div class="article-wrap">
				<?php get_template_part( 'template-parts/content/content-docs', null, array( 'parsed' => $parsed_content ) ); ?>
				<?php if ( comments_open() || get_comments_number() ) : ?>
					<?php comments_template(); ?>
				<?php endif; ?>
			</div>
			<?php get_template_part( 'template-parts/navigation/page-toc', null, array( 'toc' => $parsed_content['toc'] ) ); ?>
		</main>
		<?php
	} else {
		?>
		<main id="primary" class="site-main-standard">
			<?php get_template_part( 'template-parts/content/content-page' ); ?>
			<?php if ( comments_open() || get_comments_number() ) : ?>
				<?php comments_template(); ?>
			<?php endif; ?>
		</main>
		<?php
	}
endwhile;

get_footer();
