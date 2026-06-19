# Clinical Validation & Innovation Hub — Implementation Plan (Sub-project 1)

## Context

OPES Health Systems needs a structured way for vetted practitioners to validate the clinical software during time-boxed, specialty-segmented testing cohorts. Today the Practitioner portal supports applications, findings, suggestions, and bug reports — but nothing that drives a disciplined validation workflow (daily test sessions tied to a product/module/workflow catalog, formal 14-field issue reports, and a clinical→product triage pipeline).

This plan implements **Sub-project 1 of 4** (Phases 1–8) from the approved spec at `docs/superpowers/specs/2026-06-18-clinical-validation-hub-design.md`: foundation, cohort management, a seeded test-case catalog, daily testing sessions, issue submission, and the clinical/product review triage. It is purely additive — no existing model, controller, view, or flow is modified, except **one column** added to `practitioner_programs`.

**Architecture:** Two-phase pipeline (apply → place). A "validation program" is a `PractitionerProgram` with `program_type = 'validation'`. Practitioners apply through the existing application flow; after approval an admin "Place in Cohort" Filament action creates a `CohortMember`. The practitioner then gets a new **Validation Hub** portal section for daily sessions and issue reports. Admins triage issues through `ClinicalReview` → `ProductReview` Filament actions.

**Tech stack:** Laravel 13.8 / PHP 8.3 / Spatie Permission v8 / Filament v3 / Blade + Tailwind / SQLite (tests) / MySQL (prod).

**Definition of done:** 11 new tables + 1 column, 11 models, 11 factories, 6 Filament resources + Place-in-Cohort action, 3 portal controllers + 6 views, 1 explicit seeder (56 test cases), 4 new test classes (16+ tests). Existing suite (330 tests) stays green.

---

## Codebase conventions to follow (verified)

- **Migrations:** anonymous class `return new class extends Migration`. Use `$table->foreignId('x')->constrained('table')->cascadeOnDelete()`, `->nullable()->constrained()->nullOnDelete()`, `->restrictOnDelete()`. **No `enum()`** — use `$table->string('status', 20)->default('...'); // a|b|c`. Composite unique: `$table->unique(['a','b'])`.
- **Models:** `use HasFactory;`, `$fillable`, `$casts`, relationship methods returning `HasMany`/`BelongsTo`, static `xOptions(): array` methods for dropdowns.
- **Factories:** `protected $model = X::class;`, `definition()` with `fake()`, related models via `'fk_id' => Related::factory()`, state methods returning `$this->state(fn (array $a) => [...])`.
- **Filament resource:** `protected static ?string $model`, `$navigationIcon` (heroicon-o-*), `$navigationGroup`, `$navigationSort`; `public static function canAccess(): bool { return auth()->user()?->hasAnyRole(['super_admin','admin']) ?? false; }`; `form()`, `table()`, `getRelations()`, `getPages()`. Reference: `app/Filament/Resources/EmployeeResource.php`, `CourseResource.php`.
- **RelationManager:** extends `RelationManager`, `protected static string $relationship`, instance `form()`/`table()`, `->headerActions([CreateAction/AttachAction])`, `->actions([...])`. Reference: `EmployeeResource/RelationManagers/TrainingRecordsRelationManager.php`, `CourseResource/RelationManagers/EnrollmentsRelationManager.php`.
- **Modal action with form:** `Tables\Actions\Action::make('slug')->form([Forms\Components\Select::make(...)])->action(function (Model $record, array $data) {...})` then `Notification::make()->title(...)->success()->send();`. Reference: `TicketResource/Pages/ViewTicket.php`, and the `approve` action already in `PractitionerApplicationResource.php:113`.
- **Routes:** practitioner group at `routes/web.php:177` — `Route::middleware(['auth','role:practitioner'])->prefix('practitioner')->name('practitioner.')`. An outer `{locale}` prefix wraps it, so URLs are `/en/practitioner/...`.
- **Controller signatures (locale-aware):** methods with a bound model take `$locale` first — e.g. `show($locale, IssueReport $issue)`, `store(Request $request, $locale, ...)`. Methods with no bound model can omit `$locale` (like `SuggestionController::store(Request $request)`). Reference: `app/Http/Controllers/Practitioner/FindingController.php`.
- **Redirects from controllers:** `return redirect()->route('practitioner.x', ['locale' => app()->getLocale()])->with('success', '...');`
- **File uploads:** `$request->file('field')->store('validation/sessions', 'public')` → store returned paths as JSON. Use the `public` disk (matches codebase; lets Filament preview images).
- **Views:** `<x-layouts.practitioner title="...">`, `@csrf`, `@error('f')...@enderror`, `old('f')`, Tailwind light theme (`bg-white`, `text-slate-900`, `focus:ring-emerald-500`). Reference: `resources/views/practitioner/bug-reports/create.blade.php`.
- **Tests:** `Tests\Feature` namespace, `use RefreshDatabase;`, `setUp()` calls `parent::setUp(); app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions(); $this->seed(\Database\Seeders\RolePermissionSeeder::class);`. Create role users via `User::factory()->create()` then `->assignRole('practitioner')`. Hit URLs as `/en/practitioner/...`. Assert with `assertDatabaseHas`, `assertStatus(422)`, `assertForbidden()`, `assertRedirect()`.
- **PHP binary:** `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`. **Test command:** `php artisan test --filter=<Class>`.

