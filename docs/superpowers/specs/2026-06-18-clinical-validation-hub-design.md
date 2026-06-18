# Clinical Validation & Innovation Hub — Design Spec (Sub-project 1: Foundation + Issue Management)

> **For agentic workers:** REQUIRED SUB-SKILL: Use `superpowers:subagent-driven-development` (recommended) or `superpowers:executing-plans` to implement this plan task-by-task.

**Goal:** Extend the existing Practitioner portal with a structured clinical validation workflow — Cohorts, daily testing sessions, formal 14-field issue reporting, and a clinical/product triage pipeline — without touching any existing models or flows.

**Architecture:** Two-phase pipeline (apply → place). Practitioners apply to a ValidationProgram (a `PractitionerProgram` with `program_type = 'validation'`) via the existing application flow. After approval, an admin "Place in Cohort" Filament action creates a `CohortMember` record. The practitioner then sees a new "Validation Hub" portal section. All new entities live in new tables with no modifications to existing models except one column addition to `practitioner_programs`.

**Scope:** Sub-project 1 of 4. Covers Phases 1–8 of the full spec: foundation, cohort management, test case catalog, daily testing, issue submission, clinical review, and product review triage. Phases 9–14 (Developer Tasks, Retesting, Weekly Review, Certification, Advisory Council) are separate sub-projects.

**Tech stack:** Laravel 13.8 / PHP 8.3 / Spatie Permission v8 / Filament v3 / Blade + Tailwind / SQLite (tests) / MySQL (production)

---

## Decomposition into Sub-projects

| Sub-project | Phases | Scope |
|---|---|---|
| **1 — Foundation + Issue Management** (this spec) | 1–8 | Cohorts, catalog, daily sessions, IssueReport, ClinicalReview, ProductReview |
| 2 — Developer Resolution + Retesting | 9–10 | DeveloperTask, Retest, automation |
| 3 — Reporting + Weekly Review | 11 + dashboards | WeeklyReview, FinalEvaluation, 4 dashboards |
| 4 — Certification + Advisory Council | 13–14 | Scoring model, certificates, council membership |

---

## Section 1: Data Model

### Existing table change

`practitioner_programs` — add one column:
- `program_type` (string, default `'general'`) — values: `'general'` | `'validation'`

No other existing tables are modified.

### New tables

#### `cohorts`
```
id
practitioner_program_id  FK → practitioner_programs, cascadeOnDelete
name                     string (e.g. "July 2026 — Pharmacy Cohort")
specialty                string (e.g. "Pharmacy", "Nursing", "Laboratory")
description              text, nullable
start_date               date
end_date                 date
max_members              unsignedInteger, nullable
status                   enum: draft | active | completed  (default: draft)
timestamps
```

#### `cohort_members`
```
id
cohort_id    FK → cohorts, cascadeOnDelete
user_id      FK → users, cascadeOnDelete
status       enum: active | suspended | completed | removed  (default: active)
placed_at    timestamp, nullable
timestamps
unique(cohort_id, user_id)
```

#### `validation_products`
```
id
name         string  (e.g. "OPES Health OS")
code         string, unique  (e.g. "ohos")
description  text, nullable
is_active    boolean, default true
timestamps
```

#### `validation_modules`
```
id
validation_product_id  FK → validation_products, cascadeOnDelete
name                   string  (e.g. "Patient Registration")
code                   string  (e.g. "patient_registration")
description            text, nullable
is_active              boolean, default true
timestamps
unique(validation_product_id, code)
```

#### `validation_workflows`
```
id
validation_module_id  FK → validation_modules, cascadeOnDelete
name                  string  (e.g. "Create New Patient")
code                  string  (e.g. "create_new_patient")
description           text, nullable
is_active             boolean, default true
timestamps
unique(validation_module_id, code)
```

#### `validation_test_cases`
```
id
validation_workflow_id  FK → validation_workflows, cascadeOnDelete
title                   string
description             text, nullable
steps                   text, nullable
expected_result         text, nullable
is_active               boolean, default true
timestamps
```

