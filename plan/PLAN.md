# Docspress development and publication plan

Status: `0.9.1 LIVE / MATURITY UPGRADE IMPLEMENTED / NO NEW GIT COMMIT OR PUSH`

2026-10-24：本轮 [完整升级计划](DOCSPRESS-MATURITY-UPGRADE-PLAN-2026-10-23.md) 已实施并部署 0.9.1；[最终验收报告](../docs/audits/2026-10-23-upgrade/ACCEPTANCE.md) 为当前交付依据。以下早期阶段和 0.2.4 GitHub 发布记录保留为历史，旧版“右栏 TOC / TOC 抽屉”描述已被当前 design contract 取代。实机手机检查依用户指示改用技术/浏览器模拟；本轮未 commit/push，不提交 WordPress.org。

WordPress.org 发布升级方案：[`WORDPRESS-ORG-RELEASE-PLAN.md`](WORDPRESS-ORG-RELEASE-PLAN.md)。当前工作预览不能直接视为 WordPress.org 可提交版本；先按该方案清零发布阻塞并完成视觉/运行时验收。

## Product decision

Docspress will be a standard WordPress Classic Theme for a document-led reading site. The reference design and the implementation discussed in the supplied ChatGPT project are treated as our project material. The public product identity will be Docspress from the first working source tree onward.

The core promise is:

> WordPress Pages remain ordinary editable content, while their hierarchy becomes a calm, readable documentation site.

## Source layout

Keep the imported reference immutable and create a separate deployable source tree:

```text
docspress/
├── reference/              # imported Humanities Docs archive and extracted reference
├── docs/                   # design, code review, content and release documentation
├── plan/                   # this plan and later gate records
├── theme/                  # Docspress deployable Classic Theme source
├── site-content/           # demo-site copy for Pages and Posts, never in theme ZIP
└── build/                  # generated ZIPs, checksums, and visual QA outputs
```

The deployable ZIP must contain only one top-level `docspress/` theme folder. It must not contain the reference archive, project docs, site content, Git metadata, logs, databases, or local screenshots used only for QA. The public source repository follows the same boundary for `reference/` and generated `build/` artifacts.

## Phase 0 — freeze the contract

Before code migration, record these decisions in `docs/design.md`:

- display name: `Docspress`;
- theme slug: `docspress`;
- text domain: `docspress`;
- PHP function prefix: `docspress_`;
- CSS namespace for new selectors: `docspress-*`;
- initial license: GPLv2 or later unless a later project decision changes it;
- content model: native Pages, Posts, menus, excerpts, media, categories, tags, authors, comments, search, and feeds;
- no theme-owned custom post type, shortcode, custom block, remote font, tracker, analytics, or external runtime dependency;
- optional editor helpers, if needed, are a separate plugin and cannot be required for the core theme.

## Phase 1 — make a Docspress-owned source tree

Create `docspress/theme/` from the reference and perform a deliberate migration:

1. Change stylesheet and readme identity to Docspress.
2. Rename all public text-domain calls and regenerate the POT file.
3. Rename all PHP function prefixes and Customizer keys.
4. Rename asset handles, IDs, classes, data attributes, and package comments where they are public or project-specific.
5. Remove accidental reference wording and write Docspress-owned documentation.
6. Decide whether the optional editor theme-support flag remains; the core theme must still work without the plugin.
7. Add or settle RTL support, browser baseline, PHP baseline, and version policy.
8. Replace the screenshot and add a Docspress-owned license/copyright header.

Do not mutate `reference/humanities-docs/` while doing this.

## Phase 2 — preserve the design core

Keep the following as stable design invariants unless a visual review explicitly changes them:

- quiet white/soft-gray surface with one restrained accent;
- sticky site header;
- wide desktop: Docs Tree → article reading surface → optional page TOC;
- article rail for H2 and margin material;
- tablet local navigation without collapsing the article grammar too early;
- phone drawers for Docs Tree, page TOC, and global menu;
- Page title as H1, H2/H3 as semantic document navigation inputs;
- depth-first Previous / Next navigation;
- ordinary Classic Editor/WordPress content remains useful without special markup.

The design should remain editorial and document-like, not become a card dashboard or a page-builder canvas.

## Phase 3 — harden the implementation

Implement and test the following before any production install:

### Theme integration

- complete Classic Theme hierarchy and fallbacks;
- `wp_head`, `wp_body_open`, `body_class`, `post_class`, `wp_footer`, comments, pagination, and `wp_link_pages` checks;
- menus, custom logo, excerpts, featured images, widgets, feeds, editor styles, and Customizer controls;
- no fatal behavior when the optional editor plugin is absent.

