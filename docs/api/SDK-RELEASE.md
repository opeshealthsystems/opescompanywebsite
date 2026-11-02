SDK 0.91.0 aligns with OpenAPI 0.95.0 and adds the documented administrator payout-request operation. Payouts require an approved delegated client, the `practitioners:write` scope, an active administrator, and an `Idempotency-Key`. Accepted requests use the durable outbox and stable provider reference; uncertain provider outcomes are held for reconciliation rather than retried. The unavailable Orange Money driver is not exposed as a supported choice. The archive is 20,899 bytes with SHA-256 `8393b775e36f756c317e1eb2260dd4683b0bd6501bca82ce9bb7e721e8974af5`. The downloadable archive remains a server-side preview and is not published to npm.

SDK 0.90.0 aligns the downloadable client with OpenAPI 0.94.0. All 600 operation identifiers remain available; the contract now gives business operations action-oriented summaries, scope and role requirements, pagination and idempotency behavior, plus request and response-envelope examples. The archive is 20,694 bytes with SHA-256 `6e15234dc948cd1434dc5b1a79f24bc098942e91b60899aa28cf0ca729db0d81`. It remains a server-side preview and is not published to a registry.

SDK 0.89.0 adds four protected Validation dashboard query operations for cohort progress, issue analytics, developer throughput and paginated practitioner leaderboard data. Filament dashboards now use the same role-checked application reads, and the cohort/leaderboard UI reads the first 100 rows while API clients can request bounded pages. These analytics are admin-only under validation:read. The archive contains all 600 API operation IDs, including 594 business operations in OpenAPI 0.93.0. Archive size is 20636 bytes with SHA-256 ``575a2900cf3daf253742da2add1cb98f2e223caf784b558f46c7b23a9289ec07``. It remains a downloadable preview and is not published to a registry.

SDK 0.88.0 adds shared API query projections for all 22 Filament relation-manager tables and six protected collection reads for invoice payments, payroll entries, cohort members, cohort test cases, developer-task retests, and employee profiles. Finance quote lists also expose the lead filter used by the CRM relation table. It contains all 596 API operation IDs, including 590 business operations in OpenAPI 0.92.0. Archive size is 20500 bytes with SHA-256 `3a336724e60bacdecfc83d14c71b742b254a39870ebb322bb3f146db71719f3e`. It remains a downloadable preview, not a registry release.

SDK 0.87.0 adds protected document, support bug-report, suggestion, tester-assignment and validation-certificate reads; it also includes the shared licensing register query contract. Sensitive document signatures, screenshot paths, suggestion bodies and tester notes are omitted from list projections. It carries all 590 API operation IDs, including 584 business operations in OpenAPI 0.91.0. Archive size is 20396 bytes with SHA-256 `a98ce7b9d6960325c6eb274033e72d0da45bd8e8785ff8684c186f003b6ec501`. It remains a downloadable preview, not a registry release.

SDK 0.86.0 adds protected validation issue, developer-task, test-session and weekly-review reads with filters. Responses are paginated and limit relation fields; issue attachments, session screenshots and task resolution notes are excluded. It carries all 585 API operation IDs, including 579 business operations in OpenAPI 0.90.0. Archive size is 20270 bytes with SHA-256 `7245a40288cf091327046d8109aa7187bf8917a28377f69b56dc988664691a33`. It remains a downloadable preview, not a registry release.

SDK 0.85.0 adds admin learning enrollment and certificate list operations with validated filters. The endpoint responses limit student and certificate fields and omit certificate file paths. It carries all 581 API operation IDs, including 575 business operations in OpenAPI 0.89.0. Archive size is 20137 bytes with SHA-256 `964a08a30dfcdd2a7c7d7d64d9e9f8ae73328e693f78e2b3301f56124876ceca`. The package remains a downloadable preview and is not published to a registry.

