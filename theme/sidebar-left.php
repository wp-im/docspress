<?php
/**
 * One native navigation panel, shared by desktop and mobile.
 *
 * @package Docspress
 */

if ( ! docspress_sidebar_enabled( 'left' ) ) {
	return;
}
?>
<aside class="docspress-sidebar docspress-sidebar--left" id="docspress-leftbar" aria-label="<?php esc_attr_e( 'Library navigation', 'docspress' ); ?>" data-panel="left">
	<div class="docspress-panel-heading">
		<h2 id="docspress-leftbar-title"><?php esc_html_e( 'Library navigation', 'docspress' ); ?></h2>
		<button class="docspress-close" type="button" data-panel-close aria-label="<?php esc_attr_e( 'Close library navigation', 'docspress' ); ?>"><span aria-hidden="true"></span></button>
	</div>
	<div class="docspress-panel-body">
		<nav aria-label="<?php esc_attr_e( 'Library', 'docspress' ); ?>">
			<?php
			$has_tree = docspress_get_docs_root_id() && get_post_status( docspress_get_docs_root_id() ) === 'publish';
			if ( $has_tree ) {
				get_template_part( 'template-parts/navigation/docs-tree', null, array( 'instance' => 'docspress-library' ) );
			}
			if ( has_nav_menu( 'docspress-primary-sidebar' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'docspress-primary-sidebar',
						'container'      => false,
						'menu_class'     => 'docspress-sidebar-menu',
						'fallback_cb'    => false,
					)
				);
			} elseif ( ! $has_tree ) {
				echo '<ul class="docspress-sidebar-menu">';
				wp_list_pages(
					array(
						'title_li'    => '',
						'depth'       => 0,
						'sort_column' => 'menu_order,post_title',
					)
				);
				echo '</ul>';
			}
			?>
		</nav>
		<?php
		if ( is_active_sidebar( 'docspress-left-sidebar' ) ) {
			dynamic_sidebar( 'docspress-left-sidebar' );
		} else {
			get_search_form();
		}
		?>
	</div>
</aside>
