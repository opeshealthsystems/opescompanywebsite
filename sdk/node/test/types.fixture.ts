import { OpesBusinessApiClient, createIdempotencyKey } from '../src/index.js';

const client = new OpesBusinessApiClient({ accessToken: 'type-fixture-token' });
const key = createIdempotencyKey();

void client.request('admin_finance_quotes_create', {
  body: { title: 'Clinic proposal', currency: 'XAF', items: [] },
  idempotencyKey: key,
});
void client.request('admin_finance_quotes_update', {
  path: { quote: 123 },
  body: { title: 'Updated proposal' },
  idempotencyKey: key,
});
void client.request('readOwnBusinessActivity', { query: { page: 1, per_page: 20 } });
void client.request('admin_resourceSummaries_metric', { query: { metric: 'tickets.open' } });
void client.request('issueDelegatedUserToken', {
  body: new URLSearchParams({ grant_type: 'authorization_code' }),
});

// @ts-expect-error Quote currency is a contract enum.
void client.request('admin_finance_quotes_create', { body: { title: 'Clinic proposal', currency: 'GBP', items: [] }, idempotencyKey: key });
// @ts-expect-error This quote update requires an idempotency key.
void client.request('admin_finance_quotes_update', { path: { quote: 123 }, body: { title: 'Updated proposal' } });
// @ts-expect-error A quote update requires the quote path parameter.
void client.request('admin_finance_quotes_update', { body: { title: 'Updated proposal' }, idempotencyKey: key });
// @ts-expect-error Unknown query fields are rejected by the generated contract types.
void client.request('readOwnBusinessActivity', { query: { unexpected: true } });
// @ts-expect-error Required query parameters are enforced by the contract types.
void client.request('admin_resourceSummaries_metric');
