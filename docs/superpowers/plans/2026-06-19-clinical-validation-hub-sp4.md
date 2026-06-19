# Clinical Validation Hub — Sub-project 4 Implementation Plan (Certification + Advisory Council)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Compute a certification score from each member's SP3 final evaluation, let an admin issue a downloadable PDF certificate to qualifying practitioners, and invite top-tier certified practitioners onto a Clinical Validation Advisory Council.

**Architecture:** A stateless `App\Support\CertificationScore` service computes score+tier from a `FinalEvaluation`; the score/tier are frozen onto the `ValidationCertificate` at issuance (credential immutability). Admins issue/invite via Filament; practitioners view+download certificates and see a council badge in the existing portal certificates page. Purely additive: 2 tables, 2 additive `User` relationships, no existing table changes.

**Tech Stack:** Laravel 13.8 / PHP 8.3 / Filament v3 / Blade + Tailwind / `barryvdh/laravel-dompdf` / SQLite (tests) / MySQL (prod). PHP binary: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`. Test cmd: `<php> artisan test --filter=<Class>`. Spec: `docs/superpowers/specs/2026-06-19-clinical-validation-hub-sp4-design.md`. Baseline: 407 tests green. **Final sub-project — completes the Validation Hub.**

---

## Conventions (verified)
- **Migrations:** anonymous class; string-enum + inline `// a|b|c`; FK `->constrained('t')->cascadeOnDelete()` / `->nullable()->constrained()->nullOnDelete()`. **Unique constraints: ALWAYS standalone `$table->unique('col')`** — inline `->unique()` chained after `->constrained()` is silently dropped by SQLite (SP3 lesson).
- **Models:** `use HasFactory;`, `$fillable`, `$casts`, fully-qualified relation return types, static `xOptions()`. Auto-number via `boot()` — copy `app/Models/CourseCertificate.php`.
- **Filament resource:** `canAccess()` admin gate; `form()/table()/infolist()/getPages()`; row actions `Tables\Actions\Action::make()->visible()->form()->action()`; `Notification::make()->...->send()`.
- **dompdf:** `use Barryvdh\DomPDF\Facade\Pdf;` then `Pdf::loadView('view', [...])->setPaper('a4','landscape')->download('name.pdf')` (ref `app/Http/Controllers/CertificatePdfController.php`).
- **Practitioner controller (locale-aware):** model-bound method takes `$locale` first. Redirects/links use `['locale' => app()->getLocale()]`.
- **Tests:** `Tests\Feature`, `use RefreshDatabase;`, `setUp()` → `parent::setUp(); app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions(); $this->seed(\Database\Seeders\RolePermissionSeeder::class);`. Role user via `User::factory()->create()` + `->assignRole(...)`. Portal URLs `/en/practitioner/...`.
- **Git:** PowerShell-safe `-m`; stage explicit paths only; never stage untracked docs; never `taskkill`; don't touch MySQL/Apache.

## Existing code relied on
- `FinalEvaluation` (SP3): `rating` (outstanding/strong/satisfactory/needs_improvement), `metrics` (array cast: sessions/issues_found/issues_accepted/retests/member_name/cohort_name/as_of), `cohort_member_id`, `cohortMember()`, `ratingOptions()`; resource at `app/Filament/Resources/FinalEvaluationResource.php` with `table()->actions([ViewAction, EditAction])` and an `infolist()` (Evaluation + Frozen Contribution sections).
- `CohortMember`: `user()`, `cohort()`, `user_id`.
- `CourseCertificate`: auto-number boot pattern (`CERT-YYYY-NNNNNN`).
- `CertificateController` (practitioner): `index()` loads `courseCertificates`; view `resources/views/practitioner/certificates/index.blade.php` links `route('certificates.pdf', ...)`.
- Practitioner route group in `routes/web.php` (~line 226: `practitioner.certificates`).

## File map
| File | Task |
|---|---|
| `database/migrations/2026_06_19_430001_create_validation_certificates_table.php`, `..._430002_create_advisory_council_members_table.php` | 1 |
| `app/Models/ValidationCertificate.php`, `app/Models/AdvisoryCouncilMember.php`, `app/Models/User.php` (additive) | 2 |
| `database/factories/ValidationCertificateFactory.php`, `AdvisoryCouncilMemberFactory.php` | 2 |
| `app/Support/CertificationScore.php`, `ValidationCertificate::issueFor` | 3 |
| `app/Filament/Resources/ValidationCertificateResource.php` (+Pages), `AdvisoryCouncilMemberResource.php` (+Pages), `FinalEvaluationResource.php` (Issue action + Certification infolist) | 4 |
| `resources/views/pdf/validation-certificate.blade.php`, `routes/web.php`, `app/Http/Controllers/Practitioner/CertificateController.php`, `resources/views/practitioner/certificates/index.blade.php` | 5 |

---

## Task 1: Migrations

**Files:** Create the two migrations; Test `tests/Feature/ValidationCertificationMigrationsTest.php`.

