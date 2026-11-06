import { test } from 'node:test';
import assert from 'node:assert/strict';
import { OpesBusinessApiClient, OpesClient, OpesApiError, createIdempotencyKey } from '../src/index.js';

const oauth = { clientId: 'fictional-client', clientSecret: 'fictional-private-secret', scopes: ['catalogue:read', 'integration:read'] };
const json = (body, status = 200, headers = {}) => new Response(JSON.stringify(body), { status, headers: { 'Content-Type': 'application/json', ...headers } });
const catalogue = { data: [{ slug: 'example', name: 'Example', category: 'core', url: 'https://example.test/product' }], meta: { page: 1, per_page: 20, total: 1 } };
const token = (value = 'fictional-token') => json({ access_token: value, token_type: 'Bearer', expires_in: 899 });

test('concurrent resource calls share token issuance and keep secrets out of URLs/serialized client', async () => {
  let issued = 0; let resources = 0;
  const client = new OpesClient({ oauth, fetch: async (url, options) => {
    assert.equal(options.redirect, 'error'); assert.equal(options.credentials, 'omit');
    assert.ok(options.signal instanceof AbortSignal); assert.ok(!url.includes(oauth.clientSecret));
    if (url.endsWith('/oauth/token')) {
      issued++; const fields = new URLSearchParams(options.body);
      assert.equal(fields.get('grant_type'), 'client_credentials'); assert.equal(fields.get('scope'), 'catalogue:read integration:read');
      await new Promise(resolve => setTimeout(resolve, 5)); return token();
    }
    resources++; assert.equal(options.headers.Authorization, 'Bearer fictional-token'); return json(catalogue);
  } });
  const result = await Promise.all([client.catalogue(), client.catalogue({ locale: 'fr', perPage: 2 }), client.catalogue()]);
  assert.equal(issued, 1); assert.equal(resources, 3); assert.deepEqual(result[0], catalogue);
  assert.ok(!JSON.stringify(client).includes(oauth.clientSecret));
});

test('enquiry requires a stable key and sends no OAuth credentials', async () => {
  const key = createIdempotencyKey(); let sent = 0;
  const integrationApplication = { application_name: 'Fictional scheduler', access_type: 'delegated', redirect_uri: 'https://client.example/callback', requested_scopes: ['crm:read'], use_case: 'Read the fictional account schedule.' };
  const client = new OpesClient({ oauth, fetch: async (url, options) => {
    sent++; assert.ok(url.endsWith('/enquiries')); assert.equal(options.headers['Idempotency-Key'], key);
    assert.equal(options.headers.Authorization, undefined); assert.equal(options.method, 'POST');
    assert.deepEqual(JSON.parse(options.body).integration_application, integrationApplication);
    return json({ data: { status: 'recorded', delivery_status: 'sent' } }, 201, { 'Idempotency-Replayed': 'true' });
  } });
  await assert.rejects(client.submitEnquiry({}), TypeError);
  await assert.rejects(client.submitEnquiry({}, { idempotencyKey: key + '\n' }), TypeError);
  const response = await client.submitEnquiry({ name: 'Fictional', email: 'fictional@example.test', integration_application: integrationApplication }, { idempotencyKey: key });
  assert.equal(sent, 1); assert.equal(response.idempotencyKey, key); assert.equal(response.replayed, true);
});

test('ambiguous POST network failure is not retried and retains caller key', async () => {
  let calls = 0; const key = createIdempotencyKey();
  const client = new OpesClient({ fetch: async () => { calls++; throw new Error('transport failed'); } });
  await assert.rejects(client.submitEnquiry({}, { idempotencyKey: key }), error => error instanceof OpesApiError && error.status === 0 && error.idempotencyKey === key);
  assert.equal(calls, 1);
});

test('resource 401 refreshes once, then stops', async () => {
  let issued = 0; let resources = 0;
  const client = new OpesClient({ oauth, fetch: async url => {
    if (url.endsWith('/oauth/token')) { issued++; return token('fictional-' + issued); }
    resources++; return json({ title: 'Unauthorized', status: 401 }, 401);
  } });
  await assert.rejects(client.integration(), error => error.status === 401);
  assert.equal(issued, 2); assert.equal(resources, 2);
});