SDK 0.84.0 adds the validated rating enum for final-evaluation reads and reflects the query now shared by the admin table and API. It carries all 579 API operation IDs, including 573 business operations in OpenAPI 0.88.0. Archive size is 20103 bytes with SHA-256 `6ffc715d7e2cc9e69fb023ebf73edcda789d4ed92ab492966405be46ed19493a`. The package remains a downloadable preview and is not published to a registry.

SDK 0.82.0 adds the protected credit-note register operation with validated status, invoice and text-search filters. It carries all 579 API operation IDs, including 573 business operations in OpenAPI 0.86.0. Archive size is 20100 bytes with SHA-256 `50646a42b25508a7dd8bbfa8c20fc32145b569f718c72e9d1c0727b2023b0024`. The package remains a downloadable preview and is not published to a registry.
# Node SDK preview and contract release checks

SDK 0.77.0 adds protected detail queries for invoice, quote, supplier-bill, purchase-order and credit-note line items. The five Filament edit forms now load their line items through the same query handlers offered to API clients.
SDK 0.77.0 archive: 19537 bytes, SHA-256 `c8aad2bd1e6570a947dc9f9f16a45fc8e4087e5acab83533dabf991025f532a2`. It carries all 574 API operation IDs, including 568 business operations in OpenAPI 0.81.0. The package remains a downloadable preview and is not published to a registry.


SDK 0.76.0 extends the protected admin summary query with relationship counts and lifecycle checks used by resource views and action controls, and adds a role-permissions reference operation. These checks remain admin-only and accept only whitelisted metric names.
SDK 0.76.0 archive: 19419 bytes, SHA-256 `acc77b53d919ff83d5285fb65d1ca3c877eddcb76239762e39f540d9fdb33761`. It carries all 569 API operation IDs, including 563 business operations in OpenAPI 0.80.0. The package remains a downloadable preview and is not published to a registry.


SDK 0.75.0 adds public sitemap-data pages, customer-country and user-role references, audit-log filter references, and audit/employee summary metrics. The sitemap presenter and reference-data forms now use these shared query operations. Private reference and summary data remains administrator-protected.
SDK 0.75.0 archive: 19345 bytes, SHA-256 `3cb4ceef4cfa8f16160328230c187294a38764dd46471bb7411e1ba14ff9c174`. It carries all 568 API operation IDs, including 562 business operations in OpenAPI 0.79.0. The package remains a downloadable preview and is not published to a registry.


SDK 0.74.0 adds a protected admin resource-summary endpoint with 16 whitelisted metrics shared by Filament navigation badges. Requests require an active administrator and the `reporting:read` scope. The endpoint is included in OpenAPI 0.78.0.
SDK 0.74.0 archive: 19236 bytes, SHA-256 `6d7ee85eb5ef5d75bb85e764d6a015c2e0a58898b4678ac2f70f3c6f3bfd71e6`. It carries all 564 API operation IDs, including 558 business operations in OpenAPI 0.78.0. The package remains a downloadable preview and is not published to a registry.


SDK 0.73.0 adds protected admin reference-data operations used by Filament forms, including users, departments, salary grades, leads, projects, openings, invoices, licenses and validation cohorts. Employee ID suggestions also come from the API query layer. These operations are included in OpenAPI 0.77.0; reference reads require an active admin role and the `reference-data:read` scope.

SDK 0.73.0 archive: 19199 bytes, SHA-256 `cf783fb261355dd61aeb3f197cf200cf207614663f94b8d56615af6e5e5fde3c`. It carries all 563 API operation IDs, including 557 business operations in OpenAPI 0.77.0. The package is available as a downloadable preview; it is not published to a registry.
SDK 0.72.0 adds paginated finance register reads for invoices, expenses, quotes, supplier bills, purchase orders and payroll runs, plus a summary operation used by finance navigation badges. Filament finance tables and badges now share these application queries. These operations are included in OpenAPI 0.76.0.

