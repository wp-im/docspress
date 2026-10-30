<?php
/**
 * Site footer.
 *
 * @package Docspress
 */

?>
	</main>
	<?php get_sidebar(); ?>
</div>
<div class="docspress-backdrop" id="docspress-backdrop" hidden></div>
<footer class="site-footer">
	<?php if ( is_active_sidebar( 'docspress-footer' ) ) : ?>
		<div class="docspress-footer-widgets"><?php dynamic_sidebar( 'docspress-footer' ); ?></div>
	<?php endif; ?>
	<div class="footer-inner">
		<div>
			<?php
			$footer_text = trim( (string) get_theme_mod( 'docspress_footer_text', '' ) );
			if ( '' !== $footer_text ) {
				echo esc_html( $footer_text );
			} else {
				printf(
					/* translators: 1: current year, 2: site name. */
					esc_html__( '© %1$s %2$s', 'docspress' ),
					esc_html( wp_date( 'Y' ) ),
					esc_html( get_bloginfo( 'name' ) )
				);
			}
			?>
		</div>
		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav aria-label="<?php esc_attr_e( 'Footer navigation', 'docspress' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-menu',
						'fallback_cb'    => false,
						'depth'          => 1,
					)
				);
				?>
			</nav>
		<?php endif; ?>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