test('quota and scope errors expose safe diagnostics without credential or body leakage', async () => {
  const id = '35fbdaeb-6e1a-4eb5-91d0-c625bd10d748'; let resources = 0;
  const client = new OpesClient({ oauth, fetch: async url => {
    if (url.endsWith('/oauth/token')) return token();
    resources++; return json({ status: 429, secret: oauth.clientSecret }, 429, { 'Retry-After': '27', 'X-Request-ID': id });
  } });
  await assert.rejects(client.integration(), error => {
    assert.equal(error.retryAfter, 27); assert.equal(error.requestId, id); assert.equal(error.status, 429);
    assert.ok(!JSON.stringify(error).includes(oauth.clientSecret)); return true;
  });
  assert.equal(resources, 1);
});

test('invalid token response fails before any resource request', async () => {
  let calls = 0;
  const client = new OpesClient({ oauth, fetch: async () => { calls++; return json({ access_token: 'fictional', token_type: 'Bearer', expires_in: 0 }); } });
  await assert.rejects(client.integration(), error => error.code === 'invalid_token_response');
  assert.equal(calls, 1);
});

test('rejects insecure remote base URLs and invalid pagination', () => {
  assert.throws(() => new OpesClient({ baseUrl: 'http://remote.example.test/api/v1' }), TypeError);
  assert.throws(() => new OpesClient({ baseUrl: 'https://user:pass@example.test/api/v1' }), TypeError);
  const client = new OpesClient({ baseUrl: 'http://127.0.0.1:8087/api/v1' });
  assert.throws(() => client.catalogue({ perPage: 51 }), TypeError);
  assert.throws(() => client.catalogue({ locale: 'invalid' }), TypeError);
});

test('OAuth credential rejection is not blindly retried', async () => {
  let calls = 0;
  const client = new OpesClient({ oauth, fetch: async () => { calls++; return json({ error: 'invalid_client' }, 401); } });
  await assert.rejects(client.catalogue(), error => error.code === 'invalid_client' && error.status === 401);
  assert.equal(calls, 1);
});

test('machine client exposes its own quota snapshot', async () => {
  let requests = 0;
  const client = new OpesClient({ oauth, fetch: async url => {
    if (url.endsWith('/oauth/token')) return token();
    requests++;
    assert.ok(url.endsWith('/integration/quota'));
    return json({ data: { requests_today: 7, requests_this_month: 27, daily_limit: 10, daily_remaining: 2,
      monthly_limit: 100, monthly_remaining: 72, period: 'UTC calendar day and calendar month' } });
  } });
  const result = await client.integrationQuota();
  assert.equal(result.data.daily_remaining, 2);
  assert.equal(requests, 1);
});

test('public service catalogue selects the requested locale without OAuth credentials', async () => {
  let calls = 0;
  const client = new OpesClient({ fetch: async (url, options) => {
    calls++;
    assert.equal(url, 'https://opeshealthsystems.com/api/v1/service-offerings');
    assert.equal(options.headers['Accept-Language'], 'fr');
    assert.equal(options.headers.Authorization, undefined);
    return json({ data: { locale: 'fr', items: [{ id: 'cdss', name: 'Systèmes d’aide à la décision clinique (CDSS)' }] } });
  } });
  const result = await client.serviceOfferings({ locale: 'fr' });
  assert.equal(result.data.items[0].id, 'cdss');
  assert.equal(calls, 1);
  await assert.rejects(client.serviceOfferings({ locale: 'es' }), TypeError);
  assert.equal(calls, 1);
});

