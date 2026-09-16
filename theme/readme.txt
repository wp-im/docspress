=== Docspress ===
Contributors: wp-im
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A quiet WordPress Classic Theme for document-led sites, long-form reading, and structured documentation.

== Description ==

Docspress uses native WordPress Pages as the documentation hierarchy. It provides a collapsible Docs Tree, H2/H3 page TOC, editorial long-form layout, section indexes, responsive navigation, and standard classic-theme fallbacks.

The theme does not register a documentation custom post type, shortcode, custom block, analytics, tracking, or remote runtime dependency. Any future editorial extension must be a separately installed plugin and is not required by the theme.

== Installation ==

1. Upload and activate the Docspress theme.
2. Create a Page to act as the documentation root and add child Pages below it.
3. Open Appearance > Customize > Docspress and select the Docs Root Page.
4. Assign a menu to the Primary Menu location.
5. Use the Classic Editor or Block Editor with ordinary WordPress Pages; no companion plugin is required for the core Docs Tree or reading surface.

== Frequently Asked Questions ==

= How is the Docs Tree built? =
Choose one Docs Root Page in Appearance > Customize > Docspress. Published child Pages use Parent and Order to form the navigation tree.

= Is a custom post type required? =
No. Docspress uses native WordPress Pages so content remains portable if the theme is changed.

= Can the theme run without an editor extension? =
Yes. The core theme renders ordinary Pages, Posts, media, headings, tables, comments, search, archives, feeds, and 404 pages without an extension.

== Upgrade Notice ==

= 0.2.4 =
Corrects the document-shell background boundary, restores article-wrapper rhythm, and aligns the index width with the documented reading geometry.

= 0.2.3 =
Preserves imported documentation blocks through a scoped compatibility layer while content is migrated to namespaced classes.

= 0.2.2 =
Replaces the last decorative Unicode arrow with a CSS-drawn control-independent detail.

= 0.2.1 =
Namespaced document classes and adds a structured image-placement fallback for browsers without `:has()` support.

= 0.2.0 =
* Adds the standard searchform.php contract and unifies all theme search surfaces.
* Improves keyboard focus boundaries, visible focus, reduced-motion handling, and mobile navigation.
* Refines the responsive reading shell and removes decorative Unicode controls from the interface.

== Changelog ==

= 0.2.4 =
* Keeps the Docs Tree background inside the documentation main frame so it cannot continue through the footer.
* Restores the intended 28px article wrapper offset on real WordPress page markup, including the compact 22px offset below 980px.
* Aligns Home and Section Index content to the 924px article-width contract.

= 0.2.3 =
* Adds scoped compatibility for demo content imported before the `docspress-*` class migration.
* Keeps the compatibility selectors limited to Docspress documentation pages.

= 0.2.2 =
* Replaces directory-row arrow glyphs with a CSS-drawn line arrow.
* Adds the corresponding RTL direction rule.

= 0.2.1 =
* Namespaces document-only CSS and heading classes with the `docspress-` prefix.
* Keeps explicit figure wrapper classes as the baseline and uses `:has()` only as progressive enhancement.
* Aligns the editor stylesheet and RTL rules with the document design contract.

= 0.1.0 =
* Initial Docspress-owned Classic Theme preview.
* Establishes the native Page hierarchy, Docs Tree, page TOC, responsive navigation, and standard WordPress fallbacks.

== Resources ==

* The theme includes no remote fonts, CDN assets, tracking code, or third-party runtime libraries.
* The bundled `LICENSE` file contains the GNU General Public License, version 2 or later.
