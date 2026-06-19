# Clinical Validation Hub — Sub-project 2 Implementation Plan (Developer Resolution + Retesting)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Close the validation loop — auto-create a developer task when an issue is sent to development, track it to "fixed" in Filament, then let the original reporting practitioner retest and either pass it (→ close) or fail it (→ reopen the task).

**Architecture:** Extends SP1's on-model state machine. `IssueReport::recordProductReview()` auto-creates one `DeveloperTask` per issue on the `sent_to_development` decision. Developer work is admin-managed in Filament (no new role/portal). Retesting is practitioner-driven through the existing Validation Hub portal, gated to the issue's original reporter; each attempt is an immutable `Retest` row.

**Tech Stack:** Laravel 13.8 / PHP 8.3 / Spatie Permission v8 / Filament v3 / Blade + Tailwind / SQLite (tests) / MySQL (prod). PHP binary: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`. Test cmd: `<php> artisan test --filter=<Class>`.

**Spec:** `docs/superpowers/specs/2026-06-19-clinical-validation-hub-sp2-design.md`. Baseline: 365 tests green.

---

## Conventions (verified against SP1 — follow exactly)
- **Migrations:** `return new class extends Migration`; string-enum + inline `// a|b|c` comment (NO `$table->enum()`); FK `->constrained('table')->cascadeOnDelete()` / `->nullable()->constrained()->nullOnDelete()`; `->unique()` single or `$table->unique([...])`.
- **Models:** `use HasFactory;`, `$fillable`, `$casts`, fully-qualified relationship return types (`\Illuminate\Database\Eloquent\Relations\BelongsTo` etc.), static `xOptions(): array`.
- **Factories:** `protected $model = X::class;`, `definition()` with `fake()`, FKs via `Related::factory()`.
- **Controllers (locale-aware):** a method binding a model takes `$locale` first — `store($locale, IssueReport $issue)`. Redirects: `redirect()->route('practitioner.x', ['locale' => app()->getLocale()])->with('success', '...')`. Uploads: `$file->store('validation/retests', 'public')`.
- **Filament resource:** `canAccess()` → `auth()->user()?->hasAnyRole(['super_admin','admin']) ?? false`; nav group `'Validation Hub'`; `form()/table()` public static; RelationManager `form()/table()` instance.
- **Tests:** `Tests\Feature` ns, `use RefreshDatabase;`, `setUp()` → `parent::setUp(); app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions(); $this->seed(\Database\Seeders\RolePermissionSeeder::class);`. Role user: `User::factory()->create()` then `->assignRole('practitioner')`. Portal URLs `/en/practitioner/...`.
- **Git:** PowerShell-safe `git commit -m "..."`; stage explicit paths only (never `git add -A`/`.`); do NOT stage unrelated untracked docs. Never `taskkill`; don't touch MySQL/Apache.

## File map
| File | Responsibility | Task |
|---|---|---|
| `database/migrations/2026_06_19_410001_create_developer_tasks_table.php` | developer_tasks schema | 1 |
| `database/migrations/2026_06_19_410002_create_retests_table.php` | retests schema | 1 |
| `app/Models/DeveloperTask.php` | task model + state methods | 2,3 |
| `app/Models/Retest.php` | retest model | 2 |
| `app/Models/IssueReport.php` (modify) | relationships, 3 statuses, auto-create, recordRetest | 2,3 |
| `database/factories/DeveloperTaskFactory.php`, `RetestFactory.php` | factories | 2 |
| `app/Http/Controllers/Practitioner/Validation/RetestController.php` | portal retest submit | 4 |
| `routes/web.php` (modify) | retest route | 4 |
| `resources/views/practitioner/validation/issues/show.blade.php` (modify) | retest panel + history | 4 |
| `resources/views/practitioner/validation/issues/index.blade.php` (modify) | "Retest needed" badge | 4 |
| `resources/views/practitioner/validation/dashboard.blade.php` (modify) | "Awaiting retest" stat | 4 |
| `app/Filament/Resources/DeveloperTaskResource.php` (+ Pages, RelationManager) | admin task board | 5 |
| `app/Filament/Resources/IssueReportResource.php` (modify) + `Pages/ListIssueReports.php` (modify) | Close visibility + Ready-for-Retest tab | 6 |

---

## Task 1: Migrations (developer_tasks + retests)

**Files:**
- Create: `database/migrations/2026_06_19_410001_create_developer_tasks_table.php`
- Create: `database/migrations/2026_06_19_410002_create_retests_table.php`
- Test: `tests/Feature/ValidationSp2MigrationsTest.php`

- [ ] **Step 1: Write the failing test**

`tests/Feature/ValidationSp2MigrationsTest.php`:
```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ValidationSp2MigrationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_developer_tasks_and_retests_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('developer_tasks'));
        $this->assertTrue(Schema::hasTable('retests'));
        foreach (['issue_report_id', 'assigned_to', 'title', 'priority', 'status', 'resolution_notes', 'started_at', 'fixed_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('developer_tasks', $c), "developer_tasks.$c");
        }
        foreach (['issue_report_id', 'developer_task_id', 'cohort_member_id', 'result', 'notes', 'attachments', 'retested_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('retests', $c), "retests.$c");
        }
    }
}
```