- [ ] **Step 1: Write the failing test**
```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ValidationCertificationMigrationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sp4_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('validation_certificates'));
        $this->assertTrue(Schema::hasTable('advisory_council_members'));
        foreach (['cohort_member_id', 'final_evaluation_id', 'certificate_number', 'score', 'tier', 'issued_by', 'issued_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('validation_certificates', $c), "validation_certificates.$c");
        }
        foreach (['user_id', 'validation_certificate_id', 'title', 'term_start', 'term_end', 'status', 'invited_by', 'invited_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('advisory_council_members', $c), "advisory_council_members.$c");
        }
    }
}
```

- [ ] **Step 2: Run — expect FAIL.** `<php> artisan test --filter=ValidationCertificationMigrationsTest`

- [ ] **Step 3: `database/migrations/2026_06_19_430001_create_validation_certificates_table.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validation_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_member_id')->constrained('cohort_members')->cascadeOnDelete();
            $table->foreignId('final_evaluation_id')->nullable()->constrained('final_evaluations')->nullOnDelete();
            $table->string('certificate_number');
            $table->unsignedInteger('score');
            $table->string('tier', 20); // distinction|pass
            $table->foreignId('issued_by')->constrained('users');
            $table->timestamp('issued_at');
            $table->timestamps();
            $table->unique('cohort_member_id');
            $table->unique('certificate_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validation_certificates');
    }
};
```

- [ ] **Step 4: `database/migrations/2026_06_19_430002_create_advisory_council_members_table.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advisory_council_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('validation_certificate_id')->nullable()->constrained('validation_certificates')->nullOnDelete();
            $table->string('title');
            $table->date('term_start');
            $table->date('term_end')->nullable();
            $table->string('status', 20)->default('active'); // active|inactive
            $table->foreignId('invited_by')->constrained('users');
            $table->timestamp('invited_at');
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advisory_council_members');
    }
};
```

- [ ] **Step 5: Run — expect PASS.** Full suite (`<php> artisan test`) — 0 failures (~408).

- [ ] **Step 6: Commit**
```bash
git add database/migrations/2026_06_19_430001_create_validation_certificates_table.php database/migrations/2026_06_19_430002_create_advisory_council_members_table.php tests/Feature/ValidationCertificationMigrationsTest.php
git commit -m "feat(validation): add validation_certificates and advisory_council_members tables (SP4)"
```

---

## Task 2: Models + factories + User relationships

**Files:** Create `app/Models/ValidationCertificate.php`, `app/Models/AdvisoryCouncilMember.php`, `database/factories/ValidationCertificateFactory.php`, `AdvisoryCouncilMemberFactory.php`; Modify `app/Models/User.php`; Test `tests/Feature/ValidationCertificationModelTest.php`.

- [ ] **Step 1: Write the failing test**
```php
<?php

namespace Tests\Feature;

use App\Models\AdvisoryCouncilMember;
use App\Models\CohortMember;
use App\Models\User;
use App\Models\ValidationCertificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationCertificationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_certificate_auto_numbers_and_relationships(): void
    {
        $cert = ValidationCertificate::factory()->create();
        $this->assertStringStartsWith('VCERT-'.date('Y').'-', $cert->certificate_number);
        $this->assertInstanceOf(CohortMember::class, $cert->cohortMember);
        $this->assertNotNull($cert->issuedBy);
    }

    public function test_user_relationships(): void
    {
        $user   = User::factory()->create();
        $member = CohortMember::factory()->create(['user_id' => $user->id]);
        $cert   = ValidationCertificate::factory()->create(['cohort_member_id' => $member->id]);
        AdvisoryCouncilMember::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->validationCertificates->contains($cert));
        $this->assertNotNull($user->advisoryCouncilMembership);
    }

    public function test_option_maps(): void
    {
        $this->assertArrayHasKey('active', AdvisoryCouncilMember::statusOptions());
        $this->assertArrayHasKey('distinction', ValidationCertificate::tierBadgeColors());
        $this->assertArrayHasKey('pass', ValidationCertificate::tierBadgeColors());
    }
}
```

- [ ] **Step 2: Run — expect FAIL** (`--filter=ValidationCertificationModelTest`).

- [ ] **Step 3: `app/Models/ValidationCertificate.php`**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_member_id', 'final_evaluation_id', 'certificate_number',
        'score', 'tier', 'issued_by', 'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'score'     => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (ValidationCertificate $model) {
            if (! $model->certificate_number) {
                $year = date('Y');
                $last = static::where('certificate_number', 'like', "VCERT-{$year}-%")->max('certificate_number');
                $next = $last ? ((int) substr($last, -6)) + 1 : 1;
                $model->certificate_number = 'VCERT-' . $year . '-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
            }
            if (! $model->issued_at) {
                $model->issued_at = now();
            }
        });
    }

    public function cohortMember(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CohortMember::class);
    }

    public function finalEvaluation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FinalEvaluation::class);
    }

    public function issuedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function advisoryCouncilMember(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AdvisoryCouncilMember::class);
    }

    public static function tierBadgeColors(): array
    {
        return ['distinction' => 'success', 'pass' => 'info'];
    }
}
```

- [ ] **Step 4: `app/Models/AdvisoryCouncilMember.php`**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvisoryCouncilMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'validation_certificate_id', 'title',
        'term_start', 'term_end', 'status', 'invited_by', 'invited_at',
    ];

    protected $casts = [
        'term_start' => 'date',
        'term_end'   => 'date',
        'invited_at' => 'datetime',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function validationCertificate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ValidationCertificate::class);
    }

    public function invitedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public static function statusOptions(): array
    {
        return ['active' => 'Active', 'inactive' => 'Inactive'];
    }
}
```

