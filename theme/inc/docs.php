<?php
/**
 * Documentation hierarchy helpers.
 *
 * @package Docspress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the configured docs root Page ID.
 *
 * @return int
 */
function docspress_get_docs_root_id() {
	$id = absint( get_theme_mod( 'docspress_root_page', 0 ) );
	return $id && 'publish' === get_post_status( $id ) ? $id : 0;
}

/**
 * Determine whether a Page belongs to the configured docs subtree.
 *
 * @param int $post_id Page ID.
 * @return bool
 */
function docspress_is_docs_page( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_queried_object_id();
	$root_id = docspress_get_docs_root_id();

	if ( ! $post_id || ! $root_id || 'page' !== get_post_type( $post_id ) ) {
		return false;
	}

	if ( $post_id === $root_id ) {
		return true;
	}

	return in_array( $root_id, array_map( 'intval', get_post_ancestors( $post_id ) ), true );
}

/**
 * Sort Pages predictably by menu_order and title.
 *
 * @param array $pages Pages.
 * @return array
 */
function docspress_sort_pages( $pages ) {
	usort(
		$pages,
		static function ( $a, $b ) {
			$order_compare = (int) $a->menu_order <=> (int) $b->menu_order;
			if ( 0 !== $order_compare ) {
				return $order_compare;
			}
			$title_compare = strnatcasecmp( (string) $a->post_title, (string) $b->post_title );
			return 0 !== $title_compare ? $title_compare : (int) $a->ID <=> (int) $b->ID;
		}
	);
	return $pages;
}

/**
 * Build a nested Page tree from a flat list.
 *
 * Pure helper used by both rendering and tests.
 *
 * @param array $pages     Page-like objects with ID/post_parent/menu_order/post_title.
 * @param int   $parent_id Parent ID where traversal begins.
 * @param int   $max_depth Maximum visual depth below parent.
 * @param int   $depth     Current depth.
 * @return array
 */
function docspress_build_page_tree( $pages, $parent_id, $max_depth = 0, $depth = 1 ) {
	$groups = array();
	foreach ( $pages as $page ) {
		$groups[ (int) $page->post_parent ][] = $page;
	}
	foreach ( $groups as $id => $children ) {
		$groups[ $id ] = docspress_sort_pages( $children );
	}
	$walk = static function ( $parent_key, $level, $path ) use ( &$walk, $groups, $max_depth ) {
		if ( ( $max_depth > 0 && $level > $max_depth ) || isset( $path[ $parent_key ] ) ) {
			return array();
		}
		$path[ $parent_key ] = true;
		$nodes               = array();
		foreach ( $groups[ $parent_key ] ?? array() as $page ) {
			if ( isset( $path[ (int) $page->ID ] ) ) {
				continue;
			}
			$nodes[] = array(
				'page'     => $page,
				'children' => $walk( (int) $page->ID, $level + 1, $path ),
			);
		}
		return $nodes;
	};
	return $walk( (int) $parent_id, $depth, array() );
}

/**
 * Retrieve all published descendants of the Docs Root.
 *
 * @return array
 */
function docspress_get_docs_pages() {
	static $cache = array();

	$root_id = docspress_get_docs_root_id();
	if ( ! $root_id ) {
		return array();
	}

	$key = get_current_blog_id() . ':' . get_locale() . ':' . $root_id;
	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}

	$pages = get_pages(
		array(
			'child_of'    => $root_id,
			'sort_column' => 'menu_order,post_title',
			'sort_order'  => 'ASC',
			'post_status' => 'publish',
		)
	);

	$cache[ $key ] = is_array( $pages ) ? $pages : array();
	return $cache[ $key ];
}

/**
 * Get the docs navigation tree.
 *
 * @param int $max_depth Maximum visual depth.
 * @return array
 */
function docspress_get_docs_tree( $max_depth = 0 ) {
	static $cache = array();
	$root_id      = docspress_get_docs_root_id();
	$key          = get_current_blog_id() . ':' . get_locale() . ':' . $root_id . ':' . $max_depth;
	if ( ! isset( $cache[ $key ] ) ) {
		$cache[ $key ] = $root_id ? docspress_build_page_tree( docspress_get_docs_pages(), $root_id, $max_depth ) : array();
	}
	return $cache[ $key ];
}

/**
 * Flatten a nested docs tree in depth-first reading order.
 *
 * @param array $tree Tree nodes.
 * @return array
 */
function docspress_flatten_page_tree( $tree ) {
	$flat = array();
	$walk = static function ( $nodes ) use ( &$walk, &$flat ) {
		foreach ( $nodes as $node ) {
			$flat[] = $node['page'];
			$walk( $node['children'] );
		}
	};
	$walk( $tree );
	return $flat;
}

/**
 * Previous and next docs Pages in depth-first order.
 *
 * @param int $page_id Current Page ID.
 * @return array{previous:WP_Post|null,next:WP_Post|null}
 */
function docspress_get_prev_next_pages( $page_id ) {
	static $cache = array();
	$key          = get_current_blog_id() . ':' . get_locale() . ':' . docspress_get_docs_root_id();
	if ( ! isset( $cache[ $key ] ) ) {
		$cache[ $key ] = docspress_flatten_page_tree( docspress_get_docs_tree() );
	}
	$pages = $cache[ $key ];
	$ids   = array_map(
		static function ( $page ) {
			return (int) $page->ID;
		},
		$pages
	);
	$index = array_search( (int) $page_id, $ids, true );

	if ( false === $index ) {
		return array(
			'previous' => null,
			'next'     => null,
		);
	}

	return array(
		'previous' => $index > 0 ? $pages[ $index - 1 ] : null,
		'next'     => $index < count( $pages ) - 1 ? $pages[ $index + 1 ] : null,
	);
}