- [ ] **Step 2: Run test — expect FAIL** (`<php> artisan test --filter=ValidationSp2MigrationsTest`) with missing tables.

- [ ] **Step 3: Create `2026_06_19_410001_create_developer_tasks_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('developer_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_report_id')->constrained('issue_reports')->cascadeOnDelete()->unique();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('priority', 10); // critical|high|medium|low
            $table->string('status', 20)->default('open'); // open|in_progress|fixed|reopened|wont_fix
            $table->text('resolution_notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('fixed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developer_tasks');
    }
};
```

- [ ] **Step 4: Create `2026_06_19_410002_create_retests_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_report_id')->constrained('issue_reports')->cascadeOnDelete();
            $table->foreignId('developer_task_id')->nullable()->constrained('developer_tasks')->nullOnDelete();
            $table->foreignId('cohort_member_id')->constrained('cohort_members')->cascadeOnDelete();
            $table->string('result', 10); // passed|failed
            $table->text('notes');
            $table->json('attachments')->nullable();
            $table->timestamp('retested_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retests');
    }
};
```

- [ ] **Step 5: Run test — expect PASS.** Then full suite (`<php> artisan test`) — expect 0 failures (~366).

- [ ] **Step 6: Commit**
```bash
git add database/migrations/2026_06_19_410001_create_developer_tasks_table.php database/migrations/2026_06_19_410002_create_retests_table.php tests/Feature/ValidationSp2MigrationsTest.php
git commit -m "feat(validation): add developer_tasks and retests tables (SP2)"
```

---

## Task 2: Models, factories, IssueReport relationships + statuses

**Files:**
- Create: `app/Models/DeveloperTask.php`, `app/Models/Retest.php`
- Create: `database/factories/DeveloperTaskFactory.php`, `database/factories/RetestFactory.php`
- Modify: `app/Models/IssueReport.php` (add 2 relationships + 3 statuses only — logic comes in Task 3)
- Test: `tests/Feature/DeveloperTaskModelTest.php`

- [ ] **Step 1: Write the failing test**

`tests/Feature/DeveloperTaskModelTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\Retest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeveloperTaskModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_relationships_resolve(): void
    {
        $issue = IssueReport::factory()->create();
        $task  = DeveloperTask::factory()->create(['issue_report_id' => $issue->id]);
        $retest = Retest::factory()->create(['issue_report_id' => $issue->id, 'developer_task_id' => $task->id]);

        $this->assertEquals($task->id, $issue->fresh()->developerTask->id);
        $this->assertTrue($issue->fresh()->retests->contains($retest));
        $this->assertEquals($issue->id, $task->issueReport->id);
        $this->assertTrue($task->fresh()->retests->contains($retest));
        $this->assertEquals($task->id, $retest->developerTask->id);
    }

    public function test_new_statuses_and_option_maps(): void
    {
        $opts = IssueReport::statusOptions();
        $this->assertCount(13, $opts);
        $this->assertArrayHasKey('ready_for_retest', $opts);
        $this->assertArrayHasKey('retest_passed', $opts);
        $this->assertArrayHasKey('retest_failed', $opts);
        $this->assertCount(5, DeveloperTask::statusOptions());
        $this->assertCount(2, Retest::resultOptions());
    }
}
```

- [ ] **Step 2: Run test — expect FAIL** (`--filter=DeveloperTaskModelTest`), classes missing.

- [ ] **Step 3: Create `app/Models/DeveloperTask.php`** (state methods added in Task 3; relationships + options now)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_report_id', 'assigned_to', 'title', 'priority',
        'status', 'resolution_notes', 'started_at', 'fixed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'fixed_at'   => 'datetime',
    ];

    public function issueReport(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(IssueReport::class);
    }

    public function assignedTo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function retests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Retest::class);
    }

    public static function statusOptions(): array
    {
        return [
            'open'        => 'Open',
            'in_progress' => 'In Progress',
            'fixed'       => 'Fixed',
            'reopened'    => 'Reopened',
            'wont_fix'    => "Won't Fix",
        ];
    }

    public static function priorityOptions(): array
    {
        return IssueReport::severityOptions();
    }
}
```

- [ ] **Step 4: Create `app/Models/Retest.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retest extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_report_id', 'developer_task_id', 'cohort_member_id',
        'result', 'notes', 'attachments', 'retested_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'retested_at' => 'datetime',
    ];

    public function issueReport(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(IssueReport::class);
    }

    public function developerTask(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DeveloperTask::class);
    }

    public function cohortMember(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CohortMember::class);
    }

    public static function resultOptions(): array
    {
        return ['passed' => 'Passed', 'failed' => 'Failed'];
    }
}
```

- [ ] **Step 5: Create `database/factories/DeveloperTaskFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\DeveloperTask;
use App\Models\IssueReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DeveloperTask> */
class DeveloperTaskFactory extends Factory
{
    protected $model = DeveloperTask::class;

