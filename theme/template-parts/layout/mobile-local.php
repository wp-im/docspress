<?php
/**
 * Independent left/right controls on every compact page.
 *
 * @package Docspress
 */

if ( ! docspress_sidebar_enabled( 'left' ) && ! docspress_sidebar_enabled( 'right' ) ) {
	return;
}
?>
<div class="docspress-sidebar-controls" id="docspress-sidebar-controls">
	<div>
		<?php if ( docspress_sidebar_enabled( 'left' ) ) : ?>
			<button class="docspress-sidebar-trigger docspress-trigger--left" type="button" aria-expanded="false" aria-controls="docspress-leftbar" data-panel-open="left"><span class="docspress-menu-icon" aria-hidden="true"></span><?php esc_html_e( 'Library', 'docspress' ); ?></button>
		<?php endif; ?>
		<?php if ( docspress_sidebar_enabled( 'right' ) ) : ?>
			<button class="docspress-sidebar-trigger docspress-trigger--right" type="button" aria-expanded="false" aria-controls="docspress-rightbar" data-panel-open="right"><?php esc_html_e( 'Sidebar', 'docspress' ); ?><span class="docspress-menu-icon" aria-hidden="true"></span></button>
		<?php endif; ?>
	</div>
</div>
