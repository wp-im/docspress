# Docspress Design System and Front-end Rules

Status: `DESIGN CONTRACT / 0.1.0 WORKING PREVIEW`

Updated: 2026-09-16

2026-10-23 product decision: the [maturity upgrade plan](../plan/DOCSPRESS-MATURITY-UPGRADE-PLAN-2026-10-23.md) supersedes the historical rules below where they conflict. Every page has a leftbar and rightbar by default; compact sidebars slide from their respective sides (mobile maximum outer widths: 70% and 90%). The header menu remains separate with its own compact button. A long article's TOC belongs inside that article, never in the rightbar. WordPress toolbar geometry is a required part of the new layout contract. These are design decisions for the next implementation; source 0.2.4 has not yet been changed.

This document is the implementation contract for the Docspress Classic Theme. It turns the three static reference pages into repeatable rules for visual design, CSS, templates, Classic Editor content, responsive behavior, and QA.

The visual source is archived in `reference/static-html/`. Those files retain the historical `humanities-docs-*` names and must remain reference-only. They are not the deployable theme and their brand strings are not part of Docspress.

## 1. Product definition

Docspress is a document-led WordPress Classic Theme. Its product is not a dashboard, a page builder, or a custom publishing application. Its product is a calm reading surface that makes ordinary WordPress editorial decisions visible:

- Page parent relationships become the Docs Tree.
- Page order becomes reading order.
- Page excerpts become decks and directory descriptions.
- H2/H3 headings become the page's internal navigation.
- Media, tables, lists, quotations, and ordinary HTML remain portable WordPress content.

The theme should make the hierarchy easier to read without making editors maintain a second hierarchy.

### 1.1 Non-negotiable invariants

These are `MUST` rules. A visual redesign is not complete if it breaks one of them without an explicit design decision record.

1. The information path remains `Home → Section Index → Content Page`.
2. The document remains the dominant visual object. Navigation supports it and does not become a card dashboard.
3. The Docs Root is configured, not hard-coded.
4. Native Pages remain the source of truth for the documentation tree.
5. H1, H2, and H3 remain semantic headings in the rendered document.
6. Desktop, tablet, and phone are different navigation modes, not merely the same grid at smaller widths.
7. The visual system uses a quiet white surface, a soft gray navigation surface, fine rules, and one restrained accent.
8. The theme remains useful when the optional editor plugin is absent.
9. Ordinary Pages, Posts, media, comments, search, archives, feeds, and 404 pages continue to use standard WordPress behavior.
10. Every interactive state is usable with a keyboard and has visible focus.

### 1.2 Prohibited direction

Do not introduce the following without a new product decision:

- glossy gradients, hero marketing panels, large decorative illustrations, or heavy card shadows;
- a second custom hierarchy maintained in theme settings or JavaScript;
- a Docs custom post type owned by the theme;
- a client-side SPA/router for ordinary document navigation;
- remote fonts, analytics, trackers, or runtime external dependencies;
- theme-owned editorial data that disappears when the theme is changed;
- styling that depends on one particular sample page or one particular language;
- an icon font or platform-dependent Unicode symbol as the only meaning-bearing control icon.

## 2. Design vocabulary

Use these names consistently in design reviews and code reviews.

| Term | Meaning |
| --- | --- |
| Site shell | Header, global navigation, search, footer, and responsive global menu. |
| Docs Root | The configured Page at the top of the documentation subtree. |
| Docs Tree | The persistent or drawer-based navigation generated from descendant Pages. |
| Section Index | A directory page for a Docs Root or one of its major child sections. |
| Content Page | A long-form documentation Page rendered with the article reading system. |
| Reading surface | The central prose column, its heading rail, media, notes, and article navigation. |
| Article rail | The left internal column beside prose. H2 labels, margin notes, and margin figures can occupy it. |
| Page TOC | The H2/H3 navigation for the current Content Page. |
| Local navigation | The tablet/phone bar that opens the Docs Tree and, where available, the Page TOC. |
| Editorial block | A semantic content unit such as a definition, context panel, quote, note, figure, table, or carousel. |
| Visual grammar | The rules of alignment, spacing, type, color, and state that make the three page types feel like one system. |