#### `cohort_test_cases`
```
id
cohort_id                  FK → cohorts, cascadeOnDelete
validation_test_case_id    FK → validation_test_cases, cascadeOnDelete
due_date                   date, nullable
timestamps
unique(cohort_id, validation_test_case_id)
```

#### `daily_test_sessions`
```
id
cohort_member_id          FK → cohort_members, cascadeOnDelete
validation_product_id     FK → validation_products, restrict
validation_module_id      FK → validation_modules, restrict
validation_workflow_id    FK → validation_workflows, restrict
facility_context          string, nullable  (free text: facility name / scenario)
date                      date
start_time                time, nullable
end_time                  time, nullable
tasks_completed           unsignedInteger, default 0
screenshots               json, nullable  (array of storage paths)
comments                  text, nullable
timestamps
```

#### `issue_reports`
```
id
cohort_member_id            FK → cohort_members, cascadeOnDelete
daily_test_session_id       FK → daily_test_sessions, nullable, nullOnDelete
validation_product_id       FK → validation_products, restrict
validation_module_id        FK → validation_modules, restrict
validation_workflow_id      FK → validation_workflows, restrict
validation_test_case_id     FK → validation_test_cases, nullable, nullOnDelete
title                       string
issue_type                  enum: bug | missing_feature | workflow_problem | clinical_risk |
                                  ui_ux_problem | performance_issue | security_concern |
                                  interoperability_issue | data_quality_issue | recommendation
severity                    enum: critical | high | medium | low
description                 text
steps_to_reproduce          text
expected_result             text
actual_result               text
clinical_impact             text
recommendation              text, nullable
attachments                 json, nullable  (array of storage paths)
status                      enum: submitted | clinical_review | product_review | accepted |
                                  rejected | duplicate | needs_more_information |
                                  sent_to_development | fixed | closed
                            default: submitted
                            (Note: ready_for_retest | retest_passed | retest_failed added in Sub-project 2)
timestamps
```

#### `clinical_reviews`
```
id
issue_report_id  FK → issue_reports, cascadeOnDelete
reviewer_id      FK → users
decision         enum: approved_for_product_review | rejected | needs_more_information
notes            text, nullable
reviewed_at      timestamp, nullable
timestamps
```

#### `product_reviews`
```
id
issue_report_id  FK → issue_reports, cascadeOnDelete
reviewer_id      FK → users
decision         enum: accepted | rejected | duplicate | sent_to_development
notes            text, nullable
reviewed_at      timestamp, nullable
timestamps
```

### Relationship chain

```
PractitionerProgram (program_type = 'validation')
  └─ hasMany Cohort
       └─ hasMany CohortMember (user ↔ cohort, placed by admin)
       │    └─ hasMany DailyTestSession
       │    └─ hasMany IssueReport
       │         └─ hasOne ClinicalReview
       │         └─ hasOne ProductReview
       └─ hasMany CohortTestCase
            └─ belongsTo ValidationTestCase

ValidationProduct
  └─ hasMany ValidationModule
       └─ hasMany ValidationWorkflow
            └─ hasMany ValidationTestCase
```

### Models to create

| Model | File |
|---|---|
| `Cohort` | `app/Models/Cohort.php` |
| `CohortMember` | `app/Models/CohortMember.php` |
| `ValidationProduct` | `app/Models/ValidationProduct.php` |
| `ValidationModule` | `app/Models/ValidationModule.php` |
| `ValidationWorkflow` | `app/Models/ValidationWorkflow.php` |
| `ValidationTestCase` | `app/Models/ValidationTestCase.php` |
| `CohortTestCase` | `app/Models/CohortTestCase.php` |
| `DailyTestSession` | `app/Models/DailyTestSession.php` |
| `IssueReport` | `app/Models/IssueReport.php` |
| `ClinicalReview` | `app/Models/ClinicalReview.php` |
| `ProductReview` | `app/Models/ProductReview.php` |

`PractitionerProgram` gets two new relationship methods: `cohorts()` hasMany and a `scopeValidation()` query scope.

`User` gets: `cohortMembers()` hasMany.

---

## Section 2: Filament Admin

### Six new Filament resource classes (four primary, two hidden)

