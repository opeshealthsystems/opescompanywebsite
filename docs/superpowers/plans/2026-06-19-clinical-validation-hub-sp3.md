# Clinical Validation Hub — Sub-project 3 Implementation Plan (Reporting + Weekly Review)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add admin reporting on top of merged SP1/SP2 — a shared `ValidationMetrics` service feeding 4 live Filament dashboards, plus two snapshot-report entities (`WeeklyReview` per cohort/week, `FinalEvaluation` per cohort member) that freeze metrics + carry an admin narrative.

**Architecture:** One `App\Support\ValidationMetrics` service owns every aggregate query. Dashboards call it live; `WeeklyReview`/`FinalEvaluation` freeze its output into a JSON column via static `snapshotData()` methods on each model (Filament resources are thin wrappers). Purely additive: 2 new tables, no existing table changes, only two additive relationship methods on `Cohort`/`CohortMember`.

**Tech Stack:** Laravel 13.8 / PHP 8.3 / Filament v3 / Blade + Tailwind / SQLite (tests) / MySQL (prod). PHP binary: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`. Test cmd: `<php> artisan test --filter=<Class>`. Spec: `docs/superpowers/specs/2026-06-19-clinical-validation-hub-sp3-design.md`. Baseline: 386 tests green.

---

## Conventions (verified)
- **Migrations:** anonymous class; string-enum + inline `// a|b|c`; FK `->constrained('t')->cascadeOnDelete()`; `->unique([...])`.
- **Models:** `use HasFactory;`, `$fillable`, `$casts`, fully-qualified relation return types, static `xOptions()`.
- **Factories:** `protected $model`, `definition()` with `fake()`, FKs via `Related::factory()`.
- **Filament dashboard Page** (ref `app/Filament/Pages/HrSummary.php` + `resources/views/filament/pages/hr-summary.blade.php`): `extends Filament\Pages\Page`, `$navigationGroup`, `$navigationIcon`, `$navigationLabel`, `$navigationSort`, `$view`, `canAccess()`, public data methods, optional `getHeaderActions()` CSV export. View uses `<x-filament-panels::page>` with stat cards + tables, calling `$this->method()`. Pages auto-register in the default panel.
- **Filament resource:** `canAccess()` admin gate; `form()/table()/getPages()`; `mutateFormDataBeforeCreate()` to inject computed fields.
- **Service location:** `App\Support` (ref `app/Support/AdminNotifier.php`).
- **Tests:** `Tests\Feature`, `use RefreshDatabase;`, `setUp()` → `parent::setUp(); app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions(); $this->seed(\Database\Seeders\RolePermissionSeeder::class);`. Role user via `User::factory()->create()` + `->assignRole(...)`.
- **Git:** PowerShell-safe `-m`; stage explicit paths only; never stage untracked docs; never `taskkill`; don't touch MySQL/Apache.

## Existing relationships relied on
- `Cohort`: `members()`, `cohortTestCases()`, `testCases()`, `status`.
- `CohortMember`: `dailyTestSessions()`, `issueReports()`, `cohort()`, `user()`, status.
- `IssueReport`: `status` (13), `severity` (4), `issue_type` (10), `cohortMember()`, `retests()`, `created_at`/`updated_at`.
- `Retest`: `result` (passed|failed), `cohort_member_id`, `retested_at`.
- `DeveloperTask`: `status` (5), `assigned_to`, `assignedTo()`, `created_at`, `fixed_at`, `issueReport()`.
- `DailyTestSession`: `cohort_member_id`, `validation_workflow_id`, `date`.
- `CohortTestCase`: `cohort_id`, `validation_test_case_id`; `validation_test_cases.validation_workflow_id`.

## File map
| File | Task |
|---|---|
| `database/migrations/2026_06_19_420001_create_weekly_reviews_table.php`, `..._420002_create_final_evaluations_table.php` | 1 |
| `app/Models/WeeklyReview.php`, `app/Models/FinalEvaluation.php` | 2 (+snapshotData in 5/6) |
| `app/Models/Cohort.php`, `app/Models/CohortMember.php` (additive relations) | 2 |
| `database/factories/WeeklyReviewFactory.php`, `FinalEvaluationFactory.php` | 2 |
| `app/Support/ValidationMetrics.php` | 3 |
| `app/Filament/Pages/Validation{Cohort,Issue,Developer,Practitioner}Dashboard.php` + 4 blade views | 4 |
| `app/Filament/Resources/WeeklyReviewResource.php` (+Pages) | 5 |
| `app/Filament/Resources/FinalEvaluationResource.php` (+Pages) + `CohortResource/RelationManagers/MembersRelationManager.php` (Evaluate action) | 6 |

---

## Task 1: Migrations

**Files:** Create the two migrations; Test `tests/Feature/ValidationSp3MigrationsTest.php`.

- [ ] **Step 1: Write the failing test**
```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ValidationSp3MigrationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sp3_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('weekly_reviews'));
        $this->assertTrue(Schema::hasTable('final_evaluations'));
        foreach (['cohort_id', 'week_start', 'week_end', 'metrics', 'summary', 'action_items', 'author_id', 'generated_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('weekly_reviews', $c), "weekly_reviews.$c");
        }
        foreach (['cohort_member_id', 'metrics', 'assessment', 'rating', 'recommendation', 'evaluator_id', 'evaluated_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('final_evaluations', $c), "final_evaluations.$c");
        }
    }
}
```

- [ ] **Step 2: Run — expect FAIL.** `<php> artisan test --filter=ValidationSp3MigrationsTest`

- [ ] **Step 3: `database/migrations/2026_06_19_420001_create_weekly_reviews_table.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->date('week_start');
            $table->date('week_end');
            $table->json('metrics');
            $table->text('summary')->nullable();
            $table->text('action_items')->nullable();
            $table->foreignId('author_id')->constrained('users');
            $table->timestamp('generated_at');
            $table->timestamps();
            $table->unique(['cohort_id', 'week_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_reviews');
    }
};
```

- [ ] **Step 4: `database/migrations/2026_06_19_420002_create_final_evaluations_table.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_member_id')->constrained('cohort_members')->cascadeOnDelete()->unique();
            $table->json('metrics');
            $table->text('assessment');
            $table->string('rating', 20); // outstanding|strong|satisfactory|needs_improvement
            $table->text('recommendation')->nullable();
            $table->foreignId('evaluator_id')->constrained('users');
            $table->timestamp('evaluated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_evaluations');
    }
};
```

