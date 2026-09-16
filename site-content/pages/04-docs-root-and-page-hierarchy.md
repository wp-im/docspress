---
type: page
title: Docs Root and Page hierarchy
slug: docs-root-and-page-hierarchy
parent: content-model
menu_order: 10
template: default
excerpt: One root Page defines the document space; its descendants remain portable WordPress content.
---

<h2>One explicit boundary</h2>
<p>The Docs Root is a theme setting, not a hidden convention. This lets a site have a landing Page, a documentation tree, and ordinary Pages without guessing which content belongs to the system.</p>

<h2>Parent relationships are the outline</h2>
<p>A child Page is part of the tree when it is a published descendant of the configured root. Pages outside that subtree are rendered through the normal WordPress Page template.</p>

<div class="docspress-fact-grid"><div class="docspress-fact"><b>1</b><span>selected root</span></div><div class="docspress-fact"><b>n</b><span>published descendants</span></div><div class="docspress-fact"><b>0</b><span>custom post types required</span></div></div>
