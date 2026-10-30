# Docspress Theme 0.9.1

A WordPress Classic Theme for document-led sites. It renders WordPress-managed content and does not store site documentation content in theme files.

## Docs Root

Go to **Appearance → Customize → Docspress → Docs Root Page** and choose the Page whose descendants form the Docs Tree.

## Native hierarchy

- **Parent** controls hierarchy.
- **Order** controls ordering.
- **Excerpt** becomes the article Deck and directory description.
- All descendant depths remain navigable; visual indentation stops increasing after the third nested level.

## Page types

- **Front Page**: when WordPress is configured to use a static front page, the page becomes the Docs site index; when `show_on_front` is set to posts, the template falls back to the posts index surface.
- **Docs Section Index**: reusable Page Template with direct child groups and one nested directory level.
- **Docs article**: any Page in the Docs Root subtree using the default Page template.
- **Ordinary Page**: Pages outside the Docs Root use the standard layout.

## Heading rules

- H1 comes from the WordPress Page title.
- H2 is a section heading and appears in article contents. A genuinely wide Docs container may place top-level H2 in an editorial rail; smaller containers use plain prose.
- H3 stays in the prose column and appears as a nested TOC item.
- Existing unique H2/H3 IDs are preserved. Missing IDs are generated from text; collisions receive deterministic suffixes. Keep explicit anchors for frequently cited sections.
- H4 and below remain normal content headings.

## Navigation

- Header = site navigation (`Primary Menu`).
- Left sidebar = Docs Root Page hierarchy, optional independent Left Sidebar Menu, and Left Sidebar widgets.
- Right sidebar = native sidebar-1 widgets, not article contents.
- Article contents = the current article's expandable H2/H3 index, inside that article.
- Every template has both sidebars by default; the Customizer can explicitly disable either.
- Below 1440px the right panel becomes a drawer; below 1024px both do. Left/right outer widths are bounded at 70%/88% of the viewport. The header Menu is separate.
- Sticky controls and drawers use the actual visible WordPress toolbar height, including the small-screen toolbar that scrolls away.

## Native configuration

Appearance > Widgets offers Left Sidebar, Right Sidebar and optional Footer Widgets. The original sidebar-1 widget ID is retained. Classic and block widgets are supported. Appearance > Customize > Docspress also controls reading measure, body text size, article contents depth, modified-date display and footer text. Updated means modified, not editorially reviewed.

## Theme switch safety

The theme does not register Docs CPTs, custom editorial blocks or shortcodes. Native content remains readable when themes change. Any future editorial extension is a separate optional plugin, not a requirement.

0.9.1 is a maturity candidate, not a WordPress.org approval or a completed physical-device accessibility review. Physical-phone acceptance was waived by the project owner in favor of technical/browser simulation. See readme.txt for installation, scope and licensing.
