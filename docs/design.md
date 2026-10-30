# Docspress design contract

Version: 0.9.1 maturity candidate · 2026-10-24

This is the current implementation contract, not a claim of WordPress.org approval. The immutable source reference remains outside the deployable theme. The previous design document is archived at [design-before-upgrade.md](audits/2026-10-23/design-before-upgrade.md). Where it conflicts with this document, this document and the [approved upgrade task book](../plan/DOCSPRESS-MATURITY-UPGRADE-PLAN-2026-10-23.md) take precedence.

## 1. Product and visual character

Docspress is an editorial reading system for a durable collection, not an application dashboard. Its identity comes from proportion, type, hierarchy and rhythm: white reading paper, a softly tinted library rail, fine boundaries, restrained warm links, a small broken-ring brand mark, and stable textual wayfinding.

The theme must remain a standard Classic Theme. Pages, Post parents, Order, excerpts, menus, categories, tags, media, comments and feeds remain native WordPress data. Do not introduce theme-only records, required companion plugins, custom post types, shortcodes, external fonts, trackers or a second editorial admin system.

Do not add cards, shadows, gradients, badges or animation merely to decorate ordinary content. A shadow communicates an overlapping menu/drawer, not the importance of prose. Orange is a navigation signal; it is not a background for every section.

## 2. Four independent navigation jobs

| Surface | Ownership and source | Compact behavior |
| --- | --- | --- |
| Header | Primary Menu + native search | Its own Menu disclosure, never merged into Library |
| Leftbar | Native Page tree, optional Left Sidebar Menu, left widgets | Library button; modal drawer from inline start |
| Rightbar | Native sidebar-1 widgets or useful fallback | Sidebar button; modal drawer from inline end |
| Article contents | Current filtered article's eligible H2/H3 | In-article details disclosure, never a rightbar widget |

In left-to-right layouts inline start/end are physical left/right. RTL mirrors this relationship. Every template has both sidebars by default, including ordinary Pages, Posts, archives, search and 404. Only an explicit Customizer choice disables one. An empty widget area is not a reason to remove its panel.

Use a single DOM instance of each sidebar and each widget. CSS changes its placement; do not render a desktop copy and a second mobile copy. This avoids duplicate form IDs, widget IDs, contradictory focus targets and double screen-reader content.

## 3. Shared document structure

The structural ownership is:

```text
body
  wp_body_open + skip link
  sticky header (brand, independent primary-menu disclosure)
  sticky compact sidebar controls
  .docspress-shell
    left aside
    main#primary
      breadcrumbs (where relevant)
      article / index / standard query
        title, metadata, article-owned contents, body
      native navigation / comments
    right aside
  backdrop (viewport-level sibling, not inside article)
  footer (optional widgets, copyright, footer menu)
  wp_footer
```

Header opens the shell and the only main landmark; footer closes main, renders rightbar, then closes shell. Content templates must not add another main. Keep wp_head, wp_body_open, wp_footer and normal template hooks intact.

The left tint is a bounded background on the shell, never a fixed pseudo-element extending into the footer. Sidebars must not have an ancestor with transform, filter, isolation or a competing stacking context. Main alone uses container-type. This leaves viewport-fixed drawers outside the article's containment.

## 4. Tokens and geometry

| Token | Standard value | Purpose |
| --- | --- | --- |
| --dp-shell-max | 1500px | Overall composition, not prose measure |
| --dp-pad | 28px wide; 24px tablet; clamp(18px,4.7vw,24px) phone | Outer safety margin |
| --dp-left / --dp-right | 240px / 240px wide | Panel columns |
| --dp-gap | 32px wide, 28px medium | Inter-column separation |
| --dp-reading | 42.5rem (680px at a 16px root) | Normal prose/index measure |
| compact reading | 37.5rem (600px) | Explicit native setting |
| --dp-body-size | 1.0625rem (17px) | Standard prose |
| large body | 1.1875rem (19px) | Explicit native setting |
| --ink | #303037 | Primary text |
| --faint | #626269 | Readable secondary text |
| --accent | #b45309 | Links, active paths and focus |
| --accent-mark | #e87917 | Decorative brand only |
| --sidebar / --soft | #f6f6f7 | Quiet rail/utility fill |
| --line | #dedee2 | Decorative separators, not a sufficient input boundary |

Use logical properties for spacing, borders and direction. Viewport bounds are outer border-box bounds, including padding. Do not use a minimum width that silently defeats a drawer's maximum.

### 4.1 Responsive modes

- At least 1440px: left + main + right columns.
- 1024–1439px: left column approximately 220px, main, right drawer.
- At most 1023px: both sidebar drawers, single reading column, independent header Menu button.
- At most 767px: 56px header minimum, smaller page padding, prose line-height 1.8, linearized floats/pairs/fact groups.
- JavaScript also compacts an overflowing desktop primary menu before it can push content horizontally.

The article never expands to fill all space released by a hidden panel. It keeps its reading measure. A narrow main column does not reserve an empty heading rail.

