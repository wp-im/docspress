---
type: page
title: Fallback behavior
slug: fallback-behavior
parent: classic-theme-architecture
menu_order: 30
template: default
excerpt: A site should remain readable when the Docs Root is empty, a menu is missing, or content is ordinary.
---

<h2>Before configuration</h2>
<p>The theme can activate without a Docs Root. The tree shows a useful editor-only hint, while visitors still receive a normal WordPress surface.</p>

<h2>Without menus</h2>
<p>The global and footer menu locations remain optional. The header keeps the site identity and search form; a missing menu does not produce a broken navigation shell.</p>

<h2>Without headings</h2>
<p>A document with no H2 or H3 headings simply has no page TOC. Its title, excerpt, content, and Previous / Next navigation remain intact.</p>
