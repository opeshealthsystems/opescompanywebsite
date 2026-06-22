# Codex Audit Remediation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Complete and verify every Codex-assigned item in the June 21 OHS audit without modifying Claude-owned scope or unrelated workspace changes.

**Architecture:** Preserve existing Laravel controllers, Filament resources, Blade views, and content structure. Add narrowly targeted regression tests at HTTP or source-contract seams, make the smallest implementation changes needed, and treat already-merged remediation as verification-only.

**Tech Stack:** PHP 8.3, Laravel 13, PHPUnit 12, Filament 3, Blade, Vite 8, npm.

---

### Task 1: Verify Already-Merged Codex Remediation

**Files:**
- Verify: `app/Http/Controllers/Customer/DashboardController.php`
- Verify: `app/Http/Controllers/Practitioner/DashboardController.php`
- Verify deletion: `resources/views/auth/practitioner-register.blade.php`
- Verify: `content/articles/09-hidden-cost-paper-based-medical-records-african-healthcare.fr.md`
- Verify: `content/articles/97-*.md` through `content/articles/113-*.md`

- [ ] **Step 1: Run existing regression tests**

Run:

```powershell
php artisan test --filter=CustomerDashboardTest
php artisan test --filter=PractitionerPortalTest
php artisan test --filter=BlogValidationTest
```

Expected: customer and blog tests pass; practitioner test may expose the separate Claude-owned `overall_rating` defect.

- [ ] **Step 2: Verify content and dead-code contracts**

Run focused `rg` checks confirming the orphan view is absent, the CNPS phrase is corrected, competitor claims are attributed, and prohibited clinical overclaims are absent.

### Task 2: Support Login Redirect

**Files:**
- Modify: `tests/Feature/LoginHardeningTest.php`
- Modify: `app/Http/Controllers/Auth/LoginController.php`

- [ ] **Step 1: Write a failing support redirect test**

Add a test that creates an active support user, posts valid credentials to `/login`, and expects a redirect containing `/en/support/dashboard`.

- [ ] **Step 2: Verify RED**

Run:

```powershell
php artisan test --filter=LoginHardeningTest
```

Expected: support redirect assertion fails because the current response is `/admin`.

- [ ] **Step 3: Implement the minimal mapping change**

Limit the admin-panel branch to `super_admin` and `admin`, then add:

```php
'support' => 'support.dashboard',
```

to `$portalRoutes`.

- [ ] **Step 4: Verify GREEN**

Run the same test command and expect all tests in the class to pass.

### Task 3: Practitioner Admin Visibility Gaps

**Files:**
- Create: `tests/Feature/PractitionerAdminResourceFieldsTest.php`
- Modify: `app/Filament/Resources/PractitionerProfileResource.php`
- Modify: `app/Filament/Resources/PractitionerApplicationResource.php`
- Modify: `app/Filament/Resources/PractitionerBugReportResource.php`

- [ ] **Step 1: Write failing source-contract tests**

Assert that:

```php
PractitionerProfileResource.php
```

contains a disabled `payout_number` form field and an infolist entry;

```php
PractitionerApplicationResource.php
```

contains a `Payout Details` section with `payout_reference`, `payout_provider`, `payout_initiated_at`, `paid_at`, and `payout_failure_reason`;

```php
PractitionerBugReportResource.php
```

contains a disabled image-capable `FileUpload` for `screenshot_path`.

- [ ] **Step 2: Verify RED**

Run:

```powershell
php artisan test --filter=PractitionerAdminResourceFieldsTest
```

Expected: assertions fail because the fields are absent.

- [ ] **Step 3: Add minimal Filament components**

Add:

```php
Forms\Components\TextInput::make('payout_number')->disabled()->dehydrated(false)
```

and a matching infolist text entry; add the payout infolist section; add:

```php
Forms\Components\FileUpload::make('screenshot_path')
    ->image()
    ->disk('public')
    ->disabled()
    ->dehydrated(false)
```

- [ ] **Step 4: Verify GREEN**

Run the focused test and relevant practitioner portal tests.

### Task 4: Cameroon-Local Session Date Validation