- [ ] **Step 5: Run — expect PASS.** Then full suite (`<php> artisan test`) — 0 failures (~387).

- [ ] **Step 6: Commit**
```bash
git add database/migrations/2026_06_19_420001_create_weekly_reviews_table.php database/migrations/2026_06_19_420002_create_final_evaluations_table.php tests/Feature/ValidationSp3MigrationsTest.php
git commit -m "feat(validation): add weekly_reviews and final_evaluations tables (SP3)"
```

---

## Task 2: Models + factories + additive relationships

**Files:** Create `app/Models/WeeklyReview.php`, `app/Models/FinalEvaluation.php`, `database/factories/WeeklyReviewFactory.php`, `database/factories/FinalEvaluationFactory.php`; Modify `app/Models/Cohort.php`, `app/Models/CohortMember.php`; Test `tests/Feature/ValidationReportingModelTest.php`.

- [ ] **Step 1: Write the failing test**
```php
<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\FinalEvaluation;
use App\Models\WeeklyReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationReportingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_weekly_review_relationships_and_casts(): void
    {
        $review = WeeklyReview::factory()->create(['metrics' => ['sessions' => 3]]);
        $this->assertIsArray($review->fresh()->metrics);
        $this->assertEquals(3, $review->fresh()->metrics['sessions']);
        $this->assertInstanceOf(Cohort::class, $review->cohort);
        $this->assertNotNull($review->author);
        $this->assertTrue($review->cohort->weeklyReviews->contains($review));
    }

    public function test_final_evaluation_relationships_and_options(): void
    {
        $eval = FinalEvaluation::factory()->create();
        $this->assertInstanceOf(CohortMember::class, $eval->cohortMember);
        $this->assertNotNull($eval->evaluator);
        $this->assertEquals($eval->id, $eval->cohortMember->finalEvaluation->id);
        $this->assertArrayHasKey('strong', FinalEvaluation::ratingOptions());
        $this->assertCount(4, FinalEvaluation::ratingOptions());
    }
}
```

- [ ] **Step 2: Run — expect FAIL** (`--filter=ValidationReportingModelTest`).