> **Execution discipline (CLAUDE.md constraints):** PowerShell git commits use `-m "..."` (no here-strings). Stage only explicit file paths (never `git add -A`/`.`). Never run `taskkill mysqld.exe`/`httpd.exe`. Ports 80/443/3306 are immutable.

---

## Task list (TDD, one commit per task)

Each task: write failing test(s) → run to confirm fail → implement → run to confirm pass → run full suite → commit named files.

### Task 1 — Migrations (12 total: 1 column + 11 tables)

**Files (create in `database/migrations/`, timestamp prefix `2026_06_19_4000NN_`):**

1. `..._01_add_program_type_to_practitioner_programs.php`
```php
Schema::table('practitioner_programs', function (Blueprint $table) {
    $table->string('program_type', 20)->default('general')->after('type'); // general|validation
});
// down(): $table->dropColumn('program_type');
```
2. `..._02_create_cohorts_table.php`
```php
$table->id();
$table->foreignId('practitioner_program_id')->constrained('practitioner_programs')->cascadeOnDelete();
$table->string('name'); $table->string('specialty');
$table->text('description')->nullable();
$table->date('start_date'); $table->date('end_date');
$table->unsignedInteger('max_members')->nullable();
$table->string('status', 20)->default('draft'); // draft|active|completed
$table->timestamps();
```
3. `..._03_create_cohort_members_table.php`
```php
$table->id();
$table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
$table->string('status', 20)->default('active'); // active|suspended|completed|removed
$table->timestamp('placed_at')->nullable();
$table->timestamps();
$table->unique(['cohort_id', 'user_id']);
```
4. `..._04_create_validation_products_table.php` — `name`, `code` unique, `description` nullable text, `is_active` bool default true, timestamps.
5. `..._05_create_validation_modules_table.php` — `validation_product_id` constrained cascade, `name`, `code`, `description` nullable, `is_active` default true, `unique(['validation_product_id','code'])`.
6. `..._06_create_validation_workflows_table.php` — `validation_module_id` constrained cascade, `name`, `code`, `description` nullable, `is_active` default true, `unique(['validation_module_id','code'])`.
7. `..._07_create_validation_test_cases_table.php` — `validation_workflow_id` constrained cascade, `title`, `description` nullable, `steps` nullable text, `expected_result` nullable text, `is_active` default true, timestamps.
8. `..._08_create_cohort_test_cases_table.php` — `cohort_id` constrained cascade, `validation_test_case_id` constrained cascade, `due_date` date nullable, timestamps, `unique(['cohort_id','validation_test_case_id'])`.
9. `..._09_create_daily_test_sessions_table.php`
```php
$table->foreignId('cohort_member_id')->constrained('cohort_members')->cascadeOnDelete();
$table->foreignId('validation_product_id')->constrained('validation_products')->restrictOnDelete();
$table->foreignId('validation_module_id')->constrained('validation_modules')->restrictOnDelete();
$table->foreignId('validation_workflow_id')->constrained('validation_workflows')->restrictOnDelete();
$table->string('facility_context')->nullable();
$table->date('date');
$table->time('start_time')->nullable(); $table->time('end_time')->nullable();
$table->unsignedInteger('tasks_completed')->default(0);
$table->json('screenshots')->nullable();
$table->text('comments')->nullable();
$table->timestamps();
```
10. `..._10_create_issue_reports_table.php`
```php
$table->foreignId('cohort_member_id')->constrained('cohort_members')->cascadeOnDelete();
$table->foreignId('daily_test_session_id')->nullable()->constrained('daily_test_sessions')->nullOnDelete();
$table->foreignId('validation_product_id')->constrained('validation_products')->restrictOnDelete();
$table->foreignId('validation_module_id')->constrained('validation_modules')->restrictOnDelete();
$table->foreignId('validation_workflow_id')->constrained('validation_workflows')->restrictOnDelete();
$table->foreignId('validation_test_case_id')->nullable()->constrained('validation_test_cases')->nullOnDelete();
$table->string('title');
$table->string('issue_type', 30);  // bug|missing_feature|workflow_problem|clinical_risk|ui_ux_problem|performance_issue|security_concern|interoperability_issue|data_quality_issue|recommendation
$table->string('severity', 10);    // critical|high|medium|low
$table->text('description'); $table->text('steps_to_reproduce');
$table->text('expected_result'); $table->text('actual_result'); $table->text('clinical_impact');
$table->text('recommendation')->nullable();
$table->json('attachments')->nullable();
$table->string('status', 30)->default('submitted'); // submitted|clinical_review|product_review|accepted|rejected|duplicate|needs_more_information|sent_to_development|fixed|closed
$table->timestamps();
```
11. `..._11_create_clinical_reviews_table.php` — `issue_report_id` constrained cascade, `reviewer_id` constrained('users'), `decision` string(40) `// approved_for_product_review|rejected|needs_more_information`, `notes` nullable text, `reviewed_at` timestamp nullable, timestamps.
12. `..._12_create_product_reviews_table.php` — `issue_report_id` constrained cascade, `reviewer_id` constrained('users'), `decision` string(30) `// accepted|rejected|duplicate|sent_to_development`, `notes` nullable text, `reviewed_at` timestamp nullable, timestamps.

