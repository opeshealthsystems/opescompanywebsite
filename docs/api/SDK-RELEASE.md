# Node SDK preview and contract release checks

SDK 0.37.0 is a server-side Node.js 22+ package with TypeScript definitions. It includes the current generated operation IDs and a generic, contract-checked client for delegated business operations. Download `/downloads/opes-health-api-client-0.37.0.tgz` and verify its adjacent `.sha256` file. Machine-readable release metadata is `/downloads/opes-api-sdk.json`. It is a downloadable preview, not an npm registry publication.

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

SDK 0.22.0 adds draft quote create, update and delete operations. Shared finance commands own reference allocation, quote line items, tax calculations, draft-only changes and domain events across API and admin screens.

SDK 0.22.0 archive: 13052 bytes, SHA-256 `9416011c0ad467aac547c6bd996132e219ab16da4f040be934fb4fd9c277356f`. It carries all 258 API operation IDs, including the 252 business operations in OpenAPI 0.26.0.

SDK 0.23.0 adds draft invoice create, update and delete operations. The application commands allocate invoice numbers, validate customer identity and line items, compute amounts server-side, restrict edits to drafts and protect invoices with payment or credit-note history from deletion.

SDK 0.23.0 archive: 13093 bytes, SHA-256 `1d8e04488bafcf2a9988a647cd5c5d6a8a8d5ebcb52adbe2c8570f762d39a136`. It carries all 261 API operation IDs, including the 255 business operations in OpenAPI 0.27.0.

SDK 0.24.0 adds draft purchase order and preapproval supplier bill create, update and delete operations. The shared finance commands own references, validated line items, totals, status boundaries and domain events for API and admin UI consumers.

SDK 0.24.0 archive: 13190 bytes, SHA-256 `1f7459eb2498cc6f6d77646901a9d0bbab17636bdf19b5760b3a9f7f1bb79851`. It carries all 267 API operation IDs, including the 261 business operations in OpenAPI 0.28.0.

SDK 0.25.0 adds administrator user create/update/delete operations. Passwords are hashed by the identity model, role assignment is restricted to super administrators, users cannot deactivate or delete themselves, and the last active super administrator is protected.

SDK 0.25.0 archive: 13231 bytes, SHA-256 `a445adc38da17384587ce8d77d00160562894c0eaddd4234a3a749048f1ca165`. It carries all 270 API operation IDs, including the 264 business operations in OpenAPI 0.29.0.

SDK 0.26.0 adds asset register and CRM deal create/update/delete operations. References are generated server-side, updates and deletes are validated, closed deals remain immutable, and changes are emitted through the transactional domain outbox.

SDK 0.26.0 archive: 13356 bytes, SHA-256 `7d67c182a94a2366342ad7dba96e58ce71dc8fec5259b2fee6a65398d9f72073`. It carries all 276 API operation IDs, including the 270 business operations in OpenAPI 0.30.0.

SDK 0.27.0 adds delegated weekly review snapshots and tester assignment management. Weekly metrics are generated on the server and immutable; assignment transitions validate product ownership, active tester identity, allowed states and report-history deletion rules.

SDK 0.27.0 archive: 13478 bytes, SHA-256 `665e434a217ac734e4d1300cd383684c3288dd9dfcbb81a363660d8db92a7a91`. It carries all 281 API operation IDs, including the 275 business operations in OpenAPI 0.31.0.

SDK 0.28.0 adds contract create/update/delete and payroll-run create/update/delete business operations. Contract references and payroll references are allocated server-side; active/renewed contracts are protected from deletion, and payroll runs cannot be changed or deleted after entries exist or processing begins. The operations use explicit OAuth scopes and shared transactional command handlers.

SDK 0.28.0 archive: 13563 bytes, SHA-256 `1f886835d605e5279f7dde9e680a8b833b03933bb79e3354d4074766484e6dc4`. It carries all 287 API operation IDs, including the 281 business operations in OpenAPI 0.32.0.

SDK 0.29.0 adds document create/update/delete operations. Rendering, signature tokens, references and issuer identity are server-owned; only draft documents can be edited or deleted, while issued documents use explicit send/void transitions.