- [ ] **Step 5: Add additive relationships to `app/Models/User.php`** (place near other practitioner relationships):
```php
    public function validationCertificates(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            \App\Models\ValidationCertificate::class,
            \App\Models\CohortMember::class,
            'user_id',          // CohortMember.user_id
            'cohort_member_id', // ValidationCertificate.cohort_member_id
            'id',
            'id'
        );
    }

    public function advisoryCouncilMembership(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\AdvisoryCouncilMember::class);
    }
```

- [ ] **Step 6: `database/factories/ValidationCertificateFactory.php`**
```php
<?php

namespace Database\Factories;

use App\Models\CohortMember;
use App\Models\User;
use App\Models\ValidationCertificate;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ValidationCertificate> */
class ValidationCertificateFactory extends Factory
{
    protected $model = ValidationCertificate::class;

    public function definition(): array
    {
        return [
            'cohort_member_id'    => CohortMember::factory(),
            'final_evaluation_id' => null,
            'score'               => 75,
            'tier'                => 'pass',
            'issued_by'           => User::factory(),
            'issued_at'           => now(),
        ];
    }
}
```

- [ ] **Step 7: `database/factories/AdvisoryCouncilMemberFactory.php`**
```php
<?php

namespace Database\Factories;

use App\Models\AdvisoryCouncilMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AdvisoryCouncilMember> */
class AdvisoryCouncilMemberFactory extends Factory
{
    protected $model = AdvisoryCouncilMember::class;

    public function definition(): array
    {
        return [
            'user_id'                   => User::factory(),
            'validation_certificate_id' => null,
            'title'                     => 'Clinical Validation Advisor',
            'term_start'                => now()->toDateString(),
            'term_end'                  => null,
            'status'                    => 'active',
            'invited_by'                => User::factory(),
            'invited_at'                => now(),
        ];
    }
}
```

- [ ] **Step 8: Run — expect PASS** (`--filter=ValidationCertificationModelTest`). Full suite — 0 failures (~411).

- [ ] **Step 9: Commit**
```bash
git add app/Models/ValidationCertificate.php app/Models/AdvisoryCouncilMember.php app/Models/User.php database/factories/ValidationCertificateFactory.php database/factories/AdvisoryCouncilMemberFactory.php tests/Feature/ValidationCertificationModelTest.php
git commit -m "feat(validation): add ValidationCertificate and AdvisoryCouncilMember models"
```

---

## Task 3: CertificationScore service + issueFor

**Files:** Create `app/Support/CertificationScore.php`; Modify `app/Models/ValidationCertificate.php` (add `issueFor`); Test `tests/Feature/CertificationScoreTest.php`, `tests/Feature/ValidationCertificateTest.php`.

- [ ] **Step 1: Write the failing tests**

`tests/Feature/CertificationScoreTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\FinalEvaluation;
use App\Support\CertificationScore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificationScoreTest extends TestCase
{
    use RefreshDatabase;

    private function score(FinalEvaluation $e): array
    {
        return app(CertificationScore::class)->for($e);
    }

    public function test_outstanding_with_high_contribution_is_distinction(): void
    {
        $e = FinalEvaluation::factory()->create([
            'rating'  => 'outstanding',
            'metrics' => ['issues_accepted' => 8, 'sessions' => 10, 'retests' => 4],
        ]);
        $r = $this->score($e);
        $this->assertEquals(100, $r['score']); // 50 + min(50, 40+10+8)
        $this->assertEquals('distinction', $r['tier']);
    }

    public function test_strong_with_modest_contribution_is_pass_at_boundary(): void
    {
        $e = FinalEvaluation::factory()->create([
            'rating'  => 'strong', // 38
            'metrics' => ['issues_accepted' => 4, 'sessions' => 2, 'retests' => 0], // 20+2 = 22
        ]);
        $r = $this->score($e);
        $this->assertEquals(60, $r['score']);
        $this->assertEquals('pass', $r['tier']);
    }

    public function test_needs_improvement_is_not_certified(): void
    {
        $e = FinalEvaluation::factory()->create([
            'rating'  => 'needs_improvement', // 10
            'metrics' => ['issues_accepted' => 0, 'sessions' => 1, 'retests' => 0], // 1
        ]);
        $r = $this->score($e);
        $this->assertEquals(11, $r['score']);
        $this->assertEquals('not_certified', $r['tier']);
    }

    public function test_contribution_is_capped_at_fifty(): void
    {
        $e = FinalEvaluation::factory()->create([
            'rating'  => 'satisfactory', // 25
            'metrics' => ['issues_accepted' => 100, 'sessions' => 100, 'retests' => 100],
        ]);
        $r = $this->score($e);
        $this->assertEquals(75, $r['score']); // 25 + 50 (capped)
    }

    public function test_tier_options(): void
    {
        $this->assertCount(3, CertificationScore::tierOptions());
    }
}
```

