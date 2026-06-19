# Clinical Validation & Innovation Hub — Design Spec (Sub-project 3: Reporting + Weekly Review)

> **For agentic workers:** REQUIRED SUB-SKILL: Use `superpowers:subagent-driven-development` (recommended) or `superpowers:executing-plans` to implement the resulting plan task-by-task.

**Goal:** Give admins visibility into validation activity. Add a shared metrics service feeding four live admin dashboards (Cohort Progress, Issue Analytics, Developer Throughput, Practitioner Performance), plus two stored-snapshot report entities — a per-cohort/week `WeeklyReview` and a per-member `FinalEvaluation` — that freeze metrics at a point in time and carry an admin narrative.

**Architecture:** One `App\Support\ValidationMetrics` service owns every aggregate query (single source of truth). The four dashboards call it **live**; `WeeklyReview` and `FinalEvaluation` call the same methods and **freeze** the returned arrays into a JSON column at creation. Reporting is purely additive on top of merged SP1 (cohorts, sessions, IssueReport triage) and SP2 (DeveloperTask, Retest) — **no existing table changes**, and the only existing-model edits are two additive convenience relationships (`Cohort::weeklyReviews()`, `CohortMember::finalEvaluation()`).

**Scope:** Sub-project 3 of 4 (Phase 11 of the full vision). Phases 13–14 (Certification scoring model, certificates, Advisory Council) remain in Sub-project 4. SP3's `FinalEvaluation` is the qualitative per-member assessment that SP4's certification scoring builds on; SP3 does **not** issue certificates or compute formal scores.

**Tech stack:** Laravel 13.8 / PHP 8.3 / Spatie Permission v8 / Filament v3 / Blade + Tailwind / SQLite (tests) / MySQL (prod).

---

## Design decisions (locked in brainstorming)

| Decision | Choice |
|---|---|
| The 4 dashboards | All admin-facing Filament `Page`s: Cohort Progress, Issue Analytics, Developer Throughput, Practitioner Performance. |
| WeeklyReview | Auto-snapshot metrics + admin narrative, per cohort per week (stored record, frozen JSON + summary/action items). |
| FinalEvaluation | Per `CohortMember` (one per member). Auto-prefilled contribution snapshot + admin assessment/rating/recommendation. Feeds SP4. |
| Reporting mechanism | Shared `ValidationMetrics` service. Dashboards = live; WeeklyReview/FinalEvaluation = frozen snapshots of the same methods. |
| Charting | None. Stat cards + tables only (testable, consistent, no new dependency). |
| Practitioner visibility | None in SP3 — WeeklyReview/FinalEvaluation are admin-author/view only. Practitioner-facing surfacing is deferred to SP4 (certificates). |

---

## Section 1: Data Model

No existing table changes. Two new tables (string-enum + inline-comment style; `json` for frozen snapshots).

### `weekly_reviews`
```
id
cohort_id     FK → cohorts, cascadeOnDelete
week_start    date
week_end      date
metrics       json            (frozen ValidationMetrics::weeklySnapshot payload)
summary       text, nullable  (admin narrative)
action_items  text, nullable  (admin narrative)
author_id     FK → users
generated_at  timestamp
timestamps
unique(cohort_id, week_start)
```

### `final_evaluations`
```
id
cohort_member_id  FK → cohort_members, cascadeOnDelete, UNIQUE   (one per member)
metrics           json            (frozen ValidationMetrics::memberContributionSnapshot payload)
assessment        text            (admin qualitative write-up)
rating            string(20)      // outstanding|strong|satisfactory|needs_improvement
recommendation    text, nullable
evaluator_id      FK → users
evaluated_at      timestamp
timestamps
```

### Models
| Model | File |
|---|---|
| `WeeklyReview` | `app/Models/WeeklyReview.php` |
| `FinalEvaluation` | `app/Models/FinalEvaluation.php` |