SDK 0.72.0 archive: 18812 bytes, SHA-256 `d212d8fc9e97a58ec942ea2f6ab4c87a6e4bbfed5fb934f9e921291ff92a0276`. It carries all 546 API operation IDs, including 540 business operations in OpenAPI 0.76.0. Its live metadata, size and archive hash match. The package remains a downloadable preview and is not published to a registry.

SDK 0.71.0 adds contract list and expiring-contract summary reads, plus asset-register listing. Contract and asset Filament tables now use the same role-protected query handlers as API clients. These operations are included in OpenAPI 0.75.0.

SDK 0.71.0 archive: 18706 bytes, SHA-256 `ab0bf83d28815410e871627af77e7d2a75984c275232e65e2eb90b378d152d62`. It carries all 539 API operation IDs, including 533 business operations in OpenAPI 0.75.0. Its live metadata, size and archive hash match. The package remains a downloadable preview and is not published to a registry.

SDK 0.70.0 adds four protected admin dashboard reads for recent tickets, new leads, outstanding invoices, and audience-filtered announcements. The dashboard table widgets reuse the same query builders as these API operations. Operations are included in OpenAPI 0.74.0; access follows support, CRM, finance, and audience permissions.

SDK 0.70.0 archive: 18644 bytes, SHA-256 `1b0214a8057d75435ac56a980bd7ed06aced04ad9559ccef41c09d6886486650`. It carries all 536 API operation IDs, including 530 business operations in OpenAPI 0.74.0. The package remains a downloadable preview and is not published to a registry.


SDK 0.68.0 adds seven protected finance reports: A/P and A/R aging, cash flow, budget vs actual, payroll headcount, profit and loss, and the finance overview. Filament report pages and the overview widget now use the same query handlers. These operations are included in OpenAPI 0.72.0.

SDK 0.68.0 archive: 18428 bytes, SHA-256 `b6d8360f805be8ad471e911736fbd700dc2a9acfae1d806fce3f4ce86eefffdb`. It carries all 527 API operation IDs, including 521 business operations in OpenAPI 0.72.0.

SDK 0.67.0 adds the protected dashboard summary query. Financial measures are omitted without accounting permission, and CRM measures are limited to administrator roles. The main Filament stats widget now reads through this query. These operations are included in OpenAPI 0.71.0.

SDK 0.67.0 archive: 18198 bytes, SHA-256 `80ac92aa58cdd2e593f3b928e0ed56cd44931ef26c8447b96e40f39b63b407f6`. It carries all 520 API operation IDs, including 514 business operations in OpenAPI 0.71.0.

SDK 0.66.0 adds protected risk-register list and detail reads. The admin UI no longer offers direct risk deletion so mitigation history remains auditable. These operations are included in OpenAPI 0.70.0.

SDK 0.66.0 archive: 18165 bytes, SHA-256 `ec5d88646e2612ceb2b70a3c4b61e0cd8a9ca1fe14374efdf66e148bab854343`. It carries all 519 API operation IDs, including 513 business operations in OpenAPI 0.70.0.

SDK 0.65.0 adds protected deal list, detail and pipeline read operations. The Filament kanban now reads the shared Operations query and advances deals through the same command exposed by the API. These operations are included in OpenAPI 0.69.0.

SDK 0.65.0 archive: 18134 bytes, SHA-256 `8e36bdb860001a4e85fb78f7e3a238078c6991d4f5777abc7596e51f97899aec`. It carries all 517 API operation IDs, including 511 business operations in OpenAPI 0.69.0.


SDK 0.64.0 adds a protected invoice-payment recording operation. Filament payment entry shares the finance command with API clients, and payment deletion is removed to preserve financial audit history. These operations are included in OpenAPI 0.68.0.

SDK 0.64.0 archive: 18073 bytes, SHA-256 `e029138fcb1f310318e5788272206a14781c696adc61edb8cdb381d3d027df9c`. It carries all 514 API operation IDs, including 508 business operations in OpenAPI 0.68.0.