## 3. Reference composition

The static reference has three related but distinct page compositions.

### 3.1 Home

The Home page answers “what is this document space?” and “where can I begin?”. It contains:

1. the global site shell;
2. the Docs Tree, when a Docs Root exists;
3. a short eyebrow or site context;
4. one H1 and a restrained deck;
5. optional introductory content;
6. a quiet search entry point;
7. numbered section rows with descriptions and directional affordances;
8. optional lower notes, not a promotional feature grid.

The section list is a reading index. Each row should feel like a line in a catalogue: number, title, short description, and a small arrow. It must not become a set of equal-height cards with independent backgrounds.

### 3.2 Section Index

The Section Index answers “what is inside this branch?”. It reuses the Home composition but narrows the scope:

- eyebrow identifies the parent context;
- H1 names the section;
- deck explains its editorial scope;
- the directory lists direct children and, when appropriate, one visible nested level;
- group boundaries are expressed by rules and indentation, not cards;
- the selected section is identifiable in the Docs Tree without making all descendants look selected.

The section template should not promise that every child is an article. A child may itself be another Section Index or a normal Content Page. The directory is therefore a navigational summary, not a hard-coded content type.

### 3.3 Content Page

The Content Page is the primary reading surface:

```text
site header
  Docs Tree       article reading surface          Page TOC
                  article rail | prose
```

The H1, deck, and metadata align to prose. H2 labels sit in the article rail on wide layouts. H3 remains inside prose but appears indented in the Page TOC. Notes and margin figures can share the article rail with H2 labels, but they must not make the prose column jump unpredictably.

The article ends with source/context material when present and a depth-first Previous/Next navigation pair. The navigation is quiet and structural; it is not a large call-to-action.

## 4. Visual tokens

All production CSS should consume semantic custom properties. A component may use a local derived token, but repeated raw values should not be scattered through the stylesheet.

### 4.1 Color tokens

The values below preserve the static reference. The names are the contract; the hexadecimal values may be adjusted only through a visual review.

| Token | Reference value | Use |
| --- | --- | --- |
| `--dp-color-surface` | `#ffffff` | Reading surface, header, clean content backgrounds. |
| `--dp-color-soft` | `#f6f6f7` | Search fields, sidebars, context panels, definitions, quiet callouts. |
| `--dp-color-sidebar` | `#f6f6f7` | Docs Tree background and chapter drawer. |
| `--dp-color-ink` | `#3c3c43` | Main text and brand. |
| `--dp-color-muted` | `#7a7a80` | Metadata, captions, secondary descriptions. |
| `--dp-color-faint` | `#929299` | Placeholder text, numbers, low-priority labels. |
| `--dp-color-line` | `#e2e2e3` | Borders, separators, directory rules. |
| `--dp-color-accent` | `#e87917` | Links, active states, brand mark, section rules, semantic emphasis. |
| `--dp-color-accent-strong` | project-derived | Keyboard focus and high-contrast accent state. |
| `--dp-color-backdrop` | `rgba(20,20,18,.20)` | Drawer backdrop only. |

Rules:

- Accent is a signal, not a fill color for large surfaces.
- Do not use gray text below a readable contrast threshold merely to make the page quieter.
- A hover state must not be the only way to discover that something is interactive.
- `--dp-color-soft` is not a card system. It should appear in short contextual blocks and utility controls, not around every directory row.
- If a client site needs a different accent, change the token in one place and check links, focus, rules, buttons, and active tree states together.

### 4.2 Geometry tokens

The wide reference uses the following baseline:

| Token | Value | Meaning |
| --- | ---: | --- |
| `--dp-header-height` | `64px` | Sticky site header. |
| `--dp-local-height` | `44px` | Tablet/phone local navigation bar. |
| `--dp-shell-max` | `1500px` | Header and documentation shell maximum width. |
| `--dp-left-width` | `248px` | Wide Docs Tree column. |
| `--dp-right-width` | `208px` | Wide Page TOC column. |
| `--dp-shell-gap` | `40px` | Wide shell column gap. |
| `--dp-page-padding` | `28px` | Wide outer padding. |
| `--dp-article-max` | `924px` | Article rail plus prose maximum width. |
| `--dp-prose-target` | about `620px` | Comfortable prose measure inside the article. |
| `--dp-rail-target` | `170–210px` | Article rail width on wide layouts. |
| `--dp-rail-gap` | `28–34px` | Gap between rail and prose. |
| `--dp-sidebar-top` | `24px` | Sticky navigation offset below the header. |

