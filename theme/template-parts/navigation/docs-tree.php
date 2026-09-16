<?php
/**
 * Shared documentation tree.
 *
 * @package Docspress
 */

$root_id    = docspress_get_docs_root_id();
$current_id = get_queried_object_id();
$tree       = docspress_get_docs_tree();
$open_ids   = docspress_get_open_path_ids( $current_id );
$instance   = isset( $args['instance'] ) ? sanitize_html_class( (string) $args['instance'] ) : 'docs';

if ( $root_id ) :
	?>
	<a class="docs-root-link" href="<?php echo esc_url( get_permalink( $root_id ) ); ?>"><?php echo esc_html( get_the_title( $root_id ) ); ?></a>
	<?php
endif;

if ( $tree ) {
	docspress_render_tree_nodes( $tree, $current_id, $open_ids, 1, 0, $instance );
} elseif ( current_user_can( 'edit_theme_options' ) ) {
	?>
	<p class="docs-tree-empty"><?php esc_html_e( 'Choose a Docs Root Page in the Customizer to build the documentation tree.', 'docspress' ); ?></p>
	<?php
}