`tests/Feature/ValidationCertificateTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\FinalEvaluation;
use App\Models\User;
use App\Models\ValidationCertificate;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ValidationCertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_for_freezes_score_and_tier(): void
    {
        $admin = User::factory()->create();
        $eval  = FinalEvaluation::factory()->create([
            'rating'  => 'outstanding',
            'metrics' => ['issues_accepted' => 8, 'sessions' => 10, 'retests' => 4],
        ]);

        $cert = ValidationCertificate::issueFor($eval, $admin->id);

        $this->assertEquals(100, $cert->score);
        $this->assertEquals('distinction', $cert->tier);
        $this->assertEquals($eval->cohort_member_id, $cert->cohort_member_id);
        $this->assertEquals($admin->id, $cert->issued_by);
        $this->assertStringStartsWith('VCERT-', $cert->certificate_number);
    }

    public function test_issue_for_rejects_not_certified(): void
    {
        $admin = User::factory()->create();
        $eval  = FinalEvaluation::factory()->create([
            'rating'  => 'needs_improvement',
            'metrics' => ['issues_accepted' => 0, 'sessions' => 0, 'retests' => 0],
        ]);

        $this->expectException(HttpException::class);
        ValidationCertificate::issueFor($eval, $admin->id);
        $this->assertDatabaseCount('validation_certificates', 0);
    }

    public function test_unique_per_member(): void
    {
        $admin = User::factory()->create();
        $eval  = FinalEvaluation::factory()->create(['rating' => 'strong', 'metrics' => ['issues_accepted' => 5, 'sessions' => 5, 'retests' => 2]]);

        ValidationCertificate::issueFor($eval, $admin->id);

        $this->expectException(QueryException::class);
        ValidationCertificate::factory()->create(['cohort_member_id' => $eval->cohort_member_id]);
    }
}
```

- [ ] **Step 2: Run — expect FAIL** (`--filter=CertificationScoreTest` then `--filter=ValidationCertificateTest`).

- [ ] **Step 3: Create `app/Support/CertificationScore.php`**
```php
<?php

namespace App\Support;

use App\Models\FinalEvaluation;

class CertificationScore
{
    public const PASS_THRESHOLD = 60;
    public const DISTINCTION_THRESHOLD = 85;

    /** @return array{score:int, tier:string, breakdown:array} */
    public function for(FinalEvaluation $evaluation): array
    {
        $ratingPoints = match ($evaluation->rating) {
            'outstanding'       => 50,
            'strong'            => 38,
            'satisfactory'      => 25,
            'needs_improvement' => 10,
            default             => 0,
        };

        $m = $evaluation->metrics ?? [];
        $contribution = min(50,
            ((int) ($m['issues_accepted'] ?? 0)) * 5
            + ((int) ($m['sessions'] ?? 0)) * 1
            + ((int) ($m['retests'] ?? 0)) * 2
        );

        $score = $ratingPoints + $contribution;

        $tier = match (true) {
            $score >= self::DISTINCTION_THRESHOLD => 'distinction',
            $score >= self::PASS_THRESHOLD        => 'pass',
            default                               => 'not_certified',
        };

        return [
            'score'     => $score,
            'tier'      => $tier,
            'breakdown' => ['rating' => $ratingPoints, 'contribution' => $contribution],
        ];
    }

    public static function tierOptions(): array
    {
        return [
            'distinction'   => 'Distinction',
            'pass'          => 'Pass',
            'not_certified' => 'Not Certified',
        ];
    }
}
```

- [ ] **Step 4: Add `issueFor` to `app/Models/ValidationCertificate.php`** (after `tierBadgeColors()`):
```php
    public static function issueFor(FinalEvaluation $evaluation, int $issuedById): self
    {
        $result = app(\App\Support\CertificationScore::class)->for($evaluation);

        abort_if($result['tier'] === 'not_certified', 422, 'Member is not eligible for certification.');

        return static::create([
            'cohort_member_id'    => $evaluation->cohort_member_id,
            'final_evaluation_id' => $evaluation->id,
            'score'               => $result['score'],
            'tier'                => $result['tier'],
            'issued_by'           => $issuedById,
            'issued_at'           => now(),
        ]);
    }
```

- [ ] **Step 5: Run — expect PASS** (both filters). Full suite — 0 failures (~419).

- [ ] **Step 6: Commit**
```bash
git add app/Support/CertificationScore.php app/Models/ValidationCertificate.php tests/Feature/CertificationScoreTest.php tests/Feature/ValidationCertificateTest.php
git commit -m "feat(validation): add CertificationScore service and certificate issuance"
```

---

## Task 4: Filament resources + FinalEvaluation Issue action

**Files:** Create `app/Filament/Resources/ValidationCertificateResource.php` (+Pages List/View), `app/Filament/Resources/AdvisoryCouncilMemberResource.php` (+Pages List/Edit/View); Modify `app/Filament/Resources/FinalEvaluationResource.php`; Test `tests/Feature/CertificationAdminTest.php`.

