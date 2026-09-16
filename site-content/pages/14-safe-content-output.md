---
type: page
title: Safe content output
slug: safe-content-output
parent: accessibility-and-maintenance
menu_order: 20
template: default
excerpt: Docspress preserves WordPress filtering and escapes theme-generated values at their output boundary.
---

<h2>Content keeps WordPress filtering</h2>
<p>Page content passes through <code>the_content</code> before the theme decorates H2 and H3 elements. This keeps normal embeds, paragraphs, links, and editor output in the WordPress pipeline.</p>

<h2>Generated values are escaped</h2>
<p>Titles, URLs, IDs, labels, theme settings, and dates are escaped in their final HTML contexts. The parser only reuses already-filtered inner HTML for headings; it does not turn arbitrary editor content into new markup.</p>

<h2>Security has a boundary</h2>
<p>The theme does not add a custom write endpoint, remote request, direct SQL query, or file operation. That keeps the first release easier to inspect and easier to maintain.</p>