SDK 0.29.0 archive: 13601 bytes, SHA-256 `db38044971f990d4095284ed775159ebff366add4c601a4d1e7dd831ed895911`. It carries all 290 API operation IDs, including the 284 business operations in OpenAPI 0.33.0.

SDK 0.30.0 adds an admin-only document-template catalogue and create/update/delete operations. Listing is paginated; template placeholders are validated, template types cannot change after use, and templates referenced by documents cannot be deleted.

SDK 0.30.0 archive: 13684 bytes, SHA-256 `d7a1930c90e447426af55a06a89b8d67a3278d45d3f835d3a47042d00c5a45fe`. It carries all 295 API operation IDs, including the 289 business operations in OpenAPI 0.34.0.

SDK 0.31.0 adds paginated department and leave-type administration APIs. Department hierarchy cycles are rejected; departments with members or child departments cannot be deleted. Leave types with request history cannot be deleted. Filament and the API use the same workforce commands.

SDK 0.31.0 archive: 13890 bytes, SHA-256 `53d898bc79270d6d9a17c71594ea1135f5b9103e497e46aebe24f5787ab9dfb0`. It carries all 305 API operation IDs, including the 299 business operations in OpenAPI 0.35.0.

SDK 0.32.0 adds salary-grade and payroll-deduction administration APIs. Salary ranges are validated and employee-assigned grades cannot be deleted. Percentage deductions are capped at 100%; active/default deductions must be disabled before deletion.

SDK 0.32.0 archive: 14103 bytes, SHA-256 `455721097aaf58a13b7fede6abdb532b60c0b1090960d68aff604da034261010`. It carries all 315 API operation IDs, including the 309 business operations in OpenAPI 0.36.0.

SDK 0.33.0 adds vendor, SLA rule and validation product administration operations. Vendor deletion is blocked when purchase-order history exists; SLA rules with ticket history and validation products with modules are protected from deletion. Filament and API operations share the same application handlers.

SDK 0.33.0 archive: 14395 bytes, SHA-256 `c262bfb0adaae002fa1612ce11041491e535bd346cf7b715aeb13af81d837e0b`. It carries all 330 API operation IDs, including the 324 business operations in OpenAPI 0.37.0.

SDK 0.34.0 adds announcement and Knowledge Base article/category management APIs, including explicit article publish and archive transitions. Article authorship, publication time, view counts and status are server-controlled; articles and categories validate unique slugs and protect category hierarchy and content history.

SDK 0.34.0 archive: 14738 bytes, SHA-256 `c83e308292cf912fabce43009a96f20ae803fffaf7251dc9574165a86efa3307`. It carries all 346 API operation IDs, including the 340 business operations in OpenAPI 0.38.0.

SDK 0.35.0 adds paginated admin read and CRUD operations for customer testimonials. Ratings are constrained to one through five; API writes publish transactional domain events, matching the Filament actions.

SDK 0.35.0 archive: 14835 bytes, SHA-256 `4d339e20e54fb4a8fdddf1b8e4e990ebf4a61aa76b794f9831248bc265e5cd6d`. It carries all 351 API operation IDs, including the 345 business operations in OpenAPI 0.39.0.

SDK 0.36.0 adds paginated budget administration APIs. Year, expense category, department uniqueness, amount precision and currency constraints are enforced through shared Finance commands used by the API and Filament.

SDK 0.36.0 archive: 14929 bytes, SHA-256 `858ab23a1d8e57e45dcd897e1e1dea65445cf09499a4c6aad3eef809049eede6`. It carries all 356 API operation IDs, including the 350 business operations in OpenAPI 0.40.0.

SDK 0.37.0 adds attendance and timesheet reads and CRUD. Attendance minutes are derived from check-in/out and duplicate employee/day records are rejected; timesheet hours use quarter-hour increments and reference active/planning projects. Filament and API writes share the same workforce commands.

SDK 0.37.0 archive: 15127 bytes, SHA-256 `042609183c562df959dadf06b8424fc19dfe627f2f12d68cee4c3a557c9b7f7c`. It carries all 366 API operation IDs, including the 360 business operations in OpenAPI 0.41.0.