Use `clamp()` only when the interpolation preserves the relationship. Do not use fluid sizing to make a title or reading measure fill every available pixel.

### 4.3 Shell geometry invariant

The documentation shell has two different responsibilities that must not be confused:

- `main.doc-frame` owns the complete navigation-and-reading frame, including the Docs Tree column, article column, optional Page TOC, and bottom breathing room.
- `.article-wrap` or `.content-wrap` owns the central page composition inside that frame. A Content Page uses the `924px` article contract; Home and Section Index use the same maximum so their title and directory rhythm do not drift into a separate width system.

The Docs Tree background is a decoration of `main.doc-frame`, not a viewport background. Its painted layer must be bounded by the main frame. It must not be `position: fixed`, because a fixed layer can remain visible behind a later footer and make the footer appear to cut through the left rail. When the shell collapses below `981px`, the left background layer disappears with the persistent tree.

The real WordPress wrapper is `.article-wrap`. Any legacy selector such as `.docspress-article-wrap` may remain as a compatibility alias, but it must not be the only selector carrying article spacing. The desktop article offset is `28px`; the compact shell offset is `22px`.

### 4.4 Spacing scale

Use a small rhythm rather than arbitrary margins. The practical scale is:

```text
4  6  8  10  12  14  16  18  22  24  28  32  40  44  48  60  80
```

Interpretation:

- `4–8px`: icon/text separation, compact control padding, TOC indentation;
- `10–18px`: directory row padding, captions, notes, callouts;
- `22–32px`: heading relationships, sidebar padding, section rules;
- `40–60px`: block separation and index page rhythm;
- `80px`: bottom breathing room for long pages.

Do not apply the same vertical gap to every element. The reading rhythm should distinguish title/deck, deck/meta, heading/paragraph, paragraph/paragraph, and major section transitions.

### 4.5 Borders, radius, and shadow

- Default border: `1px solid var(--dp-color-line)`.
- Accent rule: a thin accent border with reduced opacity, never a thick decoration.
- Control radius: approximately `6–8px`.
- Directory rows: no enclosing card radius or shadow.
- Drawers: a restrained directional shadow is permitted because it establishes elevation; the reading surface itself should remain flat.
- Do not combine a border, large radius, gradient, and shadow on the same ordinary content block.

## 5. Typography rules

### 5.1 Font stack

The default stack remains local and system-based:

```css
-apple-system, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Arial,
"Noto Sans CJK SC", "PingFang SC", "Hiragino Sans GB", sans-serif
```

No remote font is required. If a site supplies a custom local brand font, it must not reduce the readability of Chinese body copy or become a hard dependency of the theme.

### 5.2 Type scale

| Role | Reference size | Weight | Line height | Notes |
| --- | ---: | ---: | ---: | --- |
| Body | `16px` | regular | `1.68` | Main reading text. |
| H1 wide | `2.05–2.75rem` | `600` | `1.06` | Fluid within the article measure. |
| H1 phone | about `1.8–1.9rem` | `600` | `1.08–1.10` | Never let the title touch the viewport edge. |
| Deck | `1.0–1.08rem` | regular | `1.62–1.66` | Secondary explanation, not another headline. |
| H2 rail | about `1.19rem` | `600` | `1.18` | Right-aligned on wide layouts, left-aligned on phone. |
| H3 | about `1.06rem` | `600` | `1.28` | Remains in the prose column. |
| Meta/caption | `.70–.78rem` | regular | `1.42–1.50` | Muted but still readable. |
| Tree/TOC | `.75–.90rem` | regular/`600` active | `1.42–1.48` | Navigation density. |

Rules:

