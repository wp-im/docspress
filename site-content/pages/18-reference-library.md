---
type: page
title: Maintaining a reference library
slug: maintaining-a-reference-library
parent: accessibility-and-maintenance
menu_order: 40
template: default
excerpt: Preserve addresses, sources, editorial context, and a useful route back to every document.
---

<h2>Preserve public addresses</h2>
<p>Choose descriptive Page slugs and stable HTML anchors. Keep old anchors when renaming a heading. Moving a Page under a new parent can change its URL: plan redirects with WordPress or a suitable plugin before reorganizing a published collection. A theme cannot promise that links survive arbitrary content migrations.</p>

<h2>Separate publication, modification, and review</h2>
<p>Docspress displays native publication and last-modified dates. Updated means the WordPress record changed; it does not certify that an editor rechecked every claim. For time-sensitive material, write a reviewed-on note, source dates, limitations, and the next review responsibility in the document itself.</p>

<h2>Make the collection findable</h2>
<p>Use Page parents and Order for a deliberate hierarchy, excerpts for directory summaries, and ordinary Posts for dated observations. Breadcrumbs and previous/next links provide context; search results include content type and public parent context. Categories, tags, and feeds remain native WordPress features rather than private theme records.</p>

<h2>Keep the tree proportional</h2>
<p>The tree is assembled once per request from published Pages and sorted by Order, title, then stable ID. Large collections shorten unopened deep branches into section links while retaining the current path. For genuinely different collections or advanced indexing, choose a separate plugin after a concrete editorial need appears.</p>

<h2>Keep content independent</h2>
<p>Back up the database, uploads, and configuration as well as the theme ZIP. Test a theme switch on a staging copy: the documents should still be ordinary readable Pages and Posts. Collections, annotations, redirects, full-text indexes, and versioned records belong in content or plugins, not in a theme-only database scheme.</p>
