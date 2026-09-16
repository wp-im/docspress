---
type: page
title: Template hierarchy
slug: template-hierarchy
parent: classic-theme-architecture
menu_order: 10
template: default
excerpt: Docspress uses the familiar hierarchy and falls back gracefully when a specialized view is not needed.
---

<h2>Special views are narrow</h2>
<p>The front page coordinates the Docs Root and section directory. The optional Docs Section Index template lists a section's children. A document Page uses the article view only when it belongs to the selected tree.</p>

<h2>Everything else falls back</h2>
<p>Ordinary Pages, Posts, archives, search, comments, pagination, and 404 responses continue through their standard Classic Theme templates. A site does not become unusable because one setting is empty.</p>

<h2>Parts keep responsibilities legible</h2>
<p>Navigation, content, layout, and the pure Docs helpers are separated into small files. That makes review and later replacement less risky than one monolithic template.</p>