- H1 is the page title. Do not add a second visible H1 from the excerpt or content body.
- The deck should be short enough to orient the reader; long editorial copy belongs below the metadata.
- Use `font-weight` to express hierarchy before increasing size dramatically.
- Use letter-spacing sparingly. Negative tracking belongs to large headings, not body text or navigation.
- Uppercase and letter spacing are for short English labels such as `PREVIOUS`, `NEXT`, and `SECTIONS`; do not force uppercase on Chinese content.
- Preserve user-authored heading text. The parser may add classes and IDs, but must not rewrite the visible wording.
- Test long Chinese titles, mixed CJK/Latin titles, numbers, punctuation, and right-to-left text before approving a type change.

### 5.3 Reading measure

On wide screens, the article should keep a visible rail and a prose column around 620px. A useful QA target is roughly 30–42 full-width CJK characters or 60–78 Latin characters per line, depending on the selected system font. This is a target, not a forced line break.

The prose must not become a full-width column merely because a right TOC disappeared. If the viewport narrows, reduce outer structure before making the actual reading line uncomfortably long.

## 6. Shell and navigation contracts

### 6.1 Header

The header is sticky, 64px high, white, and separated from the page by a single line. It contains:

1. site link and brand mark/logo;
2. primary navigation;
3. search field on wide/tablet layouts;
4. global menu button on phone.

The brand is quiet and left-aligned. The accent mark may be distinctive, but it must not become a large logo lockup that competes with the page title.

The search field is a real WordPress search form with a visible input. Placeholder text is not a substitute for a label; keep a screen-reader label and preserve the form action.

On phone, the global menu panel should contain the primary navigation and a search entry or clearly reachable search control. Hiding the desktop search without providing a mobile route creates a functional regression.

### 6.2 Docs Tree

The tree is generated from the Docs Root descendants. It must follow these behavior rules:

- title links navigate;
- arrow buttons only expand/collapse;
- every arrow has an accessible label and `aria-controls`;
- the current page has `aria-current="page"`;
- an ancestor section may have a quieter `section-current` state;
- the current path is open initially;
- unrelated branches may remain collapsed;
- the tree may render at arbitrary depth, but the visual indentation must stop at a comfortable level;
- desktop and mobile copies must remain behaviorally synchronized when both are in the DOM;
- a missing root or an empty tree gets a useful, non-breaking fallback for site administrators.

Never make the whole row a toggle if its title is also a link. That destroys the distinction between navigation and disclosure.

### 6.3 Page TOC

The Page TOC lists H2 and H3 only:

- H2 is the primary item;
- H3 is indented and visually quieter;
- IDs are stable within the rendered page;
- existing safe IDs are preserved;
- duplicates receive deterministic suffixes;
- the active item follows scroll position but does not flash between adjacent headings;
- an empty TOC is omitted rather than shown as an empty panel.

The TOC is a secondary navigation aid. It must never reduce the prose column below a readable width. It disappears before the left tree and article grammar collapse.

### 6.4 Local navigation and drawers

When the persistent left rail disappears, the local bar exposes:

- `Contents` for the Docs Tree;
- `On this page` for the Page TOC when one exists;
- current context when there is no Page TOC.

Drawers must:

- set `aria-expanded` on their opener;
- set `aria-hidden` on the panel;
- prevent interaction with hidden panels using `inert` or a robust equivalent;
- lock page scrolling without causing a visible layout jump;
- close on Escape and backdrop click;
- return focus to the opener;
- expose a clear close button;
- respect `prefers-reduced-motion`;
- avoid a z-index collision with the sticky header.

The current reference layer order is intentional: global header around `1000`, backdrop around `1090`, drawers around `1110`. Keep this as a documented token relationship rather than scattering unexplained z-index values.

## 7. Content surface contracts

### 7.1 Article order

The rendered Content Page order is:

```text
eyebrow (optional)
H1 page title
deck/excerpt (optional)
modified metadata
filtered WordPress content
page links (if content is paginated)
Previous / Next
comments (when enabled)
```

Do not insert a theme-owned summary panel between every paragraph. The editorial content should remain the author’s content.

### 7.2 Heading grammar

- H1: document/page identity.
- H2: major section; on wide layouts it occupies the article rail and receives the section rhythm.
- H3: subsection; remains in prose and becomes an indented TOC entry.
- H4 and below: ordinary nested content headings unless a later design decision extends the grammar.