### 4.2 Optional document rail and the reading axis

Only documentation Pages may gain an inner rail, and only when their actual main container is at least 52rem wide. The rail is 8.5rem plus a 1.5rem gap; the whole article/index maximum is reading measure +10rem. Ordinary Posts and ordinary Pages remain a plain centered reading measure.

In rail mode, H1, deck, metadata, TOC, body paragraphs, breadcrumbs, comments and previous/next controls align to the prose axis. Top-level H2 may occupy the inner rail; H3 remains in prose. A nested block group's headings remain in its natural content flow. At smaller widths everything returns to one column.

Never fix alignment with page-specific negative margins. Compare Home, Section Index and a Content Page in the same layout mode; H1/prose drift target is at most 2px. Place authored margin material before its related passage so normal grid order preserves the relationship. On mobile, notes and captions return to the reading sequence.

## 5. Type and rhythm

Use local system fonts with CJK fallbacks. No font download is needed to read the site. Body prose is 17px /1.75 wide and /1.8 on phones; interface text is separate from prose. Sidebar widget headings are about 15px, not default 24px H2. Footer widgets use the same quiet utility scale.

H1 is fluid, about 1.85–2.65rem, weight 650, line-height 1.2. Phone H1 tops out around 2.1rem. H2 is about 1.45rem and H3 1.2rem. Use semantic heading levels, never a different level solely for size. Explicit CJK headings cancel Latin tracking and use strict line breaking.

Paragraph/block spacing is about 1.35rem; section boundaries about 2.25rem. Header-to-body, TOC-to-body and previous/next spacing must be deliberate, not the accidental sum of browser margins. Long titles and URLs wrap; breadcrumbs must have min-inline-size:0 on flex children. Untitled native content receives a display fallback without rewriting its stored title.

Secondary text and links must meet ordinary-text contrast against their actual surface. The bright decorative ring is not a text color. Focus uses a visible 2px accent outline with offset. Do not remove outline globally or rely only on an orange hue to communicate active navigation.

## 6. Toolbar geometry is part of the layout

Never treat a logged-in toolbar as an occasional patch. Define:

- B = actual visible toolbar height in the viewport.
- H = rendered theme header height.
- L = rendered compact-controls height, or zero.
- T = B + H + L.

Header top is B; compact controls top is B+H; sticky sidebars start at T+16px. Drawers start at B and use 100dvh-B. Anchor scroll padding is T, with a small heading scroll margin. TOC observation uses the same measured stack.

The normal core toolbar is 32px, or 46px at widths up to 782px. At widths up to 600px core uses a scrolling absolute bar: B must reduce continuously as it leaves the viewport, then reach zero. Do not add core's html top margin a second time. Do not keep a fake 46px strip after scrolling.

JavaScript measures the rendered toolbar/header/controls, updates CSS custom properties, and uses rAF with passive scroll only for a scrolling toolbar. ResizeObserver handles header and toolbar size changes. No per-template offset constants.

## 7. Drawer and menu state rules

### 7.1 Sidebars

Left outer width: min(70vw,20rem), never more than 70vw. Right outer width: min(88vw,24rem), never more than 90vw. Use dynamic viewport height and safe-area bottom padding. The close/header row is sticky inside the scrolling panel.

Primary touch controls and compact tree/menu links have a 44px target. Confirm the close control is visible AND is the actual pointer hit target; DOM presence/focus alone is insufficient.

Opening a sidebar closes the independent menu and any other sidebar; exposes role=dialog, aria-modal and a named heading; makes background siblings inert, including the toolbar; locks background scrolling; and focuses the close control. Tab and Shift+Tab wrap within the active drawer. Escape, close and backdrop release every saved inert state and return focus to the visible opener, or main if the opener no longer exists visibly.

A width change closes modal state before the layout switches. A height-only change must not close a search drawer when a virtual keyboard appears. Restoring a page via pageshow removes stale open states and scroll locks. Do not introduce fixed-body iOS workarounds without a reproduced physical-device need.

### 7.2 Header menu

The header disclosure is separate and nonmodal. It has aria-expanded/controls, Escape, outside-pointer dismissal and normal Tab order. Do not give it the sidebar's focus trap or backdrop. Nested navigation is available by hover/focus on desktop and in normal indented flow when compact.

### 7.3 Progressive enhancement

The root receives docspress-js only when navigation code runs. Without it, menus and sidebars remain readable in normal document flow, tree children remain expanded and hidden drawer buttons do not become dead controls. Native article details and ordinary links work independently.

## 8. Article contents and durable anchors

Run normal the_content filters exactly once. WordPress's password form and plugin filters remain authoritative. Use the WordPress HTML API to decorate headings rather than rebuilding filtered HTML with a regular expression.

Preserve inline emphasis, links, language/data attributes and explicit unique IDs. Generated anchors support Unicode; reserve IDs on non-heading elements too. Duplicate IDs receive deterministic suffixes. Empty/directly hidden headings, script text and template contents do not form TOC entries. Never cache filtered or permission-dependent HTML across requests.