SDK 0.63.0 adds protected project-scoped milestone list/detail/create/update/delete operations. Writes verify project ownership; completion remains an explicit transition, completed milestones cannot be reopened or deleted, and Filament uses the same commands. These operations are included in OpenAPI 0.67.0.

SDK 0.63.0 archive: 18042 bytes, SHA-256 `988dc58811b0b3bedd2a4fec8fa3ad52e1085b7fa98e9c57deb5c92c7a50a128`. It carries all 513 API operation IDs, including 507 business operations in OpenAPI 0.67.0.

SDK 0.62.0 adds protected employee leave-balance list, detail, create, update and delete operations. Usage cannot be supplied by callers, entitlement changes cannot fall below approved use, and records with leave history are retained. Filament balance actions use the same Workforce commands. These operations are included in OpenAPI 0.66.0.

SDK 0.62.0 archive: 17919 bytes, SHA-256 `032c6829979bbd4f4f385552778b624c7fd74912f32ee3908561abb56bb7574e`. It carries all 508 API operation IDs, including 502 business operations in OpenAPI 0.66.0.

SDK 0.61.0 adds protected validation-module create and delete operations. Module creation is scoped to its product, deletion is rejected while workflows exist, and Filament module actions dispatch the same commands. These operations are included in OpenAPI 0.65.0.


SDK 0.60.0 adds eight protected partner-institution and practitioner-programme catalogue operations. The institution create/update calls accept `FormData` for optional image logos or a managed existing logo path; Filament edit/create, featured state and ordering use the same commands. Programme edits validate lifecycle transitions, cohort references, application history and capacity limits; deletion controls were removed.

SDK 0.60.0 archive: 17785 bytes, SHA-256 `f79bddc0189ebdd98a93cb483a876400540842867d19a2dbd36f2a9f4c9fb7cb`. It carries all 501 API operation IDs, including 495 business operations in OpenAPI 0.64.0.

SDK 0.59.0 adds four protected service-request operations for list, detail, pending-request detail edits and cancellation. Assignment and confirmation, completion and cancellation are separate lifecycle commands; general edits cannot set status, customer, assignee or confirmed schedule. These operations are included in OpenAPI 0.63.0.

SDK 0.59.0 archive: 17608 bytes, SHA-256 `8e578bb2d3c8e617c0a5b2318a22961ac64cb30e04fe1e6cf160d52ddd9d6627`. It carries all 493 API operation IDs, including 487 business operations in OpenAPI 0.63.0.

SDK 0.58.0 adds six protected CRM operations to list, inspect and update partner and tester applications. Admin writes are limited to notes and valid status transitions; contact and submission data is immutable through this interface, and the admin create/delete UI was removed because these records represent real public submissions. The public intake API remains the write-only creation path. These operations are included in OpenAPI 0.62.0.

SDK 0.58.0 archive: 17530 bytes, SHA-256 `dd9e08b37a0f6d4d03960a6ef3d3465daab0dce6e1156c2112bdfba217253323`. It carries all 489 API operation IDs, including 483 business operations in OpenAPI 0.62.0.

SDK 0.57.0 adds nine protected practitioner administration operations for profile/finding reads and edits plus application reads and admin notes. Sensitive payout/registration fields are not included in profile reads, identity/status fields are blocked from ordinary edits, and the existing verify/publish/approve/reject actions remain explicit commands. These operations are included in OpenAPI 0.61.0.

SDK 0.57.0 archive: 17371 bytes, SHA-256 `0b69e5e892f606a5e7a274cab8702f884d173f19f404dbde7e215d394977b61c`. It carries all 483 API operation IDs, including 477 business operations in OpenAPI 0.61.0.

SDK 0.56.0 adds 13 protected validation-catalogue operations for modules, workflows and test cases. Filament edits and nested create/edit/delete actions use shared Validation commands with parent-ownership checks and history-safe deletion guards. These operations are included in OpenAPI 0.60.0.

