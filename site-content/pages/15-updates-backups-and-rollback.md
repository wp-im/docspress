---
type: page
title: Updates, backups, and rollback
slug: updates-backups-and-rollback
parent: accessibility-and-maintenance
menu_order: 30
template: default
excerpt: A theme update is a small deployment: verify the package, preserve content, and keep the previous ZIP.
---

<h2>Verify before upload</h2>
<p>Run the local checks, inspect the ZIP's top-level folder, and record its SHA-256. Upload the exact ZIP that passed those checks.</p>

<h2>Keep content outside the theme</h2>
<p>The Docspress Pages, Posts, media, menus, and Customizer settings belong to the site. The theme package contains templates and assets only, so changing the theme does not erase editorial work.</p>

<h2>Keep the previous state</h2>
<p>Before activation, note the active theme and save the previous package or a recoverable backup. If the new preview fails a browser check, reactivate the prior theme and investigate locally.</p>
