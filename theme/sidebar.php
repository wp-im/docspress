<?php
/**
 * Site-wide right widget panel. Article TOCs stay inside articles.
 *
 * @package Docspress
 */

if ( ! docspress_sidebar_enabled( 'right' ) ) {
	return;
}
?>
<aside class="docspress-sidebar docspress-sidebar--right" id="docspress-rightbar" aria-label="<?php esc_attr_e( 'Sidebar', 'docspress' ); ?>" data-panel="right">
	<div class="docspress-panel-heading">
		<h2 id="docspress-rightbar-title"><?php esc_html_e( 'Sidebar', 'docspress' ); ?></h2>
		<button class="docspress-close" type="button" data-panel-close aria-label="<?php esc_attr_e( 'Close sidebar', 'docspress' ); ?>"><span aria-hidden="true"></span></button>
	</div>
	<div class="docspress-panel-body">
		<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		<?php else : ?>
			<section class="widget">
				<h2 class="widget-title"><?php bloginfo( 'name' ); ?></h2>
				<?php
				if ( get_bloginfo( 'description' ) ) :
					?>
					<p><?php bloginfo( 'description' ); ?></p><?php endif; ?>
				<a href="<?php echo esc_url( get_feed_link() ); ?>"><?php esc_html_e( 'Subscribe to updates', 'docspress' ); ?></a>
			</section>
			<?php
			the_widget(
				'WP_Widget_Recent_Posts',
				array(
					'title'  => __( 'Recent articles', 'docspress' ),
					'number' => 5,
				),
				array(
					'before_widget' => '<section class="widget">',
					'after_widget'  => '</section>',
					'before_title'  => '<h2 class="widget-title">',
					'after_title'   => '</h2>',
				)
			);
			?>
		<?php endif; ?>
	</div>
</aside>
