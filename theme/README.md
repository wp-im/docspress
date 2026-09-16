# Docspress Theme 0.2.4

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
- H2 becomes the editorial rail section title and appears in the TOC.
- H3 stays in the prose column and appears as a nested TOC item.
- Existing H2/H3 `id` attributes are preserved; IDs are generated only when absent.
- H4 and below remain normal content headings.

## Navigation

- Header = site navigation (`Primary Menu`).
- Left sidebar = Docs Root Page hierarchy.
- Right sidebar = current Page H2/H3 TOC.

## Theme switch safety

The theme does not register Docs CPTs or own editorial shortcodes. Those belong to the companion plugin.
