<?php
/**
 * Site header and shared layout opening.
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
<a class="screen-reader-text docspress-skip-link" href="#primary"><?php esc_html_e( 'Skip to content', 'docspress' ); ?></a>
<header class="top-header" id="docspress-header">
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
		<button class="header-mobile-btn" id="globalMenuBtn" type="button" aria-expanded="false" aria-controls="globalMenuPanel">
			<span class="docspress-menu-icon" aria-hidden="true"></span><span><?php esc_html_e( 'Menu', 'docspress' ); ?></span>
		</button>
		<div class="docspress-site-menu" id="globalMenuPanel">
			<nav class="global-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'docspress' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'global-nav-list',
						'fallback_cb'    => 'docspress_primary_menu_fallback',
						'depth'          => 3,
					)
				);
				?>
			</nav>
			<div class="header-search-form"><?php get_search_form(); ?></div>
		</div>
	</div>
</header>
<?php get_template_part( 'template-parts/layout/mobile-local' ); ?>
<div class="docspress-shell">
	<?php get_sidebar( 'left' ); ?>
	<main id="primary" class="docspress-main" tabindex="-1">
