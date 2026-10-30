/** Anonymous HTTP contracts, separate from visual/interactive browser acceptance. */
import assert from 'node:assert/strict';

const base = process.env.DOCSPRESS_QA_URL || 'http://localhost:8914';
assert(['localhost', '127.0.0.1'].includes(new URL(base).hostname), 'Disposable QA sites only.');
const cases = [
  '/', '/docspress/content-model/', '/docspress/reading-system/reading-surface/',
  '/reading-fixture-zh/', '/reading-fixture-ja/', '/reading-fixture-en/', '/?p=43',
  '/notes/', '/category/uncategorized/', '/?author=1', '/2026/',
  '/?s=reading', '/?s=ZZZXQNONEXISTENT', '/definitely-not-a-page/',
  '/docspress/protected-fixture/', '/?p=1171', '/?p=1169', '/?p=1148',
  '/?p=1178', '/?p=1000', '/?p=1788', '/?p=1783',
];
for (const path of cases) {
  const response = await fetch(base + path);
  const html = await response.text();
  assert.equal((html.match(/id="docspress-leftbar"/g) || []).length, 1, `${path}: leftbar`);
  assert.equal((html.match(/id="docspress-rightbar"/g) || []).length, 1, `${path}: rightbar`);
  assert.equal((html.match(/<main\b/g) || []).length, 1, `${path}: main landmark`);
  assert([...html.matchAll(/<aside\b[^>]*>[\s\S]*?<\/aside>/g)].every(match => !match[0].includes('class="docspress-toc')), `${path}: TOC must not be in a sidebar`);
  assert(!html.includes('PRIVATE_FIXTURE_DO_NOT_LEAK'), `${path}: private tree entry`);
  assert(!html.includes('PROTECTED_SECRET_BODY'), `${path}: protected body`);
  assert(!html.includes('PROTECTED_SECRET_HEADING'), `${path}: protected heading`);
  assert(!/Fatal error|Warning:|Parse error/.test(html), `${path}: runtime errors`);
  assert.equal(response.status, path === '/definitely-not-a-page/' ? 404 : 200, path);
  console.log(`${response.status} ${path}: PASS`);
}
console.log(`Docspress anonymous HTTP contracts: ${cases.length} PASS`);