- [ ] **Step 1: Write the failing test**
```php
<?php

namespace Tests\Feature;

use App\Filament\Resources\AdvisoryCouncilMemberResource;
use App\Filament\Resources\ValidationCertificateResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CertificationAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_resources_admin_gated_and_not_creatable(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $this->assertTrue(ValidationCertificateResource::canAccess());
        $this->assertTrue(AdvisoryCouncilMemberResource::canAccess());
        $this->assertFalse(ValidationCertificateResource::canCreate());
        $this->assertFalse(AdvisoryCouncilMemberResource::canCreate());

        foreach (['practitioner', 'support'] as $role) {
            $u = User::factory()->create();
            $u->assignRole($role);
            $this->actingAs($u);
            $this->assertFalse(ValidationCertificateResource::canAccess(), $role);
            $this->assertFalse(AdvisoryCouncilMemberResource::canAccess(), $role);
        }
    }
}
```

- [ ] **Step 2: Run — expect FAIL** (`--filter=CertificationAdminTest`).

- [ ] **Step 3: Create `app/Filament/Resources/ValidationCertificateResource.php`**
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ValidationCertificateResource\Pages;
use App\Models\AdvisoryCouncilMember;
use App\Models\ValidationCertificate;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ValidationCertificateResource extends Resource
{
    protected static ?string $model = ValidationCertificate::class;
    protected static ?string $navigationIcon  = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 16;

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
                Tables\Columns\TextColumn::make('cohortMember.user.name')->label('Member')->searchable(),
                Tables\Columns\TextColumn::make('cohortMember.cohort.name')->label('Cohort'),
                Tables\Columns\TextColumn::make('certificate_number')->label('Number')->searchable(),
                Tables\Columns\TextColumn::make('score'),
                Tables\Columns\TextColumn::make('tier')->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => ValidationCertificate::tierBadgeColors()[$state] ?? 'gray'),
                Tables\Columns\TextColumn::make('issuedBy.name')->label('Issued by')->placeholder('—'),
                Tables\Columns\TextColumn::make('issued_at')->dateTime(),
            ])
            ->defaultSort('issued_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('download_pdf')
                    ->label('Download PDF')->icon('heroicon-o-arrow-down-tray')->color('gray')
                    ->action(function (ValidationCertificate $record) {
                        $record->load('cohortMember.user', 'cohortMember.cohort');
                        return \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.validation-certificate', ['certificate' => $record])
                            ->setPaper('a4', 'landscape')
                            ->download($record->certificate_number . '.pdf');
                    }),
                Tables\Actions\Action::make('invite_to_council')
                    ->label('Invite to Council')->icon('heroicon-o-user-plus')->color('success')
                    ->visible(fn (ValidationCertificate $r) => $r->tier === 'distinction'
                        && ! AdvisoryCouncilMember::where('user_id', $r->cohortMember->user_id)->exists())
                    ->form([
                        Forms\Components\TextInput::make('title')->default('Clinical Validation Advisor')->required(),
                        Forms\Components\DatePicker::make('term_start')->native(false)->default(now())->required(),
                        Forms\Components\DatePicker::make('term_end')->native(false),
                    ])
                    ->action(function (ValidationCertificate $r, array $data) {
                        AdvisoryCouncilMember::create([
                            'user_id'                   => $r->cohortMember->user_id,
                            'validation_certificate_id' => $r->id,
                            'title'                     => $data['title'],
                            'term_start'                => $data['term_start'],
                            'term_end'                  => $data['term_end'] ?? null,
                            'status'                    => 'active',
                            'invited_by'                => auth()->id(),
                            'invited_at'                => now(),
                        ]);
                        Notification::make()->title('Practitioner invited to the Advisory Council.')->success()->send();
                    }),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Certificate')->columns(3)->schema([
                Infolists\Components\TextEntry::make('cohortMember.user.name')->label('Member'),
                Infolists\Components\TextEntry::make('cohortMember.cohort.name')->label('Cohort'),
                Infolists\Components\TextEntry::make('certificate_number')->label('Number'),
                Infolists\Components\TextEntry::make('score'),
                Infolists\Components\TextEntry::make('tier')->badge()->formatStateUsing(fn ($s) => ucfirst($s)),
                Infolists\Components\TextEntry::make('issuedBy.name')->label('Issued by')->placeholder('—'),
                Infolists\Components\TextEntry::make('issued_at')->dateTime(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListValidationCertificates::route('/'),
            'view'  => Pages\ViewValidationCertificate::route('/{record}'),
        ];
    }
}
```

- [ ] **Step 4: Create `app/Filament/Resources/ValidationCertificateResource/Pages/ListValidationCertificates.php`**
```php
<?php

namespace App\Filament\Resources\ValidationCertificateResource\Pages;

use App\Filament\Resources\ValidationCertificateResource;
use Filament\Resources\Pages\ListRecords;

class ListValidationCertificates extends ListRecords
{
    protected static string $resource = ValidationCertificateResource::class;
}
```

- [ ] **Step 5: Create `app/Filament/Resources/ValidationCertificateResource/Pages/ViewValidationCertificate.php`**
```php
<?php

