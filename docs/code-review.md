# Docspress reference code review

Status: `REFERENCE / NOT RELEASE-READY`

Reviewed source: `reference/humanities-docs/`, imported from the final `Humanities Docs Theme 1.0.0` archive. This review treats the design and implementation as the project's own working material, while still separating the unchanged reference tree from the future Docspress source tree.

## Executive result

The reference is a coherent Classic Theme prototype with a strong document-reading model. It is not a random visual mockup: the Page hierarchy, section indexes, article TOC, and depth-first Previous / Next navigation are connected to a clear WordPress content model.

The code has a small attack surface and passed the local syntax checks. It should not yet be published as Docspress because its public identity is still Humanities Docs, the official Theme Check/WPCS and real WordPress acceptance evidence are absent, and the production `wordpress.im` network state must be refreshed before a subsite is created.

## Verified strengths

### Classic Theme structure

The package contains the expected Classic Theme foundation: `style.css`, `index.php`, `comments.php`, and a 1200×900 `screenshot.png`. It also includes standard fallbacks for home, front page, page, single, archive, search, 404, comments, sidebar, navigation, and content states.

The templates use the important WordPress integration points:

- `wp_head()` and `wp_footer()`;
- `wp_body_open()` and `body_class()`;
- `post_class()`;
- `wp_link_pages()`;
- `comment_form()`, threaded comment list output, and comment navigation;
- normal menu locations, Custom Logo, widgets, excerpts, featured images, feeds, and editor styles.

### Content and navigation model

`inc/docs.php` is the main architectural value. It keeps the Docs Root as a theme setting, retrieves published descendant Pages, sorts by `menu_order` and title, builds a nested tree, flattens that tree for depth-first navigation, and renders independent toggle/link controls.

This gives Docspress a durable rule:

```text
WordPress Page Parent + Order
        ↓
Docs Tree + Section Index
        ↓
Article TOC + Previous / Next
```

The model does not need a custom post type or a manually maintained JavaScript sidebar configuration.

### Heading and TOC behavior

`inc/toc.php` decorates H2/H3 headings after the normal `the_content` filters have run. It preserves existing IDs, generates missing IDs, resolves duplicates, creates TOC data, adds the editorial H2 section class, and inserts section rules.

The attribute allow-list keeps `title`, `role`, `dir`, `lang`, `aria-*`, and `data-*`, while discarding event-handler attributes. Output is escaped in the generated ID, class, and attribute contexts; the already-filtered content remains the inner HTML.

### Responsive interaction

The reference has three intentional navigation modes:

- wide desktop: persistent Docs Tree plus optional right TOC;
- tablet: local navigation bar and article rail retained;
- phone: linear article flow, chapter drawer, TOC drawer, and global menu.

The JavaScript uses `aria-expanded`, `aria-hidden`, `inert`, Escape-to-close, focus return to the opener, backdrop close, and reduced-motion CSS support. The tree toggle is deliberately separate from the adjacent page link.

### Security boundary

The local static scan found no custom superglobal handling, AJAX/admin-post endpoint, REST write endpoint, direct SQL, command execution, file write/delete, dynamic include, remote HTTP call, or obfuscation pattern. The theme does not register a custom post type, shortcode, or editor data store.

This is the correct boundary for a standard theme: presentation and navigation stay in the theme; editor-specific behavior can be optional and separate.

## Findings before Docspress implementation

### P1 — public identity is still the reference identity

The source still contains `Humanities Docs`, `humanities-docs`, `humanities_docs`, `humanities_docs_setup()`, and `hdocs_*` across the stylesheet, PHP, JavaScript/CSS classes, readme files, and POT catalog. This is the main reason the imported tree must not be published directly.

Docspress needs an intentional identity migration:

- folder slug: `docspress`;
- display name: `Docspress`;
- text domain: `docspress`;
- function prefix: `docspress_`;
- short helper prefix: `dpress_` or the same full `docspress_` prefix, chosen once and used consistently;
- CSS/DOM namespace: `docspress-*` for new public selectors;
- package and translation metadata: Docspress-owned;
- new screenshot and public copy.

This must be a reviewed source migration, not a blind replacement inside the reference directory.

### P1 — official release evidence is incomplete

Local PHP and JavaScript syntax checks pass, but the following have not yet been run against a Docspress source tree:

- PHPCS with WordPress Coding Standards;
- Theme Check or the current equivalent review tooling;
- WordPress Theme Unit Test data;
- clean WordPress activation with `WP_DEBUG` enabled;
- real Classic Editor editing and saved-content output;
- keyboard, screen-reader, zoom/reflow, and mobile browser checks;
- fresh release ZIP scope and install test.

`phpcs` and `phpunit` are not currently available in the local shell, and there is no local WordPress installation bound to this project for an end-to-end run.

### P1 — production network state is not an implementation prerequisite yet

The previous project inventory recorded that WordPress Multisite was not enabled. That state is time-sensitive and must be checked again immediately before any production action. If it is still single-site, creating `wordpress.im/docspress/` requires a separate path-based Multisite migration decision; it is not a normal theme upload.

No production site, network, DNS, `wp-config.php`, `.htaccess`, or remote files were changed during this review.

### P2 — heading parser needs focused regression tests

The parser is deliberately small and works with ordinary WordPress HTML, but it is regex-based. Before release, tests should cover:

- headings with inline links, emphasis, entities, and nested markup;
- existing IDs with spaces, punctuation, and duplicate IDs;
- unquoted or unusual attributes produced by pasted HTML;
- headings inside preformatted/code content;
- empty headings and headings containing shortcode output;
- multilingual titles and stable anchor generation.

The goal is not to replace the design with a heavier parser automatically; the goal is to define the supported content contract and prove it.

### P2 — accessibility interaction needs browser proof

The drawer code already has good primitives (`inert`, ARIA state, Escape, focus return), but it has not been verified in a real browser at 390px and tablet widths. The release pass should explicitly verify focus order, close-button reachability, screen-reader naming, zoom at 200%, and whether a dialog role/focus trap is needed for the final drawer behavior.

### P2 — plugin coupling must remain optional

The reference calls `add_theme_support( 'humanities-docs-editor' )` and styles classes such as `hdocs-aside` and `hdocs-carousel`. These are harmless as an optional integration surface, but Docspress v1 should not require a companion plugin to render the core site. The first release should work with ordinary Page content, H2/H3, paragraphs, lists, quotes, tables, images, captions, and links. Any editor helper plugin must be a separately packaged project.

### P2 — compatibility and metadata need a deliberate baseline

The reference metadata says WordPress 6.5+, PHP 7.4+, and Tested up to 7.1. Docspress should choose its supported WordPress/PHP baseline after checking the target host rather than inheriting these values silently. The CSS also uses `:has()` selectors for Classic Editor image wrappers; this is acceptable only after the browser support policy is explicit and a graceful fallback is tested.

### P3 — release polish

- Add an RTL strategy and test it, even if the first public demo is LTR.
- Regenerate the Docspress POT after all text-domain changes.
- Make README, `readme.txt`, changelog, stylesheet header, and version values agree.
- Replace the reference screenshot with a Docspress screenshot showing the actual final identity.
- Ensure demo content never enters the deployable theme ZIP.

## Review conclusion

The reference architecture is worth carrying forward. The correct next action is to create an independent Docspress-owned source tree, preserve the reading system, remove public Humanities Docs identity, add tests and packaging, then validate in a disposable/staging WordPress installation before touching `wordpress.im`.