All in `app/Filament/Resources/`. All restricted via:
```php
public static function canAccess(): bool
{
    return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
}
```

---

#### `ValidationProductResource`

**File:** `app/Filament/Resources/ValidationProductResource.php`

Table columns: name, code, module count, is_active badge.

Form fields: name (required), code (required, unique, snake_case), description (textarea), is_active (toggle).

Three RelationManagers (nested tabs):
- `ModulesRelationManager` — CRUD for `ValidationModule` rows belonging to this product
- Each module row opens inline to its `WorkflowsRelationManager`
- Each workflow row opens inline to its `TestCasesRelationManager`

Because Filament's standard nested relation managers are two levels deep, implement as:
- `ValidationProductResource` → `ModulesRelationManager`
- `ValidationModuleResource` (separate flat resource, hidden from nav) → `WorkflowsRelationManager`
- `ValidationWorkflowResource` (separate flat resource, hidden from nav) → `TestCasesRelationManager`

Navigation: "Validation Catalog" group, icon `beaker`.

---

#### `CohortResource`

**File:** `app/Filament/Resources/CohortResource.php`

Table columns: name, specialty, program name, start_date, end_date, member count, status badge.

Form fields:
- `practitioner_program_id` — select filtered to `program_type = 'validation'`
- `name` — string required
- `specialty` — string required
- `description` — textarea nullable
- `start_date`, `end_date` — date pickers
- `max_members` — numeric nullable
- `status` — select (draft/active/completed)

Two RelationManagers:
- `MembersRelationManager` — view `CohortMember` rows (user name, status, placed_at). No create from here — placement happens via the action on PractitionerApplicationResource. Can change member status (active/suspended/removed).
- `TestCasesRelationManager` — attach/detach `ValidationTestCase` rows to this cohort. Shows test case title, workflow, module, product, due_date (editable inline).

Navigation: "Validation Catalog" group, icon `user-group`.

---

#### "Place in Cohort" action on `PractitionerApplicationResource`

**File:** `app/Filament/Resources/PractitionerApplicationResource/Actions/PlaceInCohortAction.php`

A `Filament\Tables\Actions\Action` added to the existing `PractitionerApplicationResource` table.

Visibility condition: `record->status === 'approved' && record->program->program_type === 'validation' && !$record->user->cohortMembers()->whereHas('cohort', fn($q) => $q->where('practitioner_program_id', $record->practitioner_program_id))->exists()`

Note: use the relationship name that already exists on `PractitionerApplication` to reach its program (verify with `grep -r "function program\|function practitionerProgram" app/Models/PractitionerApplication.php` before implementing). The relationship is expected to be named `program()` returning a BelongsTo on `PractitionerProgram`.

Modal form: single select `cohort_id` — options are active cohorts belonging to the application's program.

On submit:
```php
CohortMember::create([
    'cohort_id' => $data['cohort_id'],
    'user_id'   => $record->user_id,
    'status'    => 'active',
    'placed_at' => now(),
]);
```

Success notification: "Practitioner placed in cohort."

---

#### `IssueReportResource`

**File:** `app/Filament/Resources/IssueReportResource.php`

Table columns: title, severity (badge with colours: critical=red, high=orange, medium=yellow, low=blue), issue_type, cohort name, product name, status (badge), submitted_at.

Status filter tabs: All | Submitted | Clinical Review | Product Review | Accepted | Sent to Development | Closed.
(Ready for Retest tab added in Sub-project 2.)

No create form — issue reports are practitioner-submitted only. View-only detail page.

**Four table actions:**

1. **Start Clinical Review** — visible when `status === 'submitted'`.
   Opens modal with: `decision` (select: approved_for_product_review / rejected / needs_more_information), `notes` (textarea).
   On submit: creates `ClinicalReview` record, then maps the clinical review decision to `issue_reports.status` as follows:
   - `approved_for_product_review` → status stays `clinical_review` (awaiting "Send to Product Review" action)
   - `rejected` → status = `rejected` (same enum value reused; the `ClinicalReview.decision` field distinguishes the stage that rejected it)
   - `needs_more_information` → status = `needs_more_information`

