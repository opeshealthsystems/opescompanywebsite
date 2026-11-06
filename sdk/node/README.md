# OHS server-side Node SDK — preview 0.111.0

Requires Node.js 22 or newer. This dependency-free preview package is distributed as a downloadable archive, not published to the npm registry. It is server-side code: never bundle client secrets into a browser or mobile application. This package contains all 653 documented operation identifiers from OpenAPI 0.115.0. `serviceOfferings({ locale })` reads the same localized Health Data Services and CDSS catalogue used by the website, without OAuth credentials. Delegated users can read 30 days of API usage scoped to their own account and the current OAuth application. Signed-in developers can register, update, list and revoke up to five public OAuth applications they own with `listDeveloperApplications()`, `registerDeveloperApplication()`, `updateDeveloperApplication()` and `revokeDeveloperApplication()`. They can also create, list, rotate and revoke up to five owner-scoped machine clients with `listDeveloperMachineClients()`, `registerDeveloperMachineClient()`, `rotateDeveloperMachineClient()` and `revokeDeveloperMachineClient()`. Machine clients are limited to `catalogue:read` and `integration:read`; they cannot access patient records or delegated business resources. The client secret is returned only at creation or rotation and must be stored in a server-side secret manager. All lifecycle writes require an idempotency key; rotating or revoking invalidates existing tokens. Public OAuth registrations use authorization code with S256 PKCE, issue no client secret, restrict callbacks to HTTPS/local loopback/the reserved OPES callback, and limit scopes to those currently available to the user. `submitPartnerApplication(input, { idempotencyKey })` submits an unauthenticated, rate-limited partnership proposal, including `partner_type: 'health_ai'` for AI companies and `'academic'` for education, research and teaching-hospital collaborations; it returns only a minimal acknowledgement. Do not include patient data. Data access requires separate assessment and approval. Administrator machine-client support reports use the read-only `api-clients:read` scope and exclude credential material. Administrators can set or clear machine-client UTC daily and monthly request quotas; legacy clients remain unlimited until a limit is explicitly configured. Enforced quota limits return documented `X-Quota-*` and `Retry-After` headers, while a client can read its own usage and quota snapshot with `integration:read`. Practitioner assessments are limited to published exams in enrolled courses; correct answers remain hidden until submission. Partner accounts are returned only for approved organizations explicitly linked to the signed-in partner; organization owners can update approved profile details, while administrators grant/revoke membership and assign owner/member roles. Admins can inspect safe website form-delivery metadata and reconcile confirmed ambiguous deliveries without resending the email.

Install the reviewed archive with `npm install ./opes-health-api-client-0.111.0.tgz`, then:

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
const quota = await client.integrationQuota();
const services = await client.serviceOfferings({ locale: 'en' });
console.log(usage.data.requests_last_30_days, usage.data.daily_usage);
console.log(quota.data.daily_remaining, quota.data.monthly_remaining);
console.log(services.data.items.map(({ id, name, contact_url }) => ({ id, name, contact_url })));
```

Request reviewed credentials from OHS. Specify only scopes granted to your client. Tokens are cached with an early-refresh margin; concurrent calls share one issuance request. A GET rejected with 401 refreshes once. Quota/scope/network failures are returned to the caller, not retried in a loop. Redirects are refused and each request has a configurable 15-second timeout (maximum 60 seconds). Plain HTTP is accepted only for localhost development. Partner organization owners can use delegated business operation IDs to list, add, and remove linked members; additions require an existing active, verified partner account, and the API prevents removing or demoting the last owner.

For enquiry intake, OAuth credentials are unnecessary, but this SDK requires an explicit key:

```js
import { OpesClient, createIdempotencyKey } from '@opes-health/api-client';
const key = createIdempotencyKey(); // persist with the intended enquiry BEFORE sending
const result = await new OpesClient().submitEnquiry(
  {
    name: 'YOUR_NAME', email: 'YOUR_EMAIL',
    integration_application: {
      application_name: 'YOUR_APP', access_type: 'delegated',
      redirect_uri: 'https://your.example/callback',
      requested_scopes: ['customer:read'], use_case: 'Describe the integration use case.',
    },
  },
  { idempotencyKey: key },
);
```

This is a real write that sends contact email. Do not run placeholder enquiries against production. Reuse exactly the same key/input if you explicitly retry an ambiguous submission within its 24-hour retention period. Never generate a new key for each retry. POSTs are not automatically repeated. `OpesApiError` exposes status, requestId, code, retryAfter and the enquiry key; a status of 0 indicates a network/timeout/redirect failure. Error messages do not include request bodies, tokens or credentials.

Run `node examples/catalogue.mjs` with private environment configuration for a read-only product catalogue quickstart. Run `node examples/integration-usage.mjs` with `integration:read` for this client’s 30-day UTC usage series. Active administrators can run `node examples/admin-client-usage.mjs` with a delegated token approved for `api-clients:read` and `OPES_MACHINE_CLIENT_ID` to inspect one machine client; that token must belong to an active admin/super-admin. These examples only read data. Keep `.env` files, client secrets and tokens out of source control and logs. Default base URL: `https://opeshealthsystems.com/api/v1`.

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

Operation IDs are generated from OpenAPI 0.114.2 and exported as the `BusinessOperationId` TypeScript union. Use `api.request('readOwnBusinessUsage')` with a delegated token carrying `identity:read` to inspect this user's daily consumption for the current OAuth application. `submitPartnerApplication(input, { idempotencyKey })` sends a public partnership proposal, including `partner_type: 'health_ai'`, and returns only a minimal acknowledgement. Do not submit patient information. Path/query names, authentication and idempotency requirements are checked against the registry at runtime. Commands require a stable key; save it with the intended command and reuse it with the same body after an ambiguous network outcome. The SDK never retries a command automatically. `getAccessToken` can refresh your user's token before it expires. Keep access tokens and API data on trusted servers. OIDC and a public sandbox are not available.

Administrator payout requests use `admin_practitioners_applications_pay`. They require a reviewed delegated client with `practitioners:write`, an active `admin` or `super_admin` user, and a stable `Idempotency-Key`. A 202 response means the request was durably accepted; read the application record for settlement status before taking further action. An uncertain provider outcome is held for reconciliation and is not automatically retried. The Orange Money driver is not available.

