<?php
/**
 * Fallback template.
 *
 * @package Docspress
 */

get_header();
?>
<main id="primary" class="site-main-standard">
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
