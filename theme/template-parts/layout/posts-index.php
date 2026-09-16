<?php
/**
 * Shared posts index surface.
 *
 * @package Docspress
 */

?>
<main id="primary" class="site-main-standard">
	<div class="standard-layout-with-sidebar">
		<div>
			<?php
			$page_for_posts = absint( get_option( 'page_for_posts' ) );
			if ( $page_for_posts ) {
				echo '<h1>' . esc_html( get_the_title( $page_for_posts ) ) . '</h1>';
			} elseif ( is_front_page() ) {
				echo '<h1>' . esc_html( get_bloginfo( 'name' ) ) . '</h1>';
			}
			?>
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
		</div>
		<?php get_sidebar(); ?>
	</div>
</main>