**`WeeklyReview`** — `use HasFactory;` `$fillable = ['cohort_id','week_start','week_end','metrics','summary','action_items','author_id','generated_at'];` `$casts = ['week_start'=>'date','week_end'=>'date','metrics'=>'array','generated_at'=>'datetime'];` relationships `cohort()` belongsTo, `author()` belongsTo(User,'author_id').

**`FinalEvaluation`** — `use HasFactory;` `$fillable = ['cohort_member_id','metrics','assessment','rating','recommendation','evaluator_id','evaluated_at'];` `$casts = ['metrics'=>'array','evaluated_at'=>'datetime'];` relationships `cohortMember()` belongsTo, `evaluator()` belongsTo(User,'evaluator_id'); static `ratingOptions(): array` → outstanding/strong/satisfactory/needs_improvement.

Add the inverse convenience relationships (additive, optional but used by metrics/UI): `Cohort::weeklyReviews()` hasMany; `CohortMember::finalEvaluation()` hasOne. These are the only touches to existing models and are pure additions.

---

## Section 2: Metrics Service + Dashboards

### `App\Support\ValidationMetrics`
**File:** `app/Support/ValidationMetrics.php` (alongside the existing `App\Support\AdminNotifier`). Stateless; every method returns a plain array so it is trivially unit-testable and JSON-serialisable for snapshots.

```php
namespace App\Support;

use App\Models\Cohort;
use App\Models\CohortMember;
use Carbon\CarbonInterface;

class ValidationMetrics
{
    /** Per-cohort progress rows. If $cohort given, one row; else all cohorts. */
    public function cohortProgress(?Cohort $cohort = null): array;

    /** Issue triage funnel + severity/type breakdown + retest pass rate, optionally scoped to a cohort. */
    public function issueAnalytics(?Cohort $cohort = null): array;

    /** Developer task status mix, reopened rate, per-assignee load, avg days created→fixed. */
    public function developerThroughput(): array;

    /** One member's contribution counts. */
    public function practitionerContribution(CohortMember $member): array;

    /** Leaderboard of member contributions, optionally scoped to a cohort. */
    public function practitionerLeaderboard(?Cohort $cohort = null): array;

    /** Frozen weekly payload for a cohort and the 7-day window starting $weekStart. */
    public function weeklySnapshot(Cohort $cohort, CarbonInterface $weekStart): array;

    /** Frozen contribution payload for a single member (used by FinalEvaluation). */
    public function memberContributionSnapshot(CohortMember $member): array;
}
```

