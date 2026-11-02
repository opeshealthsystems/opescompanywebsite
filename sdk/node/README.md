# OHS server-side Node SDK — preview 0.96.0

Requires Node.js 22 or newer. This dependency-free preview package is distributed as a downloadable archive, not published to the npm registry. It is server-side code: never bundle client secrets into a browser or mobile application. This package contains all documented operation identifiers from OpenAPI 0.100.0. The machine-client usage operation returns the caller's own daily request totals for the trailing 30 UTC calendar days; it does not expose route-level or user-delegated traffic. The contract also adds server-validated pricing choices for public quote requests and reports requester receipt-delivery status.

Install the reviewed archive with `npm install ./opes-health-api-client-0.96.0.tgz`, then:

```js
import { OpesClient } from '@opes-health/api-client';

const client = new OpesClient({ oauth: {
  clientId: process.env.OPES_CLIENT_ID,
  clientSecret: process.env.OPES_CLIENT_SECRET,
  scopes: ['catalogue:read', 'integration:read'],
} });
const catalogue = await client.catalogue({ perPage: 10, locale: 'en' });
const identity = await client.integration();
const usage = await client.integrationUsage();
console.log(usage.data.requests_last_30_days, usage.data.daily_usage);
```

Request reviewed credentials from OHS. Specify only scopes granted to your client. Tokens are cached with an early-refresh margin; concurrent calls share one issuance request. A GET rejected with 401 refreshes once. Quota/scope/network failures are returned to the caller, not retried in a loop. Redirects are refused and each request has a configurable 15-second timeout (maximum 60 seconds). Plain HTTP is accepted only for localhost development.

For enquiry intake, OAuth credentials are unnecessary, but this SDK requires an explicit key:

```js
import { OpesClient, createIdempotencyKey } from '@opes-health/api-client';
const key = createIdempotencyKey(); // persist with the intended enquiry BEFORE sending
const result = await new OpesClient().submitEnquiry(
  { name: 'YOUR_NAME', email: 'YOUR_EMAIL', message: 'YOUR_ENQUIRY' },
  { idempotencyKey: key },
);
```

This is a real write that sends contact email. Do not run placeholder enquiries against production. Reuse exactly the same key/input if you explicitly retry an ambiguous submission within its 24-hour retention period. Never generate a new key for each retry. POSTs are not automatically repeated. `OpesApiError` exposes status, requestId, code, retryAfter and the enquiry key; a status of 0 indicates a network/timeout/redirect failure. Error messages do not include request bodies, tokens or credentials.

Run `node examples/catalogue.mjs` with private environment configuration for a read-only quickstart. It prints counts only. Keep `.env` files, client secrets and tokens out of source control and logs. Default base URL: `https://opeshealthsystems.com/api/v1`.

## Business APIs with delegated access

SDK 0.7 adds a refreshed operation-ID registry for the versioned business API. Obtain an access token through your own approved OAuth 2.0 authorization-code flow with S256 PKCE, consent, state validation and the exact redirect URI registered with OHS. Pass a short-lived token or a token provider; the SDK does not collect user passwords, store client secrets, or perform the delegated OAuth flow.

```js
import { OpesBusinessApiClient, createIdempotencyKey } from '@opes-health/api-client';

const api = new OpesBusinessApiClient({
  getAccessToken: async () => obtainAndRefreshYourApprovedDelegatedToken(),
});
const result = await api.request('admin_finance_expenses_approve', {
  path: { expense: 123 },
  idempotencyKey: createIdempotencyKey(),
});
console.log(result.data, result.requestId);
```

Operation IDs are generated from OpenAPI 0.100.0 and exported as the `BusinessOperationId` TypeScript union. Path/query names, authentication and idempotency requirements are checked against that registry at runtime. Commands require a stable key; save it with the intended command and reuse it with the same body after an ambiguous network outcome. The SDK never retries a command automatically. `getAccessToken` can refresh your user's token before it expires. Keep access tokens and API data on trusted servers. OIDC and a public sandbox are not available.

Administrator payout requests use `admin_practitioners_applications_pay`. They require a reviewed delegated client with `practitioners:write`, an active `admin` or `super_admin` user, and a stable `Idempotency-Key`. A 202 response means the request was durably accepted; read the application record for settlement status before taking further action. An uncertain provider outcome is held for reconciliation and is not automatically retried. The Orange Money driver is not available.