namespace App\Filament\Resources\ValidationCertificateResource\Pages;

use App\Filament\Resources\ValidationCertificateResource;
use Filament\Resources\Pages\ViewRecord;

class ViewValidationCertificate extends ViewRecord
{
    protected static string $resource = ValidationCertificateResource::class;
}
```

- [ ] **Step 6: Create `app/Filament/Resources/AdvisoryCouncilMemberResource.php`**
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvisoryCouncilMemberResource\Pages;
use App\Models\AdvisoryCouncilMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdvisoryCouncilMemberResource extends Resource
{
    protected static ?string $model = AdvisoryCouncilMember::class;
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Advisory Council';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 17;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\DatePicker::make('term_start')->native(false)->required(),
            Forms\Components\DatePicker::make('term_end')->native(false),
            Forms\Components\Select::make('status')->options(AdvisoryCouncilMember::statusOptions())->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Member')->searchable(),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('term_start')->date(),
                Tables\Columns\TextColumn::make('term_end')->date()->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => AdvisoryCouncilMember::statusOptions()[$state] ?? $state)
                    ->color(fn ($state) => $state === 'active' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('invitedBy.name')->label('Invited by')->placeholder('—'),
            ])
            ->defaultSort('invited_at', 'desc')
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Council Member')->columns(2)->schema([
                Infolists\Components\TextEntry::make('user.name')->label('Member'),
                Infolists\Components\TextEntry::make('title'),
                Infolists\Components\TextEntry::make('term_start')->date(),
                Infolists\Components\TextEntry::make('term_end')->date()->placeholder('—'),
                Infolists\Components\TextEntry::make('status')->badge(),
                Infolists\Components\TextEntry::make('invitedBy.name')->label('Invited by')->placeholder('—'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdvisoryCouncilMembers::route('/'),
            'view'  => Pages\ViewAdvisoryCouncilMember::route('/{record}'),
            'edit'  => Pages\EditAdvisoryCouncilMember::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 7: Create the 3 AdvisoryCouncilMemberResource Page classes** under `app/Filament/Resources/AdvisoryCouncilMemberResource/Pages/`:

`ListAdvisoryCouncilMembers.php`:
```php
<?php

namespace App\Filament\Resources\AdvisoryCouncilMemberResource\Pages;

use App\Filament\Resources\AdvisoryCouncilMemberResource;
use Filament\Resources\Pages\ListRecords;

class ListAdvisoryCouncilMembers extends ListRecords
{
    protected static string $resource = AdvisoryCouncilMemberResource::class;
}
```
`ViewAdvisoryCouncilMember.php`:
```php
<?php

namespace App\Filament\Resources\AdvisoryCouncilMemberResource\Pages;

use App\Filament\Resources\AdvisoryCouncilMemberResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAdvisoryCouncilMember extends ViewRecord
{
    protected static string $resource = AdvisoryCouncilMemberResource::class;
}
```
`EditAdvisoryCouncilMember.php`:
```php
<?php

namespace App\Filament\Resources\AdvisoryCouncilMemberResource\Pages;

use App\Filament\Resources\AdvisoryCouncilMemberResource;
use Filament\Resources\Pages\EditRecord;

class EditAdvisoryCouncilMember extends EditRecord
{
    protected static string $resource = AdvisoryCouncilMemberResource::class;
}
```

- [ ] **Step 8: Add the "Issue Certificate" action + Certification infolist section to `app/Filament/Resources/FinalEvaluationResource.php`**

In `table()->actions([...])` (read the file; it has ViewAction + EditAction), add as the first custom action:
```php
                Tables\Actions\Action::make('issue_certificate')
                    ->label('Issue Certificate')->icon('heroicon-o-academic-cap')->color('success')
                    ->visible(fn (\App\Models\FinalEvaluation $r) =>
                        app(\App\Support\CertificationScore::class)->for($r)['tier'] !== 'not_certified'
                        && ! \App\Models\ValidationCertificate::where('cohort_member_id', $r->cohort_member_id)->exists())
                    ->requiresConfirmation()
                    ->action(function (\App\Models\FinalEvaluation $r) {
                        \App\Models\ValidationCertificate::issueFor($r, auth()->id());
                        \Filament\Notifications\Notification::make()->title('Certificate issued.')->success()->send();
                    }),
```
In `infolist()`, append a Certification section (after the Frozen Contribution section), reading the live score:
```php
            Infolists\Components\Section::make('Certification')
                ->description('Live computed score (frozen when a certificate is issued).')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('certification_score')
                        ->label('Score')
                        ->state(fn (\App\Models\FinalEvaluation $record) => app(\App\Support\CertificationScore::class)->for($record)['score']),
                    Infolists\Components\TextEntry::make('certification_tier')
                        ->label('Tier')->badge()
                        ->state(fn (\App\Models\FinalEvaluation $record) => \App\Support\CertificationScore::tierOptions()[app(\App\Support\CertificationScore::class)->for($record)['tier']]),
                ]),
