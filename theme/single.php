<?php
/**
 * Single Post template.
 *
 * @package Docspress
 */

get_header();
?>
<main id="primary" class="site-main-standard">
	<div class="standard-layout-with-sidebar">
		<div>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<?php get_template_part( 'template-parts/content/content-post' ); ?>
				<?php the_post_navigation(); ?>
				<?php
				if ( comments_open() || get_comments_number() ) :
					comments_template();
endif;
				?>
			<?php endwhile; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</main>
<?php
get_footer();
