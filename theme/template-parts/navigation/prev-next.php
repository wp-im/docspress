<?php
/**
 * Documentation previous/next navigation.
 *
 * @package Docspress
 */

$navigation = docspress_get_prev_next_pages( get_the_ID() );
$previous   = $navigation['previous'];
$next       = $navigation['next'];

if ( ! $previous && ! $next ) {
	return;
}
?>
<nav class="docspress-prevnext" aria-label="<?php esc_attr_e( 'Documentation pagination', 'docspress' ); ?>">
	<?php if ( $previous ) : ?>
		<a class="docspress-prev-link" href="<?php echo esc_url( get_permalink( $previous ) ); ?>">
			<span class="docspress-nav-kicker"><?php esc_html_e( 'Previous', 'docspress' ); ?></span>
			<span class="docspress-nav-title"><?php echo esc_html( get_the_title( $previous ) ); ?></span>
		</a>
	<?php else : ?>
		<span></span>
	<?php endif; ?>

	<?php if ( $next ) : ?>
		<a class="docspress-next-link" href="<?php echo esc_url( get_permalink( $next ) ); ?>">
			<span class="docspress-nav-kicker"><?php esc_html_e( 'Next', 'docspress' ); ?></span>
			<span class="docspress-nav-title"><?php echo esc_html( get_the_title( $next ) ); ?></span>
		</a>
	<?php endif; ?>
</nav>
