# Docspress

Docspress is a WordPress Classic Theme for document-led sites, reference material, and long-form reading. It is based on the locally imported `Humanities Docs Theme 1.0.0` reference package, while the reference remains unchanged as an audit source.

## Current release and acceptance

Docspress **0.9.1** is installed on [the demonstration site](https://wordpress.im/docspress/). The [maturity upgrade task book](plan/DOCSPRESS-MATURITY-UPGRADE-PLAN-2026-10-23.md) has been implemented: default left/right panels on all templates, independent header menu, article-owned TOC, native widgets, toolbar-safe geometry, mobile reading and durable-library navigation. See the [final acceptance and deployment report](docs/audits/2026-10-23-upgrade/ACCEPTANCE.md) for scope, evidence, checksums, limitations and rollback.

This is a self-hosted maturity candidate, not WordPress.org approval. Physical-phone acceptance was explicitly replaced by technical/browser simulation. The original October 24 delivery did not publish to GitHub. On October 30 the owner authorized synchronizing this accepted version to the existing repository and release assets; WordPress.org submission remains out of scope.

## Imported reference

- Original package: local-only `reference/humanities-docs-theme-v1.0.0.zip`
- Extracted reference: local-only `reference/humanities-docs/`
- SHA-256: `45afb447508966aec48a502d82c873e1a89eae4faa564ca26dd32c2d75834db2`
- Package size: 52 files, 209,323 uncompressed bytes

The archive was downloaded from the supplied ChatGPT project conversation and then copied into this project. The extracted reference is immutable; the deployable Docspress source is maintained separately.

## Current scope

This subproject contains the local reference audit, design and code reviews, a Docspress-owned theme source tree, focused tests, a package script, and the reviewed demo-site content source. The public Git boundary excludes `reference/` and generated `build/` artifacts; release ZIPs are verified locally and can be attached as GitHub Release assets.

## Implemented theme boundary

- WordPress Classic Theme, using native Pages and the normal Classic Theme hierarchy.
- A configurable Docs Root Page whose descendants form the left Docs Tree.
- Page `menu_order` and title sorting for stable navigation.
- H2/H3 parsing for the article table of contents and section treatment.
- Depth-first Previous / Next links.
- A quiet, responsive reading layout with desktop rails and independent left/right mobile drawers; TOC stays inside its article.
- Three native widget areas and three menu locations, preserving existing sidebar-1 widget instances.
- Safe fallback rendering for ordinary pages, posts, archives, search, comments, and 404 pages.

The companion editor plugin is not part of this import. It should be treated as a separate work item after the theme boundary and editing requirements are confirmed.

## Working files

- `theme/`: deployable Classic Theme source.
- `site-content/`: Pages and Posts for the isolated `wordpress.im/docspress/` demo site; never included in the theme ZIP.
- `scripts/check-theme.sh`: PHP/JavaScript/reference-identity/helper-test checks.
- `scripts/check-content.sh`: manifest and content-source checks.
- `scripts/package-release.sh`: one-folder ZIP and SHA-256 generation.
- `build/docspress-0.9.1.zip`: current verified and deployed package; 0.9.0 and 0.2.4 packages remain available for rollback.