    public function definition(): array
    {
        return [
            'issue_report_id'  => IssueReport::factory(),
            'assigned_to'      => null,
            'title'            => fake()->sentence(4),
            'priority'         => 'medium',
            'status'           => 'open',
            'resolution_notes' => null,
            'started_at'       => null,
            'fixed_at'         => null,
        ];
    }
}
```

- [ ] **Step 6: Create `database/factories/RetestFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\CohortMember;
use App\Models\IssueReport;
use App\Models\Retest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Retest> */
class RetestFactory extends Factory
{
    protected $model = Retest::class;

    public function definition(): array
    {
        return [
            'issue_report_id'   => IssueReport::factory(),
            'developer_task_id' => null,
            'cohort_member_id'  => CohortMember::factory(),
            'result'            => 'passed',
            'notes'             => fake()->sentence(),
            'attachments'       => null,
            'retested_at'       => now(),
        ];
    }
}
```

- [ ] **Step 7: Modify `app/Models/IssueReport.php` — add 2 relationships after the `productReview()` method (after line 74)**

```php
    public function developerTask(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DeveloperTask::class);
    }

    public function retests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Retest::class);
    }
```

- [ ] **Step 8: Modify `IssueReport::statusOptions()` — add 3 entries before the `'closed'` line**

Change the array to insert (keep `'closed' => 'Closed'` as the last entry):
```php
            'sent_to_development' => 'Sent to Development',
            'fixed' => 'Fixed',
            'ready_for_retest' => 'Ready for Retest',
            'retest_passed' => 'Retest Passed',
            'retest_failed' => 'Retest Failed',
            'closed' => 'Closed',
```

- [ ] **Step 9: Run test — expect PASS** (`--filter=DeveloperTaskModelTest`). Then full suite — expect 0 failures (~368). Existing `IssueReportModelTest` asserted `statusOptions` count 10 in SP1 — **it will now fail (expects 10, gets 13)**. Update that one assertion in `tests/Feature/IssueReportModelTest.php` from `assertCount(10, IssueReport::statusOptions())` to `assertCount(13, IssueReport::statusOptions())` and include it in this commit.

- [ ] **Step 10: Commit**
```bash
git add app/Models/DeveloperTask.php app/Models/Retest.php database/factories/DeveloperTaskFactory.php database/factories/RetestFactory.php app/Models/IssueReport.php tests/Feature/DeveloperTaskModelTest.php tests/Feature/IssueReportModelTest.php
git commit -m "feat(validation): add DeveloperTask and Retest models with IssueReport relationships"
```

---

## Task 3: On-model state machine (auto-create + retest transitions)

**Files:**
- Modify: `app/Models/DeveloperTask.php` (add 4 state methods)
- Modify: `app/Models/IssueReport.php` (extend `recordProductReview`, add `recordRetest`)
- Test: `tests/Feature/DeveloperTaskTest.php`

- [ ] **Step 1: Write the failing test**

`tests/Feature/DeveloperTaskTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\IssueReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DeveloperTaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function reviewer(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    public function test_sent_to_development_auto_creates_one_task(): void
    {
        $issue = IssueReport::factory()->create(['status' => 'product_review', 'severity' => 'high']);
        $issue->recordProductReview($this->reviewer()->id, 'sent_to_development', 'Routing to dev');

        $this->assertEquals('sent_to_development', $issue->fresh()->status);
        $this->assertNotNull($issue->fresh()->developerTask);
        $this->assertEquals('open', $issue->fresh()->developerTask->status);
        $this->assertEquals('high', $issue->fresh()->developerTask->priority);

        // Re-running the decision must NOT create a second task.
        $issue->recordProductReview($this->reviewer()->id, 'sent_to_development');
        $this->assertDatabaseCount('developer_tasks', 1);
    }

    public function test_accepted_decision_does_not_create_task(): void
    {
        $issue = IssueReport::factory()->create(['status' => 'product_review']);
        $issue->recordProductReview($this->reviewer()->id, 'accepted');
        $this->assertDatabaseCount('developer_tasks', 0);
    }

    public function test_mark_fixed_moves_issue_to_ready_for_retest(): void
    {
        $issue = IssueReport::factory()->create(['status' => 'sent_to_development']);
        $task  = \App\Models\DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'in_progress']);

        $task->markFixed('Patched the save handler');

        $this->assertEquals('fixed', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->fixed_at);
        $this->assertEquals('ready_for_retest', $issue->fresh()->status);
    }

    public function test_mark_in_progress_and_reopen(): void
    {
        $task = \App\Models\DeveloperTask::factory()->create(['status' => 'open']);
        $task->markInProgress();
        $this->assertEquals('in_progress', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->started_at);

        $task->update(['status' => 'fixed']);
        $task->reopen();
        $this->assertEquals('reopened', $task->fresh()->status);
    }

    public function test_wont_fix_rejects_issue(): void
    {
        $issue = IssueReport::factory()->create(['status' => 'sent_to_development']);
        $task  = \App\Models\DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'open']);

        $task->markWontFix('Out of scope');

        $this->assertEquals('wont_fix', $task->fresh()->status);
        $this->assertEquals('rejected', $issue->fresh()->status);
    }

    public function test_record_retest_pass_and_fail(): void
    {
        $issue = IssueReport::factory()->create(['status' => 'ready_for_retest']);
        $task  = \App\Models\DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'fixed']);

        // FAIL → retest_failed + task reopened
        $issue->recordRetest($issue->cohort_member_id, 'failed', 'Still broken');
        $this->assertEquals('retest_failed', $issue->fresh()->status);
        $this->assertEquals('reopened', $task->fresh()->status);
        $this->assertDatabaseHas('retests', ['issue_report_id' => $issue->id, 'result' => 'failed', 'developer_task_id' => $task->id]);

        // Re-fix → ready again → PASS → retest_passed
        $task->markFixed();
        $issue->fresh()->recordRetest($issue->cohort_member_id, 'passed', 'Works now');
        $this->assertEquals('retest_passed', $issue->fresh()->status);
        $this->assertDatabaseCount('retests', 2);
    }
}
```

- [ ] **Step 2: Run test — expect FAIL** (`--filter=DeveloperTaskTest`), methods missing.

- [ ] **Step 3: Add 4 state methods to `app/Models/DeveloperTask.php`** (after `priorityOptions()`)

```php
    public function markInProgress(): void
    {
        $this->update([
            'status'     => 'in_progress',
            'started_at' => $this->started_at ?? now(),
        ]);
    }

    public function markFixed(?string $notes = null): void
    {
        $this->update([
            'status'           => 'fixed',
            'fixed_at'         => now(),
            'resolution_notes' => $notes ?? $this->resolution_notes,
        ]);
        $this->issueReport->update(['status' => 'ready_for_retest']);
    }

    public function reopen(): void
    {
        $this->update(['status' => 'reopened']);
    }

    public function markWontFix(?string $notes = null): void
    {
        $this->update([
            'status'           => 'wont_fix',
            'resolution_notes' => $notes ?? $this->resolution_notes,
        ]);
        $this->issueReport->update(['status' => 'rejected']);
    }
