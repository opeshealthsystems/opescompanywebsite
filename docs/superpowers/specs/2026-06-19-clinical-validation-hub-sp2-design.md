# Clinical Validation & Innovation Hub — Design Spec (Sub-project 2: Developer Resolution + Retesting)

> **For agentic workers:** REQUIRED SUB-SKILL: Use `superpowers:subagent-driven-development` (recommended) or `superpowers:executing-plans` to implement the resulting plan task-by-task.

**Goal:** Close the validation loop. After an admin routes an issue to development, automatically create a developer work item; let an admin/developer track it to "fixed"; then have the **original reporting practitioner** retest the fix and either pass it (→ close) or fail it (→ reopen the task). All purely additive on top of the merged Sub-project 1.

**Architecture:** Extends SP1's on-model state machine. `IssueReport::recordProductReview()` (already the chokepoint for product decisions) auto-creates a single `DeveloperTask` when the decision is `sent_to_development`. Developer work is admin-managed in Filament (no new role, no new portal). Retesting is practitioner-driven through the existing Validation Hub portal, gated to the issue's original reporter. A `Retest` row records every attempt as immutable history.

**Scope:** Sub-project 2 of 4 (Phases 9–10 of the full vision). Builds on SP1 (cohorts, daily sessions, IssueReport with clinical→product triage). Phases 11–14 (Weekly Review + dashboards, Certification, Advisory Council) remain in Sub-projects 3–4.

**Tech stack:** Laravel 13.8 / PHP 8.3 / Spatie Permission v8 / Filament v3 / Blade + Tailwind / SQLite (tests) / MySQL (prod).

---

## Design decisions (locked in brainstorming)

