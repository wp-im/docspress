<?php
/**
 * Standard non-docs sidebar.
 *
 * @package Docspress
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<aside class="standard-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'docspress' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
