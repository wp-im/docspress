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
<p>Existing unique IDs are preserved, including Unicode IDs. Missing IDs are generated from the heading text; collisions with other headings or content IDs receive a numeric suffix. Inline emphasis, links, language attributes and grouped blocks survive heading decoration.</p>

<h3>Keep important links explicit</h3>
<p>A generated ID changes when its heading text changes. For a frequently cited section, set an explicit HTML anchor in the Block Editor's Advanced settings or an id attribute in the Classic Editor. Keep that ID when revising the title. An anchor is a public address, not a disposable styling hook.</p>

<h2>Contents belong to this article</h2>
<p>The expandable On this page index is inside the article, never in the right widget panel. It appears when at least two eligible headings are present. Appearance &gt; Customize &gt; Docspress lets the site owner choose H2, H2 and H3, or no article contents. Password-protected bodies do not expose headings before the reader unlocks the document.</p>
