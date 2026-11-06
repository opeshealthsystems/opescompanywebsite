import assert from 'node:assert/strict';
import { createHash } from 'node:crypto';
import { readFile, stat } from 'node:fs/promises';
import { basename, dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import test from 'node:test';

const packageRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const repositoryRoot = resolve(packageRoot, '..', '..');
const packageJson = JSON.parse(await readFile(resolve(packageRoot, 'package.json'), 'utf8'));
const readme = await readFile(resolve(packageRoot, 'README.md'), 'utf8');
const contract = JSON.parse(await readFile(resolve(repositoryRoot, 'docs/api/openapi.json'), 'utf8'));
const metadata = JSON.parse(await readFile(resolve(repositoryRoot, 'public/downloads/opes-api-sdk.json'), 'utf8'));
const archiveName = `opes-health-api-client-${packageJson.version}.tgz`;
const archivePath = resolve(repositoryRoot, 'public/downloads', archiveName);

test('SDK release docs, contract, archive and published metadata stay aligned', async () => {
  assert.equal(metadata.name, packageJson.name);
  assert.equal(metadata.version, packageJson.version);
  assert.equal(basename(metadata.url), archiveName);
  assert.match(readme, new RegExp(`preview ${packageJson.version.replaceAll('.', '\\.')}\\b`));
  assert.match(readme, new RegExp(`OpenAPI ${contract.info.version.replaceAll('.', '\\.')}\\b`));
  assert.ok(readme.includes(`npm install ./${archiveName}`), 'README install command must match the downloadable archive');

  const archive = await readFile(archivePath);
  const checksum = createHash('sha256').update(archive).digest('hex');
  const checksumFile = await readFile(`${archivePath}.sha256`, 'utf8');
  assert.equal(archive.length, metadata.bytes);
  assert.equal(checksum, metadata.sha256);
  assert.match(checksumFile, new RegExp(`^${checksum}\\s+${archiveName}\\s*$`));
  assert.equal((await stat(archivePath)).isFile(), true);
});