**Metric definitions (exact, so dashboards and snapshots agree):**
- **cohortProgress** per cohort: `active_members` (CohortMember status=active), `sessions` (DailyTestSession count via members), `assigned_test_cases` (CohortTestCase count), `covered_test_cases` (distinct ValidationTestCase ids referenced by the cohort's sessions' workflows — i.e. test cases whose workflow has at least one session), `coverage_pct` = covered/assigned*100 (0 when assigned=0), `issues` (IssueReport count via members), `status`.
- **issueAnalytics** (scope = all members, or one cohort's members): `by_status` (count per the 13 statuses), `by_severity` (4), `by_type` (10), `retest_pass_rate` = passed retests / total retests *100 (0 when none), `avg_days_to_close` = mean(closed_at − created_at) over issues with status closed (use updated_at as the close timestamp proxy; documented below).
- **developerThroughput**: `by_status` (5 task statuses), `reopened_rate` = tasks ever reopened / total *100 (approximate via current status=reopened OR fixed-after-reopen; SP3 uses current-status `reopened` count / total for simplicity — documented), `by_assignee` (task count per assigned user, plus Unassigned), `avg_days_to_fix` = mean(fixed_at − created_at) over tasks with fixed_at set.
- **practitionerContribution / leaderboard** per member: `sessions`, `issues_found` (IssueReport count), `issues_accepted` (status in accepted/closed/sent_to_development/fixed/retest_passed), `retests` (Retest rows by the member).
- **weeklySnapshot**(cohort, weekStart): window `[weekStart, weekStart+6d]` on `created_at`/`date`: `sessions`, `issues_submitted`, `issues_by_severity`, `retests` (passed/failed), `dev_tasks_opened`, `dev_tasks_fixed`.
- **memberContributionSnapshot**(member): the `practitionerContribution` array + `cohort_name`, `member_name`, captured `as_of` date.

> **Close-time proxy:** `issue_reports` has no dedicated `closed_at`. SP3 uses `updated_at` as the close timestamp for `avg_days_to_close` (the close action is the last mutation in practice). This is documented as an approximation; a dedicated `closed_at` is out of scope.

### 4 Dashboard Pages
All in `app/Filament/Pages/`, each: `extends Filament\Pages\Page`, `$navigationGroup = 'Validation Hub'`, `canAccess()` → `auth()->user()?->hasAnyRole(['super_admin','admin']) ?? false`, a `$view` blade, public data methods that delegate to `ValidationMetrics`, and a CSV-export header action (mirroring `app/Filament/Pages/HrSummary.php`). Blade views render stat cards + tables only.

| Page | File | View | Content |
|---|---|---|---|
| `ValidationCohortDashboard` | `app/Filament/Pages/ValidationCohortDashboard.php` | `resources/views/filament/pages/validation-cohort-dashboard.blade.php` | cohortProgress table (coverage %, members, sessions, issues) + top stat cards (total cohorts/active members/sessions) |
| `ValidationIssueDashboard` | `app/Filament/Pages/ValidationIssueDashboard.php` | `…/validation-issue-dashboard.blade.php` | issueAnalytics: funnel by status, severity + type breakdowns, retest pass-rate, avg days to close |
| `ValidationDeveloperDashboard` | `app/Filament/Pages/ValidationDeveloperDashboard.php` | `…/validation-developer-dashboard.blade.php` | developerThroughput: status mix, reopened rate, assignee load, avg time-to-fix |
| `ValidationPractitionerDashboard` | `app/Filament/Pages/ValidationPractitionerDashboard.php` | `…/validation-practitioner-dashboard.blade.php` | practitionerLeaderboard table (sessions, found, accepted, retests) |

Navigation sort places them after the Validation Hub resources (sort ≥ 10).

---

## Section 3: WeeklyReview + FinalEvaluation (Filament authoring)

Both resources: `app/Filament/Resources/`, nav group `Validation Hub`, `canAccess()` admin/super_admin.

### `WeeklyReviewResource`
- **Form:** `Select::make('cohort_id')->options(Cohort::pluck('name','id'))->required()`, `DatePicker::make('week_start')->native(false)->required()` (helper: "Monday of the review week"), `Textarea::make('summary')`, `Textarea::make('action_items')`.
- **On create** (`mutateFormDataBeforeCreate($data)`): load the cohort; compute `app(ValidationMetrics::class)->weeklySnapshot($cohort, Carbon::parse($data['week_start']))`; set `$data['metrics']`, `$data['week_end'] = week_start->copy()->addDays(6)`, `$data['author_id'] = auth()->id()`, `$data['generated_at'] = now()`. Return `$data`.
- **Table:** `cohort.name`, week range (`week_start`–`week_end`), `generated_at`, `author.name`.
- **Edit:** `summary`/`action_items` editable; `metrics`/`week_end` not re-computed (frozen at create). The View/Edit page renders the frozen `metrics` as a read-only breakdown (an Infolist section or a custom view block).
- **Pages:** index/create/edit/view. `unique(cohort_id, week_start)` enforced at DB; surface a friendly validation by also adding `->unique(...)` on the cohort/week fields where feasible (DB constraint is the guarantee).

### `FinalEvaluationResource`
- **Form:** `Select::make('cohort_member_id')->options(...)->required()` (label shows "member — cohort"), `Textarea::make('assessment')->required()`, `Select::make('rating')->options(FinalEvaluation::ratingOptions())->required()`, `Textarea::make('recommendation')`.
- **On create** (`mutateFormDataBeforeCreate`): load the member; set `$data['metrics'] = app(ValidationMetrics::class)->memberContributionSnapshot($member)`, `$data['evaluator_id'] = auth()->id()`, `$data['evaluated_at'] = now()`.
- **Table:** `cohortMember.user.name` (Member), `cohortMember.cohort.name` (Cohort), `rating` badge, `evaluated_at`, `evaluator.name`.
- **View:** frozen `metrics` breakdown + assessment + recommendation.
- **Pages:** index/create/view/edit. `unique(cohort_member_id)` at DB.

### Convenience action on `CohortResource`
Add an **`Evaluate`** row action to the existing `CohortResource/RelationManagers/MembersRelationManager` (SP1). Visible always; opens a form (assessment/rating/recommendation) and on submit creates a `FinalEvaluation` for that member with the snapshot frozen — same logic as the resource create. If an evaluation already exists for the member, the action is hidden (one per member).

---

## Section 4: Authorization, Testing, Factories

### Authorization
- All 4 dashboard `Page`s and both resources: `canAccess()` → `hasAnyRole(['super_admin','admin'])`.
- No practitioner-facing routes added in SP3.

### Factories
- **`WeeklyReviewFactory`** — `cohort_id => Cohort::factory()`, `week_start => now()->startOfWeek()`, `week_end => now()->startOfWeek()->addDays(6)`, `metrics => []`, `author_id => User::factory()`, `generated_at => now()`.
- **`FinalEvaluationFactory`** — `cohort_member_id => CohortMember::factory()`, `metrics => []`, `assessment => fake()->paragraph()`, `rating => 'strong'`, `evaluator_id => User::factory()`, `evaluated_at => now()`.

### Tests (4 classes; `RefreshDatabase` + `RolePermissionSeeder` in `setUp()`)
- **`ValidationMetricsTest`** (the core): build a cohort, active members, sessions, issues across varied status/severity/type, retests (some passed/failed), and developer tasks (some fixed/reopened) via factories; assert:
  - `cohortProgress` returns correct active_members, sessions, coverage_pct (assigned vs covered), issues.
  - `issueAnalytics` by_status/by_severity/by_type counts match; `retest_pass_rate` = passed/total*100.
  - `developerThroughput` by_status counts; `avg_days_to_fix` computed from fixed_at−created_at.
  - `practitionerContribution` counts (sessions, issues_found, issues_accepted, retests).
  - `weeklySnapshot` counts only rows inside the 7-day window (seed one inside, one outside; assert only the inside one is counted).
  - `memberContributionSnapshot` includes member_name + cohort_name + the contribution counts.
- **`WeeklyReviewTest`**: creating a review (through the resource create flow or directly applying the mutate logic) freezes a non-empty `metrics` array and sets week_end/author/generated_at; the `unique(cohort_id, week_start)` constraint rejects a duplicate; `WeeklyReviewResource::canAccess()` true for admin, false for practitioner.
- **`FinalEvaluationTest`**: creating freezes the member snapshot into `metrics`; `unique(cohort_member_id)` rejects a second evaluation for the same member; `rating` accepts only `ratingOptions()` keys; resource gated.
- **`ValidationDashboardAccessTest`**: each of the 4 dashboard `Page` classes — `canAccess()` true for admin/super_admin, false for practitioner.

### Success criteria
| Area | Metric |
|---|---|
| Data model | 2 new tables, 0 column changes |
| Service | `ValidationMetrics` with 7 methods, fully unit-tested |
| Dashboards | 4 admin Filament pages + blade views + CSV export |
| Reports | WeeklyReview (generate+annotate) + FinalEvaluation (snapshot+assess) resources + cohort Evaluate action |
| Tests | 4 classes, ~18 tests, 0 failures; existing 386 stay green |

---

## Appendix: Factory attribute reference

| Factory | Key attributes |
|---|---|
| `WeeklyReviewFactory` | cohort_id (Cohort::factory), week_start=startOfWeek, week_end=+6d, metrics=[], author_id (User::factory), generated_at=now |
| `FinalEvaluationFactory` | cohort_member_id (CohortMember::factory), metrics=[], assessment, rating='strong', evaluator_id (User::factory), evaluated_at=now |