### Docs behavior

- root Page selection and invalid/deleted root fallback;
- published-only tree behavior;
- Page `menu_order` and deterministic title tie-break;
- arbitrary descendant depth with controlled visual indentation;
- current path expansion and independent desktop/mobile tree instances;
- section index with empty state;
- H2/H3 parsing and TOC links;
- duplicate/unsafe heading IDs and multilingual headings;
- depth-first Previous / Next;
- content with no headings, no excerpt, comments, password protection, and page splitting.

### Accessibility and resilience

- keyboard-only navigation;
- visible focus at all breakpoints;
- skip link and landmark naming;
- mobile drawer focus behavior, Escape, close controls, `inert`, and reflow;
- 200% zoom and narrow widths without unexpected horizontal page overflow;
- reduced motion;
- contrast for muted text, rules, active links, and form controls;
- RTL behavior or an explicit documented limitation.

### Security and code quality

- PHP syntax, JavaScript syntax, CSS parse checks;
- PHPCS with WordPress Coding Standards;
- Theme Check/current official review tooling;
- focused tests for pure Docs tree helpers and heading parser;
- scan for superglobals, SQL, dynamic includes, file operations, remote requests, command execution, and secrets;
- verify all output escaping and all translation calls.

## Phase 4 — create the self-publishing demo content

Content belongs to the `wordpress.im/docspress/` site, not the theme ZIP. Prepare it in `site-content/` so it can be reviewed and entered reproducibly.

### Pages / Docs Tree

```text
Docspress
├── Start here
│   ├── What Docspress is
│   ├── Install and activate
│   └── First Docs Tree
├── Content model
│   ├── Docs Root and Page hierarchy
│   ├── Parent, Order, and excerpts
│   └── H2/H3 headings and page TOC
├── Design system
│   ├── Reading surface
│   ├── Desktop, tablet, and mobile navigation
│   └── Images, tables, quotes, and long-form rhythm
├── Classic Theme architecture
│   ├── Template hierarchy
│   ├── Theme versus plugin boundary
│   └── Fallback behavior
├── Accessibility and security
│   ├── Keyboard and screen-reader behavior
│   ├── Safe content output
│   └── Updates, backups, and deployment hygiene
└── Release notes
```

### Posts

Use ordinary Posts to demonstrate that the theme still supports normal WordPress publishing outside the Docs Tree:

- Why Docspress uses Page hierarchy for documents;
- A short release note for each preview/RC/final version;
- A visual note about the reading system;
- A maintenance note describing update and rollback practice.

These are demo/site content, not hardcoded theme strings.

## Phase 5 — local and disposable WordPress acceptance

Use a real WordPress installation before production. The acceptance matrix should include:

| Surface | Required checks |
| --- | --- |
| Static front page | Site index, root selection, empty and populated states |
| Docs article | H1, excerpt/deck, H2/H3 TOC, notes/images/tables, Prev/Next |
| Section index | Direct children, nested child list, empty state |
| Ordinary page/post | Standard fallback, metadata, page links, comments |
| Archives/search | Category/tag/author/date/search paths and pagination |
| 404 | Recovery copy, search, home link |
| Mobile | 390px menu/drawers, 640px transition, no page overflow |
| Desktop | 1440px, 1180px, 1024px, 900px, 768px layout transitions |
| Admin | Theme activation, Customizer, menus, editor styles, excerpts |

Run with `WP_DEBUG` and log inspection enabled. Record screenshots and the exact WordPress/PHP/browser versions used.

## Phase 6 — `wordpress.im` subsite gate

This phase is deliberately separate from theme coding.

1. Read-only recheck the current `wordpress.im` URL, HTTPS certificate, WordPress version, login/session state, active theme, existing sites, and backup state.
2. Confirm whether Multisite is currently enabled.
3. If path-based Multisite is already enabled, create the `docspress` site at the intended path and keep the main site untouched.
4. If Multisite is not enabled, stop and present the separate `wp-config.php`/`.htaccess`/network migration decision. Do not silently enable it as part of a theme upload.
5. Install the Docspress ZIP on the target site, activate it only there, configure Docs Root, menus, logo, and footer text.
6. Enter the reviewed Pages and Posts from `site-content/`.
7. Re-run the full browser acceptance matrix against the real URL.

The expected path for the existing project direction is `https://wordpress.im/docspress/`, not a wildcard subdomain. DNS changes are not needed for a path-based site.