SDK 0.56.0 archive: 17219 bytes, SHA-256 `9cb449906193f0fe14cf3fccf24b7ce10420a74c9d8201de5f6e67b8ee6c4a2d`. It carries all 474 API operation IDs, including 468 business operations in OpenAPI 0.60.0.

SDK 0.55.0 adds protected performance-review read/create/update operations under dedicated review scopes. Filament creates and edits drafts through the same commands; submitted and acknowledged reviews are read-only, and delete controls are removed. Existing submit and acknowledge API scopes remain compatible.

SDK 0.55.0 archive: 16947 bytes, SHA-256 `c195e814b62efb52dcf60a6e94b6f8d206b1303aab528ba3d06fba6805ed84dc`. It carries all 461 API operation IDs, including 455 business operations in OpenAPI 0.59.0.

SDK 0.54.0 adds protected customer facility-profile and admin leave-request read/create/update operations. Filament create/edit uses the same application commands; profiles can only be assigned to customer accounts, pending leave requests alone are editable, and historical profile/leave rows cannot be deleted through the admin screens.

SDK 0.54.0 archive: 16880 bytes, SHA-256 `54941bdbd44b93f6919f292312078d8f1f98b831957b1cee0655454b3fdae25b`. It carries all 457 API operation IDs, including 451 business operations in OpenAPI 0.58.0.

SDK 0.53.0 adds protected invoice-template read/create/update operations. Filament create/edit uses the same Finance commands; deactivation retains the recurring schedule and invoice-generation history.

SDK 0.52.0 adds protected employee and HR read/create/update operations with dedicated `workforce:hr:read` and `workforce:hr:write` scopes. Payroll, bank and tax fields are returned only by the scoped employee endpoints; super-admin assignment is restricted, and employee account deletion is replaced with deactivation.

SDK 0.51.0 adds five protected CRM lead read and CRUD operations. Filament lead creation, edit and delete use the same Enquiries handlers. Leads associated with quotes or deals cannot be deleted.

SDK 0.51.0 archive: 16577 bytes, SHA-256 `2c8c9b4436773ab923d46dab749f5c801ffd4f89d86b7808c40c765ab14b3653`. It carries all 441 API operation IDs, including 435 business operations in OpenAPI 0.55.0.

SDK 0.50.0 adds protected support-ticket create and detail-edit operations. The Filament support desk uses the same commands; ticket replies and status transitions were already shared. Admin ticket deletion is disabled to preserve support history.

SDK 0.50.0 archive: 16485 bytes, SHA-256 `4191224afa748ef8796bdf2712837d96f6cdb2b841f61b403cf79f9e5e3c740d`. It carries all 436 API operation IDs, including 430 business operations in OpenAPI 0.54.0.

SDK 0.49.0 is a server-side Node.js 22+ package with TypeScript definitions. It includes the current generated operation IDs and a generic, contract-checked client for delegated business operations, including blog publishing, CRM demo-request administration, validation-cohort management, recruitment, learning and survey administration. Download `/downloads/opes-health-api-client-0.49.0.tgz` and verify its adjacent `.sha256` file. Machine-readable release metadata is `/downloads/opes-api-sdk.json`. It is a downloadable preview, not an npm registry publication.

SDK 0.49.0 adds twelve protected survey-administration operations for survey and question reads/CRUD plus activate/close lifecycle actions. Filament uses the same Programme commands. Surveys with responses and questions with response records cannot be structurally changed; multiple-choice question choices are validated, and participant read/submit flows honor survey start/end windows.

SDK 0.48.0 adds ten protected learning operations for paginated course and lesson reads and CRUD. Filament course forms, the active toggle and lesson relation manager use the shared Learning commands. Courses with enrollment/certificate history cannot be deleted, lessons with learner progress are retained, course images must be managed image files, and drag-reorder controls were removed in favor of API-backed sort-order edits.

