# Docspress 0.9.1 GitHub synchronization

The owner authorized repository synchronization on 2026-10-30, superseding the October 24 delivery's no-GitHub boundary for this explicit follow-up only.

- Target: existing `wp-im/docspress`, public, `origin` verified against GitHub.
- Site: `https://wordpress.im/docspress/`, already running the accepted 0.9.1 source.
- Revalidation: PHP/JS syntax, helper tests, 31 content sources, `git diff --check`, and all 10 public-asset SHA comparisons passed.
- Theme ZIP: `docspress-0.9.1.zip`, SHA-256 `74a7f4cb0da1024546e99b7f934d6fab93f87161416c1425aec756ca1258a967`.
- Reference packages and production content exports remain local-only; no WordPress.org submission.

The prior full runtime/source evidence remains in `docs/audits/2026-10-23-upgrade/ACCEPTANCE.md`; its no-push statement describes the original delivery, not this authorized follow-up. No redundant production installation is needed when the verified deployment already matches.

Repository synchronization completed with implementation commit `647bb8fa33d393c74597dcc095b6769e7de58d3f` and annotated tag `v0.9.1`; subsequent main-branch commits record verification only. Initial GitHub Release requests returned HTTP 500. On October 31 the release service accepted the same verified tag and package: <https://github.com/wp-im/docspress/releases/tag/v0.9.1>. The published installation asset reports the matching SHA-256 shown above. All ten production public assets were rechecked against local source and still match; no redundant production installation was performed.