**Test:** `tests/Feature/ValidationMigrationsTest.php` — asserts `Schema::hasTable()` for all 11 tables + `Schema::hasColumn('practitioner_programs','program_type')`.
**Verify/commit:** `php artisan migrate` (dev MySQL), `php artisan test --filter=ValidationMigrationsTest`. Commit all 12 migrations + test.

---

### Task 2 — Catalog models + factories (Product → Module → Workflow → TestCase + CohortTestCase)

**Create models** `app/Models/`: `ValidationProduct`, `ValidationModule`, `ValidationWorkflow`, `ValidationTestCase`, `CohortTestCase`. Each `use HasFactory;`, `$fillable`, relationships:
- `ValidationProduct`: `hasMany(ValidationModule)`; `$casts['is_active'=>'boolean']`.
- `ValidationModule`: `belongsTo(ValidationProduct)`, `hasMany(ValidationWorkflow)`.
- `ValidationWorkflow`: `belongsTo(ValidationModule)`, `hasMany(ValidationTestCase)`.
- `ValidationTestCase`: `belongsTo(ValidationWorkflow)`.
- `CohortTestCase`: `belongsTo(Cohort)`, `belongsTo(ValidationTestCase)`; `$casts['due_date'=>'date']`.

Add `IssueReport::issueTypeOptions()` and `severityOptions()` static maps later (Task 6); here add `is_active` casts.

**Create factories** `database/factories/`: `ValidationProductFactory` (`code` via `fake()->unique()->slug(2)`), `ValidationModuleFactory` (`validation_product_id => ValidationProduct::factory()`), `ValidationWorkflowFactory`, `ValidationTestCaseFactory` (`title`, `steps`, `expected_result`).