```

- [ ] **Step 9: Run — expect PASS** (`--filter=CertificationAdminTest`). Sanity: `<php> artisan route:list --path=admin 2>&1 | head -5`. Full suite — 0 failures (~420).

- [ ] **Step 10: Commit**
```bash
git add app/Filament/Resources/ValidationCertificateResource.php app/Filament/Resources/ValidationCertificateResource/Pages/ListValidationCertificates.php app/Filament/Resources/ValidationCertificateResource/Pages/ViewValidationCertificate.php app/Filament/Resources/AdvisoryCouncilMemberResource.php app/Filament/Resources/AdvisoryCouncilMemberResource/Pages/ListAdvisoryCouncilMembers.php app/Filament/Resources/AdvisoryCouncilMemberResource/Pages/ViewAdvisoryCouncilMember.php app/Filament/Resources/AdvisoryCouncilMemberResource/Pages/EditAdvisoryCouncilMember.php app/Filament/Resources/FinalEvaluationResource.php tests/Feature/CertificationAdminTest.php
git commit -m "feat(validation): add certificate + advisory council Filament resources and issue action"
```

---

## Task 5: Practitioner portal (PDF + download + certificates page + badge)

**Files:** Create `resources/views/pdf/validation-certificate.blade.php`; Modify `routes/web.php`, `app/Http/Controllers/Practitioner/CertificateController.php`, `resources/views/practitioner/certificates/index.blade.php`; Test `tests/Feature/ValidationCertificateDownloadTest.php`, `tests/Feature/AdvisoryCouncilTest.php`.

- [ ] **Step 1: Write the failing tests**

`tests/Feature/ValidationCertificateDownloadTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\CohortMember;
use App\Models\User;
use App\Models\ValidationCertificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ValidationCertificateDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function certFor(User $user): ValidationCertificate
    {
        $member = CohortMember::factory()->create(['user_id' => $user->id]);
        return ValidationCertificate::factory()->create(['cohort_member_id' => $member->id, 'tier' => 'distinction', 'score' => 90]);
    }

    public function test_owner_can_download(): void
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $cert = $this->certFor($user);

        $this->actingAs($user)
            ->get("/en/practitioner/certificates/validation/{$cert->id}/download")
            ->assertOk()
            ->assertDownload($cert->certificate_number.'.pdf');
    }

    public function test_other_practitioner_forbidden(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('practitioner');
        $cert = $this->certFor($owner);

        $other = User::factory()->create();
        $other->assignRole('practitioner');
        $this->actingAs($other)
            ->get("/en/practitioner/certificates/validation/{$cert->id}/download")
            ->assertForbidden();
    }
}
```

`tests/Feature/AdvisoryCouncilTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\AdvisoryCouncilMember;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdvisoryCouncilTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_unique_membership_per_user(): void
    {
        $user = User::factory()->create();
        AdvisoryCouncilMember::factory()->create(['user_id' => $user->id]);

        $this->expectException(QueryException::class);
        AdvisoryCouncilMember::factory()->create(['user_id' => $user->id]);
    }

    public function test_active_member_sees_council_badge_on_certificates_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        AdvisoryCouncilMember::factory()->create([
            'user_id' => $user->id, 'status' => 'active', 'title' => 'Clinical Validation Advisor',
        ]);

        $this->actingAs($user)
            ->get('/en/practitioner/certificates')
            ->assertOk()
            ->assertSee('Clinical Validation Advisor');
    }
}
```

- [ ] **Step 2: Run — expect FAIL** (`--filter=ValidationCertificateDownloadTest` — route missing; `--filter=AdvisoryCouncilTest` — badge missing).

- [ ] **Step 3: Add the download route** to `routes/web.php` — inside the practitioner group, after the existing `practitioner.certificates` route:
```php
                Route::get('/certificates/validation/{certificate}/download', [\App\Http\Controllers\Practitioner\CertificateController::class, 'downloadValidation'])->name('certificates.validation-download');
```

- [ ] **Step 4: Replace `app/Http/Controllers/Practitioner/CertificateController.php`**
```php
<?php
namespace App\Http\Controllers\Practitioner;