2. **Send to Product Review** — visible when `status === 'clinical_review'` and clinical review decision is `approved_for_product_review`.
   No modal — single click updates status to `product_review`.

3. **Product Decision** — visible when `status === 'product_review'`.
   Opens modal with: `decision` (select: accepted / rejected / duplicate / sent_to_development), `notes` (textarea).
   On submit: creates `ProductReview` record, updates status accordingly.

4. **Close** — visible when status is `accepted`, `rejected`, or `duplicate`.
   Single click sets status to `closed`.
   (Retest statuses added to this action's visibility condition in Sub-project 2.)

Navigation: "Validation Hub" group, icon `exclamation-circle`.

---

#### `DailyTestSessionResource`

**File:** `app/Filament/Resources/DailyTestSessionResource.php`

Read-only table: date, practitioner name, cohort name, product, module, workflow, tasks_completed, issue count (count of IssueReports linked to this session).

Detail view shows: all session fields + screenshots (rendered as image previews from storage) + linked issue reports table.

No create/edit actions — sessions are practitioner-submitted.

Navigation: "Validation Hub" group, icon `calendar`.

---

## Section 3: Practitioner Portal

### New sub-section: Validation Hub

Navigation link added to `resources/views/components/layouts/practitioner.blade.php` after existing nav links. Visible only when `auth()->user()->cohortMembers()->where('status', 'active')->exists()`.

### Routes

Added inside the existing practitioner route group in `routes/web.php`:

```php
Route::prefix('validation')->name('validation.')->group(function () {
    Route::get('/',                [\App\Http\Controllers\Practitioner\Validation\DashboardController::class, 'show'])->name('dashboard');
    Route::get('/sessions',        [\App\Http\Controllers\Practitioner\Validation\SessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/create', [\App\Http\Controllers\Practitioner\Validation\SessionController::class, 'create'])->name('sessions.create');
    Route::post('/sessions',       [\App\Http\Controllers\Practitioner\Validation\SessionController::class, 'store'])->name('sessions.store');
    Route::get('/issues',          [\App\Http\Controllers\Practitioner\Validation\IssueReportController::class, 'index'])->name('issues.index');
    Route::get('/issues/create',   [\App\Http\Controllers\Practitioner\Validation\IssueReportController::class, 'create'])->name('issues.create');
    Route::post('/issues',         [\App\Http\Controllers\Practitioner\Validation\IssueReportController::class, 'store'])->name('issues.store');
    Route::get('/issues/{issue}',  [\App\Http\Controllers\Practitioner\Validation\IssueReportController::class, 'show'])->name('issues.show');
});
```

All routes inherit `auth` + `role:practitioner` from the parent group.

### Controllers

**Directory:** `app/Http/Controllers/Practitioner/Validation/`

#### `DashboardController`

`show()`:
- Loads the user's active `CohortMember` with `cohort.practitionerProgram`.
- If no active cohort: returns view with `$cohortMember = null` (shows "not yet placed" message).
- Stats: session count, issue count, open issues, closed issues.
- Returns `practitioner.validation.dashboard`.

#### `SessionController`

`index()`: paginates `DailyTestSession` scoped to the user's `CohortMember`. Shows date, product, module, workflow, tasks_completed, linked issue count.

`create()`:
- Loads active `CohortMember`. Redirects to dashboard with notice if none.
- Loads **only** `ValidationProduct`, `ValidationModule`, `ValidationWorkflow` records that have at least one `CohortTestCase` row for this cohort. The query chain: `CohortTestCase → validation_test_case_id → ValidationTestCase.validation_workflow_id → ValidationWorkflow.validation_module_id → ValidationModule.validation_product_id`. Concretely:
  ```php
  $allowedWorkflowIds = CohortTestCase::where('cohort_id', $member->cohort_id)
      ->join('validation_test_cases', 'cohort_test_cases.validation_test_case_id', '=', 'validation_test_cases.id')
      ->pluck('validation_test_cases.validation_workflow_id');
  $workflows = ValidationWorkflow::whereIn('id', $allowedWorkflowIds)->get();
  $modules   = ValidationModule::whereIn('id', $workflows->pluck('validation_module_id')->unique())->get();
  $products  = ValidationProduct::whereIn('id', $modules->pluck('validation_product_id')->unique())->get();
  ```
- Returns `practitioner.validation.sessions.create` with `$products`, `$modules`, `$workflows`.

`store()`:
- Validates:
  ```php
  'validation_product_id'  => 'required|exists:validation_products,id',
  'validation_module_id'   => 'required|exists:validation_modules,id',
  'validation_workflow_id' => 'required|exists:validation_workflows,id',
  'facility_context'       => 'nullable|string|max:200',
  'date'                   => 'required|date|before_or_equal:today',
  'start_time'             => 'nullable|date_format:H:i',
  'end_time'               => 'nullable|date_format:H:i|after:start_time',
  'tasks_completed'        => 'required|integer|min:0|max:999',
  'comments'               => 'nullable|string|max:3000',
  'screenshots.*'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
  ```
- Stores uploaded files to `storage/app/validation/sessions/`, saves paths as JSON in `screenshots`.
- Creates `DailyTestSession` linked to `cohort_member_id`.
- Redirects to `validation.sessions.index` with success flash.

#### `IssueReportController`

`index()`: paginates `IssueReport` scoped to user's `CohortMember`. Shows title, severity badge, status badge, submitted_at.

`create()`:
- Loads same product/module/workflow scope as SessionController.
- Also loads `ValidationTestCase` records for the cohort.
- Returns `practitioner.validation.issues.create`.

`store()`:
- Validates all 14 spec fields:
  ```php
  'validation_product_id'   => 'required|exists:validation_products,id',
  'validation_module_id'    => 'required|exists:validation_modules,id',
  'validation_workflow_id'  => 'required|exists:validation_workflows,id',
  'validation_test_case_id' => 'nullable|exists:validation_test_cases,id',
  'daily_test_session_id'   => 'nullable|exists:daily_test_sessions,id',
  'title'                   => 'required|string|max:200',
  'issue_type'              => 'required|in:bug,missing_feature,workflow_problem,clinical_risk,ui_ux_problem,performance_issue,security_concern,interoperability_issue,data_quality_issue,recommendation',
  'severity'                => 'required|in:critical,high,medium,low',
  'description'             => 'required|string|max:5000',
  'steps_to_reproduce'      => 'required|string|max:5000',
  'expected_result'         => 'required|string|max:2000',
  'actual_result'           => 'required|string|max:2000',
  'clinical_impact'         => 'required|string|max:2000',
  'recommendation'          => 'nullable|string|max:2000',
  'attachments.*'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
  ```
- Stores attachments to `storage/app/validation/issues/`, saves paths as JSON.
- Creates `IssueReport` with `status = 'submitted'`, linked to `cohort_member_id`.
- Redirects to `validation.issues.index` with success flash.

`show()`:
- Loads `IssueReport` with `clinicalReview`, `productReview`, `cohortMember`.
- Aborts 403 if `$issue->cohortMember->user_id !== auth()->id()`.
- Shows all 14 fields, current status badge, most recent review note (from `ClinicalReview` or `ProductReview`).

### Views

All in `resources/views/practitioner/validation/`. All use `<x-layouts.practitioner>`.

| File | Purpose |
|---|---|
| `dashboard.blade.php` | Cohort card (name, specialty, dates) + 4 stat cards (sessions, issues, open, closed). "Not yet placed" state if no active cohort. |
| `sessions/index.blade.php` | Paginated session table. "Start New Session" button. |
| `sessions/create.blade.php` | Session form: product/module/workflow selects (scoped to cohort), date, times, tasks_completed, comments, screenshot upload. |
| `issues/index.blade.php` | Paginated issue table with severity + status badges. "Report New Issue" button. |
| `issues/create.blade.php` | 14-field issue form. Two columns: left=classification (product, module, workflow, test_case, issue_type, severity), right=detail (title, description, steps, expected, actual, clinical_impact, recommendation, attachments). |
| `issues/show.blade.php` | Issue detail: all fields, status timeline (submitted → clinical_review → product_review → ...), latest review note. |

---

## Section 4: Data Flow, Authorization & Seeding

### End-to-end flow

```
[Admin Setup]
1. Admin creates PractitionerProgram (program_type = 'validation')
2. Admin creates Cohorts (specialty + dates) under that program
3. Admin assigns ValidationTestCases to each Cohort via CohortTestCase pivot
4. Admin opens recruitment (program status → active)

[Recruitment]
5. Practitioner applies via existing PractitionerApplication flow
6. Admin approves application
7. Admin clicks "Place in Cohort" action → CohortMember created (status = active)

[Active Testing]
8. Practitioner sees "Validation Hub" nav link
9. Practitioner opens Dashboard → sees cohort context and stats
10. Practitioner submits DailyTestSession (product/module/workflow from cohort scope)
11. Practitioner submits IssueReport (14 fields, optional session link)

[Triage]
12. Admin sees IssueReport in "Submitted" tab of IssueReportResource
13. Admin: Start Clinical Review → decision + notes → status moves to clinical_review
    - If approved: Admin clicks "Send to Product Review" → status = product_review
    - If rejected/needs info: status updated, practitioner sees note on issue show page
14. Admin: Product Decision → accepted / rejected / duplicate / sent_to_development
15. Admin: Close → status = closed

[Visibility]
- Practitioner sees current status and latest review note on issues/{issue} page
```

### Authorization

**Practitioner portal:**
- Routes: `auth` + `role:practitioner` middleware (inherited from parent group)
- Active cohort check: done in each controller — not middleware. If no active `CohortMember`, redirect to practitioner dashboard with notice: "You have not been placed in a validation cohort yet."
- `IssueReport::show()`: abort 403 if `$issue->cohortMember->user_id !== auth()->id()`
- `DailyTestSession` FK scoping: `store()` verifies submitted IDs are in the cohort scope using the same `$allowedWorkflowIds` query from `create()`. Add a custom validation rule after `$request->validate([...])`:
  ```php
  abort_unless(in_array($validated['validation_workflow_id'], $allowedWorkflowIds->toArray()), 422, 'Workflow not in cohort scope.');
  ```

**Filament:**
- All 6 new resource classes: `canAccess()` returns `hasAnyRole(['super_admin', 'admin'])`
- `DailyTestSessionResource` and `IssueReportResource`: no `canCreate()` (practitioners submit via portal, not Filament)

### Seeder: `ValidationDataSeeder`

**File:** `database/seeders/ValidationDataSeeder.php`

Creates one `ValidationProduct` ("OPES Health OS", code: `ohos`) with 10 `ValidationModule` rows and 56 `ValidationWorkflow` children from the Workflow Test Library appendix at the bottom of this spec. One `ValidationTestCase` created per workflow as a baseline.

**Counts after seeding:** 1 product, 10 modules, 56 workflows, 56 test cases.

**Does NOT run in `DatabaseSeeder`** — must be called explicitly:
```bash
php artisan db:seed --class=ValidationDataSeeder
```

This avoids polluting the test database.

### Testing

**4 test classes, all using `RefreshDatabase` + `RolePermissionSeeder::class`.** All model instances are created via factories — `ValidationDataSeeder` is never called in tests. See Factories appendix for required factory files.

#### `ValidationCohortTest`
- Admin can create a cohort under a validation program
- Admin cannot create a cohort under a general program (validation)
- "Place in Cohort" action creates CohortMember record
- Placing same practitioner twice in same cohort fails (unique constraint)
- Practitioner without active cohort is redirected from validation routes

#### `DailyTestSessionTest`
- Practitioner with active cohort can submit a session
- Session is scoped to cohort's assigned products/modules/workflows
- Non-practitioner role gets 403 on session routes
- Practitioner without cohort is redirected

#### `IssueReportTest`
- Practitioner can submit a 14-field issue report
- All required fields validated (missing `severity` → 422)
- `issue_type` must be a valid enum value (invalid → 422)
- Owner can view their issue report
- Different practitioner gets 403 on someone else's issue

#### `IssueReportTriageTest`
- Admin can start clinical review (status: submitted → clinical_review)
- Approved clinical review enables product review action
- Product decision updates status correctly (sent_to_development, rejected, etc.)
- Non-admin cannot access IssueReportResource

---

## Success Criteria

| Area | Metric |
|---|---|
| Data model | 11 new tables, 1 column addition, all migrations run cleanly |
| Catalog | 56 test cases seeded from Workflow Test Library appendix |
| Admin | 6 Filament resource classes (4 primary + 2 hidden) + Place in Cohort action, all restricted to admin/super_admin |
| Practitioner portal | 6 new views, 3 controllers, all validation-scoped |
| Issue triage | 14-state status machine, ClinicalReview + ProductReview records created by admin actions |
| Tests | 4 test classes, 16+ tests, 0 failures, existing suite still green |

---

## Appendix A: Factories

One factory file required per new model. All in `database/factories/`. Minimum required attributes for each:

| Factory | Key attributes |
|---|---|
| `CohortFactory` | practitioner_program_id (validation type), name, specialty, start_date, end_date, status='active' |
| `CohortMemberFactory` | cohort_id, user_id, status='active', placed_at=now() |
| `ValidationProductFactory` | name, code (unique), is_active=true |
| `ValidationModuleFactory` | validation_product_id, name, code (unique per product), is_active=true |
| `ValidationWorkflowFactory` | validation_module_id, name, code (unique per module), is_active=true |
| `ValidationTestCaseFactory` | validation_workflow_id, title, steps, expected_result, is_active=true |
| `CohortTestCaseFactory` | cohort_id, validation_test_case_id, due_date=null |
| `DailyTestSessionFactory` | cohort_member_id, validation_product_id, validation_module_id, validation_workflow_id, date=today, tasks_completed=3 |
| `IssueReportFactory` | cohort_member_id, validation_product_id, validation_module_id, validation_workflow_id, title, issue_type='bug', severity='medium', description, steps_to_reproduce, expected_result, actual_result, clinical_impact, status='submitted' |
| `ClinicalReviewFactory` | issue_report_id, reviewer_id, decision='approved_for_product_review', reviewed_at=now() |
| `ProductReviewFactory` | issue_report_id, reviewer_id, decision='accepted', reviewed_at=now() |

---

## Appendix B: Workflow Test Library

Source data for `ValidationDataSeeder`. 1 product → 10 modules → 56 workflows → 56 test cases.

**Product:** OPES Health OS (code: `ohos`)

| Module (code) | Workflows |
|---|---|
| Patient Registration (`patient_registration`) | create_new_patient, search_existing_patient, detect_duplicate_patient, generate_health_id, print_or_display_qr_code |
| Clinical Consultation (`clinical_consultation`) | capture_vitals, record_history, document_diagnosis, create_treatment_plan, prescribe_medication, order_laboratory_test, complete_visit_note |
| Triage (`triage`) | capture_emergency_symptoms, assign_priority_level, escalate_critical_patient, send_to_consultation_queue |
| Laboratory (`laboratory`) | receive_lab_order, collect_sample, track_sample, enter_result, approve_result, send_result_to_doctor |
| Pharmacy (`pharmacy`) | receive_prescription, check_stock, dispense_medication, deduct_inventory, flag_expired_drug, record_substitution |
| Billing (`billing`) | bill_consultation, bill_lab_test, bill_medication, generate_invoice, record_payment, issue_receipt |
| CDMS (`cdms`) | upload_document, classify_document, approve_document, search_document, retrieve_patient_file, audit_document_access |
| Health ID & Interoperability (`health_id_interoperability`) | create_health_id, match_patient_across_facilities, send_referral, receive_referral, exchange_lab_result, view_longitudinal_record |
| CDSS (`cdss`) | trigger_drug_interaction_alert, trigger_allergy_alert, review_clinical_recommendation, accept_or_override_alert, document_override_reason |
| Reporting (`reporting`) | generate_daily_report, generate_clinical_report, generate_financial_report, generate_dashboard, export_report |

Each workflow gets one `ValidationTestCase` seeded with: title = human-readable name (e.g. "Create New Patient"), steps = null, expected_result = null. These are baseline stubs — clinical content expanded post-deployment.