**Test:** `tests/Feature/ValidationCatalogTest.php` — build full chain via factories, assert relationship traversal (`$product->modules->first()->workflows->first()->testCases` non-empty) and unique-code constraints.
**Commit:** 5 models + 4 factories + test.

---

### Task 3 — Cohort + CohortMember models/factories + PractitionerProgram/User extensions + ValidationCohortTest

**Models:**
- `app/Models/Cohort.php`: `belongsTo(PractitionerProgram)`, `hasMany(CohortMember)`, `belongsToMany(ValidationTestCase, 'cohort_test_cases')->withPivot('due_date')->withTimestamps()` (named `testCases`), `hasMany(CohortTestCase)`; `$casts` dates; `statusOptions()`.
- `app/Models/CohortMember.php`: `belongsTo(Cohort)`, `belongsTo(User)`, `hasMany(DailyTestSession)`, `hasMany(IssueReport)`; `$casts['placed_at'=>'datetime']`; `statusOptions()`.

**Modify `app/Models/PractitionerProgram.php`** (additive only): add `'program_type'` to `$fillable`; add `cohorts(): HasMany`; add `scopeValidation($q) => $q->where('program_type','validation')`; add `programTypeOptions(): array { return ['general'=>'General','validation'=>'Validation']; }`.

**Modify `app/Models/User.php`** (additive only): add `cohortMembers(): HasMany => hasMany(CohortMember::class, 'user_id')`.

**Factories:** `CohortFactory` (`practitioner_program_id => PractitionerProgram::factory()->state(['program_type'=>'validation'])`, `name`, `specialty`, `start_date`/`end_date`, `status=>'active'`), `CohortMemberFactory` (`cohort_id => Cohort::factory()`, `user_id => User::factory()`, `status=>'active'`, `placed_at => now()`).

**Test:** `tests/Feature/ValidationCohortTest.php` (subset — admin/portal placement tests come in Tasks 5/8):
- `program->cohorts()` returns cohorts; `PractitionerProgram::validation()` scope filters correctly.
- Placing same `user` in same `cohort` twice throws (unique constraint).
- `user->cohortMembers()->where('status','active')` returns active memberships.

**Commit:** 2 models + 2 model edits + 2 factories + test.

---

### Task 4 — Filament catalog resources (ValidationProductResource + 2 hidden + 3 RelationManagers)

**Files:**
- `app/Filament/Resources/ValidationProductResource.php` (+ `Pages/{List,Create,Edit}ValidationProduct.php`). Nav group `'Validation Catalog'`, icon `heroicon-o-beaker`, `canAccess()` = admin/super_admin. Table: name, code, `modules_count` (`->counts('modules')`), `is_active` badge. Form: name (required), code (required, `->unique(ignoreRecord:true)`), description textarea, is_active toggle. `getRelations()` → `ModulesRelationManager`.
- `ValidationProductResource/RelationManagers/ModulesRelationManager.php` — `$relationship='modules'`, CRUD form (name, code, description, is_active), header `CreateAction`, row `EditAction`+`DeleteAction`, plus a row `Action::make('manage_workflows')->url(fn($record)=>ValidationModuleResource::getUrl('edit',['record'=>$record]))` to descend.
- `app/Filament/Resources/ValidationModuleResource.php` — `protected static bool $shouldRegisterNavigation = false;` (hidden), `canAccess()` admin, `getRelations()` → `WorkflowsRelationManager`, `getPages()` index+edit only.
- `ValidationModuleResource/RelationManagers/WorkflowsRelationManager.php` — `$relationship='workflows'`, CRUD + row link to `ValidationWorkflowResource::getUrl('edit',...)`.
- `app/Filament/Resources/ValidationWorkflowResource.php` — hidden, `getRelations()` → `TestCasesRelationManager`, index+edit pages.
- `ValidationWorkflowResource/RelationManagers/TestCasesRelationManager.php` — `$relationship='testCases'`, CRUD form (title, description, steps, expected_result, is_active).

**Test:** `tests/Feature/ValidationCatalogAdminTest.php` — admin reaches `/admin/validation-products` (assertOk); non-admin (practitioner) `assertForbidden`/redirect; hidden resources not in nav but reachable by admin via getUrl.
**Commit:** 3 resources + 3 relation managers + pages + test.

