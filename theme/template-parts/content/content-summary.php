<?php
/**
 * Archive/search summary.
 *
 * @package Docspress
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-summary' ); ?>>
	<h2><a href="<?php the_permalink(); ?>"><?php echo wp_kses_post( docspress_get_title() ); ?></a></h2>
	<div class="docspress-result-context">
		<?php
		$content_type = get_post_type_object( get_post_type() );
		echo esc_html( $content_type ? $content_type->labels->singular_name : '' );
		$parent_id = wp_get_post_parent_id( get_the_ID() );
		if ( $parent_id && is_post_publicly_viewable( $parent_id ) ) {
			echo ' <span aria-hidden="true">·</span> ' . esc_html( docspress_get_title( $parent_id ) );
		}
		?>
	</div>
	<?php the_excerpt(); ?>
</article>
