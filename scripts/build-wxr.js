#!/usr/bin/env node

const fs = require('node:fs');
const path = require('node:path');

const projectRoot = path.resolve(__dirname, '..');
const contentRoot = path.join(projectRoot, 'site-content');
const manifestPath = path.join(contentRoot, 'manifest.tsv');
const outputPath = path.join(contentRoot, 'docspress-demo.xml');

function cdata(value) {
  return `<![CDATA[${String(value).replaceAll(']]>', ']]]]><![CDATA[>')}]]>`;
}

function escapeXml(value) {
  return String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&apos;');
}

function parseSource(relativeFile) {
  const source = fs.readFileSync(path.join(contentRoot, relativeFile), 'utf8');
  const parts = source.split(/^---\s*$/m);
  if (parts.length < 3) {
    throw new Error(`Missing front matter: ${relativeFile}`);
  }

  const metadata = {};
  for (const line of parts[1].trim().split('\n')) {
    const separator = line.indexOf(':');
    if (separator < 0) continue;
    metadata[line.slice(0, separator).trim()] = line.slice(separator + 1).trim();
  }

  return { metadata, body: parts.slice(2).join('---').trim() };
}

function readManifest() {
  return fs.readFileSync(manifestPath, 'utf8')
    .trimEnd()
    .split('\n')
    .slice(1)
    .map((line) => {
      const [type, title, slug, parent, menuOrder, template, excerpt, file] = line.split('\t');
      return { type, title, slug, parent, menuOrder, template, excerpt, file };
    });
}

function postItem(entry, index, ids, source) {
  const postId = 1001 + index;
  const parentId = entry.parent ? (ids.get(entry.parent) || 0) : 0;
  const postType = entry.type === 'post' ? 'post' : 'page';
  const templateMeta = entry.template === 'Docs Section Index'
    ? `\n\t\t<wp:postmeta>\n\t\t\t<wp:meta_key>_wp_page_template</wp:meta_key>\n\t\t\t<wp:meta_value>${cdata('page-templates/docs-section.php')}</wp:meta_value>\n\t\t</wp:postmeta>`
    : '';

  return `\t<item>
\t\t<title>${cdata(entry.title)}</title>
\t\t<link>https://wordpress.im/docspress/?p=${postId}</link>
\t\t<pubDate>Wed, 16 Sep 2026 00:00:00 +0000</pubDate>
\t\t<dc:creator>${cdata('docspress')}</dc:creator>
\t\t<guid isPermaLink="false">https://wordpress.im/docspress/?p=${postId}</guid>
\t\t<description></description>
\t\t<content:encoded>${cdata(source.body)}</content:encoded>
\t\t<excerpt:encoded>${cdata(entry.excerpt)}</excerpt:encoded>
\t\t<wp:post_id>${postId}</wp:post_id>
\t\t<wp:post_date>2026-09-16 00:00:00</wp:post_date>
\t\t<wp:post_date_gmt>2026-09-16 00:00:00</wp:post_date_gmt>
\t\t<wp:comment_status>closed</wp:comment_status>
\t\t<wp:ping_status>closed</wp:ping_status>
\t\t<wp:post_name>${escapeXml(entry.slug)}</wp:post_name>
\t\t<wp:status>publish</wp:status>
\t\t<wp:post_parent>${parentId}</wp:post_parent>
\t\t<wp:menu_order>${Number(entry.menuOrder) || 0}</wp:menu_order>
\t\t<wp:post_type>${postType}</wp:post_type>
\t\t<wp:post_password></wp:post_password>
\t\t<wp:is_sticky>0</wp:is_sticky>${templateMeta}
\t</item>`;
}

const entries = readManifest();
const ids = new Map(entries.map((entry, index) => [entry.slug, 1001 + index]));
const items = entries.map((entry, index) => postItem(entry, index, ids, parseSource(entry.file)));

const xml = `<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0"
\txmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
\txmlns:content="http://purl.org/rss/1.0/modules/content/"
\txmlns:wfw="http://wellformedweb.org/CommentAPI/"
\txmlns:dc="http://purl.org/dc/elements/1.1/"
\txmlns:wp="http://wordpress.org/export/1.2/">
\t<channel>
\t\t<title>Docspress</title>
\t\t<link>https://wordpress.im/docspress/</link>
\t\t<description>Docspress self-documentation demo site</description>
\t\t<pubDate>Wed, 16 Sep 2026 00:00:00 +0000</pubDate>
\t\t<language>zh-CN</language>
\t\t<wp:wxr_version>1.2</wp:wxr_version>
\t\t<wp:base_site_url>https://wordpress.im</wp:base_site_url>
\t\t<wp:base_blog_url>https://wordpress.im/docspress</wp:base_blog_url>
${items.join('\n')}
\t</channel>
</rss>
`;

fs.writeFileSync(outputPath, xml);
console.log(`${outputPath} (${entries.length} entries)`);