---

### Task 5 — CohortResource + PlaceInCohortAction (modify PractitionerApplicationResource)

**Files:**
- `app/Filament/Resources/CohortResource.php` (+ List/Create/Edit pages). Nav group `'Validation Catalog'`, icon `heroicon-o-user-group`, admin-gated. Table: name, specialty, `practitionerProgram.title`, start_date, end_date, `members_count`, status badge. Form: `practitioner_program_id` Select `->options(PractitionerProgram::validation()->pluck('title','id'))->required()`, name, specialty, description, start_date/end_date pickers, max_members numeric nullable, status select. `getRelations()` → `MembersRelationManager`, `TestCasesRelationManager`.
- `CohortResource/RelationManagers/MembersRelationManager.php` — `$relationship='members'`, **no create**; table user.name, status badge, placed_at; row `EditAction` limited to `status` select (active/suspended/removed). Reference read-only pattern: `EnrollmentsRelationManager`.
- `CohortResource/RelationManagers/TestCasesRelationManager.php` — `$relationship='testCases'`, `->headerActions([AttachAction::make()->preloadRecordSelect()])`, `->actions([DetachAction::make()])`; columns test-case title, `validationWorkflow.name`, module, product, editable `due_date` pivot column (`Tables\Columns\TextInputColumn` or pivot-aware `due_date`).
- **Modify** `app/Filament/Resources/PractitionerApplicationResource.php` (`->actions([...])` at line 111): add
```php
\App\Filament\Resources\PractitionerApplicationResource\Actions\PlaceInCohortAction::make(),
```
- `app/Filament/Resources/PractitionerApplicationResource/Actions/PlaceInCohortAction.php`:
```php
public static function make(?string $name = null): \Filament\Tables\Actions\Action
{
    return \Filament\Tables\Actions\Action::make('place_in_cohort')
        ->label('Place in Cohort')->icon('heroicon-o-user-plus')->color('primary')
        ->visible(fn (PractitionerApplication $r) =>
            $r->status === 'approved'
            && optional($r->program)->program_type === 'validation'
            && ! $r->practitioner->cohortMembers()
                 ->whereHas('cohort', fn ($q) => $q->where('practitioner_program_id', $r->program_id))
                 ->exists())
        ->form([
            \Filament\Forms\Components\Select::make('cohort_id')->label('Cohort')->required()
                ->options(fn (PractitionerApplication $r) =>
                    \App\Models\Cohort::where('practitioner_program_id', $r->program_id)
                        ->where('status', 'active')->pluck('name', 'id')),
        ])
        ->action(function (PractitionerApplication $r, array $data) {
            \App\Models\CohortMember::create([
                'cohort_id' => $data['cohort_id'], 'user_id' => $r->practitioner_id,
                'status' => 'active', 'placed_at' => now(),
            ]);
            \Filament\Notifications\Notification::make()->title('Practitioner placed in cohort.')->success()->send();
        });
}
```
> Note: `PractitionerApplication` uses `practitioner()` (FK `practitioner_id`) and `program()` (FK `program_id`) — confirmed in `app/Models/PractitionerApplication.php`.