SDK 0.47.0 adds ten protected recruitment operations for paginated opening/application reads and CRUD. Filament create, edit and delete actions share Workforce commands with the API. Opening deletion is blocked when applications exist, hired applications are retained, initial status and creator are server-controlled, and resume uploads are restricted to PDF files in the managed resume directory.

SDK 0.44.0 adds the five delegated blog-administration operations. Filament and API writes share the same Publishing commands; rich text is sanitized, reading time is calculated by the server, published articles cannot be deleted, and each lifecycle change emits a domain event. The existing anonymous public blog read operations continue to expose only articles visible under the publishing schedule.

SDK 0.45.0 adds the five delegated CRM demo-request operations. Search and filtered reads remain role-protected; administration creates do not send customer mail, writes use the same handlers as Filament, and deletion is blocked until the contact-mail delivery ledger is safely resolved.

SDK 0.46.0 adds eight validation-cohort operations for cohort administration, member status and test-case assignment. Cohort and assignment rules are enforced by shared application commands used by API and Filament. Members remain visible only to authorized administrators, and assignments with recorded sessions cannot be detached.

SDK 0.47.0 archive: 16052 bytes, SHA-256 `9f4b04d09d3791ca6967db817108ad84f83ef39a0262c2a7b2a77d221221132b`. It carries all 412 API operation IDs, including 406 business operations in OpenAPI 0.51.0.

SDK 0.48.0 archive: 16237 bytes, SHA-256 `f4224b41e76a31512c48d6a3abbad1298a8efc3b3592ee4393e87d0c0c6334b4`. It carries all 422 API operation IDs, including 416 business operations in OpenAPI 0.52.0.

SDK 0.49.0 archive: 16468 bytes, SHA-256 `a839e7170906cb3b68cb1b990197329cced2d21b71c40555eaa1e4663a36bb5d`. It carries all 434 API operation IDs, including 428 business operations in OpenAPI 0.53.0.

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

SDK 0.38.0 adds paginated training-record reads and CRUD with certificate upload support. Completion status and date remain server-controlled, and completed or expired records are retained as compliance evidence. The OpenAPI builder now follows shared handler validation helpers so operation schemas include the same field rules used by commands.

SDK 0.38.0 archive: 15237 bytes, SHA-256 `66743a14137c38d4fbc88e731436ff3f63ed4421a186105804e78e65044d60ba`. It carries all 371 API operation IDs, including the 365 business operations in OpenAPI 0.42.0.

SDK 0.39.0 adds product catalogue administration operations for paginated reads, creating configured products, editing catalogue metadata, and archiving products without deleting licence history. Its API writes and Filament create/edit/archive actions use shared Publishing application handlers. Archive size is 15349 bytes, SHA-256 `6432bfced4005b3b2a37e7bfff26eeeea700c8d9db8ead806b4d56f0ab1ff18b`. It carries all 376 API operation IDs, including the 370 business operations in OpenAPI 0.43.0.

SDK 0.40.0 adds delegated Advisory Council member reads and updates. Member creation remains the existing validation-certificate invitation workflow, and Filament edits now share the same Validation command. Archive size is 15428 bytes, SHA-256 `aa52ed5d10487d76b8e85e94fb1dd711f3637ca31053255dce5f39ecb41c479a`. It carries all 379 API operation IDs, including the 373 business operations in OpenAPI 0.44.0.

SDK 0.41.0 adds protected email-template reads and CRUD operations. Template variables and placeholders are validated, active templates must be deactivated before deletion, and Filament writes share the same Communications commands. The archive is 15507 bytes with SHA-256 `db63279c5e87c4a9f7666c028e5f87fcb80a2aba180813de640ca94f03e9719a`. It carries all 384 API operation IDs, including the 378 business operations in OpenAPI 0.45.0.

