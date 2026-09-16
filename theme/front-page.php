<?php
/**
 * Front page / site index.
 *
 * @package Docspress
 */

get_header();

if ( 'page' !== get_option( 'show_on_front' ) ) {
	get_template_part( 'template-parts/layout/posts-index' );
	get_footer();
	return;
}

while ( have_posts() ) :
	the_post();
	$root_id  = docspress_get_docs_root_id();
	$sections = $root_id ? docspress_get_direct_children( $root_id ) : array();
	get_template_part( 'template-parts/layout/mobile-local', null, array( 'show_toc' => false ) );
	?>
	<main id="primary" class="doc-frame index-frame">
		<?php get_template_part( 'template-parts/layout/docs-sidebar' ); ?>
		<div class="content-wrap">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'index-page' ); ?>>
				<?php if ( get_bloginfo( 'description' ) ) : ?>
					<div class="page-eyebrow"><?php bloginfo( 'description' ); ?></div>
				<?php endif; ?>
				<?php the_title( '<h1>', '</h1>' ); ?>
				<?php if ( has_excerpt() ) : ?>
					<div class="index-deck"><?php echo wp_kses_post( get_the_excerpt() ); ?></div>
				<?php endif; ?>
				<div class="docs-directory-content"><?php the_content(); ?></div>

				<div class="lead-search">
					<?php get_search_form(); ?>
				</div>

				<section class="directory-block" aria-labelledby="docspress-sections-heading">
					<div class="directory-heading">
						<h2 id="docspress-sections-heading"><?php esc_html_e( 'Sections', 'docspress' ); ?></h2>
						<span><?php echo esc_html( (string) count( $sections ) ); ?></span>
					</div>
					<?php if ( $sections ) : ?>
						<div class="directory-list">
							<?php foreach ( $sections as $index => $section ) : ?>
								<a class="directory-row is-primary" href="<?php echo esc_url( get_permalink( $section ) ); ?>">
									<span class="directory-num"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
									<span>
										<span class="directory-title"><?php echo esc_html( get_the_title( $section ) ); ?></span>
										<span class="directory-desc"><?php echo esc_html( docspress_get_directory_description( $section ) ); ?></span>
									</span>
									<span class="directory-arrow" aria-hidden="true"></span>
								</a>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<p class="directory-empty"><?php esc_html_e( 'Add child Pages below the configured Docs Root to populate this index.', 'docspress' ); ?></p>
					<?php endif; ?>
				</section>
			</article>
		</div>
	</main>
	<?php
endwhile;

get_footer();
