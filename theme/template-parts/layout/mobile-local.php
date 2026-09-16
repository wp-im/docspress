<?php
/**
 * Local navigation bar shown after the desktop docs sidebar collapses.
 *
 * @package Docspress
 */

$show_toc = ! empty( $args['show_toc'] );
$root_id  = docspress_get_docs_root_id();
?>
<div class="mobile-local">
	<div class="mobile-local-inner<?php echo $show_toc ? '' : ' is-single'; ?>">
			<button class="local-trigger" id="chapterMenuBtn" type="button" aria-controls="chapterDrawer" aria-expanded="false">
				<span class="icon icon-menu" aria-hidden="true"></span>
			<span><?php esc_html_e( 'Contents', 'docspress' ); ?></span>
		</button>
		<?php if ( $show_toc ) : ?>
			<button class="local-trigger" id="tocMenuBtn" type="button" aria-controls="tocDrawer" aria-expanded="false">
				<span><?php esc_html_e( 'On this page', 'docspress' ); ?></span>
					<span class="icon icon-toc" aria-hidden="true"></span>
			</button>
		<?php elseif ( $root_id ) : ?>
			<span class="local-context"><?php echo esc_html( get_the_title( $root_id ) ); ?></span>
		<?php endif; ?>
	</div>
</div>
