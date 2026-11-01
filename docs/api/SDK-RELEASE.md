# Node SDK preview and contract release checks

SDK 0.21.0 is a server-side Node.js 22+ package with TypeScript definitions. It includes the current generated operation IDs and a generic, contract-checked client for delegated business operations. Download `/downloads/opes-health-api-client-0.21.0.tgz` and verify its adjacent `.sha256` file. Machine-readable release metadata is `/downloads/opes-api-sdk.json`. It is a downloadable preview, not an npm registry publication.

The archive contains only package metadata, client sources, the generated business operation registry, type definitions, README and an executable read-only catalogue example. It contains no credentials, test fixtures, environment files or runtime dependencies. Install from the archive URL or a reviewed local download; the README includes machine and delegated business usage.

Tokens are cached with an early-refresh margin and concurrent calls share one token request. A protected GET rejected with 401 refreshes once; other errors return status, request ID and retry guidance. Enquiry POSTs require the caller to provide and retain an idempotency key. They are not silently retried, including on ambiguous timeouts. Secrets are sent only to the token endpoint, redirects are refused, and exception messages omit request bodies and credentials. This is server code, not a browser/mobile secret-storage solution.

## Validation and archive builds

```sh
node --test sdk/node/test/*.test.mjs
python -m unittest discover -s scripts/api -p test_compatibility.py
python scripts/api/check_compatibility.py docs/api/baselines/0.3.0.json docs/api/openapi.json
```

From `sdk/node`, `npm pack --ignore-scripts --json --pack-destination ../../public/downloads` builds the package. Review its file list and SHA-256 before deployment. A released archive URL is immutable: bump the SDK package version for changed contents and retain the previous archive for consumers. Do not publish to an external registry as part of this process.

SDK 0.4.0 through 0.10.0 remain available at their immutable URLs. SDK 0.11.0 shipped with the OpenAPI 0.15.0 operation registry; immutable downloads and checksums are retained under public/downloads. The 11 Node SDK tests cover contract-backed paths, delegated authentication, idempotency headers, input rejection, no automatic command retries, network failures and the machine/enquiry client. Regenerate the operation registry with `php scripts/api/generate_business_sdk_operations.php` after updating `docs/api/openapi.json`.

The 0.3.0 archive was extracted and exercised against the live read-only API with temporary reviewed credentials. Its catalogue and integration calls worked, and its bundled example returned two products out of 23. Two token issuances were observed: one shared by repeated calls in the main SDK instance, and one from the separate executable example. The verification client and tokens were revoked and its private credential file removed. A clean local npm install also verifies package exports. Evidence: `evidence/sdk-live.json`.

## Compatibility policy

The baseline is the released OpenAPI 0.3.0 document. The check is deliberately conservative: existing expanded operation shapes, authentication definitions, dialect and base URLs must remain unchanged. New operations and documentation changes are allowed. Even additive edits to an existing operation are flagged for review. External/recursive references and path-item references are unsupported and fail closed; this is not a universal semantic OpenAPI diff engine.

CI compares against the previous Git revision's contract when available, with the checked-in release baseline for initial runs. It also validates OpenAPI, tests the compatibility checker and SDK, reviews package contents with a dry run, checks Composer advisories and runs the API/mail regression suite. Changes to released shapes require an explicit versioning/review decision; do not merely regenerate the baseline to hide a difference. Repository review/branch protection remains an owner-managed setting, and remote CI has not yet been run or claimed passing.

SDK 0.4 accepts a short-lived delegated token or token provider for business operations. It does not collect user passwords, implement OAuth authorization-code/PKCE, or manage a user's consent and redirect flow. Keep that flow in the approved integrating application and validate state and S256 PKCE there. This increment does not create a public sandbox, self-service credential registration, OIDC or private clinical APIs.












SDK 0.14.0 accompanies API 0.18.0 and contains the generated registry for all 236 documented business workflow operations. This includes finance approvals and invoice transitions plus final-evaluation creation and updates; admin screens dispatch through the same operations. Archive SHA-256: `05dd3763f072ad8214897e1c6d3a25c83cf8667d58a720a43c95c91011a31180` (12621 bytes). It remains a downloadable preview and is not published to npm.

SDK 0.15.0 accompanies OpenAPI 0.19.0 and contains all 238 business workflow operation IDs, including idempotent training, milestone and course-enrollment completion commands. The course command creates one certificate per enrollment and queues the email and in-app notification through the transactional outbox. This release is a downloadable preview, not an npm registry publication.

SDK 0.15.0 archive: 12716 bytes, SHA-256 `7de0178e32dbbd95b24388159d6a18494140b274f8b7599dc2b85265c37d83de`. The checked-in generated registry contains 244 API operation IDs, including all 238 business workflow operations in OpenAPI 0.19.0.

SDK 0.16.0 adds the delegated admin lead follow-up operation. Follow-up email is recorded in the transaction outbox and queued using escaped HTML rendering; the lead record itself is never included in event payload data.

SDK 0.16.0 archive: 12791 bytes, SHA-256 `4b56e31a3cfd1485bf2d9045d22e502d23a73775796e96594a85dc7980bd1095`. It includes 245 API operation IDs, including all 239 business operations in OpenAPI 0.20.0.

SDK 0.17.0 adds admin-only company-settings read and update operations. The API exposes an allowlisted settings projection; updates validate supported fields, constrain logo paths to existing uploaded company assets, and append a domain event without storing field values in the event payload.

SDK 0.17.0 archive: 12840 bytes, SHA-256 `c144790398b3c770f0e2371a510b56adae1a7146e58221b5244df601c4c56add`. It contains 247 API operation IDs, including all 241 business operations in OpenAPI 0.21.0.

SDK 0.18.0 includes the admin workforce-summary query; the HR dashboard now reads the same scoped application query as API consumers.

SDK 0.18.0 archive: 12885 bytes, SHA-256 `794f05294ea4677f1fb0ed3d08bdc51b60094da1eb1dbcebcfd35385b6959a3f`. It includes 248 API operation IDs, including all 242 business workflows in OpenAPI 0.22.0.

SDK 0.19.0 includes admin finance expense create and update commands. Both Filament forms dispatch through the same validated, role-protected application operations; updates are limited to pending expenses and references use a locked business sequence.

SDK 0.19.0 archive: 12911 bytes, SHA-256 `4607c714bf7c04cf93fb8f58d99f96456f976705303e7f3545f4bdf3536bef1b`. It carries all 250 API operation IDs, including the 244 business operations in OpenAPI 0.23.0.

SDK 0.20.0 adds delegated risk-register create and update operations. The shared application commands generate risk references and scores, enforce open-only updates, and publish transactional domain events; the admin forms use the same commands.

SDK 0.20.0 archive: 12955 bytes, SHA-256 `34819519a1c74267975079d1b5174313991febedc03530d385d948bf35ef8102`. It carries all 252 API operation IDs, including the 246 business operations in OpenAPI 0.24.0.

SDK 0.21.0 adds draft credit note create, update and delete operations. Shared commands own reference allocation, line-item persistence, total calculation, draft-only mutations and transactional outbox events for the API and admin UI.

SDK 0.21.0 archive: 13001 bytes, SHA-256 `8d8e384f6c918b69d79efb2fc93da4f024517476b0acc3529372fc9b152e4f25`. It carries all 255 API operation IDs, including the 249 business operations in OpenAPI 0.25.0.
