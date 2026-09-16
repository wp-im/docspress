<?php
/**
 * Comments template.
 *
 * @package Docspress
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area standard-article">
	<?php if ( have_comments() ) : ?>
		<h2><?php esc_html_e( 'Comments', 'docspress' ); ?></h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>
	<?php comment_form(); ?>
</section>
