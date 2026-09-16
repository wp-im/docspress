<?php
/**
 * Archive template.
 *
 * @package Docspress
 */

get_header();
?>
<main id="primary" class="site-main-standard">
	<header class="standard-article">
		<?php the_archive_title( '<h1>', '</h1>' ); ?>
		<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
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
