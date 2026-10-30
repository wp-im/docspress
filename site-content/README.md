# Docspress demo-site content

This directory is the reviewed content source for the `wordpress.im/docspress/` demo site. It is intentionally outside the theme ZIP.

## Entry rules

- Create the page titled `Docspress` first and use it as the static front page and Docs Root.
- Create section pages with the `Docs Section Index` template.
- Create child pages with the default page template. Set the listed parent and menu order exactly.
- Set each page excerpt from its front matter. The theme uses excerpts as decks and directory descriptions.
- Keep the HTML body between the front-matter markers when entering content in the WordPress editor.
- Create the Posts separately. They demonstrate ordinary WordPress publishing outside the Docs Tree.

## Page tree

The authoritative order is in `manifest.tsv`. Parent values are slugs so the content can be entered reproducibly without depending on database IDs.

```text
Docspress
├── Start here
│   ├── What Docspress is
│   ├── Install and activate
│   └── First Docs Tree
├── Content model
│   ├── Docs Root and Page hierarchy
│   ├── Parent, order, and excerpts
│   └── Headings and page TOC
├── Reading system
│   ├── Reading surface
│   ├── Responsive navigation
│   ├── Images, tables, and quotes
│   └── Widgets and menus
├── Classic Theme architecture
│   ├── Template hierarchy
│   ├── Theme and plugin boundary
│   └── Fallback behavior
├── Accessibility and maintenance
│   ├── Keyboard and screen readers
│   ├── Safe content output
│   ├── Updates, backups, and rollback
│   └── Maintaining a reference library
└── Release notes
    ├── 0.1.0 working preview
    └── 0.9.0 maturity upgrade (includes 0.9.1 corrections)
```

## Acceptance intent

The first live pass should verify that the root page shows all six sections, section pages show their direct children, article pages produce an H2/H3 page TOC, and the ordinary Posts appear on the Posts page without entering the Docs Tree.

The standalone `Notes` Page is the Posts Page. It stays outside the Docs Root and gives the ordinary Posts a visible home without mixing them into the documentation tree.

For a **new, empty site**, regenerate `docspress-demo.xml` with `node scripts/build-wxr.js`, then use the WordPress Importer. Map the `docspress` author to an existing site administrator; do not create a user. Then configure the static front page, Docs Root and Notes Posts Page. The local acceptance pass also supports `scripts/seed-site-content.php`, whose hierarchy lookup is idempotent.

The WXR has 31 entries (27 Pages, 4 Posts), deterministic fixture IDs and the original demonstration date. It is not a production database snapshot or an incremental updater. Never re-import it wholesale into an established site: preserve actual production IDs, dates, URLs and revisions. The 2026-10-23/24 upgrade updated the two existing navigation/TOC Pages and one maintenance Post through the native editor, and created only three new documentation Pages. Details are in the final acceptance report.
