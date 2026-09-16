# Docspress reference design analysis

## One-sentence core

The reference treats WordPress as a structured document database and the front end as a reading system: the editor maintains a Page hierarchy, while the theme derives the Docs Tree, section directory, article TOC, and Previous / Next flow from that structure.

## Design characteristics

### 1. The information architecture is the product

- A selected **Docs Root Page** defines the documentation site.
- Native Page `Parent` defines hierarchy; `Order` defines editorial sequence.
- Descendant Pages become the left navigation tree without a manually maintained sidebar configuration.
- The front page lists top-level sections; a reusable `Docs Section Index` lists a section's children.
- Article navigation is computed in depth-first order, so the navigation follows the same sequence as the content tree.

This is the strongest idea to retain in Docspress: the backend content relationship is the navigation source of truth.

### 2. The page is a reading surface, not a card dashboard

The visual language is restrained and editorial:

- white reading surface, soft gray navigation surfaces, thin rules, dark gray text;
- one muted orange accent for links, active states, and the brand mark;
- low visual noise: no gradients, heavy shadows, or card-grid decoration;
- hierarchy comes from alignment, type scale, spacing, and rules;
- article metadata is quiet: eyebrow, deck/excerpt, and modified date support the title without competing with it.

The layout creates density through relationships rather than decoration.

### 3. Desktop uses three navigation layers

At the wide breakpoint the shell is:

`site header → left Docs Tree → article → right page TOC`

The article itself is a second grid: an editorial rail for H2/notes alongside the prose column. The reference uses a sticky 64px header, a 248px left rail, a 208px right TOC, 28px page padding, and a centered article capped around 924px. The right TOC disappears before the prose becomes cramped.

### 4. Responsive behavior changes the navigation model

The reference does not simply shrink the desktop geometry:

- below roughly 1280px, the right TOC is removed;
- below roughly 980px, the persistent left rail becomes a local navigation bar while the article's internal rail remains;
- below roughly 640px, the article linearizes, local navigation and TOC move into drawers, and the global menu becomes a phone menu;
- images and editorial blocks change weight at mobile widths instead of preserving awkward desktop placement.

This is a structural responsive strategy, not only a collection of smaller font sizes.

### 5. H2/H3 are semantic layout inputs

The theme parses rendered content headings only where it needs presentation behavior:

- H1 comes from the Page title;
- H2 becomes an editorial section heading and a TOC item;
- H3 remains in the prose flow and becomes an indented TOC item;
- H4 and below remain ordinary content headings;
- existing safe IDs are preserved, missing IDs are generated, duplicate IDs receive suffixes;
- heading attributes are allow-listed and event-handler attributes are discarded;
- H2 boundaries can receive section rules.

This keeps ordinary Classic Editor content usable while adding a document-specific reading layer.

### 6. Theme/plugin boundary is intentionally narrow

The reference theme owns presentation, templates, navigation, TOC parsing, responsive behavior, Customizer settings, and editor styles. It does not register a Docs custom post type or own editorial shortcodes. Special editing semantics are intended for a companion plugin, while ordinary headings, paragraphs, images, tables, lists, blockquotes, and captions remain native WordPress content.

That boundary is valuable for switch safety: if an optional plugin is disabled, the theme still renders ordinary WordPress content instead of failing because the theme owns a custom data model.

## What Docspress should inherit

1. Native Page hierarchy as the Docs Tree source of truth.
2. A configurable root Page instead of a hard-coded site structure.
3. Depth-first Previous / Next navigation.
4. H2/H3 TOC generation with safe IDs and safe attribute handling.
5. The quiet rail–prose reading composition.
6. Responsive drawers and local navigation as alternate information architecture.
7. Classic Theme fallbacks outside the Docs subtree.
8. Editor styles that use the same reading measure as the front end.

## What must become Docspress-owned

- theme name, slug, PHP namespace/function prefix, text domain, and translation catalog;
- screenshot, brand mark, copy, metadata, and README/readme.txt identity;
- CSS variables and any visual decisions that should express Docspress rather than Humanities Docs;
- tests and documentation that prove the Docspress behavior rather than merely the imported package behavior;
- a fresh review of the reference license and any companion-plugin dependency before redistribution.

## Implementation boundary for the next pass

The imported archive is a design and implementation reference, not yet the production Docspress source. The minimum next pass should first establish a Docspress-owned working tree and rename the public identity deliberately. It should not add a custom post type, page builder, remote dependency, or large editor framework unless a later requirement makes one necessary.
