<?php
/**
 * Search results.
 *
 * @package Docspress
 */

get_header();
?>
<main id="primary" class="site-main-standard">
	<header class="standard-article">
		<h1>
			<?php
			printf(
				/* translators: %s: search query. */
				esc_html__( 'Search results for “%s”', 'docspress' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
	</header>
	<div class="post-list">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<?php get_template_part( 'template-parts/content/content-summary' ); ?>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content/content-none' ); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