```

- [ ] **Step 4: Extend `IssueReport::recordProductReview()`** — replace the method body's tail so it auto-creates a task on `sent_to_development`:

```php
    public function recordProductReview(int $reviewerId, string $decision, ?string $notes = null): void
    {
        $this->productReview()->create([
            'reviewer_id' => $reviewerId,
            'decision'    => $decision,
            'notes'       => $notes,
            'reviewed_at' => now(),
        ]);

        // decision values: accepted | rejected | duplicate | sent_to_development — all are valid statuses
        $this->update(['status' => $decision]);

        if ($decision === 'sent_to_development') {
            $this->developerTask()->firstOrCreate(
                ['issue_report_id' => $this->id],
                ['title' => $this->title, 'priority' => $this->severity, 'status' => 'open']
            );
        }
    }
```

- [ ] **Step 5: Add `recordRetest()` to `app/Models/IssueReport.php`** (after `clinicalApproved()`)

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

- [ ] **Step 6: Run test — expect PASS.** Then full suite — expect 0 failures (~373). Existing `IssueReportTriageTest` (SP1) exercises `recordProductReview('sent_to_development')`; it will now also create a task — that does not break its assertions (it asserts status only). Verify it still passes.

- [ ] **Step 7: Commit**
```bash
git add app/Models/DeveloperTask.php app/Models/IssueReport.php tests/Feature/DeveloperTaskTest.php
git commit -m "feat(validation): add developer task state machine and retest transitions"
```

---

## Task 4: Retest portal flow (controller + route + view edits)

**Files:**
- Create: `app/Http/Controllers/Practitioner/Validation/RetestController.php`
- Modify: `routes/web.php` (add 1 route inside the `validation` group)
- Modify: `resources/views/practitioner/validation/issues/show.blade.php`, `issues/index.blade.php`, `dashboard.blade.php`
- Test: `tests/Feature/RetestTest.php`

- [ ] **Step 1: Write the failing test**

`tests/Feature/RetestTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RetestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    /** Reporter + their ready_for_retest issue + a fixed task. */
    private function scenario(): array
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $cohort = Cohort::factory()->create();
        $member = CohortMember::factory()->create(['cohort_id' => $cohort->id, 'user_id' => $user->id, 'status' => 'active']);
        $issue  = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'ready_for_retest']);
        $task   = DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'fixed']);
        return compact('user', 'member', 'issue', 'task');
    }

    public function test_reporter_can_pass_retest(): void
    {
        ['user' => $user, 'issue' => $issue] = $this->scenario();

        $this->actingAs($user)
            ->post("/en/practitioner/validation/issues/{$issue->id}/retests", [
                'result' => 'passed', 'notes' => 'Confirmed fixed.',
            ])->assertRedirect();

        $this->assertEquals('retest_passed', $issue->fresh()->status);
        $this->assertDatabaseHas('retests', ['issue_report_id' => $issue->id, 'result' => 'passed']);
    }

    public function test_reporter_failed_retest_reopens_task(): void
    {
        ['user' => $user, 'issue' => $issue, 'task' => $task] = $this->scenario();

        $this->actingAs($user)
            ->post("/en/practitioner/validation/issues/{$issue->id}/retests", [
                'result' => 'failed', 'notes' => 'Still broken.',
            ])->assertRedirect();

        $this->assertEquals('retest_failed', $issue->fresh()->status);
        $this->assertEquals('reopened', $task->fresh()->status);
    }

    public function test_other_practitioner_cannot_retest(): void
    {
        ['issue' => $issue] = $this->scenario();
        $other = User::factory()->create();
        $other->assignRole('practitioner');

        $this->actingAs($other)
            ->post("/en/practitioner/validation/issues/{$issue->id}/retests", [
                'result' => 'passed', 'notes' => 'x',
            ])->assertForbidden();
    }

    public function test_cannot_retest_issue_not_ready(): void
    {
        ['user' => $user, 'member' => $member] = $this->scenario();
        $notReady = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'submitted']);

        $this->actingAs($user)
            ->post("/en/practitioner/validation/issues/{$notReady->id}/retests", [
                'result' => 'passed', 'notes' => 'x',
            ])->assertStatus(422);
    }

    public function test_non_practitioner_forbidden(): void
    {
        ['issue' => $issue] = $this->scenario();
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)
            ->post("/en/practitioner/validation/issues/{$issue->id}/retests", [
                'result' => 'passed', 'notes' => 'x',
            ])->assertForbidden();
    }
}
```

- [ ] **Step 2: Run test — expect FAIL** (`--filter=RetestTest`), route missing (404/405).

- [ ] **Step 3: Add the route** in `routes/web.php` — inside the existing `Route::prefix('validation')->name('validation.')->group(...)`, after the `issues.show` line:

```php
                    Route::post('/issues/{issue}/retests', [\App\Http\Controllers\Practitioner\Validation\RetestController::class, 'store'])->name('issues.retests.store');