| Decision | Choice |
|---|---|
| Who works DeveloperTasks | Admin/super_admin in Filament. No `developer` role, no new portal. `assigned_to` is any user. |
| Who retests | The original reporting practitioner only (the issue's `cohortMember.user`), via the existing portal. |
| DeveloperTask creation | Auto-created when a product decision sets the issue to `sent_to_development` (one task per issue, `firstOrCreate`). |
| Failed retest | Reopens the **same** DeveloperTask (`status = reopened`); one task per issue; each `Retest` row is the per-attempt history. |
| Closure | Explicit admin **Close** (SP1 action, visibility extended to `retest_passed`) — consistent with SP1's audit philosophy; no auto-close. |
| Auto-create mechanism | On-model in `IssueReport::recordProductReview()` (not an Eloquent observer) — matches SP1's explicit, testable state-machine methods. |

---

## Section 1: Data Model

### Existing table change
**None.** `issue_reports.status` is already a `string` column, so SP2 simply activates the three statuses SP1 reserved: `ready_for_retest | retest_passed | retest_failed`. They are added to `IssueReport::statusOptions()` (now 13 entries).

### New tables

Both use SP1 house style: `return new class extends Migration`, string columns with inline `// a|b|c` comments (no `$table->enum()`), FK helpers `->constrained()->cascadeOnDelete()` / `->nullable()->constrained()->nullOnDelete()`.

#### `developer_tasks`
```
id
issue_report_id   FK → issue_reports, cascadeOnDelete, UNIQUE   (one task per issue)
assigned_to       FK → users, nullable, nullOnDelete
title             string                                        (copied from issue.title at creation)
priority          string(10)   // critical|high|medium|low      (copied from issue.severity, editable)
status            string(20)   // open|in_progress|fixed|reopened|wont_fix   default: open
resolution_notes  text, nullable
started_at        timestamp, nullable
fixed_at          timestamp, nullable
timestamps
```

#### `retests`
```
id
issue_report_id    FK → issue_reports, cascadeOnDelete
developer_task_id  FK → developer_tasks, nullable, nullOnDelete   (the fix attempt this verifies)
cohort_member_id   FK → cohort_members, cascadeOnDelete           (the reporter's membership)
result             string(10)   // passed|failed
notes              text
attachments        json, nullable   (array of storage paths)
retested_at        timestamp
timestamps
```

### Relationship chain
```
IssueReport
  └─ hasOne  DeveloperTask        (developerTask)
  │    └─ belongsTo User          (assignedTo)
  │    └─ hasMany Retest          (retests)
  └─ hasMany Retest               (retests)
       └─ belongsTo CohortMember  (cohortMember)
       └─ belongsTo DeveloperTask (developerTask)
```

### Models to create
| Model | File |
|---|---|
| `DeveloperTask` | `app/Models/DeveloperTask.php` |
| `Retest` | `app/Models/Retest.php` |

**`DeveloperTask`** — `use HasFactory;` `$fillable = ['issue_report_id','assigned_to','title','priority','status','resolution_notes','started_at','fixed_at'];` `$casts = ['started_at'=>'datetime','fixed_at'=>'datetime'];`
- Relationships: `issueReport()` belongsTo, `assignedTo()` belongsTo(User,'assigned_to'), `retests()` hasMany.
- Statics: `statusOptions()` (open/in_progress/fixed/reopened/wont_fix), `priorityOptions()` (critical/high/medium/low — reuse `IssueReport::severityOptions()` values).
- Methods (the state machine):
  - `markInProgress(): void` — `status='in_progress'`, set `started_at` if null.
  - `markFixed(?string $notes = null): void` — `status='fixed'`, `fixed_at=now()`, `resolution_notes=$notes`; sets `issueReport.status='ready_for_retest'`.
  - `reopen(): void` — `status='reopened'`.
  - `markWontFix(?string $notes = null): void` — `status='wont_fix'`, `resolution_notes=$notes`; sets `issueReport.status='rejected'` (so it can be Closed via the normal SP1 path).

**`Retest`** — `use HasFactory;` `$fillable = ['issue_report_id','developer_task_id','cohort_member_id','result','notes','attachments','retested_at'];` `$casts = ['attachments'=>'array','retested_at'=>'datetime'];`
- Relationships: `issueReport()` belongsTo, `developerTask()` belongsTo, `cohortMember()` belongsTo.
- Static: `resultOptions()` (passed/failed).

### Edits to existing `IssueReport` model (additive only)
- Add `developerTask(): HasOne` and `retests(): HasMany`.
- Add 3 entries to `statusOptions()`: `ready_for_retest => 'Ready for Retest'`, `retest_passed => 'Retest Passed'`, `retest_failed => 'Retest Failed'`.
- Extend `recordProductReview(int $reviewerId, string $decision, ?string $notes = null)`: after the existing logic, when `$decision === 'sent_to_development'`, create the task:
  ```php
  if ($decision === 'sent_to_development') {
      $this->developerTask()->firstOrCreate(
          ['issue_report_id' => $this->id],
          ['title' => $this->title, 'priority' => $this->severity, 'status' => 'open']
      );
  }
  ```
- Add a retest helper used by the portal controller (keeps transitions on-model):
  ```php
  public function recordRetest(int $cohortMemberId, string $result, ?string $notes, ?array $attachments = null): Retest
  {
      $retest = $this->retests()->create([
          'developer_task_id' => $this->developerTask?->id,
          'cohort_member_id'  => $cohortMemberId,
          'result'            => $result,
          'notes'             => $notes,
          'attachments'       => $attachments,
          'retested_at'       => now(),
      ]);

      if ($result === 'passed') {
          $this->update(['status' => 'retest_passed']);
      } else {
          $this->update(['status' => 'retest_failed']);
          $this->developerTask?->reopen();
      }

      return $retest;
  }
  ```

---

## Section 2: State-machine flow

```
[SP1] Admin product decision = sent_to_development
   └─► IssueReport::recordProductReview() firstOrCreate DeveloperTask (open, priority=severity, title=issue.title)
       IssueReport.status = sent_to_development

[Developer work — admin/Filament]
   open ──Start──► in_progress ──Mark Fixed──► fixed
       markFixed(): fixed_at=now; IssueReport.status = ready_for_retest
   (manual escape hatch) any → wont_fix: IssueReport.status = rejected

[Practitioner retest — original reporter's portal]
   IssueReport.status = ready_for_retest → reporter submits Retest:
     PASS → Retest(passed); IssueReport.status = retest_passed
     FAIL → Retest(failed); IssueReport.status = retest_failed; DeveloperTask.reopen()

[Re-fix loop]
   reopened ──Start──► in_progress ──Mark Fixed──► fixed → ready_for_retest → (retest again)

[Closure]
   retest_passed ──admin Close──► closed   (SP1 Close action; visibility extended)
   rejected (from wont_fix) ──admin Close──► closed
```

**Transition rules:**
- **Auto-create guard:** `firstOrCreate` keyed on `issue_report_id` → re-running a product decision never duplicates the task.
- **`markFixed()`** valid from `in_progress` or `reopened` (Filament action visibility enforces this).
- **Retest authorization** (portal `store`): `abort_unless($issue->cohortMember->user_id === auth()->id(), 403)` and `abort_unless($issue->status === 'ready_for_retest', 422, 'Issue is not awaiting retest.')`.
- **Failed retest** calls `developerTask?->reopen()` — same task, attempt preserved as a `Retest` row.
- **Close** (SP1) visibility extends from `[accepted, rejected, duplicate]` to `[accepted, rejected, duplicate, retest_passed]`. Close remains explicit (no auto-close).

`Retest` rows are immutable history; the issue show page and the task detail page both render the full retest timeline.

---

## Section 3: Admin (Filament) + Practitioner Portal

### Filament — `Validation Hub` nav group, admin/super_admin gated

#### `DeveloperTaskResource` (new)
**File:** `app/Filament/Resources/DeveloperTaskResource.php` (+ List/View pages).
- `canAccess()` → `hasAnyRole(['super_admin','admin'])`; `canCreate(): false` (auto-created only).
- Nav icon `heroicon-o-wrench-screwdriver`, group `'Validation Hub'`, sort 3.
- **Table:** `title` (searchable, limit 40); `issueReport.severity` badge; `assignedTo.name` (label 'Assignee', placeholder '—'); `status` badge (open=gray, in_progress=info, fixed=success, reopened=warning, wont_fix=danger, formatted via `statusOptions()`); `priority` badge; `retests_count` via `->counts('retests')`; `created_at`. Default sort `created_at` desc.
- **Status filter tabs** (`getTabs()` on the List page): All | Open | In Progress | Fixed | Reopened | Won't Fix.
- **Row actions** (status-gated, calling on-model methods):
  1. `assign` — visible always; modal `Select::make('assigned_to')->options(User::...)`; sets assignee.
  2. `start` — visible `status ∈ [open, reopened]`; calls `markInProgress()`.
  3. `mark_fixed` — visible `status ∈ [in_progress, reopened]`; modal `Textarea::make('resolution_notes')`; calls `markFixed($data['resolution_notes'])`.
  4. `reopen` — visible `status === 'fixed'`; confirmation; calls `reopen()`.
  5. `wont_fix` — visible `status ∉ [wont_fix]`; modal notes; calls `markWontFix($data['notes'])`.
  Include `ViewAction` first.
- **View page:** infolist with full issue context (the issue's 14 fields via `issueReport`) + task fields.
- **`RetestsRelationManager`** (`$relationship = 'retests'`, read-only): columns attempt index/created_at, `cohortMember.user.name`, `result` badge (passed=success, failed=danger), notes. No create/edit/delete.

#### `IssueReportResource` (extend SP1 — additive)
- Add a **"Ready for Retest"** status tab to `getTabs()`.
- Extend the **Close** action `visible()` to include `'retest_passed'`.
- (Optional, low-risk) Infolist gains a Developer Task summary + a Retests count; not required for tests.

### Practitioner Portal (existing Validation Hub — reuse `ResolvesCohortScope` + ownership checks)

#### Route (add inside the existing `validation` group in `routes/web.php`)
```php
Route::post('/issues/{issue}/retests', [\App\Http\Controllers\Practitioner\Validation\RetestController::class, 'store'])->name('issues.retests.store');
```
Full name: `practitioner.validation.issues.retests.store`. Inherits `auth` + `role:practitioner`.

#### `RetestController`
**File:** `app/Http/Controllers/Practitioner/Validation/RetestController.php`
- `store($locale, IssueReport $issue)`:
  - `abort_unless($issue->cohortMember->user_id === auth()->id(), 403)`.
  - `abort_unless($issue->status === 'ready_for_retest', 422, 'Issue is not awaiting retest.')`.
  - Validate: `result => 'required|in:passed,failed'`, `notes => 'required|string|max:3000'`, `attachments.* => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240'`.
  - Store attachments to `validation/retests` on the `public` disk → JSON array.
  - Call `$issue->recordRetest($issue->cohort_member_id, $data['result'], $data['notes'], $paths ?: null)`.
  - Redirect to `practitioner.validation.issues.show` with success flash.

#### Views (edit existing SP1 views — additive)
- **`issues/show.blade.php`:** when `$issue->status === 'ready_for_retest'` AND viewer is the reporter, render a **Retest panel** (result radio/select pass|fail, notes textarea, optional attachments, submit to the new route). For all statuses, render a read-only **Retest history** list (attempt #, result badge, notes, date) from `$issue->retests`.
- **`issues/index.blade.php`:** a `ready_for_retest` row shows an "Action needed: Retest" badge.
- **`dashboard.blade.php`:** add one stat card — **Awaiting your retest** = count of the member's `issueReports()->where('status','ready_for_retest')`.

No new nav link, layout, or role.

---

## Section 4: Testing, Factories & Authorization

### Factories (no seeder — SP2 has no catalog data; tests build graphs via factories)
- **`DeveloperTaskFactory`** — `issue_report_id => IssueReport::factory()`, `assigned_to => null`, `title => fake()->sentence(4)`, `priority => 'medium'`, `status => 'open'`.
- **`RetestFactory`** — `issue_report_id => IssueReport::factory()`, `developer_task_id => null`, `cohort_member_id => CohortMember::factory()`, `result => 'passed'`, `notes => fake()->sentence()`, `retested_at => now()`.

### Authorization
- **Portal:** routes inherit `auth` + `role:practitioner`. Retest gated to the original reporter (`cohortMember.user_id === auth id`) AND `status === ready_for_retest`.
- **Filament:** `DeveloperTaskResource` admin/super_admin only; `canCreate(): false`; tasks created only by the on-model auto-create.

### Tests (4 classes; `RefreshDatabase` + `RolePermissionSeeder` in `setUp()`; SP1 style)
- **`DeveloperTaskTest`** (model/lifecycle):
  - product decision `sent_to_development` auto-creates exactly one task; calling `recordProductReview('sent_to_development')` again does NOT duplicate.
  - `markFixed()` from in_progress sets `fixed_at` + issue `ready_for_retest`.
  - `reopen()` sets `reopened`.
  - `markWontFix()` sets task `wont_fix` + issue `rejected`.
- **`RetestTest`** (portal):
  - original reporter retests a `ready_for_retest` issue → pass sets `retest_passed`; fail sets `retest_failed` AND reopens the linked task.
  - a different practitioner → 403.
  - retesting an issue not in `ready_for_retest` → 422.
  - non-practitioner role → 403 on the route.
  - each retest persists as a `Retest` row (history immutable — multiple attempts accumulate).
- **`DeveloperTaskAdminTest`** (Filament):
  - admin/super_admin `canAccess()` true; practitioner false; `canCreate()` false.
  - `getPages()` registers index + view; the List page `getTabs()` returns the 6 status tabs.
- **`IssueRetestTriageTest`** (SP1 surface extension):
  - SP1 `Close` action visible for an issue in `retest_passed`.
  - `IssueReport::statusOptions()` now has 13 entries.

### Success criteria
| Area | Metric |
|---|---|
| Data model | 2 new tables, 0 column changes, 3 statuses activated |
| Models | DeveloperTask + Retest (+ on-model auto-create & retest helpers on IssueReport) |
| Admin | DeveloperTaskResource + RetestsRelationManager + 5 task actions; IssueReportResource Close/tab extension |
| Portal | RetestController + 1 route + 3 view edits (show panel, index badge, dashboard stat) |
| Tests | 4 classes, ~16 tests, 0 failures; existing 365 stay green |

---

## Appendix: Factory attribute reference

| Factory | Key attributes |
|---|---|
| `DeveloperTaskFactory` | issue_report_id (IssueReport::factory), assigned_to=null, title, priority='medium', status='open' |
| `RetestFactory` | issue_report_id (IssueReport::factory), developer_task_id=null, cohort_member_id (CohortMember::factory), result='passed', notes, retested_at=now() |
