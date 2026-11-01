# OPES Health mobile app — screen and workflow blueprint

**Product promise:** one trusted app for health operations and learning.

**Design status:** proposal and clickable visual preview. This plan preserves the approved OPES seal palette; it does not replace app source styling before review.

**Visual direction 02:** move from a generic card dashboard to a branded operations workspace. The home screen now leads with one role-specific priority panel, uses compact metric tiles only for quick scanning, and groups the next two tasks as tap-ready rows. The prototype applies this hierarchy to all six roles and gives the app frame a navy edge, restrained role switcher and line icons.

## Visual system

- **Canvas:** light-first, high-contrast work surfaces for clinical and operational reading. Page background `#F3F6FA`; white cards `#FFFFFF`; body text `#102A43`; secondary text `#425A70`.
- **Brand:** navy `#073656` for app identity and navigation; mature steel blue `#315F7D` for primary actions (6.85:1 contrast with white); darker readable links `#075985`; cyan `#69D5F5` and blue-silver `#8AC5ED` are small accents only.
- **State:** success `#176644`, warning `#77520B`, danger `#8E3540`, each paired with a text label and neutral outline/surface. Never encode state by color alone.
- **Surface treatment:** solid fills, 1px borders and visible focus outlines. No gradients, glow, colored shadows, blur or text shadows. Reserve rounded shapes for buttons, filter chips and modest card corners; avoid floating decoration.
- **Type:** Source Sans 3 for UI and learning content; use tabular numerals for money, percentages and due dates. System fallback remains available. Use a monospace face only for copyable references/IDs.
- **Spacing and targets:** 4px rhythm, 16px default page gutter (14px on very narrow phones), 8–10px card corners, 48px primary controls, at least 44px role/filter controls, safe-area-aware bottom navigation, and a visible focus outline in both themes.
- **Readability floor:** 16px form input text to prevent mobile browser zoom; 14px primary body copy and labels; 12px minimum supporting copy/status labels; navigation labels at 11px or larger. Keep muted text readable against both light and dark surfaces.
- **Responsive behavior:** one column from 320px through phone widths; long values wrap; forms use a single column unless paired numeric values remain readable; tablet/desktop content caps near 620px. Every screen scrolls vertically with no horizontal page scroll.

### Dashboard composition

Every role home follows the same scan order while keeping its own content:

1. **Where am I?** A short workspace label, personalized greeting and current date sit directly below the persistent branded header.
2. **What matters next?** One solid navy priority panel names a specific outcome, summarizes the key context in one sentence, shows a concise due/status cue, and contains one obvious action. Practitioner gets a resume-learning action; customer gets service status; partner gets directory/application access.
3. **How much is active?** Two-by-two metric tiles show small, scope-authorized counts with a clear label and a short qualifier. Avoid charts, decorative graphics and duplicated dashboard totals.
4. **What should I open?** The next two real work items appear as compact rows with a consistent outlined icon, clear title, supporting reference and status word. Each row opens its detail.
5. **What else can I do?** Secondary actions use a horizontal row of text buttons. The four persistent tabs remain the main wayfinding system.

The preview values and identities are explicitly illustrative. Production homes must be driven by the signed-in account and live summary/list APIs, with no sample-data fallback on error.

## App shell and entry

1. **Welcome / sign-in:** OPES mark, email sign-in action and create-account action. No role chooser. OAuth authorization code with S256 PKCE returns through `opeshealth://oauth/callback`; `/me` and current effective scopes determine the workspace.
2. **Create account:** individual, health facility or practitioner. Account role is assigned by the live server. Staff/admin and tester access is invite/approval controlled; partner access requires a supported account/API.
3. **Consent / secure authentication:** system-managed OPES authorization sheet; the app does not collect an OAuth password in an embedded form. Show clear progress, cancellation and recoverable failure states.
4. **Shared app shell:** branded top bar, current workspace label, optional alert count, a 4-destination bottom bar, safe areas, offline/retry banner and a profile/account destination. Tab labels and routes adapt to effective RBAC scopes. A user never sees actions the API will reject based on scopes.

## Role workspaces

| Role | Home screen | Four primary destinations | Core workflows |
| --- | --- | --- | --- |
| Administrator | Operations overview with only authorized live counts, approvals and overdue work | Home · Work · People · More | Quote review/send, enquiry follow-up, customer and partner records, CRM, authorized API client and access administration |
| Staff | Personal work queue and due follow-ups; no admin-only totals | Home · Work · Contacts · Profile | Draft/review quotes, follow up leads, inspect assigned customer/partner records, update allowed statuses |
| Customer / facility | Account status, recent requests, open tickets and due invoices | Home · Services · Support · Account | Request service, view request status, open/reply to tickets, view invoices and update organization profile |
| Practitioner | Continue a course, progress summary and earned certificates | Home · Courses · Exams · Account | Browse courses, study lessons, save lesson progress, review certificates, manage professional profile; Exams opens real exam records only when an exam API exists |
| Tester | Assigned test work and report status | Home · Assignments · Issues · Profile | Open assignment, update allowed status, file a bug report and revisit submitted issues |
| Partner | Public network information and application status | Home · Directory · Applications · Profile | Browse listed partners and submit/track an application; private account settings wait for partner self-service API support |

