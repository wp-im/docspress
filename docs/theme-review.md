# Docspress Theme Visual and CSS Review

Date: 2026-09-16

Scope: current files under `docspress/theme/`, the current `theme/screenshot.png`, and the archived static HTML/CSS reference pages.

This file began as the pre-hardening review. The 0.2.4 implementation pass has now resolved the mobile search, visible focus, drawer focus boundary, control-glyph, selector namespace, editor rhythm, RTL, `:has()` fallback, shell-background boundary, and article-wrapper rhythm findings. The remaining notes are maintenance guidance and evidence gates, not an instruction to undo the current design.

## Executive assessment

The current theme is not a blank implementation. It has successfully carried over the distinctive design idea:

- a sticky, restrained header;
- a soft-gray Docs Tree rail;
- a wide article with an internal margin rail;
- H2/H3 parsing and a Page TOC;
- section directories that use rules and numbered rows instead of cards;
- responsive drawers and local navigation;
- native WordPress Pages as the content hierarchy;
- an optional plugin boundary rather than a theme-owned content system.

The screenshot also confirms the strongest visual result: the title, prose, H2 rail, margin image, orange rule, and Previous/Next navigation read as one editorial instrument. The next pass should preserve this character.

The main risks are implementation quality and long-term CSS stability, not the central visual direction.

## Findings by priority

### P1 — consolidate `main.css` before adding more features

Evidence: `theme/assets/css/main.css` begins with the static design layer, then adds WordPress integration around lines 1129–1280, then adds “integration corrections” and repeated image rules around lines 1281–1450.

What is good:

- the tokens and reference geometry are easy to identify near the top;
- the responsive transitions are explicit;
- WordPress-native media and standard surfaces have been considered.

Problem:

The file has become a historical cascade: a reference rule is defined, an integration rule is appended later, and a correction is appended after that. This makes future visual changes expensive because a reviewer must know which later selector wins. It also creates parallel generic and namespaced concepts.

Recommendation:

Refactor without changing the visual result into explicit sections:

```text
tokens.css or token section
base.css
shell.css
navigation.css
article.css
editorial-blocks.css
wordpress.css
responsive.css
accessibility.css
compatibility.css
```

The project may keep one shipped CSS file if packaging simplicity is preferred, but the source order and comments should follow this structure. Remove superseded rules rather than adding another correction below them.

Acceptance:

- one authoritative definition for each component state;
- no duplicate `.docspress-section-rule` block;
- image placement rules appear in one component section with documented fallbacks;
- every responsive override is adjacent to the component it changes or clearly grouped in one responsive layer;
- visual screenshots remain unchanged within normal rendering variation.

### P1 — namespace generic document selectors

Evidence: the current CSS uses generic names including `.article`, `.section`, `.margin`, `.note`, `.quote`, `.pair`, `.source`, `.meta`, `.fact-grid`, `.steps`, and `.table-wrap`.

Problem:

These names can collide with user HTML, plugins, block markup, or ordinary WordPress templates. A page outside the Docs Root can accidentally inherit a document rule if it happens to use the same class name.

Recommendation:

Move new selectors toward a stable namespace, for example:

```text
.docspress-article
.docspress-heading--section
.docspress-article-rail
.docspress-note
.docspress-quote
.docspress-media-pair
.docspress-source
```

Keep a scoped compatibility layer only when existing imported content needs it:

```css
.docspress-docs-page .section { ... }
```

Do not rename the archived static files. Rename the deployable theme classes deliberately and update PHP, JS, editor CSS, tests, and documentation together.

Acceptance:

- ordinary Page and Post fixtures do not inherit Docs Tree/article rules;
- the theme has one documented selector namespace;
- the reference identity scan remains clean.

### P1 — add a complete visible focus system

Evidence: the current rules emphasize `:hover` and active/current colors, but `theme/assets/css/main.css` does not yet provide a comprehensive `:focus-visible` treatment for links, tree toggles, local controls, drawer close buttons, search fields, and directory rows.

Problem:

The screenshot is quiet because focus is not visible in the static image, but keyboard users need a stronger signal than color changes. This is particularly important for the tree, where the arrow and title are adjacent controls.

Recommendation:

Add a shared focus rule scoped to Docspress controls. It should work on white, soft-gray, and sidebar backgrounds, avoid clipping at the viewport edge, and not change layout dimensions. Test the skip link separately.

Also ensure that the whole directory row has an obvious focus state even if only its anchor receives focus.

Acceptance:

- keyboard-only traversal is visible at 100% and 200% zoom;
- focus is not represented only by orange text;
- focus remains visible inside open drawers;
- `:focus-visible` and the fallback behavior are intentional.

### P1 — strengthen drawer modality and scroll locking

Evidence: `theme/assets/js/navigation.js` sets `aria-hidden`, `inert`, `aria-expanded`, Escape handling, backdrop close, and focus return. It moves focus into a panel but does not implement an explicit focus trap or a documented modal boundary.

What is good:

- the hidden panels are made non-interactive;
- the opener state is updated;
- the panel closes on Escape and returns focus;
- the three navigation panels are mutually exclusive.

Recommendation:

- decide explicitly whether these panels are modal drawers or non-modal disclosure regions;
- for modal drawers, trap Tab/Shift+Tab within the open panel and mark the panel state consistently;
- preserve the header relationship when the TOC drawer starts below the header;
- use `scrollbar-gutter: stable` where supported or a small measured compensation to prevent the body from shifting when `overflow:hidden` is applied;
- provide a fallback for browsers that lack `MediaQueryList.addEventListener` if the supported browser baseline requires it;
- set the initial active TOC link deterministically before the first IntersectionObserver callback.

Acceptance:

- no hidden control is reached by Tab;
- focus cannot escape the open modal drawer;
- closing by Escape, close button, backdrop, and navigation link all leave a predictable focus state;
- resizing across 640/980px closes stale panels without trapping focus.

### P1 — remove `:has()` as a base-layout dependency

Evidence: `theme/assets/css/main.css` uses selectors such as `p:has(> img.docspress-figure--margin)` and `figure:has(img.docspress-figure--breakout)` around lines 1298–1390.

Problem:

`:has()` is now available in modern browsers, but the theme's basic media placement should not silently fail in a supported-but-lower capability browser or in an embedded editor preview. The current selector also makes the final layout harder to understand because the class lives on a descendant but controls the wrapper's grid placement.

Recommendation:

Make the wrapper carry the placement class during content generation or through documented author markup:

```html
<figure class="docspress-figure docspress-figure--margin">…</figure>
```

For a Classic Editor paragraph containing an image, prefer a small filtered wrapper transformation or a plugin-generated figure rather than requiring the theme to infer every case with `:has()`. Keep a `:has()` enhancement only if it adds convenience and is not necessary for the fallback.

Acceptance:

- margin, float, breakout, and inline figures still render as readable normal figures when the enhancement selector is unavailable;
- no empty grid row is created;
- editor and front-end markup use the same placement vocabulary.

### P1 — make mobile search a first-class path

Evidence: `header.php` contains a desktop search form and the CSS hides `.header-search-form` at `max-width:900px`. The global mobile menu contains the primary menu but no parallel search form.

Problem:

At phone widths the main search affordance disappears. This is a functional gap in a documentation theme where search is a primary navigation route.

Recommendation:

Add a compact search form inside the mobile global menu, or add a visible search control that opens a labeled search panel. Reuse the same WordPress query and label. Keep it visually subordinate to the navigation links.

Acceptance:

- a phone user can reach site search without switching to a desktop width;
- the search field has a label and a clear submit path;
- the mobile menu remains calm and does not become a dashboard.

### P2 — replace platform-dependent Unicode controls

Evidence: the templates use `☰`, `≡`, `⌕`, `→`, and `×` in `mobile-local.php`, `front-page.php`, `prev-next.php`, and close controls.

Problem:

These symbols vary by operating system and font. They may be visually inconsistent with the thin-line reference language and may have different perceived sizes in CJK environments.

Recommendation:

Use one small icon strategy:

- CSS-drawn lines for menu/close where the shape is trivial;
- inline SVG with `aria-hidden="true"` for arrows and search;
- keep the accessible text label in HTML;
- do not depend on an icon font.

The arrow in a directory row remains decorative; the anchor text is the accessible name.

### P2 — align the editor stylesheet to the design contract

Evidence: `theme/assets/css/editor.css` is intentionally small, while the front-end CSS contains the complete article rail, block, table, and image grammar.

Recommendation:

Make the editor stylesheet express the same base content rhythm:

- body type and prose measure;
- H1/H2/H3 hierarchy;
- excerpt/deck expectation where the editor exposes it;
- blockquote, definition/context, figure, caption, table, list, and link states;
- documented placement classes without attempting to reproduce the sticky shell.

The editor does not need a fake left Docs Tree. It does need to show authors what an H2, H3, figure, caption, and callout will feel like.

### P2 — make section-index behavior less template-fragile

Evidence: `page-templates/docs-section.php` is a reusable explicit template, while the default Docs Page path renders an article.