- [ ] **Step 3: `app/Models/WeeklyReview.php`**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_id', 'week_start', 'week_end', 'metrics',
        'summary', 'action_items', 'author_id', 'generated_at',
    ];

    protected $casts = [
        'week_start'   => 'date',
        'week_end'     => 'date',
        'metrics'      => 'array',
        'generated_at' => 'datetime',
    ];

    public function cohort(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
```

- [ ] **Step 4: `app/Models/FinalEvaluation.php`**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_member_id', 'metrics', 'assessment',
        'rating', 'recommendation', 'evaluator_id', 'evaluated_at',
    ];

    protected $casts = [
        'metrics'      => 'array',
        'evaluated_at' => 'datetime',
    ];

    public function cohortMember(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CohortMember::class);
    }

    public function evaluator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public static function ratingOptions(): array
    {
        return [
            'outstanding'       => 'Outstanding',
            'strong'            => 'Strong',
            'satisfactory'      => 'Satisfactory',
            'needs_improvement' => 'Needs Improvement',
        ];
    }
}
```

- [ ] **Step 5: Add additive relationship to `app/Models/Cohort.php`** (after `testCases()`):
```php
    public function weeklyReviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WeeklyReview::class);
    }
```

- [ ] **Step 6: Add additive relationship to `app/Models/CohortMember.php`** (after `issueReports()`):
```php
    public function finalEvaluation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\FinalEvaluation::class);
    }
```

- [ ] **Step 7: `database/factories/WeeklyReviewFactory.php`**
```php
<?php

namespace Database\Factories;

use App\Models\Cohort;
use App\Models\User;
use App\Models\WeeklyReview;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WeeklyReview> */
class WeeklyReviewFactory extends Factory
{
    protected $model = WeeklyReview::class;

    public function definition(): array
    {
        $start = now()->startOfWeek();
        return [
            'cohort_id'    => Cohort::factory(),
            'week_start'   => $start->toDateString(),
            'week_end'     => $start->copy()->addDays(6)->toDateString(),
            'metrics'      => [],
            'summary'      => fake()->sentence(),
            'action_items' => null,
            'author_id'    => User::factory(),
            'generated_at' => now(),
        ];
    }
}
```

- [ ] **Step 8: `database/factories/FinalEvaluationFactory.php`**
```php
<?php

namespace Database\Factories;

use App\Models\CohortMember;
use App\Models\FinalEvaluation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FinalEvaluation> */
class FinalEvaluationFactory extends Factory
{
    protected $model = FinalEvaluation::class;

    public function definition(): array
    {
        return [
            'cohort_member_id' => CohortMember::factory(),
            'metrics'          => [],
            'assessment'       => fake()->paragraph(),
            'rating'           => 'strong',
            'recommendation'   => fake()->sentence(),
            'evaluator_id'     => User::factory(),
            'evaluated_at'     => now(),
        ];
    }
}
```

- [ ] **Step 9: Run — expect PASS** (`--filter=ValidationReportingModelTest`). Then full suite — 0 failures (~389).

- [ ] **Step 10: Commit**
```bash
git add app/Models/WeeklyReview.php app/Models/FinalEvaluation.php app/Models/Cohort.php app/Models/CohortMember.php database/factories/WeeklyReviewFactory.php database/factories/FinalEvaluationFactory.php tests/Feature/ValidationReportingModelTest.php
git commit -m "feat(validation): add WeeklyReview and FinalEvaluation models with factories"
```

---

## Task 3: ValidationMetrics service

**Files:** Create `app/Support/ValidationMetrics.php`; Test `tests/Feature/ValidationMetricsTest.php`.

- [ ] **Step 1: Write the failing test**
```php
<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\CohortTestCase;
use App\Models\DailyTestSession;
use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\Retest;
use App\Models\ValidationModule;
use App\Models\ValidationProduct;
use App\Models\ValidationTestCase;
use App\Models\ValidationWorkflow;
use App\Support\ValidationMetrics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationMetricsTest extends TestCase
{
    use RefreshDatabase;

    private function metrics(): ValidationMetrics
    {
        return app(ValidationMetrics::class);
    }

    /** Cohort with 1 active member, 1 assigned+covered test case, 1 session. */
    private function seedCohort(): array
    {
        $cohort  = Cohort::factory()->create();
        $member  = CohortMember::factory()->create(['cohort_id' => $cohort->id, 'status' => 'active']);
        $product = ValidationProduct::factory()->create();
        $module  = ValidationModule::factory()->create(['validation_product_id' => $product->id]);
        $workflow = ValidationWorkflow::factory()->create(['validation_module_id' => $module->id]);
        $testCase = ValidationTestCase::factory()->create(['validation_workflow_id' => $workflow->id]);
        CohortTestCase::create(['cohort_id' => $cohort->id, 'validation_test_case_id' => $testCase->id]);
        DailyTestSession::factory()->create([
            'cohort_member_id'       => $member->id,
            'validation_product_id'  => $product->id,
            'validation_module_id'   => $module->id,
            'validation_workflow_id' => $workflow->id,
            'date'                   => now()->toDateString(),
        ]);
        return compact('cohort', 'member', 'product', 'module', 'workflow', 'testCase');
    }

    public function test_cohort_progress_counts_and_coverage(): void
    {
        ['cohort' => $cohort] = $this->seedCohort();
        $rows = $this->metrics()->cohortProgress($cohort);
        $this->assertCount(1, $rows);
        $this->assertEquals(1, $rows[0]['active_members']);
        $this->assertEquals(1, $rows[0]['sessions']);
        $this->assertEquals(1, $rows[0]['assigned_test_cases']);
        $this->assertEquals(1, $rows[0]['covered_test_cases']);
        $this->assertEquals(100, $rows[0]['coverage_pct']);
    }

    public function test_issue_analytics_breakdowns_and_retest_rate(): void
    {
        ['member' => $member] = $this->seedCohort();
        IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'submitted', 'severity' => 'high', 'issue_type' => 'bug']);
        IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'closed', 'severity' => 'low', 'issue_type' => 'recommendation']);
        Retest::factory()->create(['cohort_member_id' => $member->id, 'result' => 'passed']);
        Retest::factory()->create(['cohort_member_id' => $member->id, 'result' => 'failed']);

        $a = $this->metrics()->issueAnalytics();
        $this->assertEquals(2, $a['total']);
        $this->assertEquals(1, $a['by_status']['submitted']);
        $this->assertEquals(1, $a['by_status']['closed']);
        $this->assertEquals(1, $a['by_severity']['high']);
        $this->assertEquals(1, $a['by_type']['bug']);
        $this->assertEquals(50, $a['retest_pass_rate']);
    }

    public function test_developer_throughput(): void
    {
        $issue = IssueReport::factory()->create();
        DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'fixed', 'fixed_at' => now()]);
        DeveloperTask::factory()->create(['status' => 'reopened']);

        $t = $this->metrics()->developerThroughput();
        $this->assertEquals(2, $t['total']);
        $this->assertEquals(1, $t['by_status']['fixed']);
        $this->assertEquals(1, $t['by_status']['reopened']);
        $this->assertEquals(50, $t['reopened_rate']);
    }

    public function test_practitioner_contribution(): void
    {
        ['member' => $member] = $this->seedCohort();
        IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'accepted']);
        Retest::factory()->create(['cohort_member_id' => $member->id, 'result' => 'passed']);

        $c = $this->metrics()->practitionerContribution($member);
        $this->assertEquals(1, $c['sessions']);
        $this->assertEquals(1, $c['issues_found']);
        $this->assertEquals(1, $c['issues_accepted']);
        $this->assertEquals(1, $c['retests']);
    }

    public function test_weekly_snapshot_respects_window(): void
    {
        ['cohort' => $cohort, 'member' => $member] = $this->seedCohort();
        // inside the current week
        IssueReport::factory()->create(['cohort_member_id' => $member->id, 'created_at' => now()]);
        // far outside
        IssueReport::factory()->create(['cohort_member_id' => $member->id, 'created_at' => now()->subMonths(2)]);

        $snap = $this->metrics()->weeklySnapshot($cohort, now()->startOfWeek());
        $this->assertEquals(1, $snap['issues_submitted']);
        $this->assertArrayHasKey('week_start', $snap);
    }

    public function test_member_contribution_snapshot_has_labels(): void
    {
        ['member' => $member] = $this->seedCohort();
        $snap = $this->metrics()->memberContributionSnapshot($member);
        $this->assertArrayHasKey('member_name', $snap);
        $this->assertArrayHasKey('cohort_name', $snap);
        $this->assertArrayHasKey('sessions', $snap);
    }
}
```

- [ ] **Step 2: Run — expect FAIL** (`--filter=ValidationMetricsTest`), service missing.

- [ ] **Step 3: Create `app/Support/ValidationMetrics.php`**
```php
<?php

namespace App\Support;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\CohortTestCase;
use App\Models\DailyTestSession;
use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\Retest;
use Carbon\CarbonInterface;

class ValidationMetrics
{
    /** Issue statuses counted as "accepted" practitioner contributions. */
    private const ACCEPTED_STATUSES = ['accepted', 'closed', 'sent_to_development', 'fixed', 'retest_passed'];

    public function cohortProgress(?Cohort $cohort = null): array
    {
        $cohorts = $cohort ? collect([$cohort]) : Cohort::all();

        return $cohorts->map(function (Cohort $c) {
            $memberIds = $c->members()->pluck('id');
            $assigned  = $c->cohortTestCases()->count();

            $sessionWorkflowIds = DailyTestSession::whereIn('cohort_member_id', $memberIds)
                ->distinct()->pluck('validation_workflow_id');

            $covered = CohortTestCase::where('cohort_id', $c->id)
                ->join('validation_test_cases', 'cohort_test_cases.validation_test_case_id', '=', 'validation_test_cases.id')
                ->whereIn('validation_test_cases.validation_workflow_id', $sessionWorkflowIds)
                ->distinct('validation_test_cases.id')
                ->count('validation_test_cases.id');

            return [
                'cohort_id'           => $c->id,
                'name'                => $c->name,
                'status'              => $c->status,
                'active_members'      => $c->members()->where('status', 'active')->count(),
                'sessions'            => DailyTestSession::whereIn('cohort_member_id', $memberIds)->count(),
                'assigned_test_cases' => $assigned,
                'covered_test_cases'  => $covered,
                'coverage_pct'        => $assigned > 0 ? (int) round($covered / $assigned * 100) : 0,
                'issues'              => IssueReport::whereIn('cohort_member_id', $memberIds)->count(),
            ];
        })->values()->all();
    }

    public function issueAnalytics(?Cohort $cohort = null): array
    {
        $memberIds = $cohort ? $cohort->members()->pluck('id') : null;

        $issues = IssueReport::query()
            ->when($memberIds, fn ($q) => $q->whereIn('cohort_member_id', $memberIds))
            ->get();

        $byStatus = [];
        foreach (array_keys(IssueReport::statusOptions()) as $s) {
            $byStatus[$s] = $issues->where('status', $s)->count();
        }
        $bySeverity = [];
        foreach (array_keys(IssueReport::severityOptions()) as $s) {
            $bySeverity[$s] = $issues->where('severity', $s)->count();
        }
        $byType = [];
        foreach (array_keys(IssueReport::issueTypeOptions()) as $t) {
            $byType[$t] = $issues->where('issue_type', $t)->count();
        }

        $retests = Retest::query()
            ->when($memberIds, fn ($q) => $q->whereIn('cohort_member_id', $memberIds))
            ->get();
        $passRate = $retests->count() > 0
            ? (int) round($retests->where('result', 'passed')->count() / $retests->count() * 100)
            : 0;

        $closed = $issues->where('status', 'closed');
        $avgDaysToClose = $closed->count() > 0
            ? round($closed->avg(fn (IssueReport $i) => $i->created_at->diffInDays($i->updated_at)), 1)
            : 0;

        return [
            'total'             => $issues->count(),
            'by_status'         => $byStatus,
            'by_severity'       => $bySeverity,
            'by_type'           => $byType,
            'retest_pass_rate'  => $passRate,
            'avg_days_to_close' => $avgDaysToClose,
        ];
    }

    public function developerThroughput(): array
    {
        $tasks = DeveloperTask::with('assignedTo')->get();

        $byStatus = [];
        foreach (array_keys(DeveloperTask::statusOptions()) as $s) {
            $byStatus[$s] = $tasks->where('status', $s)->count();
        }
        $reopenedRate = $tasks->count() > 0
            ? (int) round($tasks->where('status', 'reopened')->count() / $tasks->count() * 100)
            : 0;

        $byAssignee = $tasks
            ->groupBy(fn (DeveloperTask $t) => $t->assignedTo?->name ?? 'Unassigned')
            ->map(fn ($group, $name) => ['name' => $name, 'count' => $group->count()])
            ->values()->all();

        $fixed = $tasks->whereNotNull('fixed_at');
        $avgDaysToFix = $fixed->count() > 0
            ? round($fixed->avg(fn (DeveloperTask $t) => $t->created_at->diffInDays($t->fixed_at)), 1)
            : 0;

        return [
            'total'           => $tasks->count(),
            'by_status'       => $byStatus,
            'reopened_rate'   => $reopenedRate,
            'by_assignee'     => $byAssignee,
            'avg_days_to_fix' => $avgDaysToFix,
        ];
    }

    public function practitionerContribution(CohortMember $member): array
    {
        return [
            'sessions'        => $member->dailyTestSessions()->count(),
            'issues_found'    => $member->issueReports()->count(),
            'issues_accepted' => $member->issueReports()->whereIn('status', self::ACCEPTED_STATUSES)->count(),
            'retests'         => Retest::where('cohort_member_id', $member->id)->count(),
        ];
    }

    public function practitionerLeaderboard(?Cohort $cohort = null): array
    {
        $members = CohortMember::query()
            ->with(['user', 'cohort'])
            ->when($cohort, fn ($q) => $q->where('cohort_id', $cohort->id))
            ->get();

        return $members->map(fn (CohortMember $m) => array_merge([
            'member' => $m->user?->name ?? '—',
            'cohort' => $m->cohort?->name ?? '—',
        ], $this->practitionerContribution($m)))
            ->sortByDesc('issues_found')
            ->values()->all();
    }

    public function weeklySnapshot(Cohort $cohort, CarbonInterface $weekStart): array
    {
        $start = $weekStart->copy()->startOfDay();
        $end   = $weekStart->copy()->addDays(6)->endOfDay();
        $memberIds = $cohort->members()->pluck('id');

        $sessions = DailyTestSession::whereIn('cohort_member_id', $memberIds)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])->count();

        $issues = IssueReport::whereIn('cohort_member_id', $memberIds)
            ->whereBetween('created_at', [$start, $end])->get();
        $issuesBySeverity = [];
        foreach (array_keys(IssueReport::severityOptions()) as $s) {
            $issuesBySeverity[$s] = $issues->where('severity', $s)->count();
        }

        $retests = Retest::whereIn('cohort_member_id', $memberIds)
            ->whereBetween('retested_at', [$start, $end])->get();

        $devOpened = DeveloperTask::whereHas('issueReport', fn ($q) => $q->whereIn('cohort_member_id', $memberIds))
            ->whereBetween('created_at', [$start, $end])->count();
        $devFixed = DeveloperTask::whereHas('issueReport', fn ($q) => $q->whereIn('cohort_member_id', $memberIds))
            ->whereBetween('fixed_at', [$start, $end])->count();

        return [
            'week_start'         => $start->toDateString(),
            'week_end'           => $end->toDateString(),
            'sessions'           => $sessions,
            'issues_submitted'   => $issues->count(),
            'issues_by_severity' => $issuesBySeverity,
            'retests_passed'     => $retests->where('result', 'passed')->count(),
            'retests_failed'     => $retests->where('result', 'failed')->count(),
            'dev_tasks_opened'   => $devOpened,
            'dev_tasks_fixed'    => $devFixed,
        ];
    }

    public function memberContributionSnapshot(CohortMember $member): array
    {
        $member->loadMissing(['user', 'cohort']);
        return array_merge($this->practitionerContribution($member), [
            'member_name' => $member->user?->name ?? '—',
            'cohort_name' => $member->cohort?->name ?? '—',
            'as_of'       => now()->toDateString(),
        ]);
    }
}
```

- [ ] **Step 4: Run — expect PASS** (`--filter=ValidationMetricsTest`). Then full suite — 0 failures (~395).

- [ ] **Step 5: Commit**
```bash
git add app/Support/ValidationMetrics.php tests/Feature/ValidationMetricsTest.php
git commit -m "feat(validation): add ValidationMetrics aggregation service"
```

---

## Task 4: 4 dashboard pages + views

**Files:** Create 4 Pages in `app/Filament/Pages/` + 4 views in `resources/views/filament/pages/`; Test `tests/Feature/ValidationDashboardAccessTest.php`.

- [ ] **Step 1: Write the failing test**
```php
<?php

namespace Tests\Feature;

use App\Filament\Pages\ValidationCohortDashboard;
use App\Filament\Pages\ValidationDeveloperDashboard;
use App\Filament\Pages\ValidationIssueDashboard;
use App\Filament\Pages\ValidationPractitionerDashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ValidationDashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_admin_can_access_all_dashboards_practitioner_cannot(): void
    {
        $pages = [
            ValidationCohortDashboard::class,
            ValidationIssueDashboard::class,
            ValidationDeveloperDashboard::class,
            ValidationPractitionerDashboard::class,
        ];

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        foreach ($pages as $p) {
            $this->assertTrue($p::canAccess(), $p.' admin');
        }

        $prac = User::factory()->create();
        $prac->assignRole('practitioner');
        $this->actingAs($prac);
        foreach ($pages as $p) {
            $this->assertFalse($p::canAccess(), $p.' practitioner');
        }
    }
}
```

- [ ] **Step 2: Run — expect FAIL** (`--filter=ValidationDashboardAccessTest`).

- [ ] **Step 3: `app/Filament/Pages/ValidationCohortDashboard.php`**
```php
<?php

namespace App\Filament\Pages;

use App\Support\ValidationMetrics;
use Filament\Pages\Page;

class ValidationCohortDashboard extends Page
{
    protected static ?string $title           = 'Cohort Progress';
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Cohort Progress';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 10;
    protected static string  $view            = 'filament.pages.validation-cohort-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getRows(): array
    {
        return app(ValidationMetrics::class)->cohortProgress();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('export_csv')
                ->label('Export CSV')->icon('heroicon-o-arrow-down-tray')->color('gray')
                ->action(function (): \Symfony\Component\HttpFoundation\StreamedResponse {
                    $rows = $this->getRows();
                    return response()->streamDownload(function () use ($rows) {
                        echo "Cohort,Status,Active Members,Sessions,Covered,Assigned,Coverage %,Issues\n";
                        foreach ($rows as $r) {
                            echo '"'.$r['name'].'",'.$r['status'].','.$r['active_members'].','.$r['sessions'].','.$r['covered_test_cases'].','.$r['assigned_test_cases'].','.$r['coverage_pct'].','.$r['issues']."\n";
                        }
                    }, 'cohort-progress-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
                }),
        ];
    }
}
```

> **CSV scope note:** CSV export is added only to the two tabular dashboards (Cohort Progress, Practitioner Performance) where rows export cleanly. The Issue/Developer dashboards are card-grid breakdowns and remain on-screen only — a deliberate YAGNI trim of the spec's "each dashboard" wording.

- [ ] **Step 4: `resources/views/filament/pages/validation-cohort-dashboard.blade.php`**
```blade
<x-filament-panels::page>
    <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    @foreach (['Cohort', 'Status', 'Active Members', 'Sessions', 'Coverage', 'Issues'] as $h)
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($this->getRows() as $row)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ ucfirst($row['status']) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['active_members'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['sessions'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $row['covered_test_cases'] }}/{{ $row['assigned_test_cases'] }} ({{ $row['coverage_pct'] }}%)
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['issues'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-sm text-gray-400">No cohorts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
```

- [ ] **Step 5: `app/Filament/Pages/ValidationIssueDashboard.php`**
```php
<?php

namespace App\Filament\Pages;

use App\Support\ValidationMetrics;
use Filament\Pages\Page;

class ValidationIssueDashboard extends Page
{
    protected static ?string $title           = 'Issue Analytics';
    protected static ?string $navigationIcon  = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'Issue Analytics';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 11;
    protected static string  $view            = 'filament.pages.validation-issue-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getAnalytics(): array
    {
        return app(ValidationMetrics::class)->issueAnalytics();
    }
}
```

- [ ] **Step 6: `resources/views/filament/pages/validation-issue-dashboard.blade.php`**
```blade
<x-filament-panels::page>
    @php $a = $this->getAnalytics(); @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ([
            ['Total Issues', $a['total'], 'text-blue-600'],
            ['Retest Pass Rate', $a['retest_pass_rate'].'%', 'text-emerald-600'],
            ['Avg Days to Close', $a['avg_days_to_close'], 'text-amber-600'],
            ['Closed', $a['by_status']['closed'] ?? 0, 'text-gray-600'],
        ] as [$label, $value, $color])
            <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 p-6 text-center">
                <div class="text-3xl font-bold {{ $color }}">{{ $value }}</div>
                <div class="text-sm text-gray-500 mt-1">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        @foreach ([
            ['By Status', $a['by_status'], \App\Models\IssueReport::statusOptions()],
            ['By Severity', $a['by_severity'], \App\Models\IssueReport::severityOptions()],
            ['By Type', $a['by_type'], \App\Models\IssueReport::issueTypeOptions()],
        ] as [$title, $data, $labels])
            <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 p-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">{{ $title }}</h3>
                <ul class="space-y-1 text-sm">
                    @foreach ($data as $key => $count)
                        @if ($count > 0)
                            <li class="flex justify-between"><span class="text-gray-500">{{ $labels[$key] ?? $key }}</span><span class="font-medium">{{ $count }}</span></li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
```

- [ ] **Step 7: `app/Filament/Pages/ValidationDeveloperDashboard.php`**
```php
<?php

namespace App\Filament\Pages;

use App\Support\ValidationMetrics;
use Filament\Pages\Page;

class ValidationDeveloperDashboard extends Page
{
    protected static ?string $title           = 'Developer Throughput';
    protected static ?string $navigationIcon  = 'heroicon-o-wrench';
    protected static ?string $navigationLabel = 'Developer Throughput';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 12;
    protected static string  $view            = 'filament.pages.validation-developer-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getThroughput(): array
    {
        return app(ValidationMetrics::class)->developerThroughput();
    }
}
```

- [ ] **Step 8: `resources/views/filament/pages/validation-developer-dashboard.blade.php`**
```blade
<x-filament-panels::page>
    @php $t = $this->getThroughput(); @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ([
            ['Total Tasks', $t['total'], 'text-blue-600'],
            ['Reopened Rate', $t['reopened_rate'].'%', 'text-orange-600'],
            ['Avg Days to Fix', $t['avg_days_to_fix'], 'text-amber-600'],
            ['Fixed', $t['by_status']['fixed'] ?? 0, 'text-emerald-600'],
        ] as [$label, $value, $color])
            <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 p-6 text-center">
                <div class="text-3xl font-bold {{ $color }}">{{ $value }}</div>
                <div class="text-sm text-gray-500 mt-1">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 p-4">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">By Status</h3>
            <ul class="space-y-1 text-sm">
                @foreach ($t['by_status'] as $key => $count)
                    <li class="flex justify-between"><span class="text-gray-500">{{ \App\Models\DeveloperTask::statusOptions()[$key] ?? $key }}</span><span class="font-medium">{{ $count }}</span></li>
                @endforeach
            </ul>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 p-4">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">By Assignee</h3>
            <ul class="space-y-1 text-sm">
                @forelse ($t['by_assignee'] as $row)
                    <li class="flex justify-between"><span class="text-gray-500">{{ $row['name'] }}</span><span class="font-medium">{{ $row['count'] }}</span></li>
                @empty
                    <li class="text-gray-400">No tasks yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-filament-panels::page>
```

- [ ] **Step 9: `app/Filament/Pages/ValidationPractitionerDashboard.php`**
```php
<?php

namespace App\Filament\Pages;

use App\Support\ValidationMetrics;
use Filament\Pages\Page;

class ValidationPractitionerDashboard extends Page
{
    protected static ?string $title           = 'Practitioner Performance';
    protected static ?string $navigationIcon  = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Practitioner Performance';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 13;
    protected static string  $view            = 'filament.pages.validation-practitioner-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getLeaderboard(): array
    {
        return app(ValidationMetrics::class)->practitionerLeaderboard();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('export_csv')
                ->label('Export CSV')->icon('heroicon-o-arrow-down-tray')->color('gray')
                ->action(function (): \Symfony\Component\HttpFoundation\StreamedResponse {
                    $rows = $this->getLeaderboard();
                    return response()->streamDownload(function () use ($rows) {
                        echo "Practitioner,Cohort,Sessions,Issues Found,Accepted,Retests\n";
                        foreach ($rows as $r) {
                            echo '"'.$r['member'].'","'.$r['cohort'].'",'.$r['sessions'].','.$r['issues_found'].','.$r['issues_accepted'].','.$r['retests']."\n";
                        }
                    }, 'practitioner-performance-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
                }),
        ];
    }
}
```

- [ ] **Step 10: `resources/views/filament/pages/validation-practitioner-dashboard.blade.php`**
```blade
<x-filament-panels::page>
    <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    @foreach (['Practitioner', 'Cohort', 'Sessions', 'Issues Found', 'Accepted', 'Retests'] as $h)
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($this->getLeaderboard() as $row)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $row['member'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['cohort'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['sessions'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['issues_found'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['issues_accepted'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['retests'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-sm text-gray-400">No members yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
```

- [ ] **Step 11: Run — expect PASS** (`--filter=ValidationDashboardAccessTest`). Sanity: `<php> artisan route:list --path=admin 2>&1 | head -5` (panel boots). Full suite — 0 failures (~396).

- [ ] **Step 12: Commit**
```bash
git add app/Filament/Pages/ValidationCohortDashboard.php app/Filament/Pages/ValidationIssueDashboard.php app/Filament/Pages/ValidationDeveloperDashboard.php app/Filament/Pages/ValidationPractitionerDashboard.php resources/views/filament/pages/validation-cohort-dashboard.blade.php resources/views/filament/pages/validation-issue-dashboard.blade.php resources/views/filament/pages/validation-developer-dashboard.blade.php resources/views/filament/pages/validation-practitioner-dashboard.blade.php tests/Feature/ValidationDashboardAccessTest.php
git commit -m "feat(validation): add 4 admin validation dashboards"
```

---

## Task 5: WeeklyReviewResource (generate + annotate)

**Files:** Create `app/Filament/Resources/WeeklyReviewResource.php` + Pages (List/Create/Edit/View); Modify `app/Models/WeeklyReview.php` (add `snapshotData`); Test `tests/Feature/WeeklyReviewTest.php`.

- [ ] **Step 1: Write the failing test**
```php
<?php

namespace Tests\Feature;

use App\Filament\Resources\WeeklyReviewResource;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\IssueReport;
use App\Models\User;
use App\Models\WeeklyReview;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class WeeklyReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_snapshot_data_freezes_week_metrics(): void
    {
        $admin  = User::factory()->create();
        $cohort = Cohort::factory()->create();
        $member = CohortMember::factory()->create(['cohort_id' => $cohort->id]);
        IssueReport::factory()->create(['cohort_member_id' => $member->id, 'created_at' => now()]);

        $data = WeeklyReview::snapshotData($cohort, now()->startOfWeek(), $admin->id);

        $this->assertEquals(1, $data['metrics']['issues_submitted']);
        $this->assertEquals($admin->id, $data['author_id']);
        $this->assertNotNull($data['week_end']);
        $this->assertNotNull($data['generated_at']);
    }

    public function test_unique_cohort_week(): void
    {
        $cohort = Cohort::factory()->create();
        $start  = now()->startOfWeek()->toDateString();
        WeeklyReview::factory()->create(['cohort_id' => $cohort->id, 'week_start' => $start]);

        $this->expectException(QueryException::class);
        WeeklyReview::factory()->create(['cohort_id' => $cohort->id, 'week_start' => $start]);
    }

    public function test_resource_admin_gated(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $this->assertTrue(WeeklyReviewResource::canAccess());

        $prac = User::factory()->create();
        $prac->assignRole('practitioner');
        $this->actingAs($prac);
        $this->assertFalse(WeeklyReviewResource::canAccess());
    }
}
```

- [ ] **Step 2: Run — expect FAIL** (`--filter=WeeklyReviewTest`).

- [ ] **Step 3: Add `snapshotData` to `app/Models/WeeklyReview.php`** (after `author()`, add `use` imports for Cohort already same namespace; add `use App\Support\ValidationMetrics;` and `use Carbon\CarbonInterface;` at top):
```php
    public static function snapshotData(Cohort $cohort, \Carbon\CarbonInterface $weekStart, int $authorId): array
    {
        return [
            'metrics'      => app(\App\Support\ValidationMetrics::class)->weeklySnapshot($cohort, $weekStart),
            'week_end'     => $weekStart->copy()->addDays(6)->toDateString(),
            'author_id'    => $authorId,
            'generated_at' => now(),
        ];
    }
```

- [ ] **Step 4: Create `app/Filament/Resources/WeeklyReviewResource.php`**
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeeklyReviewResource\Pages;
use App\Models\Cohort;
use App\Models\WeeklyReview;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WeeklyReviewResource extends Resource
{
    protected static ?string $model = WeeklyReview::class;
    protected static ?string $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 14;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('cohort_id')
                ->label('Cohort')->options(fn () => Cohort::pluck('name', 'id'))
                ->searchable()->required()
                ->disabledOn('edit'),
            Forms\Components\DatePicker::make('week_start')
                ->label('Week start')->native(false)->required()
                ->helperText('Start of the review week (snapshot covers 7 days).')
                ->disabledOn('edit'),
            Forms\Components\Textarea::make('summary')->rows(4),
            Forms\Components\Textarea::make('action_items')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cohort.name')->label('Cohort')->searchable(),
                Tables\Columns\TextColumn::make('week_start')->date(),
                Tables\Columns\TextColumn::make('week_end')->date(),
                Tables\Columns\TextColumn::make('author.name')->label('Author')->placeholder('—'),
                Tables\Columns\TextColumn::make('generated_at')->dateTime(),
            ])
            ->defaultSort('week_start', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWeeklyReviews::route('/'),
            'create' => Pages\CreateWeeklyReview::route('/create'),
            'view'   => Pages\ViewWeeklyReview::route('/{record}'),
            'edit'   => Pages\EditWeeklyReview::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 5: Create the 4 Page classes** under `app/Filament/Resources/WeeklyReviewResource/Pages/`:

`ListWeeklyReviews.php`:
```php
<?php

namespace App\Filament\Resources\WeeklyReviewResource\Pages;

use App\Filament\Resources\WeeklyReviewResource;
use Filament\Resources\Pages\ListRecords;

class ListWeeklyReviews extends ListRecords
{
    protected static string $resource = WeeklyReviewResource::class;
}
```

`CreateWeeklyReview.php` (this is where the snapshot is frozen):
```php
<?php

namespace App\Filament\Resources\WeeklyReviewResource\Pages;

use App\Filament\Resources\WeeklyReviewResource;
use App\Models\Cohort;
use App\Models\WeeklyReview;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateWeeklyReview extends CreateRecord
{
    protected static string $resource = WeeklyReviewResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $cohort = Cohort::findOrFail($data['cohort_id']);
        return array_merge(
            $data,
            WeeklyReview::snapshotData($cohort, Carbon::parse($data['week_start']), auth()->id())
        );
    }
}
```

`EditWeeklyReview.php`:
```php
<?php

namespace App\Filament\Resources\WeeklyReviewResource\Pages;

use App\Filament\Resources\WeeklyReviewResource;
use Filament\Resources\Pages\EditRecord;

class EditWeeklyReview extends EditRecord
{
    protected static string $resource = WeeklyReviewResource::class;
}
```

`ViewWeeklyReview.php`:
```php
<?php

namespace App\Filament\Resources\WeeklyReviewResource\Pages;

use App\Filament\Resources\WeeklyReviewResource;
use Filament\Resources\Pages\ViewRecord;

class ViewWeeklyReview extends ViewRecord
{
    protected static string $resource = WeeklyReviewResource::class;
}
```

- [ ] **Step 6: Run — expect PASS** (`--filter=WeeklyReviewTest`). Sanity: `route:list --path=admin`. Full suite — 0 failures (~399).

- [ ] **Step 7: Commit**
```bash
git add app/Models/WeeklyReview.php app/Filament/Resources/WeeklyReviewResource.php app/Filament/Resources/WeeklyReviewResource/Pages/ListWeeklyReviews.php app/Filament/Resources/WeeklyReviewResource/Pages/CreateWeeklyReview.php app/Filament/Resources/WeeklyReviewResource/Pages/EditWeeklyReview.php app/Filament/Resources/WeeklyReviewResource/Pages/ViewWeeklyReview.php tests/Feature/WeeklyReviewTest.php
git commit -m "feat(validation): add WeeklyReviewResource with metric snapshot generation"
```

---

## Task 6: FinalEvaluationResource + cohort Evaluate action

**Files:** Create `app/Filament/Resources/FinalEvaluationResource.php` + Pages (List/Create/View/Edit); Modify `app/Models/FinalEvaluation.php` (add `snapshotData`), `app/Filament/Resources/CohortResource/RelationManagers/MembersRelationManager.php` (Evaluate action); Test `tests/Feature/FinalEvaluationTest.php`.

- [ ] **Step 1: Write the failing test**
```php
<?php

namespace Tests\Feature;

use App\Filament\Resources\FinalEvaluationResource;
use App\Models\CohortMember;
use App\Models\FinalEvaluation;
use App\Models\IssueReport;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class FinalEvaluationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_snapshot_data_freezes_member_metrics(): void
    {
        $admin  = User::factory()->create();
        $member = CohortMember::factory()->create();
        IssueReport::factory()->create(['cohort_member_id' => $member->id]);

        $data = FinalEvaluation::snapshotData($member, $admin->id);

        $this->assertEquals(1, $data['metrics']['issues_found']);
        $this->assertArrayHasKey('member_name', $data['metrics']);
        $this->assertEquals($admin->id, $data['evaluator_id']);
        $this->assertNotNull($data['evaluated_at']);
    }

    public function test_unique_per_member(): void
    {
        $member = CohortMember::factory()->create();
        FinalEvaluation::factory()->create(['cohort_member_id' => $member->id]);

        $this->expectException(QueryException::class);
        FinalEvaluation::factory()->create(['cohort_member_id' => $member->id]);
    }

    public function test_resource_admin_gated(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $this->assertTrue(FinalEvaluationResource::canAccess());

        $prac = User::factory()->create();
        $prac->assignRole('practitioner');
        $this->actingAs($prac);
        $this->assertFalse(FinalEvaluationResource::canAccess());
    }
}
```

- [ ] **Step 2: Run — expect FAIL** (`--filter=FinalEvaluationTest`).

- [ ] **Step 3: Add `snapshotData` to `app/Models/FinalEvaluation.php`** (after `ratingOptions()`):
```php
    public static function snapshotData(CohortMember $member, int $evaluatorId): array
    {
        return [
            'metrics'      => app(\App\Support\ValidationMetrics::class)->memberContributionSnapshot($member),
            'evaluator_id' => $evaluatorId,
            'evaluated_at' => now(),
        ];
    }
```

- [ ] **Step 4: Create `app/Filament/Resources/FinalEvaluationResource.php`**
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinalEvaluationResource\Pages;
use App\Models\CohortMember;
use App\Models\FinalEvaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FinalEvaluationResource extends Resource
{
    protected static ?string $model = FinalEvaluation::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 15;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('cohort_member_id')
                ->label('Cohort member')
                ->options(fn () => CohortMember::with(['user', 'cohort'])->get()
                    ->mapWithKeys(fn (CohortMember $m) => [$m->id => ($m->user?->name ?? 'Member #'.$m->id).' — '.($m->cohort?->name ?? '')]))
                ->searchable()->required()->disabledOn('edit'),
            Forms\Components\Textarea::make('assessment')->rows(4)->required(),
            Forms\Components\Select::make('rating')->options(FinalEvaluation::ratingOptions())->required(),
            Forms\Components\Textarea::make('recommendation')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cohortMember.user.name')->label('Member')->searchable(),
                Tables\Columns\TextColumn::make('cohortMember.cohort.name')->label('Cohort'),
                Tables\Columns\TextColumn::make('rating')->badge()
                    ->formatStateUsing(fn ($state) => FinalEvaluation::ratingOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'outstanding' => 'success', 'strong' => 'info',
                        'satisfactory' => 'gray', 'needs_improvement' => 'warning', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('evaluator.name')->label('Evaluator')->placeholder('—'),
                Tables\Columns\TextColumn::make('evaluated_at')->dateTime(),
            ])
            ->defaultSort('evaluated_at', 'desc')
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFinalEvaluations::route('/'),
            'create' => Pages\CreateFinalEvaluation::route('/create'),
            'view'   => Pages\ViewFinalEvaluation::route('/{record}'),
            'edit'   => Pages\EditFinalEvaluation::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 5: Create the 4 Page classes** under `app/Filament/Resources/FinalEvaluationResource/Pages/`:

`ListFinalEvaluations.php`, `EditFinalEvaluation.php`, `ViewFinalEvaluation.php` — same shape as the WeeklyReview equivalents (swap class names + `protected static string $resource = FinalEvaluationResource::class;`, extending `ListRecords`/`EditRecord`/`ViewRecord` respectively).

`CreateFinalEvaluation.php` (freezes the snapshot):
```php
<?php

namespace App\Filament\Resources\FinalEvaluationResource\Pages;

use App\Filament\Resources\FinalEvaluationResource;
use App\Models\CohortMember;
use App\Models\FinalEvaluation;
use Filament\Resources\Pages\CreateRecord;

class CreateFinalEvaluation extends CreateRecord
{
    protected static string $resource = FinalEvaluationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $member = CohortMember::findOrFail($data['cohort_member_id']);
        return array_merge($data, FinalEvaluation::snapshotData($member, auth()->id()));
    }
}
```

For completeness, `ListFinalEvaluations.php`:
```php
<?php

namespace App\Filament\Resources\FinalEvaluationResource\Pages;

use App\Filament\Resources\FinalEvaluationResource;
use Filament\Resources\Pages\ListRecords;

class ListFinalEvaluations extends ListRecords
{
    protected static string $resource = FinalEvaluationResource::class;
}
```
`EditFinalEvaluation.php`:
```php
<?php

namespace App\Filament\Resources\FinalEvaluationResource\Pages;

use App\Filament\Resources\FinalEvaluationResource;
use Filament\Resources\Pages\EditRecord;

class EditFinalEvaluation extends EditRecord
{
    protected static string $resource = FinalEvaluationResource::class;
}
```
`ViewFinalEvaluation.php`:
```php
<?php

namespace App\Filament\Resources\FinalEvaluationResource\Pages;

use App\Filament\Resources\FinalEvaluationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewFinalEvaluation extends ViewRecord
{
    protected static string $resource = FinalEvaluationResource::class;
}
```

- [ ] **Step 6: Add an `evaluate` action to `app/Filament/Resources/CohortResource/RelationManagers/MembersRelationManager.php`** — add to the `->actions([...])` array (read the file first; it currently has an `EditAction`):
```php
                \Filament\Tables\Actions\Action::make('evaluate')
                    ->label('Evaluate')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->visible(fn (\App\Models\CohortMember $record) => $record->finalEvaluation()->doesntExist())
                    ->form([
                        \Filament\Forms\Components\Textarea::make('assessment')->rows(4)->required(),
                        \Filament\Forms\Components\Select::make('rating')
                            ->options(\App\Models\FinalEvaluation::ratingOptions())->required(),
                        \Filament\Forms\Components\Textarea::make('recommendation')->rows(3),
                    ])
                    ->action(function (\App\Models\CohortMember $record, array $data) {
                        \App\Models\FinalEvaluation::create(array_merge(
                            [
                                'cohort_member_id' => $record->id,
                                'assessment'       => $data['assessment'],
                                'rating'           => $data['rating'],
                                'recommendation'   => $data['recommendation'] ?? null,
                            ],
                            \App\Models\FinalEvaluation::snapshotData($record, auth()->id())
                        ));
                        \Filament\Notifications\Notification::make()->title('Final evaluation recorded.')->success()->send();
                    }),
```

- [ ] **Step 7: Run — expect PASS** (`--filter=FinalEvaluationTest`). Sanity: `route:list --path=admin`. Full suite — 0 failures (~402).

- [ ] **Step 8: Commit**
```bash
git add app/Models/FinalEvaluation.php app/Filament/Resources/FinalEvaluationResource.php app/Filament/Resources/FinalEvaluationResource/Pages/ListFinalEvaluations.php app/Filament/Resources/FinalEvaluationResource/Pages/CreateFinalEvaluation.php app/Filament/Resources/FinalEvaluationResource/Pages/EditFinalEvaluation.php app/Filament/Resources/FinalEvaluationResource/Pages/ViewFinalEvaluation.php app/Filament/Resources/CohortResource/RelationManagers/MembersRelationManager.php tests/Feature/FinalEvaluationTest.php
git commit -m "feat(validation): add FinalEvaluationResource and cohort Evaluate action"
```

---

## Final verification
- Full suite: `<php> artisan test` — expect ~402 passing (386 baseline + ~16 new), 0 failures.
- `<php> artisan route:list --path=admin 2>&1 | head` — panel boots; the 4 dashboard pages + 2 resources register.
- Manual smoke (dev server): as admin, open each of the 4 Validation Hub dashboards (data renders); create a WeeklyReview for a cohort+week (metrics frozen in the view); create a FinalEvaluation via the resource and via the cohort member "Evaluate" action (one per member). Confirm non-admin → 403 on the dashboards/resources.

## Post-completion
- Run `superpowers:finishing-a-development-branch` to merge.
- Memory: update [[clinical-validation-hub]] to mark SP3 done; SP4 (Certification + Advisory Council) remains.