OpenAPI 0.46.0 and SDK 0.42.0 publish complete field and array-item validation schemas for 34 existing operations. The correction adds contract detail for request fields already accepted or required by the handlers; no endpoint behavior or authorization changed. A narrowly scoped compatibility exception list records each reviewed request-schema addition.

OpenAPI 0.47.0 and SDK 0.43.0 add array item length/pattern constraints and explicit confirmation fields inferred from existing validation rules. Endpoint behavior and authorization are unchanged.

SDK 0.43.0 archive: 15503 bytes, SHA-256 `6f2a9f8dbe4eeea989c8e5b23a339a51e1f477fb80feb4f3e244a76443d61977`. It carries all 384 API operation IDs described by OpenAPI 0.47.0.

SDK 0.42.0 archive: 15502 bytes, SHA-256 `864059425e43cb661a3d71deeac6f3bff15257e6708fcfad9c55277991dcdada`. It carries all 384 API operation IDs described by OpenAPI 0.46.0.

SDK 0.61.0 archive: 17823 bytes, SHA-256 `433a53212857309fe65b026d02334738f904e29db96514d768d1a74a1b480d8e`. It carries all 503 API operation IDs, including 497 business operations in OpenAPI 0.65.0.

SDK 0.64.0 adds a protected invoice-payment recording operation. Filament payment entry shares the finance command with API clients, and payment deletion is removed to preserve financial audit history. These operations are included in OpenAPI 0.68.0.

SDK 0.69.0 adds five protected operational reporting reads for the admin overview, report dashboard, expiring contracts, CRM summary, and people summary. The Filament reports and CRM/people dashboard widgets call the same application query handlers. These operations are included in OpenAPI 0.73.0.

SDK 0.69.0 archive: 18553 bytes, SHA-256 `bccbba9ef05710568d0cf3d9db59093e50add31842edeaaa24f1fc1bbf792ca5`. It carries all 532 API operation IDs, including 526 business operations in OpenAPI 0.73.0. The package remains a downloadable preview and is not published to a registry.










SDK 0.92.0 adds administrator API-client lifecycle operation identifiers: list, create, rotate and revoke. OpenAPI 0.96.0 documents administrator delegation, the two permitted machine-client scopes, safe list projections and one-time no-store secret responses. The archive contains all 605 operation IDs; it remains a server-side preview and is not published to npm.

SDK 0.93.0 adds administrator OAuth client lifecycle operation identifiers for listing, registering and revoking reviewed public authorization-code clients. OpenAPI 0.97.0 requires exact HTTPS redirect URIs, active admin ownership, owner-approved scopes, consent and S256 PKCE; no client secret is issued. The archive contains all 608 operation IDs and remains a downloadable server-side preview, not an npm registry release.

SDK 0.94.0 adds public API status and release changelog operation identifiers. OpenAPI 0.98.0 explicitly limits status claims to the API application responding to the current request; it does not claim dependency health or service-level availability. The archive contains all 610 operation IDs and remains a downloadable server-side preview.

SDK 0.94.0 adds public API status and release changelog operation identifiers. OpenAPI 0.98.0 explicitly limits status claims to the API application responding to the current request; it does not claim dependency health or service-level availability. The archive contains all 610 operation IDs and remains a downloadable server-side preview.

SDK candidate 0.96.0 aligns with OpenAPI candidate 0.100.0 and adds `integrationUsage()` for the machine client's 30-day UTC request-usage series. It remains a downloadable server-side preview and is not published to npm.

SDK 0.95.0 adds the authenticated self-profile operation for role-aware mobile clients. OpenAPI 0.99.0 documents the reserved OPES native callback and per-user role-derived scopes. The archive contains all 611 operation IDs and remains a downloadable server-side preview.

SDK 0.95.0 adds the authenticated self-profile operation for role-aware mobile clients. OpenAPI 0.99.0 documents the reserved OPES native callback and per-user role-derived scopes. The archive contains all 611 operation IDs and remains a downloadable server-side preview.