```

- [ ] **Step 4: Create `app/Http/Controllers/Practitioner/Validation/RetestController.php`**

```php
<?php

namespace App\Http\Controllers\Practitioner\Validation;

use App\Http\Controllers\Controller;
use App\Models\IssueReport;
use Illuminate\Http\Request;

class RetestController extends Controller
{
    public function store(Request $request, $locale, IssueReport $issue)
    {
        abort_unless($issue->cohortMember->user_id === auth()->id(), 403);
        abort_unless($issue->status === 'ready_for_retest', 422, 'Issue is not awaiting retest.');

        $validated = $request->validate([
            'result'        => 'required|in:passed,failed',
            'notes'         => 'required|string|max:3000',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('validation/retests', 'public');
            }
        }

        $issue->recordRetest($issue->cohort_member_id, $validated['result'], $validated['notes'], $paths ?: null);

        return redirect()
            ->route('practitioner.validation.issues.show', ['locale' => app()->getLocale(), 'issue' => $issue->id])
            ->with('success', 'Retest submitted.');
    }
}
```
**Note on signature:** the route is `/{locale}/practitioner/validation/issues/{issue}/retests`. Laravel injects `Request` by type, then fills scalar `$locale` from the first route param and binds `{issue}` by name. Order matches SP1's proven `FindingController::store(Request $request, $locale, PractitionerApplication $application)`.

- [ ] **Step 5: Edit `resources/views/practitioner/validation/issues/show.blade.php`** — add, inside the layout, a retest panel + history. Place after the existing status/detail blocks (before the layout close). Match the file's existing card styling (study the file first):

```blade
        {{-- Retest panel: only the original reporter, only when awaiting retest --}}
        @if($issue->status === 'ready_for_retest' && $issue->cohortMember->user_id === auth()->id())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mt-6">
            <h2 class="text-lg font-semibold text-white mb-1">Retest this fix</h2>
            <p class="text-slate-400 text-sm mb-4">The development team marked this issue fixed. Please retest and report the result.</p>
            <form method="POST" action="{{ route('practitioner.validation.issues.retests.store', ['locale' => app()->getLocale(), 'issue' => $issue->id]) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Result <span class="text-red-400">*</span></label>
                    <select name="result" required class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-3 py-2 text-sm">
                        <option value="passed">Passed — the fix works</option>
                        <option value="failed">Failed — still broken</option>
                    </select>
                    @error('result')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Notes <span class="text-red-400">*</span></label>
                    <textarea name="notes" rows="3" required maxlength="3000" class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
                    @error('notes')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Attachments</label>
                    <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm text-slate-400">
                </div>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Submit Retest</button>
            </form>
        </div>
        @endif

        {{-- Retest history (read-only) --}}
        @if($issue->retests->isNotEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mt-6">
            <h2 class="text-lg font-semibold text-white mb-4">Retest history</h2>
            <ul class="space-y-3">
                @foreach($issue->retests->sortByDesc('retested_at') as $i => $retest)
                <li class="flex items-start gap-3">
                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $retest->result === 'passed' ? 'bg-emerald-900 text-emerald-300' : 'bg-red-900 text-red-300' }}">
                        {{ \App\Models\Retest::resultOptions()[$retest->result] ?? $retest->result }}
                    </span>
                    <div class="text-sm text-slate-300">
                        <div>{{ $retest->notes }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">{{ optional($retest->retested_at)->format('M j, Y g:ia') }}</div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
```
The `show()` controller already eager-loads relationships; ensure `retests` is available — if `$issue->retests` triggers a lazy load that's fine here, but for cleanliness the existing `IssueReportController::show()` `->load(...)` call may be extended to include `'retests'` and `'developerTask'`. Add `'retests'` to that load array if present.

- [ ] **Step 6: Edit `resources/views/practitioner/validation/issues/index.blade.php`** — in the row rendering, add a "Retest needed" badge when `$issue->status === 'ready_for_retest'`. Near the status badge cell add:

```blade
                            @if($issue->status === 'ready_for_retest')
                            <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium bg-amber-900 text-amber-300">Action needed: Retest</span>
                            @endif
```

- [ ] **Step 7: Edit `resources/views/practitioner/validation/dashboard.blade.php`** — add an "Awaiting your retest" stat card in the active-cohort stats block. The dashboard receives `$stats` (sessions/issues/open/closed). Add a computed count inline using the member:

In the active-cohort branch, alongside the existing stat cards add:
```blade
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                <div class="text-2xl font-bold text-amber-400">{{ $cohortMember->issueReports()->where('status', 'ready_for_retest')->count() }}</div>
                <div class="text-xs text-slate-400 mt-1">Awaiting your retest</div>
            </div>
```

- [ ] **Step 8: Run test — expect PASS** (`--filter=RetestTest`). Then full suite — expect 0 failures (~378).

- [ ] **Step 9: Commit**
```bash
git add app/Http/Controllers/Practitioner/Validation/RetestController.php routes/web.php resources/views/practitioner/validation/issues/show.blade.php resources/views/practitioner/validation/issues/index.blade.php resources/views/practitioner/validation/dashboard.blade.php tests/Feature/RetestTest.php
git commit -m "feat(validation): add practitioner retest portal flow"
```

---

## Task 5: DeveloperTaskResource (Filament admin board)

**Files:**
- Create: `app/Filament/Resources/DeveloperTaskResource.php`
- Create: `app/Filament/Resources/DeveloperTaskResource/Pages/ListDeveloperTasks.php`, `ViewDeveloperTask.php`
- Create: `app/Filament/Resources/DeveloperTaskResource/RelationManagers/RetestsRelationManager.php`
- Test: `tests/Feature/DeveloperTaskAdminTest.php`

- [ ] **Step 1: Write the failing test**

`tests/Feature/DeveloperTaskAdminTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Filament\Resources\DeveloperTaskResource;
use App\Filament\Resources\DeveloperTaskResource\Pages\ListDeveloperTasks;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DeveloperTaskAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_admin_can_access_resource_practitioner_cannot(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $this->assertTrue(DeveloperTaskResource::canAccess());
        $this->assertFalse(DeveloperTaskResource::canCreate());

        $prac = User::factory()->create();
        $prac->assignRole('practitioner');
        $this->actingAs($prac);
        $this->assertFalse(DeveloperTaskResource::canAccess());
    }

    public function test_pages_and_tabs_registered(): void
    {
        $this->assertArrayHasKey('index', DeveloperTaskResource::getPages());
        $this->assertArrayHasKey('view', DeveloperTaskResource::getPages());

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $page = new ListDeveloperTasks();
        $this->assertCount(6, $page->getTabs());
    }
}
```

- [ ] **Step 2: Run test — expect FAIL** (`--filter=DeveloperTaskAdminTest`).

- [ ] **Step 3: Create `app/Filament/Resources/DeveloperTaskResource.php`**

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeveloperTaskResource\Pages;
use App\Filament\Resources\DeveloperTaskResource\RelationManagers\RetestsRelationManager;
use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\User;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeveloperTaskResource extends Resource
{
    protected static ?string $model = DeveloperTask::class;
    protected static ?string $navigationIcon  = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->weight('semibold'),
                Tables\Columns\TextColumn::make('issueReport.severity')->label('Severity')->badge()
                    ->formatStateUsing(fn ($state) => IssueReport::severityOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('assignedTo.name')->label('Assignee')->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => DeveloperTask::statusOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'open' => 'gray', 'in_progress' => 'info', 'fixed' => 'success',
                        'reopened' => 'warning', 'wont_fix' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('priority')->badge(),
                Tables\Columns\TextColumn::make('retests_count')->counts('retests')->label('Retests'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('assign')
                    ->label('Assign')->icon('heroicon-o-user')->color('gray')
                    ->form([
                        Forms\Components\Select::make('assigned_to')->label('Assignee')
                            ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()->required(),
                    ])
                    ->action(function (DeveloperTask $record, array $data) {
                        $record->update(['assigned_to' => $data['assigned_to']]);
                        Notification::make()->title('Task assigned.')->success()->send();
                    }),
                Tables\Actions\Action::make('start')
                    ->label('Start')->icon('heroicon-o-play')->color('info')
                    ->visible(fn (DeveloperTask $r) => in_array($r->status, ['open', 'reopened']))
                    ->action(function (DeveloperTask $r) {
                        $r->markInProgress();
                        Notification::make()->title('Task in progress.')->success()->send();
                    }),
                Tables\Actions\Action::make('mark_fixed')
                    ->label('Mark Fixed')->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn (DeveloperTask $r) => in_array($r->status, ['in_progress', 'reopened']))
                    ->form([Forms\Components\Textarea::make('resolution_notes')->rows(3)])
                    ->action(function (DeveloperTask $r, array $data) {
                        $r->markFixed($data['resolution_notes'] ?? null);
                        Notification::make()->title('Marked fixed — issue ready for retest.')->success()->send();
                    }),
                Tables\Actions\Action::make('reopen')
                    ->label('Reopen')->icon('heroicon-o-arrow-path')->color('warning')
                    ->visible(fn (DeveloperTask $r) => $r->status === 'fixed')
                    ->requiresConfirmation()
                    ->action(function (DeveloperTask $r) {
                        $r->reopen();
                        Notification::make()->title('Task reopened.')->success()->send();
                    }),
                Tables\Actions\Action::make('wont_fix')
                    ->label("Won't Fix")->icon('heroicon-o-no-symbol')->color('danger')
                    ->visible(fn (DeveloperTask $r) => $r->status !== 'wont_fix')
                    ->form([Forms\Components\Textarea::make('notes')->rows(3)])
                    ->action(function (DeveloperTask $r, array $data) {
                        $r->markWontFix($data['notes'] ?? null);
                        Notification::make()->title("Marked won't fix — issue rejected.")->success()->send();
                    }),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Task')->columns(3)->schema([
                Infolists\Components\TextEntry::make('title')->columnSpanFull()->weight('semibold'),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->formatStateUsing(fn ($state) => DeveloperTask::statusOptions()[$state] ?? $state),
                Infolists\Components\TextEntry::make('priority')->badge(),
                Infolists\Components\TextEntry::make('assignedTo.name')->label('Assignee')->placeholder('—'),
                Infolists\Components\TextEntry::make('started_at')->dateTime()->placeholder('—'),
                Infolists\Components\TextEntry::make('fixed_at')->dateTime()->placeholder('—'),
                Infolists\Components\TextEntry::make('resolution_notes')->columnSpanFull()->placeholder('—'),
            ]),
            Infolists\Components\Section::make('Issue')->columns(2)->schema([
                Infolists\Components\TextEntry::make('issueReport.title')->label('Issue')->columnSpanFull(),
                Infolists\Components\TextEntry::make('issueReport.description')->label('Description')->columnSpanFull()->placeholder('—'),
                Infolists\Components\TextEntry::make('issueReport.steps_to_reproduce')->label('Steps to Reproduce')->columnSpanFull()->placeholder('—'),
                Infolists\Components\TextEntry::make('issueReport.expected_result')->label('Expected')->placeholder('—'),
                Infolists\Components\TextEntry::make('issueReport.actual_result')->label('Actual')->placeholder('—'),
                Infolists\Components\TextEntry::make('issueReport.clinical_impact')->label('Clinical Impact')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [RetestsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeveloperTasks::route('/'),
            'view'  => Pages\ViewDeveloperTask::route('/{record}'),
        ];
    }
}
```