The theme may decorate headings after WordPress content filters have run, but it must not bypass normal content filtering or print untrusted raw content as trusted theme markup.

### 7.3 Paragraphs and links

- Paragraphs use a readable bottom rhythm, not a first-line indent.
- Links use the accent color and visible underlines by default or on a clearly documented interaction rule. Do not rely on color alone.
- Link hover may darken or remove the underline only if the non-hover state remains discoverable.
- Long URLs may wrap; they must not expand the page horizontally.

### 7.4 Editorial blocks

Use semantic HTML first, then style it.

| Block | Visual treatment | Placement |
| --- | --- | --- |
| Context panel | soft gray surface, normal padding | prose column |
| Definition | accent left rule plus soft gray surface | prose column |
| Historical/note aside | compact text with a short rule | article rail wide; prose flow on phone |
| Quote | larger quiet text, optional cite | prose column |
| Figure inline | full prose width | prose column |
| Figure margin | narrower image with caption | article rail wide; centered reduced-width image on phone |
| Figure breakout | full article width | both columns wide; prose width on phone |
| Float figure | reduced width aligned to one side | prose column wide; centered block on phone |
| Pair/gallery | two or more related images | prose column, responsive collapse |
| Table | thin rules, horizontal overflow when necessary | prose column |
| Carousel | optional plugin behavior, ordinary media underneath | prose column |

The theme must render ordinary `<figure>`, `<figcaption>`, `<blockquote>`, lists, tables, galleries, and captions sensibly even without the optional plugin.

### 7.5 Images

Every image must have an intentional width behavior. Avoid accidental browser-native dimensions.

- `max-width: 100%` and `height: auto` are the default safety rule.
- Captions are quiet and right-aligned on wide layouts where that improves the editorial feel; phone captions return to normal reading alignment.
- A margin figure must not create a blank prose row. If a layout technique uses an anchor or zero-height wrapper, document it and provide a fallback.
- Mobile removes float/wrap behavior when it harms reading order.
- Image treatment must not depend solely on `:has()` without a graceful fallback; see the implementation review.

## 8. Responsive design method

Responsive work changes composition in stages.

| Range | Shell | Article | Navigation |
| --- | --- | --- | --- |
| `>1280px` | three columns | article rail + prose | persistent Docs Tree + Page TOC |
| `981–1280px` | left tree + content | narrower rail | Page TOC removed before prose is cramped |
| `901–980px` | content-only shell | rail retained | local bar and chapter drawer |
| `641–900px` | content-only shell | smaller rail | search hidden, global nav still usable where it fits |
| `≤640px` | one-column | article linearizes | global menu, chapter drawer, optional TOC drawer |

This table is a behavior contract, not a demand that every site use exactly these numbers. A change to a breakpoint must be tested against the relationships it protects:

- Does the header still fit long site names and menu labels?
- Does the article retain a readable measure?
- Does H2 remain visually related to the section it introduces?
- Can the user reach both tree and Page TOC without losing place?
- Does a table or image create horizontal overflow outside its intended wrapper?

Phone rules:

- outer content padding is approximately 18px;
- H1 remains prominent but not oversized;
- H2 moves above prose and becomes left-aligned;
- margin notes become normal flow blocks;
- fact grids collapse to one column;
- tables may scroll within a bounded wrapper;
- Previous/Next becomes a vertical pair;
- footer items wrap without clipping;
- all tap targets should be at least approximately 44px high where practical.

## 9. CSS engineering rules

### 9.1 Namespace

New public selectors should use the `docspress` namespace or a clearly scoped context:

```css
.docspress-shell {}
.docspress-article {}
.docspress-tree {}
.docspress-toc {}
.docspress-callout {}
```

Avoid generic selectors such as `.article`, `.section`, `.note`, `.source`, `.pair`, `.meta`, `.quote`, and `.margin` in a theme that must coexist with plugins and user content. If a generic class is retained for compatibility, scope it under `.docspress-docs-page` and do not add more generic names.

### 9.2 Cascade order

The stylesheet should have an understandable order:

