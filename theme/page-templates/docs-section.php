<?php
/**
 * Template Name: Docs Section Index
 * Template Post Type: page
 *
 * @package Docspress
 */

get_header();

while ( have_posts() ) :
	the_post();
	docspress_breadcrumbs();
	$section_tree = docspress_get_section_tree( get_the_ID(), 2 );
	?>
		<div class="content-wrap">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'index-page' ); ?>>
				<?php
				$eyebrow = docspress_get_page_eyebrow( get_the_ID() );
				if ( $eyebrow ) :
					?>
					<div class="page-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
				<?php endif; ?>
				<h1><?php echo wp_kses_post( docspress_get_title() ); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<div class="index-deck"><?php echo wp_kses_post( get_the_excerpt() ); ?></div>
				<?php endif; ?>
				<div class="docs-directory-content"><?php the_content(); ?></div>

				<section class="directory-block" aria-labelledby="docspress-section-pages-heading">
					<div class="directory-heading">
						<h2 id="docspress-section-pages-heading"><?php esc_html_e( 'In this section', 'docspress' ); ?></h2>
						<span><?php echo esc_html( (string) count( $section_tree ) ); ?></span>
					</div>
					<?php if ( $section_tree ) : ?>
						<div class="directory-list">
							<?php foreach ( $section_tree as $index => $node ) : ?>
								<?php $child = $node['page']; ?>
								<div class="directory-group">
									<a class="directory-row<?php echo ! empty( $node['children'] ) ? ' is-primary' : ''; ?>" href="<?php echo esc_url( get_permalink( $child ) ); ?>">
										<span class="directory-num"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
										<span>
											<span class="directory-title"><?php echo esc_html( docspress_get_title( $child ) ); ?></span>
											<span class="directory-desc"><?php echo esc_html( docspress_get_directory_description( $child ) ); ?></span>
										</span>
										<span class="directory-arrow" aria-hidden="true"></span>
									</a>
									<?php if ( ! empty( $node['children'] ) ) : ?>
										<ul class="directory-sublist">
											<?php foreach ( $node['children'] as $subnode ) : ?>
												<?php $subpage = $subnode['page']; ?>
												<li>
													<a class="directory-subrow" href="<?php echo esc_url( get_permalink( $subpage ) ); ?>">
														<span class="directory-subtitle"><?php echo esc_html( docspress_get_title( $subpage ) ); ?></span>
														<span class="directory-arrow" aria-hidden="true"></span>
													</a>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<p class="directory-empty"><?php esc_html_e( 'This section has no child Pages yet.', 'docspress' ); ?></p>
					<?php endif; ?>
				</section>
			</article>
		</div>
	<?php
endwhile;

get_footer();