Admin and staff share business entities but not access: account scopes decide whether People, API client administration, financial totals and approvals appear. The interface must not equate an `admin` label with permission to every operation.

## Screen inventory

### Shared screens

- Sign-in, account creation, authorization progress/cancel/error, session expired, offline, request denied, retry and no access.
- Home per role, role-appropriate inbox/alerts, search within the signed-in user's authorized scope, profile, notification preferences, help/support and sign out.
- Reusable detail page: title/reference, plain-text status, timeline/audit trail where available, record fields, permitted actions and an explicit loading/empty/error state.
- Reusable form page: validated fields, required markers, autosafe layout, disabled/busy submission, idempotent retry, success reference and next action.

### Administrator and staff

- **Home:** `Today`, work requiring action, recent activity and quick actions. Values are API-supplied and scope-gated; never hard-coded demo totals.
- **Work:** quotes, CRM deals and partner applications. Filters, status chips, detail, quote line items, draft/send actions and refresh.
- **People:** customers, facilities, contacts/leads, search and profile/detail pages. Admin-only create/update actions.
- **More:** own profile, notification preferences, API client/public OAuth client management only when the matching management scope is effective, help and sign out.
- **Quote flow:** choose product/pricing from the server catalogue → create quote with quantities/approximate amount → review saved quote → send/mark sent when permitted → show email/transport result distinctly from inbox delivery.

### Customer / facility

- **Home:** active service requests, open support count, next due invoice and facility account summary.
- **Services:** request form, product/service, need, preferred time/location, saved reference and status timeline.
- **Support:** ticket list, ticket detail, conversation, reply and new ticket. Internal replies are never exposed.
- **Account:** facility/contact profile, editable fields, invoice list/detail and sign-out/security settings.

### Practitioner

- **Home:** resume study, total active courses, overall progress and recently earned certificate.
- **Courses:** browse/filter, course details, lesson list, lesson reader, previous/next and completion state. Show progress from live enrollments, not client estimates.
- **Exams:** separate exam catalogue, instructions, attempt, question/answer, submit, result and review screens only after a versioned assessment API exists. Until then show a clear “No exam service is available for this account yet” state; do not mislabel study lessons as exams.
- **Certificates:** course award and validation certificate detail, issuer, dates and download action only when the API supplies a permitted file link.
- **Account:** practitioner profile and workplace details.

### Tester

- **Home:** assigned, in-progress and due items from live API records.
- **Assignments:** assignment list/detail, start/complete actions, due date and project/version information if available.
- **Issues:** bug report form with steps, expected/actual results, priority, attachment only if an upload endpoint exists, and report history.
- **Profile:** tester profile and sign out.

### Partner

- **Home:** network discovery and application status based on public records. Do not imply private partnership data exists.
- **Directory:** public partner list and detail with verification labels sourced from the API.
- **Applications:** start application, validation, submission reference and status if the API returns it.
- **Profile:** public application contact information only; private organization membership and service management remain blocked on an absent API contract.

## Navigation and interaction rules

- A bottom-tab change preserves the current role and scrolls the target destination to its top. Detail and form pages use an in-stack back action and keep draft state while returning.
- Forms show server validation next to fields, distinguish network errors from permission failures, preserve values on retry and use a stable idempotency key for an unchanged command.
- Detail actions are API-driven. A completed action refreshes the source resource and announces the returned status/reference.
- Status badges pair words with subdued semantic colors. Progress bars include a numeric percentage and accessible value.
- Content loading uses neutral skeletons/spinners; empty states name the next supported action; failures show retry and request ID when available.
- Demo workspaces stay clearly labelled and remain local-only. Live dashboards never fall back to sample rows after an API failure.

## API dependencies and honest gaps

- **Available:** `/me` identity/effective scopes; customer profile/service requests/tickets/invoices; staff financial/CRM/customer records; tester assignments; practitioner profile/courses/lesson completion/certificates; public partner directory/application; OAuth PKCE registration and token lifecycle.
- **Required before full release:** dashboard summary projections for each role; partner private organization/profile/membership/workflows; a separately modelled assessment/exam domain; any missing customer/employee/admin operations revealed by endpoint-by-endpoint UI inventory; API usage/support/billing views for API customers.
- A screen is not marked functional until its endpoint exists, response fields are documented, authorization is enforced, and one live account can complete the whole interaction. A visible button without that backing is removed or clearly disabled.

## Main navigation flow

```mermaid
flowchart TD
  A[Welcome] --> B[Create account]
  A --> C[OAuth PKCE sign-in]
  B --> D[Live account registration API]
  D --> A
  C --> E[App callback and token exchange]
  E --> F[/me: roles and effective scopes]
  F --> G[RBAC-selected home]
  G --> H[Role-specific tabs]
  H --> I[Live list and detail APIs]
  I --> J[Validated command API]
  J --> K[Refresh resource and show saved outcome]
```
