<?php
/**
 * Search form.
 *
 * @package Docspress
 */

?>
<form role="search" method="get" class="search-form docspress-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="docspress-search-label">
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'docspress' ); ?></span>
		<input type="search" class="search-field" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" placeholder="<?php esc_attr_e( 'Search…', 'docspress' ); ?>">
	</label>
	<button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Submit search', 'docspress' ); ?>">
		<svg class="docspress-icon docspress-icon--search" aria-hidden="true" viewBox="0 0 24 24" focusable="false">
			<circle cx="10.75" cy="10.75" r="6.25"></circle>
			<path d="m15.5 15.5 5 5"></path>
		</svg>
		<span class="screen-reader-text"><?php esc_html_e( 'Submit search', 'docspress' ); ?></span>
	</button>
</form>
