# API identity and client onboarding

OHS uses Laravel Passport 13.10 and League OAuth2 Server for approved server-to-server clients and administrator-reviewed public OAuth clients. `POST /api/v1/oauth/token` remains the machine-only client-credentials endpoint. Delegated user tokens use authorization code with S256 PKCE and `POST /api/v1/oauth/user-token`. This is OAuth 2.0 authorization, not OpenID Connect authentication. Password/implicit grants, userinfo, organization tenancy and OIDC are not published. Existing web sessions and password resets remain the website sign-in mechanism.

Public developer entry points are `/en/developers` and `/fr/developers`; the versioned contract is `/api/v1/openapi.json`. The `0.22.0` contract documents delegated authorization and the business workflows. Review integration requests before provisioning. There is no automatic public client registration or sandbox.

## Delegate user authorization

Create a public client only after reviewing the application owner and callback. Supply the exact HTTPS callback and explicit registered scopes; the command returns the public client ID and endpoints, never a client secret:

```sh
php artisan api:user-client --owner="admin@opeshealthsystems.com" --name="Approved browser integration" --redirect="https://consumer.example/callback" --scope="identity:read"
```

The authorization request must include `response_type=code`, the client ID, an exact registered `redirect_uri`, a unique `state` of at least 32 characters, `code_challenge_method=S256`, and a valid S256 `code_challenge`. The signed-in user sees a branded consent screen and may allow or deny. Authorization codes are single-use and bound to the client, callback, user and PKCE verifier. Exchange them at `/api/v1/oauth/user-token`; authorization-code and refresh-token grants are supported there only. Access tokens expire after 15 minutes and refresh tokens after seven days.

Current user status, role-derived approved scopes, client owner status and token/client/user association are checked again on token refresh and every private API request. A session cookie alone is not API authorization, and a machine token cannot call user business operations. Do not put access or refresh tokens in URLs or logs. Revoke the client with `php artisan api:client revoke --client=CLIENT_UUID`; this revokes its existing tokens. The machine-client `rotate` action deliberately rejects public PKCE clients because they have no client secret to rotate.

Business operations use the context paths listed in the OpenAPI contract, such as `/api/v1/customer/profile` and `/api/v1/administration/projects`, with role-derived OAuth scopes. Their results use explicit resource field allowlists; command requests require an `Idempotency-Key`. `GET /api/v1/me/activity` returns only the authenticated user's activity projection. OpenAPI role/scope requirements supplement the application's existing policy and record-level checks; they do not replace them.

## Provision a reviewed client

Run on the server from the application root. Specify an active administrator who is accountable for this integration. This association does not make the machine token a user login or grant access to that administrator's records.

```sh
php artisan api:client create --name="Approved integration name" --owner="admin@opeshealthsystems.com" --scope=catalogue:read --scope=integration:read
```

The command prints a client ID and private credential-file path. It never prints the secret. The JSON is written with mode 0600 under `storage/app/private/api-credentials/`; deliver it to the approved consumer through an appropriate private channel, then remove that credential file after verified handoff. Do not commit it, attach it to public support tickets, put it in browser storage, or include it in access logs. Secret hashes alone are stored in `oauth_clients`.

The owner must remain active. Provisioning permits only the explicit scopes `catalogue:read` and `integration:read`; no wildcard grants. Clients with no approved owner or scope list cannot obtain tokens. Ownership here is administrative accountability, not a completed multi-organization tenancy model.

## Use and limits

Exchange credentials as a form-encoded POST body (or supported confidential-client HTTP Basic authentication) over HTTPS. Request only approved scopes. Tokens expire in 900 seconds, with no refresh or ID token. Request a new token when needed rather than minting one per catalogue call. Keep secrets and bearer tokens on the consumer's server.

- `catalogue:read`: bounded, explicitly whitelisted published product metadata at `GET /api/v1/catalogue/products`. Page size 1–50. English/French supported.
- `integration:read`: the current token client's identity, granted scopes and UTC daily authenticated-attempt count at `GET /api/v1/integration`. A caller cannot select another client by ID.
- Token issuance: 20 attempts/minute per source IP. Protected resources: 60 authorized attempts/minute per client. The daily counter includes current calls, validation failures and quota rejections after successful identity/scope checks; it excludes unauthenticated/scope-denied calls and token issuance.

The token endpoint uses standard OAuth error JSON. Protected API endpoints use Problem Details for 401/403/422/429 errors, with request correlation identifiers. Resource authentication validates the signed token through League, its stored token/client association, database expiry/revocation, active owner and current scope approval on each request. A website session alone cannot access these machine endpoints.

## Rotate or revoke

```sh
php artisan api:client list
php artisan api:client rotate --client=CLIENT_UUID
php artisan api:client revoke --client=CLIENT_UUID
```

Rotation creates a new private credential file and immediately revokes existing access tokens. The client ID stays the same; old secrets stop working. Coordinate that brief consumer interruption. Revocation disables minting and existing access immediately. Revoked clients cannot be rotated back into service. Deactivating the accountable user also blocks client access.

## Keys and deployment

Signing keys live under `storage/app/private/oauth/`, outside public assets. Generate them once with `php artisan passport:keys`; never use `--force` during normal releases. Keep private keys mode 0600 and back them up securely with restricted access. A signing-key replacement invalidates existing tokens and needs a separately planned rotation. No secrets or private keys belong in the source release archive.

The additive migrations create the standard Passport tables and explicit client scopes/usage storage. Preserve these tables on source rollback. Build dependencies in the staged release before maintenance, back up source/database, then switch the prepared vendor directory alongside the reviewed application files. Restore both previous source and vendor on a failed cutover. Website enquiry outbox/idempotency tables are unaffected.

## Remaining identity gates

OIDC, MFA for a future developer control panel, customer-managed application registration, organization ownership and additional private API workflows remain separate work. Do not expose private resources based solely on OAuth scopes or label this release as federated SSO. The business contract is a first product surface, but it does not mean every internal/admin action or the entire application has been converted to an API.

References: [Laravel Passport](https://laravel.com/framework/docs/13.x/passport), [OAuth security best current practice](https://www.rfc-editor.org/rfc/rfc9700.html).