Production creation, theme activation, content writes, and publication remain action-time operations. Before those writes, state the exact target, scope, and rollback point and obtain the required confirmation.

Production gate completed on 2026-09-16:

- Created `https://wordpress.im/docspress/` as the sixth path-based network site; the main site and existing sibling sites were not changed.
- Installed the exact `build/docspress-0.1.0.zip` (`cc82fb3ffb94802af2f75c28814a4a58cc8e3d5f79cfc428af6a0bf9775d6804`), network-enabled it, and activated it only on Docspress.
- Imported `site-content/docspress-demo.xml`: 24 Pages and 4 Posts. The default Hello World post was removed from the new site.
- Configured the static front page, Notes Posts Page, Docs Root, primary and footer menus, site identity, and footer text.
- Verified the live home, nested Docs article, Notes archive, search, 404, 390px mobile Contents drawer, Docspress CSS/JS resources, and zero browser console errors.
- The WordPress Importer remains installed but inactive for possible future maintenance imports.

Upgrade gate completed on 2026-09-16:

- Hardened the theme through 0.2.4: standard `searchform.php`, visible focus, focus trap, reduced motion, scoped document classes, namespaced H2/H3 classes, structured image fallbacks, CSS-drawn controls, RTL alignment, editor rhythm, bounded Docs Tree background, and shared article-wrapper geometry.
- Uploaded the exact `build/docspress-0.2.4.zip` to the existing subsite workflow and verified the live version, byte-for-byte CSS/JS equality, public routes, mobile menu, compatibility styling, and zero browser warnings/errors.
- Pushed commit `e1a8365` to `main`, created tag `v0.2.4`, and verified the GitHub release asset against the local package checksum.

## Phase 7 — release packaging

Adopt the proven standalone-theme release pattern:

- package with a reproducible script;
- one top-level `docspress/` directory;
- exclude `reference/`, `docs/`, `plan/`, `site-content/`, `build/`, `.git`, logs, databases, secrets, and local QA artifacts;
- run `unzip -t`;
- record SHA-256;
- compare the packaged files against the intended theme source;
- install the exact ZIP through the WordPress uploader;
- publish release notes only after local and staging evidence passes.

Version path:

```text
0.1.0  working prototype
0.2.x  content and responsive refinement / current 0.2.4 candidate
0.9.0  release candidate / full audit
1.0.0  public release after real WordPress acceptance
```

## Definition of done

Docspress is ready for public release only when:

- no Humanities Docs identity remains in the deployable theme;
- the theme activates cleanly and renders all required fallback surfaces;
- ordinary WordPress content remains portable if the theme changes;
- Docs Tree, section index, TOC, and Prev/Next are tested with real Pages;
- keyboard, mobile, zoom, reduced motion, and contrast checks pass;
- PHP/JS/CSS, PHPCS, Theme Check, and package checks are recorded;
- the theme ZIP contains no demo content, credentials, project docs, or reference archive;
- the `docspress` site is verified independently from the main `wordpress.im` site;
- the live URL, installed version, ZIP SHA-256, and rollback path are documented.

## Current gate

Completed now:

- reference downloaded and checksum-verified;
- design analysis written;
- local static code review completed;
- production site, theme installation, and the GitHub v0.2.4 release are complete; the remaining work is the post-release accessibility, editor, archive, and official review audit.

Completed after the initial review:

- independent `theme/` source tree with Docspress identity and no reference identity;
- Docspress screenshot, RTL baseline, focused tree/TOC tests, content manifest, and demo-site copy;
- local PHP/JavaScript checks, content-source checks, ZIP test, and SHA-256 record;
- WordPress Coding Standards 3.4.1 / PHPCS 3.13.6: zero errors and zero warnings;
- disposable WordPress 7.1 + PHP 8.5.7 + SQLite activation and runtime acceptance passed, including 28 seeded Pages/Posts and no `WP_DEBUG` log output;
- pre-write production verification: HTTPS responded, WordPress 7.1 was visible, the Multisite registration surface existed with registration disabled, and `/docspress/` was absent before deployment;
- production `docspress` site creation, exact theme install/activation, content import, Customizer, menus, cleanup, and core browser acceptance passed;
- GitHub repository `wp-im/docspress` is published at v0.2.4; the release asset was downloaded and its SHA-256 matched the local package.

Next gate: optional pre-1.0 audit for Theme Check, keyboard-only use, 200% zoom, reduced motion, RTL, editor workflows, comments, archives, feeds, and cache/TLS behavior. The working preview itself is live and self-documenting at `https://wordpress.im/docspress/`.