**Files:**
- Modify: `tests/Feature/DailyTestSessionTest.php`
- Modify: `app/Http/Controllers/Practitioner/Validation/SessionController.php`

- [ ] **Step 1: Write a failing timezone-boundary test**

Freeze time at `2026-06-21 23:30:00 UTC`, submit a session dated `2026-06-22`, and assert it is accepted because the local Cameroon date is already June 22.

- [ ] **Step 2: Verify RED**

Run:

```powershell
php artisan test --filter=DailyTestSessionTest
```

Expected: the new assertion fails with a date validation error.

- [ ] **Step 3: Use an explicit local date boundary**

Replace `before_or_equal:today` with:

```php
'required|date|before_or_equal:'.now('Africa/Douala')->toDateString()
```

- [ ] **Step 4: Verify GREEN**

Run the focused session test class and expect all tests to pass.

### Task 5: Public Form HTML5 Validation and Sitemap Coverage

**Files:**
- Create: `tests/Feature/PublicFormMarkupTest.php`
- Create: `tests/Feature/SitemapTest.php`
- Modify: `resources/views/pages/home.blade.php`
- Modify: `resources/views/pages/contact.blade.php`

- [ ] **Step 1: Write failing markup tests**

Assert required and maximum-length attributes matching server validation for home/contact name and email fields, plus bounded optional phone, products, and message fields.

- [ ] **Step 2: Verify RED**

Run:

```powershell
php artisan test --filter=PublicFormMarkupTest
```

Expected: assertions fail for the current unbounded/unrequired fields.

- [ ] **Step 3: Add matching HTML5 attributes**

Use `required`, `type="email"`, and the controller-aligned limits: name 100, email 150, phone 30, products 255, message 2000.

- [ ] **Step 4: Add sitemap regression coverage**

Assert `/sitemap.xml` contains public locale URLs and does not contain `/customer/`, `/practitioner/`, `/tester/`, `/manager/`, `/hr/`, `/accountant/`, `/support/`, or `/admin`.

- [ ] **Step 5: Verify GREEN**

Run both focused test classes.

### Task 6: Node Advisory and Production Log Rotation

**Files:**
- Modify: `package.json` if required
- Modify: `package-lock.json`
- Modify: `.env.production.example`
- Modify: `.env.live`

- [ ] **Step 1: Capture current audit failure**

Run:

```powershell
npm.cmd audit --audit-level=high
```

Expected: shell-quote advisory is reported.

- [ ] **Step 2: Apply the narrow dependency remediation**

Run:

```powershell
npm.cmd audit fix
```

Accept only lockfile/package changes resolving `shell-quote`; do not change unrelated dependencies.

- [ ] **Step 3: Enable daily log rotation**

Set:

```dotenv
LOG_CHANNEL=daily
```

in `.env.production.example` and `.env.live`, removing the obsolete single-file stack pairing where applicable.

- [ ] **Step 4: Verify**

Run `npm.cmd audit --audit-level=high` and `npm.cmd run build`; expect exit code 0.

### Task 7: Local Database Seeding

**Files:**
- No source changes expected.

- [ ] **Step 1: Run the assigned seeder**

Run:

```powershell
php artisan db:seed
```

Expected: all configured seeders complete without exception.

- [ ] **Step 2: Verify seeded records**

Use read-only Artisan/Tinker queries or database counts to confirm users, roles, permissions, products, document templates, and blog posts exist.

### Task 8: Final Verification and Report

**Files:**
- Verify only all assistant-attributable changes.

- [ ] **Step 1: Check scope isolation**

Run `git diff --name-only`, inspect every changed file, and confirm Claude-owned and pre-existing dirty files were not changed.

- [ ] **Step 2: Run focused tests**

Run all audit-related test classes.

- [ ] **Step 3: Run broad verification**

Run:

```powershell
php artisan test
npm.cmd audit --audit-level=high
npm.cmd run build
git diff --check
```

- [ ] **Step 4: Report evidence**

List completed, already-completed, blocked, or deferred items; include exact verification results and identify pre-existing or Claude-owned failures separately.
