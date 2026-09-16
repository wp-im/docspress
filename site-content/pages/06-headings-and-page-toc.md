---
type: page
title: Headings and page TOC
slug: headings-and-page-toc
parent: content-model
menu_order: 30
template: default
excerpt: H2 and H3 headings become semantic anchors and a quiet on-this-page index.
---

<h2>Use H2 for the document's main turns</h2>
<p>Docspress decorates H2 headings as the major sections of an article. H3 headings sit beneath them as subsections. The page TOC is generated from those two levels.</p>

<h3>Keep the hierarchy honest</h3>
<p>Do not choose a heading level for size. Choose it for structure. The CSS supplies the visual scale, while the HTML communicates the outline to browsers and assistive technology.</p>

<h2>Anchors are stable enough to share</h2>
<p>Existing IDs are preserved. Missing IDs are generated from the heading text, and duplicates receive a numeric suffix. This keeps pasted editorial HTML useful without requiring manual anchor maintenance.</p>
