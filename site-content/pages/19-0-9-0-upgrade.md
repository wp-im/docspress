---
type: page
title: 0.9.0 maturity upgrade
slug: 0-9-0-maturity-upgrade
parent: release-notes
menu_order: 20
template: default
excerpt: A shared two-panel reading system, independent header navigation, native widgets, and toolbar-aware mobile reading.
---

<h2>A consistent shell on every template</h2>
<p>Docspress 0.9.0 brings the library panel and right widget panel to ordinary Posts and Pages, indexes, archives, search and 404 pages. The Primary Menu remains independent. An article's On this page contents is part of that article, not a widget in the right panel.</p>

<h2>Responsive navigation without duplicate content</h2>
<p>At medium widths the right panel becomes a drawer; on phones both panels do. The left panel is bounded at 70% of the viewport and the right at 88%. Both have a visible close control, a backdrop, Escape, keyboard focus containment and focus return. WordPress's actual visible admin toolbar height is shared by the header, local controls, drawers and anchor offsets.</p>

<h2>Native configuration and durable content</h2>
<p>Use the three native widget areas and independent menu locations. Existing sidebar-1 widgets retain their IDs. Reading width, type size, article contents and modified-date display are available through the Customizer. Stable explicit anchors, non-destructive heading decoration, search context, published-only trees and bounded large-tree rendering support a long-lived reference collection.</p>

<h2>A candidate, not a directory approval</h2>
<p>This release is a maturity candidate for the self-hosted demonstration site. Automated checks, isolated WordPress tests and desktop viewport inspection are separate from physical iOS/Android, cross-browser and human accessibility acceptance. No WordPress.org approval or completed 1.0 review is claimed.</p>

<h2>0.9.1 final boundary corrections</h2>
<p>The current demonstration runs 0.9.1. Untitled Pages keep meaningful labels in the tree, chapter indexes, breadcrumbs and reading-order links. Directory descriptions honor native excerpt filters, and request caches keep locale-specific navigation separate. These changes do not rename URLs or migrate content.</p>
<p>The project owner chose technical and browser-simulation acceptance instead of physical-phone testing. Chromium, Safari and Firefox rendering, keyboard navigation, toolbar geometry, minimum PHP/WordPress compatibility and package/source consistency were checked. A complete human screen-reader assessment and WordPress.org submission remain separate future work.</p>