**Test:** extend `tests/Feature/ValidationCohortTest.php`:
- Admin creates cohort under validation program (Livewire/CreateRecord or direct model + assertDatabaseHas).
- PlaceInCohortAction creates `CohortMember` (instantiate action's `->action` closure logic via a Livewire table test, or test the underlying create + visibility predicate directly).
- Action hidden when program is `general`.
**Commit:** resource + 2 relation managers + action + PractitionerApplicationResource edit + test.

---

### Task 6 — Session + Issue models/factories (DailyTestSession, IssueReport, ClinicalReview, ProductReview)

**Models** `app/Models/`:
- `DailyTestSession`: `belongsTo(CohortMember)`, `belongsTo(ValidationProduct/Module/Workflow)`; `$casts['date'=>'date','screenshots'=>'array']`.
- `IssueReport`: `belongsTo(CohortMember)`, `belongsTo(DailyTestSession)`, product/module/workflow/testCase belongsTo, `hasOne(ClinicalReview)`, `hasOne(ProductReview)`; `$casts['attachments'=>'array']`; statics `issueTypeOptions()`, `severityOptions()`, `statusOptions()`.
- `ClinicalReview`: `belongsTo(IssueReport)`, `belongsTo(User,'reviewer_id')` as `reviewer`; `$casts['reviewed_at'=>'datetime']`; `decisionOptions()`.
- `ProductReview`: same shape; `decisionOptions()`.

**Factories:** `DailyTestSessionFactory`, `IssueReportFactory` (all 14 fields, `status='submitted'`), `ClinicalReviewFactory` (`decision='approved_for_product_review'`, `reviewed_at=now()`), `ProductReviewFactory` (`decision='accepted'`). FKs via related factories (e.g. `cohort_member_id => CohortMember::factory()`).

**Test:** `tests/Feature/IssueReportModelTest.php` — factory builds full graph; `$issue->clinicalReview`/`productReview` resolve; option maps contain expected keys (10 issue types, 4 severities, 10 statuses).
**Commit:** 4 models + 4 factories + test.

---

### Task 7 — Portal foundation (routes + DashboardController + nav link + dashboard view)

**Modify `routes/web.php`** — inside practitioner group (after Bug Reports block, before Courses, ~line 207) add the spec's nested group:
```php
// Validation Hub
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
Route names resolve as `practitioner.validation.*`.

**`app/Http/Controllers/Practitioner/Validation/DashboardController.php`:**
```php
public function show()
{
    $member = auth()->user()->cohortMembers()->where('status','active')
        ->with('cohort.practitionerProgram')->latest('placed_at')->first();
    $stats = $member ? [
        'sessions' => $member->dailyTestSessions()->count(),
        'issues'   => $member->issueReports()->count(),
        'open'     => $member->issueReports()->whereNotIn('status',['closed','rejected','duplicate'])->count(),
        'closed'   => $member->issueReports()->where('status','closed')->count(),
    ] : null;
    return view('practitioner.validation.dashboard', ['cohortMember' => $member, 'stats' => $stats]);
}
```

**Nav link** — `resources/views/components/layouts/practitioner.blade.php`, insert after Bug Reports link, gated on active membership:
```blade
@if(auth()->user()?->cohortMembers()->where('status','active')->exists())
<a href="{{ route('practitioner.validation.dashboard', ['locale' => app()->getLocale()]) }}"
   class="flex items-center gap-1.5 px-3 py-1.5 rounded text-sm no-underline transition-colors {{ request()->routeIs('practitioner.validation.*') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
    <i data-lucide="clipboard-check" style="width:16px;height:16px"></i> Validation Hub
</a>
@endif
```

**View** `resources/views/practitioner/validation/dashboard.blade.php` — cohort card (name, specialty, dates) + 4 stat cards; "not yet placed" empty state when `$cohortMember === null`.

**Test:** part of `tests/Feature/DailyTestSessionTest.php` (Task 8) — placed practitioner sees dashboard (assertOk + cohort name); unplaced practitioner sees "not yet placed" copy.
**Commit:** routes edit + controller + layout edit + view.

---

### Task 8 — SessionController + views + DailyTestSessionTest

**`app/Http/Controllers/Practitioner/Validation/SessionController.php`:**
- Private helper `activeMember()` → active `CohortMember` or `null`.
- Private helper `scopedCatalog($member)` returns `[$products,$modules,$workflows,$allowedWorkflowIds]` using the spec's join:
```php
$allowedWorkflowIds = \App\Models\CohortTestCase::where('cohort_id', $member->cohort_id)
    ->join('validation_test_cases','cohort_test_cases.validation_test_case_id','=','validation_test_cases.id')
    ->pluck('validation_test_cases.validation_workflow_id');
$workflows = ValidationWorkflow::whereIn('id',$allowedWorkflowIds)->get();
$modules   = ValidationModule::whereIn('id',$workflows->pluck('validation_module_id')->unique())->get();
$products  = ValidationProduct::whereIn('id',$modules->pluck('validation_product_id')->unique())->get();
```
- `index()`: redirect to dashboard if no member; paginate `$member->dailyTestSessions()->latest()`.
- `create()`: redirect to dashboard w/ notice if no member; pass scoped `$products/$modules/$workflows`.
- `store(Request $request)`: validate per spec (`validation_product_id|module_id|workflow_id` exists, `facility_context` nullable ≤200, `date` required before_or_equal:today, times H:i, `tasks_completed` 0–999, `comments` ≤3000, `screenshots.*` file mimes jpg,jpeg,png,pdf max 5120). Re-derive `$allowedWorkflowIds`, then `abort_unless(in_array($validated['validation_workflow_id'], $allowedWorkflowIds->map(fn($i)=>(string)$i)->toArray()) || in_array($validated['validation_workflow_id'], $allowedWorkflowIds->toArray()), 422, 'Workflow not in cohort scope.');` Store files via `->store('validation/sessions','public')` into JSON array. Create `DailyTestSession` with `cohort_member_id => $member->id`. Redirect to `validation.sessions.index` with success.

**Views:** `resources/views/practitioner/validation/sessions/index.blade.php` (paginated table + "Start New Session" button), `.../sessions/create.blade.php` (product/module/workflow selects, date, start/end time, tasks_completed, comments, multi-file screenshot upload; `enctype="multipart/form-data"`; locale-aware action).

**Test:** `tests/Feature/DailyTestSessionTest.php`:
- Placed practitioner POSTs valid session → `assertRedirect` + `assertDatabaseHas('daily_test_sessions', ['cohort_member_id'=>...])`.
- Session with workflow outside cohort scope → 422.
- Non-practitioner role → `assertForbidden` on `/en/practitioner/validation/sessions`.
- Unplaced practitioner → redirect from create.
**Commit:** controller + 2 views + test.

---

### Task 9 — IssueReportController + views + IssueReportTest

**`app/Http/Controllers/Practitioner/Validation/IssueReportController.php`:**
- Reuse `activeMember()`/`scopedCatalog()` (extract to a trait `app/Http/Controllers/Practitioner/Validation/ResolvesCohortScope.php` shared by both controllers — keeps DRY).
- `index()`: paginate `$member->issueReports()->latest()`.
- `create()`: scoped catalog + cohort `ValidationTestCase` list (`ValidationTestCase::whereIn('validation_workflow_id',$allowedWorkflowIds)->get()`).
- `store(Request $request)`: validate all 14 fields per spec (issue_type `in:bug,...,recommendation`; severity `in:critical,high,medium,low`; lengths per spec; `attachments.*` mimes jpg,jpeg,png,pdf max 10240). Store attachments → JSON. Create `IssueReport` with `cohort_member_id => $member->id`, `status='submitted'`. Redirect to `validation.issues.index` with success.
- `show($locale, IssueReport $issue)`: `$issue->load('clinicalReview','productReview','cohortMember')`; `abort_unless($issue->cohortMember->user_id === auth()->id(), 403)`; pass latest review note.

**Views:** `.../issues/index.blade.php` (table + severity/status badges + "Report New Issue"), `.../issues/create.blade.php` (two-column 14-field form: left classification = product/module/workflow/test_case/issue_type/severity; right detail = title/description/steps/expected/actual/clinical_impact/recommendation/attachments), `.../issues/show.blade.php` (all fields, status timeline, latest review note).

**Test:** `tests/Feature/IssueReportTest.php`:
- Placed practitioner submits 14-field issue → redirect + `assertDatabaseHas('issue_reports', ['status'=>'submitted'])`.
- Missing `severity` → 422; invalid `issue_type` → 422.
- Owner GET `/en/practitioner/validation/issues/{id}` → assertOk.
- Different practitioner → assertForbidden (403).
**Commit:** controller + trait + 3 views + test.

---

### Task 10 — Filament IssueReportResource + DailyTestSessionResource + IssueReportTriageTest

**`app/Filament/Resources/IssueReportResource.php`** (+ List/View pages, **no create**). Nav group `'Validation Hub'`, icon `heroicon-o-exclamation-circle`, admin-gated. Table columns: title, severity badge (critical=red/high=orange/medium=yellow/low=blue), issue_type, `cohortMember.cohort.name`, `validationProduct.name`, status badge, created_at. Status filter tabs via `getTabs()` on List page: All | Submitted | Clinical Review | Product Review | Accepted | Sent to Development | Closed. **Four table actions:**
1. `start_clinical_review` — visible `status==='submitted'`; form `decision` select (approved_for_product_review/rejected/needs_more_information) + `notes`. Action creates `ClinicalReview` (reviewer_id=auth id, reviewed_at=now) then sets `issue.status`: approved→`clinical_review`, rejected→`rejected`, needs_more_information→`needs_more_information`.
2. `send_to_product_review` — visible `status==='clinical_review'` && latest clinicalReview decision `approved_for_product_review`; no modal; set `status='product_review'`.
3. `product_decision` — visible `status==='product_review'`; form `decision` (accepted/rejected/duplicate/sent_to_development) + `notes`; create `ProductReview`; set `status` = decision value.
4. `close` — visible `status` in [accepted,rejected,duplicate]; set `status='closed'`.

**`app/Filament/Resources/DailyTestSessionResource.php`** (+ List/View pages, read-only). Nav group `'Validation Hub'`, icon `heroicon-o-calendar`, admin-gated. Table: date, `cohortMember.user.name`, cohort name, product/module/workflow, tasks_completed, `issue_reports_count`. View page: all fields + screenshot image previews + linked issues.

**Test:** `tests/Feature/IssueReportTriageTest.php` — drive each transition at model level + action closures:
- start clinical review: submitted → clinical_review (and ClinicalReview row created).
- approved clinical review enables product review; send → product_review.
- product decision sets status (sent_to_development, rejected, etc.) + ProductReview row.
- close sets closed.
- non-admin cannot access resource (`canAccess()` false / `/admin/issue-reports` forbidden).
**Commit:** 2 resources + pages + test.

---

### Task 11 — ValidationDataSeeder (1 product, 10 modules, 56 workflows, 56 test cases)

**`database/seeders/ValidationDataSeeder.php`** — `run()` creates `ValidationProduct` (OPES Health OS, code `ohos`) then loops the Appendix B map (10 modules → 56 workflows), creating one `ValidationTestCase` per workflow (title = Title Case of workflow code, steps/expected null). Idempotent via `firstOrCreate` on codes. **Not** added to `DatabaseSeeder`.

**Test:** `tests/Feature/ValidationDataSeederTest.php` — `$this->seed(ValidationDataSeeder::class)` then assert counts: `assertDatabaseCount('validation_products',1)`, `('validation_modules',10)`, `('validation_workflows',56)`, `('validation_test_cases',56)`.
**Verify:** `php artisan db:seed --class=ValidationDataSeeder` runs clean on dev DB.
**Commit:** seeder + test.

---

## Final verification

1. **Full suite green:** `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test` — expect 330 prior + ~20 new, 0 failures.
2. **Migrations clean:** `php artisan migrate:fresh` on dev MySQL (NOT test) then `php artisan db:seed --class=ValidationDataSeeder`.
3. **Manual smoke (preview/dev server):**
   - As admin: create a validation `PractitionerProgram` (program_type=validation) → create a Cohort → attach test cases → approve a practitioner application → "Place in Cohort".
   - As that practitioner: Validation Hub nav appears → dashboard shows cohort → submit a daily session (only cohort-scoped products/workflows selectable) → submit a 14-field issue.
   - Back as admin: IssueReportResource "Submitted" tab → Start Clinical Review (approve) → Send to Product Review → Product Decision (sent_to_development) → Close. Confirm practitioner sees status + latest note on issue show page.
4. **Authorization spot-checks:** non-practitioner → 403 on `/en/practitioner/validation/*`; non-admin → 403 on `/admin/issue-reports`, `/admin/cohorts`, `/admin/validation-products`; one practitioner cannot open another's issue (403).

## Post-approval housekeeping
- Copy this plan to `docs/superpowers/plans/2026-06-19-clinical-validation-hub.md` (writing-plans convention) before execution.
- Execute via `superpowers:subagent-driven-development` (fresh subagent per task + spec/quality review), or inline via `superpowers:executing-plans`.