- [ ] **Step 4: Create `app/Filament/Resources/DeveloperTaskResource/Pages/ListDeveloperTasks.php`**

```php
<?php

namespace App\Filament\Resources\DeveloperTaskResource\Pages;

use App\Filament\Resources\DeveloperTaskResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListDeveloperTasks extends ListRecords
{
    protected static string $resource = DeveloperTaskResource::class;

    public function getTabs(): array
    {
        return [
            'all'         => Tab::make('All'),
            'open'        => Tab::make('Open')->modifyQueryUsing(fn ($q) => $q->where('status', 'open')),
            'in_progress' => Tab::make('In Progress')->modifyQueryUsing(fn ($q) => $q->where('status', 'in_progress')),
            'fixed'       => Tab::make('Fixed')->modifyQueryUsing(fn ($q) => $q->where('status', 'fixed')),
            'reopened'    => Tab::make('Reopened')->modifyQueryUsing(fn ($q) => $q->where('status', 'reopened')),
            'wont_fix'    => Tab::make("Won't Fix")->modifyQueryUsing(fn ($q) => $q->where('status', 'wont_fix')),
        ];
    }
}
```

- [ ] **Step 5: Create `app/Filament/Resources/DeveloperTaskResource/Pages/ViewDeveloperTask.php`**

