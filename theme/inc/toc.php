<?php
/**
 * Non-destructive article heading decoration using the WordPress HTML API.
 *
 * @package Docspress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter the current content exactly once, honoring password/content filters.
 *
 * @return array{html:string,toc:array}
 */
function docspress_get_parsed_content() {
	$content = apply_filters( 'the_content', get_the_content() );
	if ( post_password_required() ) {
		return array(
			'html' => $content,
			'toc'  => array(),
		);
	}
	return docspress_parse_article_content( $content );
}

/**
 * Preserve content markup, add stable unique heading anchors, and build a TOC.
 *
 * Explicit unique heading IDs are retained, even when titles change. Generated
 * anchors reserve every existing content ID, including non-heading elements.
 * No filtered content or permission-dependent HTML is cached across requests.
 *
 * @param string $content Filtered WordPress content HTML.
 * @return array{html:string,toc:array}
 */
function docspress_parse_article_content( $content ) {
	$content = (string) $content;
	if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return array(
			'html' => $content,
			'toc'  => array(),
		);
	}

	$reader    = new WP_HTML_Tag_Processor( $content );
	$reserved  = array();
	$foreign   = array();
	$headings  = array();
	$current   = null;
	$inert_tag = 0;
	while ( $reader->next_token() ) {
		$tag = $reader->get_tag();
		if ( 'TEMPLATE' === $tag ) {
			$inert_tag += $reader->is_tag_closer() ? -1 : 1;
		}
		if ( $reader->is_tag_closer() ) {
			if ( null !== $current && $tag === $current['tag'] ) {
				$headings[] = $current;
				$current    = null;
			}
			continue;
		}
		if ( null !== $tag ) {
			$id = $reader->get_attribute( 'id' );
			if ( is_string( $id ) && '' !== $id ) {
				$reserved[ $id ] = true;
				if ( ! in_array( $tag, array( 'H2', 'H3' ), true ) ) {
					$foreign[ $id ] = true;
				}
			}
		}
		if ( in_array( $tag, array( 'H2', 'H3' ), true ) ) {
			if ( null !== $current ) {
				$headings[] = $current;
			}
			$current = array(
				'tag'    => $tag,
				'title'  => '',
				'id'     => $reader->get_attribute( 'id' ),
				'hidden' => $inert_tag > 0 || null !== $reader->get_attribute( 'hidden' ) || 'true' === $reader->get_attribute( 'aria-hidden' ),
			);
		} elseif ( null !== $current && '#text' === $reader->get_token_type() ) {
			$current['title'] .= $reader->get_modifiable_text();
		}
	}
	if ( null !== $current ) {
		$headings[] = $current;
	}

	$writer   = new WP_HTML_Tag_Processor( $content );
	$used     = array();
	$toc      = array();
	$position = 0;
	while ( $writer->next_tag() ) {
		$tag = $writer->get_tag();
		if ( ! in_array( $tag, array( 'H2', 'H3' ), true ) ) {
			continue;
		}
		$heading = isset( $headings[ $position ] ) ? $headings[ $position ] : null;
		++$position;
		if ( ! $heading ) {
			continue;
		}
		$title = trim( preg_replace( '/\\s+/u', ' ', $heading['title'] ) );
		if ( '' === $title || $heading['hidden'] ) {
			continue;
		}
		$given   = is_string( $heading['id'] ) ? trim( $heading['id'] ) : '';
		$base_id = '' !== $given ? $given : rawurldecode( sanitize_title( $title ) );
		if ( '' === $base_id ) {
			$base_id = 'section-' . $position;
		}
		$id     = $base_id;
		$suffix = 2;
		while ( isset( $used[ $id ] ) || isset( $foreign[ $id ] ) || ( ( '' === $given || $id !== $given ) && isset( $reserved[ $id ] ) ) ) {
			$id = $base_id . '-' . $suffix;
			++$suffix;
		}
		$used[ $id ] = true;
		$writer->set_attribute( 'id', $id );
		$writer->add_class( 'H2' === $tag ? 'docspress-section' : 'docspress-subsection' );
		foreach ( (array) $writer->get_attribute_names_with_prefix( 'on' ) as $attribute ) {
			$writer->remove_attribute( $attribute );
		}
		$toc[] = array(
			'level' => 'H2' === $tag ? 2 : 3,
			'id'    => $id,
			'title' => $title,
		);
	}
	return array(
		'html' => $writer->get_updated_html(),
		'toc'  => $toc,
	);
}
