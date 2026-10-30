---
type: page
title: Responsive navigation
slug: responsive-navigation
parent: reading-system
menu_order: 20
template: default
excerpt: The same information architecture moves from persistent sidebars to focused mobile drawers.
---

<h2>Wide screens keep the map visible</h2>
<p>Every template has a left library panel and a right widget panel by default. At 1440 CSS pixels and above, both stay beside the reading surface. The footer starts after the complete shell, not behind a fixed sidebar background.</p>

<h2>Tablet keeps the article grammar</h2>
<p>Below 1440 pixels, the right panel moves into a drawer opened by the Sidebar button. Below 1024 pixels, Library opens the left drawer too. Medium screens use a plain reading column; decorative heading rails never take space away from a narrow article.</p>

<h2>Phone prioritizes one task</h2>
<p>The left drawer enters from the left and occupies no more than 70% of the viewport. The right drawer enters from the right and uses at most 88%, below the 90% limit. Each has a visible close button, a modal backdrop, keyboard focus containment, Escape, and focus return. Opening a panel never duplicates its widgets.</p>

<h2>The article owns its contents</h2>
<p>The On this page disclosure belongs inside the article, below its heading and metadata. It is not a right-sidebar widget. The header Menu is a separate disclosure for the site's primary menu and search, not another copy of the Library tree.</p>

<h2>Logged-in reading is still reading</h2>
<p>The header, local controls, panels, and anchor offset share the actual visible toolbar height. On small phones the WordPress toolbar scrolls away; Docspress reduces its offset to zero instead of leaving an empty 46-pixel strip. Without the navigation script, the menus and panels remain available in normal document flow.</p>
