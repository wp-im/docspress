<?php
/**
 * Desktop and mobile page TOC.
 *
 * @package Docspress
 */

$toc = isset( $args['toc'] ) && is_array( $args['toc'] ) ? $args['toc'] : array();
if ( ! $toc ) {
	return;
}
?>
<aside class="right-toc" aria-label="<?php esc_attr_e( 'On this page', 'docspress' ); ?>">
	<strong><?php esc_html_e( 'On this page', 'docspress' ); ?></strong>
	<ul>
		<?php foreach ( $toc as $item ) : ?>
			<li class="toc-level-<?php echo esc_attr( (string) $item['level'] ); ?>">
				<a href="#<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</aside>

<aside class="toc-drawer" id="tocDrawer" aria-hidden="true" aria-label="<?php esc_attr_e( 'On this page', 'docspress' ); ?>">
	<div class="toc-drawer-head">
		<strong><?php esc_html_e( 'On this page', 'docspress' ); ?></strong>
			<button class="close-btn" type="button" data-close aria-label="<?php esc_attr_e( 'Close table of contents', 'docspress' ); ?>"><span class="close-icon" aria-hidden="true"></span></button>
	</div>
	<div class="toc-drawer-body">
		<?php foreach ( $toc as $item ) : ?>
			<a class="toc-level-<?php echo esc_attr( (string) $item['level'] ); ?>" href="#<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
		<?php endforeach; ?>
	</div>
</aside>