1. tokens and reset;
2. base typography and link rules;
3. shell and header;
4. docs tree and navigation;
5. article and content primitives;
6. index pages;
7. standard WordPress surfaces;
8. responsive overrides;
9. accessibility and reduced-motion overrides;
10. compatibility rules with a short explanation.

Do not keep appending emergency corrections to the bottom of a large stylesheet. If a rule is replaced, remove or consolidate the earlier rule. The final CSS should not require the reviewer to trace three versions of the same selector.

### 9.3 Selector and layout discipline

- Prefer classes and direct component context over deep descendant selectors.
- Use logical properties (`margin-inline`, `padding-block`, `inset-inline-start`) where they improve RTL behavior.
- Use grid for the shell and article rail; use flex for linear control groups.
- Keep `min-width: 0` on grid children that contain user content.
- Use `overflow-wrap: anywhere` only where needed for long URLs; do not mask ordinary layout mistakes with global overflow hiding.
- Keep `position: sticky` offsets tied to header tokens.
- Every z-index should belong to a small documented layer scale.
- Use `@supports` or a structural class when a modern selector is not essential to the base reading experience.
- Do not use a fixed height for user-authored text blocks.
- Do not use `!important` for ordinary cascade problems; fix the component scope instead.

### 9.4 State styles

Every link, button, input, disclosure control, and drawer needs at least:

```text
default → hover → focus-visible → active/current → disabled (when applicable)
```

Focus styles must be visible on both white and soft-gray surfaces. A focus ring should not be removed merely because the static screenshot is cleaner.

### 9.5 Motion

Only small state changes need motion: drawer translation, menu icon transition, or a subtle opacity change. No content should slide in a way that changes the reading position unexpectedly. `prefers-reduced-motion: reduce` must disable smooth scrolling and long transitions.

## 10. WordPress and Classic Theme method

### 10.1 Durable content boundary

The theme owns:

- template hierarchy and fallbacks;
- Docs Root setting;
- Docs Tree rendering;
- article layout and heading decoration;
- Page TOC;
- responsive navigation;
- theme support, menus, widgets, editor styles, and standard WordPress surfaces.

The optional plugin may own:

- Classic Editor buttons or editorial helpers;
- special semantic blocks/shortcodes;
- carousel behavior and its content syntax;
- narrowly scoped metadata that is not required to read ordinary content.

The theme must still render a readable page when that plugin is absent or deactivated.

### 10.2 Template responsibilities

Use the WordPress hierarchy rather than routing every request through a custom dispatcher:

- `front-page.php`: static front page / Docs index;
- `page-templates/docs-section.php`: explicit reusable section index;
- `page.php`: docs article or ordinary Page fallback;
- `single.php`, `home.php`, `archive.php`, `search.php`, `404.php`: standard WordPress routes;
- template parts: shell, tree, TOC, article content, previous/next, posts, and comments.

The specialized Docs presentation should be detectable from context, but it must not make ordinary Pages outside the Docs Root inherit the document shell accidentally.

### 10.3 Output and security

- Apply normal WordPress content filters before presentation parsing.
- Escape URLs with `esc_url()`, attributes with `esc_attr()`, text with `esc_html()`, and permitted editor HTML with an explicit KSES boundary.
- Use post-type-specific capabilities for settings or editorial operations.
- Never add direct SQL, runtime file writes, dynamic executable includes, remote HTTP calls, or custom upload endpoints to the theme.
- Treat all excerpts, titles, menu labels, image captions, and metadata as user-controlled content.

## 11. Editor-to-front-end contract

Classic Editor authors should be able to produce a good page using familiar operations:

1. enter a Page title;
2. enter an excerpt for the deck/directory description;
3. set Parent and Order;
4. write H2/H3 headings and ordinary paragraphs;
5. insert images, captions, tables, lists, and quotations;
6. optionally apply documented editorial classes or plugin tools;
7. preview at wide and phone widths.

The editor stylesheet should approximate the front-end prose measure and heading rhythm. It does not need to reproduce the full three-column shell, but it must make the semantic content structure visible while editing.

When a special class is supported, its name and fallback must be documented. For example:

