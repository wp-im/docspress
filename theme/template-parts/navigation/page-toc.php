<?php
/**
 * This navigation belongs to the current article, never a sidebar.
 *
 * @package Docspress
 */

$depth = get_theme_mod( 'docspress_toc_depth', 'h2-h3' );
$toc   = isset( $args['toc'] ) && is_array( $args['toc'] ) ? $args['toc'] : array();
if ( 'h2' === $depth ) {
	$toc = array_values(
		array_filter(
			$toc,
			static function ( $heading ) {
				return 2 === $heading['level'];
			}
		)
	);
}
if ( 'off' === $depth || count( $toc ) < 2 || post_password_required() ) {
	return;
}
?>
<details class="docspress-toc">
	<summary><?php esc_html_e( 'On this page', 'docspress' ); ?> <span><?php echo esc_html( (string) count( $toc ) ); ?></span></summary>
	<nav aria-label="<?php esc_attr_e( 'Article contents', 'docspress' ); ?>">
		<ul>
			<?php foreach ( $toc as $heading ) : ?>
				<li class="toc-level-<?php echo esc_attr( (string) $heading['level'] ); ?>"><a href="<?php echo esc_url( '#' . rawurlencode( $heading['id'] ) ); ?>"><?php echo esc_html( $heading['title'] ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
</details>