```php
<?php

namespace App\Filament\Resources\DeveloperTaskResource\Pages;

use App\Filament\Resources\DeveloperTaskResource;
use Filament\Resources\Pages\ViewRecord;

class ViewDeveloperTask extends ViewRecord
{
    protected static string $resource = DeveloperTaskResource::class;
}
```

- [ ] **Step 6: Create `app/Filament/Resources/DeveloperTaskResource/RelationManagers/RetestsRelationManager.php`**

```php
<?php

namespace App\Filament\Resources\DeveloperTaskResource\RelationManagers;

use App\Models\Retest;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RetestsRelationManager extends RelationManager
{
    protected static string $relationship = 'retests';
    protected static ?string $title = 'Retests';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('result')->badge()
                    ->formatStateUsing(fn ($state) => Retest::resultOptions()[$state] ?? $state)
                    ->color(fn ($state) => $state === 'passed' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('cohortMember.user.name')->label('By')->placeholder('—'),
                Tables\Columns\TextColumn::make('notes')->limit(60),
                Tables\Columns\TextColumn::make('retested_at')->dateTime(),
            ])
            ->defaultSort('retested_at', 'desc');
    }
}
```

- [ ] **Step 7: Run test — expect PASS** (`--filter=DeveloperTaskAdminTest`). Then sanity: `<php> artisan route:list --path=admin 2>&1 | head -5` (panel registers). Then full suite — expect 0 failures (~380).