Problem:

Editors must remember to assign the template to every section index. A forgotten assignment produces a valid page but the wrong information architecture.

Recommendation:

Keep the explicit template for user control, but add a safe detection/fallback rule in a later pass. Candidates include:

- an optional page setting that marks a page as an index;
- automatic index behavior only for pages with children and an explicit opt-in;
- a documented default based on a page template selection with an admin hint.

Do not silently convert every page with children into a directory until the editorial consequences are tested. A page may have children for reasons other than a visible directory.

### P2 — separate standard WordPress surfaces from the docs shell

Evidence: the theme already has standard fallbacks, but the large `main.css` shares many tokens and generic rules across docs, posts, comments, widgets, and search.

Recommendation:

Define a small shared foundation and then separate:

```text
Docspress docs surfaces
Docspress index surfaces
standard WordPress surfaces
```

The standard surfaces may share type, colors, borders, and form controls, but should not inherit the article rail or Docs Tree grid.

Acceptance:

- ordinary Posts remain readable without a fake documentation tree;
- comments and widgets do not receive docs-only spacing unexpectedly;
- search and 404 maintain the same visual family without pretending to be articles.

### P2 — add explicit layout and token tests

The current project has strong PHP/JS/CSS syntax and runtime checks. Add a small static contract test for:

- required token names and values;
- presence of focus-visible rules;
- no unscoped new generic selectors;
- one authoritative breakpoint definition;
- mobile search markup;
- no reference identity in deployable theme files;
- no accidental `:has()` use outside the documented compatibility layer.

This test should not try to understand all CSS. It should catch regressions in the design contract.

## Visual observations to preserve

### The article screenshot is already distinctive

The current screenshot demonstrates the correct balance:

- a left navigation field that is visible but not loud;
- a title with enough scale to establish the page;
- an orange eyebrow as orientation, not decoration;
- H2 labels placed in the margin rail;
- an image that occupies the rail without interrupting the prose;
- a thin orange section boundary;
- a soft definition/context panel;
- restrained Previous/Next links.

Do not “improve” this by adding more cards, a hero image, a large colored banner, or a heavy sticky TOC.

### Current areas to check visually

The next screenshot round should pay special attention to:

1. long brand/site names in the 900–1024px header;
2. the right edge of the article when the TOC disappears at 1280px;
3. H2 rail alignment when the heading wraps to two or three lines;
4. margin notes and figures next to a long paragraph;
5. the first and last items in the Docs Tree when the sidebar scrolls;
6. the drawer header against the sticky site header;
7. focus rings on orange links over white and gray surfaces;
8. the phone global menu after a search form is added;
9. wide tables and long URLs;
10. the difference between a current page and a current ancestor in the tree.

## Recommended implementation order

### Pass A — no visual redesign

1. Create the semantic token layer and document the current values.
2. Consolidate duplicate CSS and isolate compatibility rules.
3. Namespace generic selectors.
4. Add focus-visible states and a stable z-index layer.
5. Add static contract checks.

### Pass B — interaction and content robustness

1. Add mobile search.
2. Strengthen drawer modality, focus handling, and scroll-lock behavior.
3. Replace or wrap Unicode control icons.
4. Remove base dependence on `:has()`.
5. Test heading IDs, duplicate headings, tables, images, and empty/missing settings.

### Pass C — editorial calibration

1. Align `editor.css` with the content grammar.
2. Recheck CJK and mixed-language typography.
3. Recheck index row density and section descriptions.
4. Recheck standard Posts, archives, search, comments, and 404 surfaces.

### Pass D — release gate

1. Run Theme Check and WPCS against the current source.
2. Run the disposable WordPress runtime with the exact release ZIP.
3. Capture the viewport matrix and interaction matrix.
4. Verify the package contains only the deployable theme.
5. Compare the final ZIP checksum after all edits.

## What should not be changed in the next pass

- Do not change the native Page hierarchy into a custom post type.
- Do not merge the optional editor plugin into the theme.
- Do not place demo copy or site-specific IDs in the deployable theme.
- Do not make the whole Docs Tree row a disclosure button.
- Do not turn Section Index rows into cards.
- Do not remove the article rail merely because it requires responsive rules.
- Do not treat the current static HTML file names as the Docspress public identity.

## Review conclusion

Docspress has a credible visual core and a sound content boundary. The correct next move is a hardening and consolidation pass that protects the calm editorial composition while making the CSS namespace, interaction states, media fallbacks, mobile search, editor preview, and standard WordPress surfaces more robust.
