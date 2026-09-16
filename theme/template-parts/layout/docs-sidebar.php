<?php
/**
 * Desktop docs sidebar plus mobile chapter drawer.
 *
 * @package Docspress
 */

?>
<aside class="left-sidebar" aria-label="<?php esc_attr_e( 'Documentation navigation', 'docspress' ); ?>">
	<?php get_template_part( 'template-parts/navigation/docs-tree', null, array( 'instance' => 'docs-desktop' ) ); ?>
</aside>

<aside class="chapter-drawer" id="chapterDrawer" aria-hidden="true" aria-label="<?php esc_attr_e( 'Documentation navigation', 'docspress' ); ?>">
	<div class="chapter-drawer-head">
		<strong><?php esc_html_e( 'Contents', 'docspress' ); ?></strong>
			<button class="close-btn" type="button" data-close aria-label="<?php esc_attr_e( 'Close navigation', 'docspress' ); ?>"><span class="close-icon" aria-hidden="true"></span></button>
	</div>
	<div class="chapter-drawer-body">
		<?php get_template_part( 'template-parts/navigation/docs-tree', null, array( 'instance' => 'docs-mobile' ) ); ?>
	</div>
</aside>

<div class="drawer-backdrop" id="drawerBackdrop" aria-hidden="true"></div>