/**
 * Direct child Pages, ordered for index pages.
 *
 * @param int $page_id Parent Page ID.
 * @return array
 */
function docspress_get_direct_children( $page_id ) {
	$pages = get_pages(
		array(
			'parent'      => absint( $page_id ),
			'sort_column' => 'menu_order,post_title',
			'sort_order'  => 'ASC',
			'post_status' => 'publish',
		)
	);
	return is_array( $pages ) ? $pages : array();
}

/**
 * Build a shallow directory tree for a Section Index page.
 *
 * @param int $page_id   Section Page ID.
 * @param int $max_depth Number of directory levels to include.
 * @return array
 */
function docspress_get_section_tree( $page_id, $max_depth = 2 ) {
	$page_id = absint( $page_id );
	if ( ! $page_id ) {
		return array();
	}

	$pages = get_pages(
		array(
			'child_of'    => $page_id,
			'sort_column' => 'menu_order,post_title',
			'sort_order'  => 'ASC',
			'post_status' => 'publish',
		)
	);

	return is_array( $pages ) ? docspress_build_page_tree( $pages, $page_id, $max_depth ) : array();
}

/**
 * IDs that should be open in the docs tree.
 *
 * @param int $current_id Current page.
 * @return int[]
 */
function docspress_get_open_path_ids( $current_id ) {
	$ids   = array_map( 'intval', get_post_ancestors( $current_id ) );
	$ids[] = (int) $current_id;
	return array_values( array_unique( $ids ) );
}

/**
 * Render tree nodes with independent toggle and link controls.
 *
 * @param array  $nodes      Tree nodes.
 * @param int    $current_id Current Page ID.
 * @param int[]  $open_ids   IDs on current path.
 * @param int    $depth      Current depth.
 * @param int    $parent_id Parent node ID used for subtree aria-controls.
 * @param string $instance  Unique rendered tree instance prefix.
 */
function docspress_render_tree_nodes( $nodes, $current_id, $open_ids, $depth = 1, $parent_id = 0, $instance = 'docs' ) {
	if ( empty( $nodes ) ) {
		return;
	}

	$instance = sanitize_html_class( $instance );
	$list_id  = $parent_id ? $instance . '-subtree-' . $parent_id : '';
	echo '<ul class="' . ( 1 === $depth ? 'docs-tree' : 'tree-children' ) . '"' . ( $list_id ? ' id="' . esc_attr( $list_id ) . '"' : '' ) . '>';
	foreach ( $nodes as $node ) {
		$page         = $node['page'];
		$has_children = ! empty( $node['children'] );
		$is_current   = (int) $page->ID === (int) $current_id;
		$is_open      = $has_children && in_array( (int) $page->ID, $open_ids, true );
		$is_ancestor  = ! $is_current && in_array( (int) $page->ID, $open_ids, true );
		$classes      = array( 'tree-node' );
		if ( $is_open ) {
			$classes[] = 'is-open';
		}

		echo '<li class="' . esc_attr( implode( ' ', $classes ) ) . '" data-tree-node="' . esc_attr( (string) $page->ID ) . '">';
		echo '<div class="tree-row' . ( $has_children ? '' : ' no-toggle' ) . '">';
		if ( $has_children ) {
			/* translators: %s: page title. */
			$toggle_label = sprintf( __( 'Toggle %s', 'docspress' ), docspress_get_title( $page ) );
			echo '<button class="tree-toggle" type="button" data-tree-key="' . esc_attr( (string) $page->ID ) . '" aria-expanded="' . ( $is_open ? 'true' : 'false' ) . '" aria-controls="' . esc_attr( $instance . '-subtree-' . $page->ID ) . '" aria-label="' . esc_attr( $toggle_label ) . '"></button>';
		} else {
			echo '<span class="tree-spacer" aria-hidden="true"></span>';
		}

		$link_classes = array( 'tree-link' );
		if ( $is_current ) {
			$link_classes[] = 'active';
		} elseif ( $is_ancestor ) {
			$link_classes[] = 'section-current';
		}

		echo '<a class="' . esc_attr( implode( ' ', $link_classes ) ) . '" href="' . esc_url( get_permalink( $page ) ) . '"' . ( $is_current ? ' aria-current="page"' : '' ) . '>' . esc_html( docspress_get_title( $page ) ) . '</a>';
		echo '</div>';
		if ( $has_children ) {
			if ( $depth >= 2 && ! $is_open && count( docspress_get_docs_pages() ) > 200 ) {
				echo '<ul class="tree-children" id="' . esc_attr( $instance . '-subtree-' . $page->ID ) . '"><li class="docspress-tree-continuation"><a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html__( 'Open this section', 'docspress' ) . '</a></li></ul>';
			} else {
				docspress_render_tree_nodes( $node['children'], $current_id, $open_ids, $depth + 1, (int) $page->ID, $instance );
			}
		}
		echo '</li>';
	}
	echo '</ul>';
}