use App\Http\Controllers\Controller;
use App\Models\ValidationCertificate;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $certificates = $user->courseCertificates()->with('course')->latest('issued_at')->get();
        $validationCertificates = $user->validationCertificates()->with('cohortMember.cohort')->latest('issued_at')->get();
        $councilMembership = $user->advisoryCouncilMembership()->where('status', 'active')->first();

        return view('practitioner.certificates.index', compact('certificates', 'validationCertificates', 'councilMembership'));
    }

    public function downloadValidation($locale, ValidationCertificate $certificate)
    {
        abort_unless($certificate->cohortMember?->user_id === auth()->id(), 403);

        $certificate->load('cohortMember.user', 'cohortMember.cohort');

        return Pdf::loadView('pdf.validation-certificate', ['certificate' => $certificate])
            ->setPaper('a4', 'landscape')
            ->download($certificate->certificate_number . '.pdf');
    }
}
```

- [ ] **Step 5: Create `resources/views/pdf/validation-certificate.blade.php`** (standalone HTML for dompdf — NOT the practitioner layout)
```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; margin: 0; padding: 40px; }
        .frame { border: 6px solid #047857; padding: 40px 60px; text-align: center; }
        .brand { color: #047857; font-size: 18px; letter-spacing: 3px; text-transform: uppercase; }
        h1 { font-size: 34px; margin: 18px 0 6px; }
        .subtitle { color: #64748b; font-size: 14px; }
        .name { font-size: 28px; font-weight: bold; margin: 28px 0 6px; }
        .detail { font-size: 14px; color: #334155; margin: 4px 0; }
        .tier { display: inline-block; margin-top: 16px; padding: 6px 18px; border-radius: 999px;
                background: #ecfdf5; color: #047857; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; font-size: 12px; color: #64748b; }
        .sigline { border-top: 1px solid #94a3b8; width: 200px; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="frame">
        <div class="brand">OPES Health Systems</div>
        <h1>Certificate of Clinical Validation</h1>
        <div class="subtitle">This certifies that</div>
        <div class="name">{{ $certificate->cohortMember?->user?->name ?? 'Practitioner' }}</div>
        <div class="detail">successfully completed the clinical validation programme as part of</div>
        <div class="detail"><strong>{{ $certificate->cohortMember?->cohort?->name ?? 'Validation Cohort' }}</strong>
            @if($certificate->cohortMember?->cohort?->specialty) — {{ $certificate->cohortMember->cohort->specialty }} @endif
        </div>
        <div class="tier">{{ ucfirst($certificate->tier) }} &middot; Score {{ $certificate->score }}/100</div>
        <div class="footer">
            <div class="sigline">Clinical Validation Lead</div>
            <div style="text-align:right">
                <div>Certificate No. {{ $certificate->certificate_number }}</div>
                <div>Issued {{ $certificate->issued_at?->format('d M Y') }}</div>
            </div>
        </div>
    </div>
</body>
</html>
```

- [ ] **Step 6: Edit `resources/views/practitioner/certificates/index.blade.php`** — add the council badge (top) + a Validation Certificates section. Insert after the opening header `</div>` (line ~5), before the course `@if`:
```blade
    @if(isset($councilMembership) && $councilMembership)
        <div class="bg-gradient-to-r from-emerald-900/40 to-slate-900 border border-emerald-700/50 rounded-xl p-5 mb-6 flex items-center gap-3">
            <i data-lucide="shield-check" style="width:28px;height:28px" class="text-emerald-400"></i>
            <div>
                <h3 class="font-semibold text-white">Clinical Validation Advisory Council</h3>
                <p class="text-xs text-emerald-300/80 mt-0.5">
                    {{ $councilMembership->title }} &middot; since {{ $councilMembership->term_start?->format('M Y') }}
                </p>
            </div>
        </div>
    @endif

    @if(!empty($validationCertificates) && $validationCertificates->isNotEmpty())
        <h2 class="text-lg font-semibold text-white mb-3">Validation Certificates</h2>
        <div class="space-y-4 mb-8">
            @foreach($validationCertificates as $vcert)
                <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i data-lucide="badge-check" style="width:28px;height:28px" class="text-emerald-400"></i>
                        <div>
                            <h3 class="font-semibold text-white">{{ $vcert->cohortMember?->cohort?->name ?? 'Validation Cohort' }}
                                <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-300">{{ ucfirst($vcert->tier) }} &middot; {{ $vcert->score }}/100</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $vcert->certificate_number }} &middot; Issued {{ $vcert->issued_at?->format('d M Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('practitioner.certificates.validation-download', ['locale' => app()->getLocale(), 'certificate' => $vcert->id]) }}"
                       class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition no-underline">
                        Download
                    </a>
                </div>
            @endforeach
        </div>
    @endif
```

- [ ] **Step 7: Run — expect PASS** (both filters). Full suite — 0 failures (~424).

- [ ] **Step 8: Commit**
```bash
git add resources/views/pdf/validation-certificate.blade.php routes/web.php app/Http/Controllers/Practitioner/CertificateController.php resources/views/practitioner/certificates/index.blade.php tests/Feature/ValidationCertificateDownloadTest.php tests/Feature/AdvisoryCouncilTest.php
git commit -m "feat(validation): add certificate PDF download and advisory council badge to portal"
```

---

## Final verification
- Full suite: `<php> artisan test` — expect ~424 passing (407 baseline + ~17 new), 0 failures.
- `<php> artisan route:list --path=admin 2>&1 | head` — panel boots; validation-certificates + advisory-council-members resources register.
- Manual smoke (dev server): as admin, open a high-scoring FinalEvaluation → "Issue Certificate" (gated on score) → certificate appears in ValidationCertificateResource; "Download PDF" returns a landscape A4 PDF; for a distinction cert, "Invite to Council" → member in AdvisoryCouncilMemberResource. As that practitioner: certificates page shows the validation certificate (Download works) and, if invited, the Advisory Council badge. Non-admin → 403 on the resources; another practitioner → 403 on the download.

## Post-completion
- Run `superpowers:finishing-a-development-branch` to merge.
- Memory: update [[clinical-validation-hub]] — **all 4 sub-projects done; Clinical Validation Hub complete.**