- [ ] **Step 8: Commit**
```bash
git add app/Filament/Resources/DeveloperTaskResource.php app/Filament/Resources/DeveloperTaskResource/Pages/ListDeveloperTasks.php app/Filament/Resources/DeveloperTaskResource/Pages/ViewDeveloperTask.php app/Filament/Resources/DeveloperTaskResource/RelationManagers/RetestsRelationManager.php tests/Feature/DeveloperTaskAdminTest.php
git commit -m "feat(validation): add DeveloperTaskResource admin board with retest history"
```

---

## Task 6: Extend IssueReportResource (Close visibility + Ready-for-Retest tab)

**Files:**
- Modify: `app/Filament/Resources/IssueReportResource.php` (Close action `visible()` — line ~128)
- Modify: `app/Filament/Resources/IssueReportResource/Pages/ListIssueReports.php` (add tab)
- Test: `tests/Feature/IssueRetestTriageTest.php`

- [ ] **Step 1: Write the failing test**

`tests/Feature/IssueRetestTriageTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Filament\Resources\IssueReportResource\Pages\ListIssueReports;
use App\Models\IssueReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class IssueRetestTriageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_ready_for_retest_tab_present(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $page = new ListIssueReports();
        $this->assertArrayHasKey('ready_for_retest', $page->getTabs());
    }

    public function test_status_options_include_retest_states(): void
    {
        $opts = IssueReport::statusOptions();
        $this->assertArrayHasKey('ready_for_retest', $opts);
        $this->assertArrayHasKey('retest_passed', $opts);
        $this->assertArrayHasKey('retest_failed', $opts);
    }
}
```
(The Close-action visibility for `retest_passed` is exercised behaviourally; the unit assertions above guard the resource wiring. The visibility extension itself is verified by reading the action — keep it minimal.)

- [ ] **Step 2: Run test — expect FAIL** (`--filter=IssueRetestTriageTest`), `ready_for_retest` tab missing.

- [ ] **Step 3: Extend the Close action `visible()`** in `app/Filament/Resources/IssueReportResource.php` — change:
```php
                    ->visible(fn (IssueReport $r) => in_array($r->status, ['accepted', 'rejected', 'duplicate']))
```
to:
```php
                    ->visible(fn (IssueReport $r) => in_array($r->status, ['accepted', 'rejected', 'duplicate', 'retest_passed']))
```

- [ ] **Step 4: Add the Ready-for-Retest tab** in `app/Filament/Resources/IssueReportResource/Pages/ListIssueReports.php` — insert into the `getTabs()` array (after `'product_review'`):
```php
            'ready_for_retest'    => Tab::make('Ready for Retest')->modifyQueryUsing(fn ($q) => $q->where('status', 'ready_for_retest')),
```

- [ ] **Step 5: Run test — expect PASS.** Then full suite — expect 0 failures (~382).

- [ ] **Step 6: Commit**
```bash
git add app/Filament/Resources/IssueReportResource.php app/Filament/Resources/IssueReportResource/Pages/ListIssueReports.php tests/Feature/IssueRetestTriageTest.php
git commit -m "feat(validation): extend issue triage with retest tab and close transition"
```

---

## Final verification
- Full suite: `<php> artisan test` — expect ~382 passing (365 baseline + ~17 new), 0 failures.
- Manual smoke (dev server): admin product decision = sent_to_development → DeveloperTask appears in Validation Hub; assign + Start + Mark Fixed → issue shows `ready_for_retest`; as the reporting practitioner, open the issue → Retest panel → submit Fail → task reopens; Mark Fixed again → submit Pass → status `retest_passed` → admin Close → `closed`.
- Auth spot-checks: non-admin → 403 on `/admin/developer-tasks`; a different practitioner → 403 on another's retest endpoint; retest on a non-`ready_for_retest` issue → 422.

## Post-completion
- Run `superpowers:finishing-a-development-branch` to merge.
- Memory: update [[clinical-validation-hub]] to mark Sub-project 2 done; SP3 (Weekly Review + dashboards) and SP4 (Certification + Advisory Council) remain.