test('business operation client uses generated paths, delegated auth and command keys', async () => {
  const key = createIdempotencyKey(); const requestId = '8a8bced2-2135-462f-82af-5b083a90632f'; let calls = 0;
  const client = new OpesBusinessApiClient({ accessToken: 'fictional-delegated-token', fetch: async (url, options) => {
    calls++;
    assert.equal(url, 'https://opeshealthsystems.com/api/v1/finance/expenses/123/approve');
    assert.equal(options.method, 'POST'); assert.equal(options.redirect, 'error'); assert.equal(options.credentials, 'omit');
    assert.equal(options.headers.Authorization, 'Bearer fictional-delegated-token');
    assert.equal(options.headers['Idempotency-Key'], key);
    assert.equal(options.body, undefined);
    return json({ data: { id: 123, status: 'approved' } }, 200, { 'X-Request-ID': requestId });
  } });
  const result = await client.request('admin_finance_expenses_approve', { path: { expense: 123 }, idempotencyKey: key });
  assert.equal(calls, 1); assert.equal(result.data.data.status, 'approved'); assert.equal(result.requestId, requestId);

  const usageClient = new OpesBusinessApiClient({ accessToken: 'fictional-delegated-token', fetch: async (url, options) => {
    assert.equal(url, 'https://opeshealthsystems.com/api/v1/me/usage');
    assert.equal(options.method, 'GET');
    assert.equal(options.headers.Authorization, 'Bearer fictional-delegated-token');
    return json({ data: { requests_today: 2, requests_last_30_days: 9, daily_usage: [] } });
  } });
  const usage = await usageClient.request('readOwnBusinessUsage');
  assert.equal(usage.data.data.requests_last_30_days, 9);
});

test('business operation client refuses missing keys, path values and unknown query fields', async () => {
  const client = new OpesBusinessApiClient({ accessToken: 'fictional-token', fetch: async () => { throw new Error('must not send'); } });
  await assert.rejects(client.request('admin_finance_expenses_approve', { path: { expense: 1 } }), TypeError);
  await assert.rejects(client.request('admin_finance_expenses_approve', { idempotencyKey: createIdempotencyKey() }), /Missing path parameter/);
  await assert.rejects(client.request('admin_finance_expenses_approve', { path: { expense: 1 }, idempotencyKey: createIdempotencyKey(), query: { unexpected: 'value' } }), /Unexpected query parameter/);
  await assert.rejects(client.request('unknown_operation'), /Unknown OHS business operation/);
});

test('business API client rejects machine-client operations before requesting a delegated token', async () => {
  let tokenCalls = 0;
  const client = new OpesBusinessApiClient({ getAccessToken: async () => { tokenCalls++; return 'fictional-user-token'; }, fetch: async () => {
    throw new Error('machine-client operations must not use delegated transport');
  } });
  await assert.rejects(client.request('inspectIntegration'), /client-credentials token/);
  await assert.rejects(client.request('readIntegrationUsage'), /client-credentials token/);
  assert.equal(tokenCalls, 0);
});

test('business SDK manages owner-scoped machine clients with delegated auth and replay metadata', async () => {
  const calls = [];
  const client = new OpesBusinessApiClient({ accessToken: 'delegated-user-token', fetch: async (url, options) => {
    calls.push({ url, options });
    assert.equal(options.headers.Authorization, 'Bearer delegated-user-token');
    if (url.endsWith('/developer/machine-clients') && options.method === 'POST') {
      assert.equal(options.headers['Idempotency-Key'], 'machine-client-sdk-create-01');
      assert.deepEqual(JSON.parse(options.body), { name: 'Catalogue sync', scopes: ['catalogue:read'] });
      return json({ data: { client_id: 'machine-1', name: 'Catalogue sync', client_secret: 'one-time',
        token_url: 'https://opeshealthsystems.com/api/v1/oauth/token', scopes: ['catalogue:read'], secret_shown_once: true },
        meta: { operation: 'developer.machine_clients.create' }, messages: {} }, 201,
      { 'Idempotency-Replayed': 'true' });
    }
    return json({ data: { items: [], pagination: { current_page: 1, per_page: 20, total: 0, last_page: 1 },
      meta: { operation: 'developer.machine_clients.index' }, messages: {} } });
  } });

  const result = await client.registerDeveloperMachineClient({ name: 'Catalogue sync', scopes: ['catalogue:read'] },
    { idempotencyKey: 'machine-client-sdk-create-01' });
  assert.equal(result.data.data.client_secret, 'one-time');
  assert.equal(result.replayed, true);
  await client.listDeveloperMachineClients();
  assert.equal(calls[1].url, 'https://opeshealthsystems.com/api/v1/developer/machine-clients?page=1&per_page=20');
});
