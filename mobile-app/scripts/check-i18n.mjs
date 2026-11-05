// Fails when a t('…') key in src/ has no French entry, or when the dictionary holds unused keys.
// Usage: node scripts/check-i18n.mjs [--list-missing]
import { readFileSync, readdirSync, statSync } from 'node:fs';
import { join } from 'node:path';

const root = new URL('../src/', import.meta.url).pathname.replace(/^\/([A-Za-z]:)/, '$1');
const files = [];
const walk = dir => readdirSync(dir).forEach(name => {
  const path = join(dir, name);
  if (statSync(path).isDirectory()) walk(path);
  else if (/\.(tsx?|jsx?)$/.test(name) && !name.startsWith('i18n')) files.push(path);
});
walk(root);

const unescape = s => s.replace(/\\(['\\])/g, '$1');
const used = new Set();
for (const file of files) {
  for (const match of readFileSync(file, 'utf8').matchAll(/\bt\('((?:[^'\\]|\\.)*)'/g)) used.add(unescape(match[1]));
}

const dictSource = readFileSync(join(root, 'i18n.fr.ts'), 'utf8');
const dict = new Set([...dictSource.matchAll(/^\s*'((?:[^'\\]|\\.)*)':/gm)].map(m => unescape(m[1])));
// Keys reached through t(variable): feature titles, labels and humanized API codes.
const dynamic = new Set([...dictSource.matchAll(/^\s*'((?:[^'\\]|\\.)*)':.*\/\/ dynamic/gm)].map(m => unescape(m[1])));

const missing = [...used].filter(key => !dict.has(key)).sort();
const unused = [...dict].filter(key => !used.has(key) && !dynamic.has(key)).sort();
if (process.argv.includes('--list-missing')) { console.log(JSON.stringify(missing, null, 1)); process.exit(0); }
if (missing.length) console.error(`Missing French translations (${missing.length}):\n  ${missing.join('\n  ')}`);
if (unused.length) console.error(`Unused dictionary keys (${unused.length}) – remove or mark "// dynamic":\n  ${unused.join('\n  ')}`);
console.log(`i18n: ${used.size} keys used, ${dict.size} translated.`);
process.exit(missing.length || unused.length ? 1 : 0);
