# Docspress QA report

Current acceptance (2026-10-24, deployed 0.9.1): [maturity upgrade report](audits/2026-10-23-upgrade/ACCEPTANCE.md). Everything below is the dated 2026-09-16 history, not the current open-work list or package identity.

Date: 2026-09-16

## Local checks

- PHP syntax: passed for every PHP file in `theme/`.
- JavaScript syntax: passed with Node 22.23.1.
- Focused helper tests: passed for Page-tree sorting/flattening and H2/H3 parsing.
- Content source checks: passed for 28 manifest entries and source files.
- Reference identity scan: passed; no `Humanities` or `hdocs` identity remains in the deployable theme.
- Package verification: `unzip -t` passed; ZIP has one top-level `docspress/` folder.
- Current package SHA-256: `e69ba4673e7cda4dc62cd1a6d1dd41fd1552a947eeb9f4fd7d8aa3ca034b9112` (`docspress-0.2.4.zip`).

## WordPress Coding Standards

PHPCS 3.13.6 with WordPress Coding Standards 3.4.1 passed with zero errors and zero warnings after formatting and translator-comment fixes. The QA dependencies were installed in a temporary directory and are not part of the project or release ZIP.

## Disposable WordPress runtime

- WordPress 7.1 on PHP 8.5.7 with SQLite was initialized in a temporary directory.
- The exact `docspress-0.1.0.zip` installed and activated successfully.
- The seed script created 28 reviewed Pages/Posts, configured the static front page, Posts Page, Docs Root, menus, and footer text.
- HTTP checks passed for the home page, Docs Section Index, Docs article, ordinary Post, search, and 404 surfaces.
- The article rendered H2/H3 anchors, page TOC, section rules, and Previous / Next links.
- Browser interaction checks passed for the chapter and page-TOC drawers, including ARIA state, inert state, body scroll locking, and close behavior.
- `WP_DEBUG`/`WP_DEBUG_LOG` produced no runtime log entries during the checked requests.

## Production `wordpress.im/docspress/` acceptance

- Created the path-based Multisite site at `https://wordpress.im/docspress/`; the main site and existing sibling sites were left unchanged.
- Installed the exact `docspress-0.1.0.zip`, network-enabled it, and activated Docspress only on the Docspress site.
- Imported the reviewed WXR with 24 Pages and 4 Posts, assigned imported content to the `docspress` author, and removed the default Hello World post from the new site.
- Configured the site title/tagline, static front page, Notes Posts Page, Docs Root, primary menu, footer menu, and footer text through WordPress admin/Customizer.
- Live home check passed with six populated Docs sections. A nested Docs article rendered the documentation tree, H2/H3 headings, page TOC semantics, and Previous / Next links.
- Notes archive check passed with four Docspress Posts. Search and 404 checks passed.
- Mobile check at 390×844 passed: the global navigation collapses, the Contents drawer opens, `aria-expanded` becomes `true`, and the body receives scroll locking; the temporary viewport override was reset.
- Live theme resource check passed for the Docspress stylesheet and navigation script; browser console error count was zero.
- The WordPress Importer used for the one-time WXR import is installed but inactive after the import.

## Still open

- Theme Check/current official review tooling has not yet been run against an activated WordPress site.
- Classic Editor/Block Editor editing, comments, archives, feeds, and live cache/TLS behavior need a later maintenance pass if the project advances beyond the working preview.
- Keyboard-only, 200% zoom, reduced motion, and RTL remain part of the pre-1.0 audit; the production desktop, phone, and core content routes are now verified.

## 0.2.3 upgrade acceptance

- The exact `docspress-0.2.3.zip` was uploaded through the WordPress Network Theme uploader and replaced the active Docspress source without changing the active theme or site content.
- The live stylesheet reports `Version: 0.2.3`; live `style.css`, `main.css`, and `navigation.js` SHA-256 values match the local source byte-for-byte.
- Public home, nested article, search, and 404 routes returned `200, 200, 200, 404` respectively.
- The public page retained the `wp-theme-docspress` identity, three standard search forms, and no `Humanities`/`hdocs` identity.
- Browser verification confirmed the namespaced `docspress-section` headings, the scoped compatibility styling for pre-migration `.callout` content, the mobile global menu search, ARIA state, body scroll lock, focus return, and zero console warnings/errors.
- The package remains one top-level `docspress/` ZIP folder and excludes project docs, reference files, site content, and generated build artifacts from the install package.

## 0.2.4 visual acceptance

- The exact `docspress-0.2.4.zip` was uploaded through the WordPress Network Theme uploader and replaced the active Docspress source without changing the active theme or site content.
- The live stylesheet reports `Version: 0.2.4`; the live source carries the bounded `main.doc-frame::before` layer and the real `.article-wrap` spacing rule.
- At `1440`, `1280`, `1024`, `768`, and `390` CSS-pixel viewport widths, Home, Section Index, Content Page, and 404 routes were inspected for horizontal overflow, shell transitions, title baseline, footer placement, and mobile linearization.
- At `1440` and `390` CSS-pixel viewport widths, the standard Classic Theme surfaces were also checked: Notes/posts index, search results, single Post, date archive, category archive, ordinary Page, and 404. They retain the standard reading/sidebar layout and do not inherit the Docs Tree or article rail.
- The desktop and compact Content Page title baseline now aligns with the corresponding Home/Section heading rhythm; the desktop article wrapper begins at `28px`, and the compact wrapper begins at `22px` below `981px`.
- The Docs Tree background is now bounded by the main frame (`position:absolute; top:0; bottom:0`) and ends before the footer; it no longer remains as a viewport-fixed layer behind the footer.
- The package remains one top-level `docspress/` ZIP folder and excludes project docs, reference files, site content, and generated build artifacts from the install package.

## GitHub 0.2.4 release acceptance

- Commit `e1a8365` is pushed to `main` and tag `v0.2.4` points to the same commit.
- GitHub release [`v0.2.4`](https://github.com/wp-im/docspress/releases/tag/v0.2.4) contains `docspress-0.2.4.zip`.
- The downloaded release asset SHA-256 matches the local package: `e69ba4673e7cda4dc62cd1a6d1dd41fd1552a947eeb9f4fd7d8aa3ca034b9112`.
