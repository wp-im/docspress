---
type: page
title: Theme and plugin boundary
slug: theme-and-plugin-boundary
parent: classic-theme-architecture
menu_order: 20
template: default
excerpt: The theme owns presentation and navigation; editorial extensions belong in optional plugins.
---

<h2>What belongs in the theme</h2>
<p>Docspress owns the reading surface, the Docs Tree renderer, heading anchors, page TOC, Previous / Next links, menus, widgets, and Customizer settings needed for that presentation.</p>

<h2>What does not belong in the theme</h2>
<p>Business data, custom post types, shortcodes, editorial workflows, and durable content migrations should live in plugins. Those features should survive a theme change.</p>

<h3>Optional integrations stay optional</h3>
<p>An editor helper can add convenience classes or patterns later, but the core theme must still render ordinary paragraphs, headings, media, tables, quotes, and lists on its own.</p>
