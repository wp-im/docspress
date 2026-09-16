<?php
/**
 * Site header.
 *
 * @package Docspress
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'docspress' ); ?></a>
<header class="top-header" role="banner">
	<div class="header-inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php
			$custom_logo_id = absint( get_theme_mod( 'custom_logo', 0 ) );
			if ( $custom_logo_id ) {
				echo wp_get_attachment_image( $custom_logo_id, 'full', false, array( 'class' => 'brand-logo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo '<span class="brand-mark" aria-hidden="true"></span>';
			}
			?>
			<span><?php bloginfo( 'name' ); ?></span>
		</a>

		<nav class="global-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'docspress' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'global-nav-list',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</nav>

		<div class="header-search-form">
			<?php get_search_form(); ?>
		</div>

		<button class="header-mobile-btn" id="globalMenuBtn" type="button" aria-expanded="false" aria-controls="globalMenuPanel" aria-label="<?php esc_attr_e( 'Open site menu', 'docspress' ); ?>">
			<span></span>
		</button>
	</div>
</header>

<div class="global-menu-panel" id="globalMenuPanel" aria-label="<?php esc_attr_e( 'Site menu', 'docspress' ); ?>" aria-hidden="true">
	<div class="global-menu-links">
		<div class="global-menu-search">
			<?php get_search_form(); ?>
		</div>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'global-nav-list',
				'fallback_cb'    => false,
				'depth'          => 1,
			)
		);
		?>
	</div>
</div>
