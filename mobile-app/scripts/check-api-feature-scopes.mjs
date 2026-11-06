import { readFile, readdir } from 'node:fs/promises';
import { join, resolve } from 'node:path';

const root = resolve(import.meta.dirname, '../..');
const source = await readFile(resolve(root, 'mobile-app/src/context/SessionContext.tsx'), 'utf8');
const contract = JSON.parse(await readFile(resolve(root, 'docs/api/openapi.json'), 'utf8'));
const problems = [];
const features = [...source.matchAll(/^\s+(\w+): \{ id: '[^']+', ([^\n]+)\},$/gm)];

function scopes(operation) {
  return (operation?.security ?? []).flatMap(requirement => Object.values(requirement).flat());
}

function normalized(path) {
  return path.replace(/\$\{[^}]+\}/g, '{param}').replace(/\{[^}]+\}/g, '{param}');
}

function checkRoute(path, method, sourceName) {
  if (!path.startsWith('/')) return;
  const contractPath = Object.keys(contract.paths).find(candidate => normalized(candidate) === normalized(path));
  if (!contractPath || !contract.paths[contractPath][method.toLowerCase()]) {
    problems.push(`${sourceName}: ${method.toUpperCase()} ${path} is missing from OpenAPI ${contract.info.version}`);
  }
}

for (const [, key, fields] of features) {
  const pathMatch = fields.match(/endpoint: '([^']+)'/);
  if (!pathMatch) continue;
  const endpoint = pathMatch[1].startsWith('/') ? pathMatch[1] : `/${pathMatch[1]}`;
  const pathItem = contract.paths[endpoint];
  if (!pathItem?.get) {
    problems.push(`${key}: GET ${endpoint} is missing from OpenAPI`);
    continue;
  }
  const readScope = fields.match(/readScope: '([^']+)'/)?.[1];
  if (readScope && !scopes(pathItem.get).includes(readScope)) {
    problems.push(`${key}: readScope ${readScope} does not match GET ${endpoint}`);
  }
  const mutating = Object.entries(contract.paths)
    .filter(([path]) => path === endpoint || path.startsWith(`${endpoint}/`))
    .flatMap(([path, item]) => Object.entries(item)
      .filter(([method]) => ['post', 'put', 'patch', 'delete'].includes(method))
      .map(([method, operation]) => ({ path, method, scopes: scopes(operation) })));
  const writeScope = fields.match(/writeScope: '([^']+)'/)?.[1];
  const protectedMutations = mutating.filter(operation => operation.scopes.length > 0);
  if (protectedMutations.length && !writeScope) {
    problems.push(`${key}: protected API mutations exist but the mobile feature declares no writeScope (${protectedMutations.map(item => `${item.method.toUpperCase()} ${item.path}`).join(', ')})`);
  } else if (writeScope && !protectedMutations.some(operation => operation.scopes.includes(writeScope))) {
    problems.push(`${key}: writeScope ${writeScope} does not match a protected mutation under ${endpoint}`);
  }
}

const requestPattern = /session\.request(?:<[^)]{1,300}>)?\s*\(\s*([`'\"])(.*?)\1(?:\s*,\s*([`'\"])(GET|POST|PUT|PATCH|DELETE)\3)?/gs;
async function sourceFiles(directory) {
  const entries = await readdir(directory, { withFileTypes: true });
  const files = [];
  for (const entry of entries) {
    const path = join(directory, entry.name);
    if (entry.isDirectory()) files.push(...await sourceFiles(path));
    else if (/\.(tsx?|jsx?)$/.test(entry.name)) files.push(path);
  }
  return files;
}

for (const path of await sourceFiles(resolve(root, 'mobile-app/src'))) {
  const text = await readFile(path, 'utf8');
  for (const match of text.matchAll(requestPattern)) {
    checkRoute(match[2], match[4] ?? 'GET', path.replace(`${root}/`, ''));
  }
}

const actionForms = await readFile(resolve(root, 'mobile-app/src/components/ActionFormScreen.tsx'), 'utf8');
const cases = [...actionForms.matchAll(/case '([^']+)'\s*:\s*([\s\S]*?)(?=\n\s*case '|\n\s*default:)/g)];
for (const [, name, body] of cases) {
  const routes = [...body.matchAll(/path\s*=\s*([`'\"])([\s\S]*?)\1/g)];
  if (!routes.length) continue;
  const methods = [...body.matchAll(/method\s*=\s*'(GET|POST|PUT|PATCH|DELETE)'/g)];
  checkRoute(routes.at(-1)[2], methods.at(-1)?.[1] ?? 'POST', `ActionFormScreen ${name}`);
}

if (problems.length) {
  console.error(problems.join('\n'));
  process.exitCode = 1;
} else {
  console.log(`Validated ${features.length} mobile features and their declared request routes against OpenAPI ${contract.info.version}.`);
}