Display the article contents disclosure only when at least two eligible headings remain after the chosen depth filter. Settings are Off, H2, H2+H3. It is below title/metadata inside the article. Fragment links encode the ID and focus the real heading; toolbar/header offsets must leave the target visible.

A generated anchor follows the heading title. It cannot promise durability after a title rewrite. Frequently cited sections need explicit native HTML anchors, retained across editorial revisions. Do not claim that last-modified means reviewed.

## 9. Native settings and widget contracts

Keep sidebar-1 unchanged. Add docspress-left-sidebar and docspress-footer; do not silently migrate widget instances. Classic and block widgets must render once and share the design tokens. Empty rightbar supplies site information, feed and recent articles; empty left widgets supply search.

Native menu locations are primary, docspress-primary-sidebar and footer. Do not reuse the Primary Menu as an implicit sidebar menu. The Page tree remains independent of an optional curated left menu.

Customizer stores theme mods for Docs Root, footer text, explicit left/right visibility (default true), modified label, TOC depth, reading measure and body size. Sanitize according to each control's registered choices. There is no separate settings framework or content database.

## 10. Content edge cases

- Responsive media keeps its natural ratio; featured images do not reveal password-protected bodies.
- Figures/captions stay together; phone floats become block figures.
- Tables and pre/code scroll locally, never widen the page. Overflow regions receive a keyboard tab stop and localized scroll instruction when the script is available; remove only attributes the theme added.
- Core wide/full blocks stay within the reading composition; do not break through the external sidebars.
- Lists, nested comments, quotes, galleries, forms, search and pagination use native markup. Page-number controls are not tiny text-only targets.
- Search widgets and theme search forms have visible/accessible labels, 44px submit controls and no duplicate IDs.
- Print hides navigation chrome, toolbar, sidebars and comments; returns to a plain printable document; wraps code and avoids breaking headings/figures unnecessarily.
- Reduced motion disables drawer/tree transitions. Forced-colors uses system-appropriate focus and boundaries.
- RTL mirrors layout and directional CSS without changing the stored content or requiring a second template.

## 11. Reference-library behavior

Only a published configured root produces a documentation tree. Published descendants are sorted by menu_order, title, then ID. Group Pages once, traverse with cycle/depth guards, and cache source/tree/flat reading order for the request, keyed by site/root/locale as appropriate.

For more than 200 Pages, unopened deep branches can use an Open this section destination instead of outputting the entire collection. The current path must still be present. A collapsed toggle must not imply its children are loaded if only a section link is available.

Breadcrumbs expose public ancestors. Directory summaries refuse password-protected/private bodies. Search context uses native content type and public parent; previous/next follows the native tree. No private-post title/body is allowed into public tree output.

Test real native Page data, not only synthetic objects. Record query counts, rendered-node counts, HTML size and current-path retention separately from total server TTFB. A sub-millisecond helper benchmark is not a production performance claim.

## 12. Change and acceptance method

Before modifying a visual detail, locate its existing token, template owner and state transition. Prefer changing that source rule over appending an override. For a defect record URL, identity, viewport, scroll position, visible symptom, computed geometry, cause, smallest fix and after evidence.

Required test families: all standard templates; mobile/tablet/wide modes; both sides of 600/782/1024/1440 boundaries; real logged-in toolbar on/off; ordinary logged-in reader; anonymous access; three-language long text; password/private content; widgets; native editors; minimum PHP/WordPress; RTL; no-navigation-script fallback; page/keyboard/browser return; package and rollback.

The current [acceptance and deployment record](audits/2026-10-23-upgrade/ACCEPTANCE.md) distinguishes technical checks, actual desktop cross-engine testing, and unperformed human/device reviews. The owner explicitly replaced physical iOS/Android acceptance with technical/browser simulation for this delivery. This does not turn simulation into a physical-device result or establish screen-reader certification/WordPress.org approval. Do not promote a candidate to 1.0 simply because a ZIP builds.

## 13. File ownership

- header.php / footer.php: shared shell, global menu, root backdrop and footer boundary.
- sidebar-left.php / sidebar.php: single native panels and fallbacks.
- assets/css/main.css: tokens, shell, components, responsive and user-preference states.
- assets/js/navigation.js: disclosure/modal state, focus, geometry and local overflow enhancement.
- inc/docs.php: native Page hierarchy, ordering, bounded tree and reading neighbors.
- inc/toc.php: non-destructive current-article parsing.
- inc/widgets.php / inc/customizer.php: native configuration contracts.
- assets/css/editor.css / rtl.css: editor reading surface and logical-direction adjustments.
- site-content/: reviewed demonstration copy, never packaged in the theme.
- tests/ / scripts/: reproducible checks and disposable fixtures, never shipped as theme code.

A release contains only the docspress/ theme folder with license, readme, POT and an authentic 1200×900 screenshot. No reference archive, credentials, QA fixtures, demo database, node_modules or administration scripts.