| Author intent | Preferred semantic source | Front-end behavior | Fallback |
| --- | --- | --- | --- |
| Context | `aside` or plugin helper | soft panel in prose | normal blockquote/paragraph flow |
| Definition | `aside`/definition helper | accent rule + soft panel | normal paragraph |
| Margin image | documented figure class | article rail wide | normal inline figure |
| Breakout image | documented figure class | article-wide media | prose-width figure |
| Carousel | optional plugin helper | horizontal media track | stacked figures/gallery |

## 12. Accessibility contract

The visual system is not accepted without these checks:

- one clear H1 per page;
- skip link reaches `#primary`;
- header, tree, TOC, main, footer, and search have understandable names;
- current page and current section are distinguishable;
- disclosure buttons expose expanded/collapsed state;
- links remain links and buttons remain buttons;
- every interactive item is reachable in logical tab order;
- focus is visible at 200% zoom;
- drawers do not leave hidden interactive content in the tab order;
- Escape closes an open panel and returns focus;
- scroll locking does not remove access to the open panel;
- color is not the only signal for current/active states;
- reduced-motion mode is respected;
- long labels, CJK text, RTL text, and zoomed layouts do not clip;
- no horizontal overflow occurs except inside an intentionally scrollable table/media wrapper.

## 13. Visual QA protocol

### 13.1 Viewports

Capture or inspect at:

```text
1440 × 900   wide desktop
1280 × 800   right TOC transition
1024 × 768   narrow desktop/tablet edge
900  × 800   local navigation transition
768  × 1024  tablet portrait
390  × 844   phone
```

### 13.2 Content matrix

Use at least these fixtures:

- short English title;
- long Chinese title;
- mixed CJK/Latin title with punctuation;
- no excerpt and a multi-line excerpt;
- one H2, several H2/H3 levels, duplicate headings, and a heading with an existing ID;
- long URL;
- figure with caption, margin figure, breakout figure, float figure, pair/gallery;
- wide table;
- empty Docs Root and missing menu;
- ordinary Page outside the Docs Root;
- search, archive, 404, comments, and paginated content.

### 13.3 Interaction matrix

- open/close global menu;
- open/close chapter drawer;
- open/close TOC drawer;
- click backdrop;
- press Escape;
- tab through a drawer and return focus;
- expand one tree node while keeping its title link navigable;
- click an H2/H3 TOC link and verify scroll offset below sticky UI;
- resize across every breakpoint with a panel open;
- test reduced motion and keyboard-only use.

### 13.4 Acceptance language

Use precise outcomes in QA notes:

- `matches`: visual relationship is preserved;
- `acceptable`: minor pixel variation, no functional or hierarchy issue;
- `needs adjustment`: visible hierarchy, overflow, focus, or spacing defect;
- `blocked`: missing WordPress/runtime condition prevents verification.

Do not call a live deployment verified merely because a ZIP was uploaded. Check the rendered page, loaded assets, browser console, and the exact content routes.

## 14. Change control

Before changing a token or breakpoint, record:

1. the visual problem;
2. the affected page types and viewports;
3. the current rule and source file;
4. the smallest change that solves it;
5. the accessibility and Classic Editor impact;
6. the screenshots or route checks needed after the change.

The preferred implementation sequence is:

```text
design decision → token/markup contract → CSS change → content fixture → viewport QA → package QA
```

If a change only fixes one sample page by adding a one-off selector, stop and revisit the component rule. Docspress is a theme, so a correct fix must survive different titles, languages, content lengths, hierarchy depths, and ordinary WordPress content.

## 15. Current implementation target

The current `docspress/theme` is a strong working preview and already contains most of this visual grammar. The next work should be a controlled hardening pass, not a rewrite:

1. consolidate the CSS cascade and introduce the semantic token namespace;
2. namespace generic document selectors;
3. complete visible focus and drawer keyboard behavior;
4. remove dependence on `:has()` for the base media layout;
5. make mobile search and icon behavior explicit;
6. align editor styles and ordinary WordPress fallbacks with the same reading tokens;
7. rerun viewport, content, Theme Check, and staging QA.

The detailed evidence and proposed priority order are in [`theme-review.md`](theme-review.md).
