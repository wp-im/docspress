/** Verify shipped public assets against the deployed theme, without authentication. */
import fs from 'node:fs';
import crypto from 'node:crypto';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
const project = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const base = 'https://wordpress.im/wp-content/themes/docspress/';
const version = fs.readFileSync(path.join(project, 'theme/style.css'), 'utf8').match(/^Version:\s*(.+)$/m)[1].trim();
const files = ['style.css','assets/css/main.css','assets/css/editor.css','assets/js/navigation.js','rtl.css','screenshot.png','readme.txt','README.md','languages/docspress.pot','LICENSE'];
const sha = data => crypto.createHash('sha256').update(data).digest('hex');
const results = [];
for (const file of files) {
  const response = await fetch(`${base}${file}?docspress-verify=${encodeURIComponent(version)}`);
  const body = Buffer.from(await response.arrayBuffer());
  const local = sha(fs.readFileSync(path.join(project, 'theme', file)));
  const production = sha(body);
  const match = response.ok && local === production;
  results.push({ file, status: response.status, bytes: body.length, local, production, match });
  if (!match) process.exitCode = 1;
}
console.log(JSON.stringify(results, null, 2));
