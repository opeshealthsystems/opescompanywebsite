# Clinical Validation & Innovation Hub — Design Spec (Sub-project 4: Certification + Advisory Council)

> **For agentic workers:** REQUIRED SUB-SKILL: Use `superpowers:subagent-driven-development` (recommended) or `superpowers:executing-plans` to implement the resulting plan task-by-task.

**Goal:** Close the validation programme loop with recognition. Compute a certification score from each member's final evaluation, let an admin issue a downloadable PDF certificate to qualifying practitioners, and invite the top-tier certified practitioners onto a Clinical Validation Advisory Council.

**Architecture:** A stateless `App\Support\CertificationScore` service computes a 0–100 score + tier from an SP3 `FinalEvaluation` (its rating + frozen contribution metrics). Certification is a credential, so the score + tier are **frozen onto the `ValidationCertificate`** at issuance (the service computes; the certificate stores). Admins issue certificates and invite council members through Filament; practitioners view/download certificates and see a council badge in the existing portal certificates page. Purely additive: 2 new tables, no existing table changes; the only existing-model edits are two additive `User` relationships.

**Scope:** Sub-project 4 of 4 — the final phase (Phases 13–14 of the full vision). Builds on merged SP1 (cohorts/sessions/issues), SP2 (developer tasks/retests), SP3 (`ValidationMetrics`, `WeeklyReview`, `FinalEvaluation`). This completes the Clinical Validation Hub.

**Tech stack:** Laravel 13.8 / PHP 8.3 / Spatie Permission v8 / Filament v3 / Blade + Tailwind / `barryvdh/laravel-dompdf` (already installed) / SQLite (tests) / MySQL (prod).

---

## Design decisions (locked in brainstorming)

| Decision | Choice |
|---|---|
| Scoring | `CertificationScore` service: rating component (0–50) + contribution component (0–50, capped) → 0–100 score + tier (distinction/pass/not_certified). |
| Certificate issuance | Admin "Issue Certificate" action on `FinalEvaluationResource`, visible only when tier ≠ not_certified and no cert exists. Score/tier frozen at issuance. |
| Certificate delivery | Practitioner-facing in the existing certificates page; downloadable dompdf PDF. |
| Advisory Council | Admin "Invite to Council" action on a `distinction`-tier certificate → `AdvisoryCouncilMember` record. Practitioner sees a council badge. No new auth role. |
| Immutability | Credential score/tier frozen on the certificate; never recomputed after issuance. |

---

## Section 1: Scoring service + Data Model

### `App\Support\CertificationScore`
**File:** `app/Support/CertificationScore.php` (alongside `AdminNotifier`, `ValidationMetrics`). Stateless; returns a plain array.

```php
namespace App\Support;

use App\Models\FinalEvaluation;

class CertificationScore
{
    public const PASS_THRESHOLD = 60;
    public const DISTINCTION_THRESHOLD = 85;

    /** @return array{score:int, tier:string, breakdown:array} */
    public function for(FinalEvaluation $evaluation): array;

    public static function tierOptions(): array; // distinction|pass|not_certified
}
```

**Computation (exact):**
- **Rating component (0–50)** from `FinalEvaluation::rating`: `outstanding`=50, `strong`=38, `satisfactory`=25, `needs_improvement`=10 (unknown → 0).
- **Contribution component (0–50)** from the evaluation's frozen `metrics` array: `min(50, ($m['issues_accepted'] ?? 0) * 5 + ($m['sessions'] ?? 0) * 1 + ($m['retests'] ?? 0) * 2)`.
- **score** = rating + contribution (already ≤ 100). **tier**: `score >= 85` → `distinction`; `score >= 60` → `pass`; else `not_certified`.
- **breakdown** = `['rating' => <ratingPts>, 'contribution' => <contribPts>]`.
- `tierOptions()` = `['distinction' => 'Distinction', 'pass' => 'Pass', 'not_certified' => 'Not Certified']`.

A FinalEvaluation is **certifiable** when `tier !== 'not_certified'` (score ≥ 60).

### New tables (mirror the `app/Models/CourseCertificate.php` auto-number boot pattern)

#### `validation_certificates`
```
id
cohort_member_id     FK → cohort_members, cascadeOnDelete, UNIQUE   (one cert per membership)
final_evaluation_id  FK → final_evaluations, nullable, nullOnDelete (basis)
certificate_number   string, unique                                 (auto VCERT-YYYY-NNNNNN via boot)
score                unsignedInteger                                (frozen 0–100)
tier                 string(20)   // distinction|pass
issued_by            FK → users
issued_at            timestamp
timestamps
```
No `pdf_path` — the PDF is rendered on demand (YAGNI; no stored file).

#### `advisory_council_members`
```
id
user_id                   FK → users, cascadeOnDelete, UNIQUE       (one membership per user)
validation_certificate_id FK → validation_certificates, nullable, nullOnDelete (qualifying cert)
title                     string                                    (e.g. "Clinical Validation Advisor")
term_start                date
term_end                  date, nullable
status                    string(20)   // active|inactive   default: active
invited_by                FK → users
invited_at                timestamp
timestamps
```

### Models
| Model | File |
|---|---|
| `ValidationCertificate` | `app/Models/ValidationCertificate.php` |
| `AdvisoryCouncilMember` | `app/Models/AdvisoryCouncilMember.php` |

**`ValidationCertificate`** — `use HasFactory;` `$fillable = ['cohort_member_id','final_evaluation_id','certificate_number','score','tier','issued_by','issued_at'];` `$casts = ['issued_at'=>'datetime','score'=>'integer'];`
- `boot()` auto-generates `certificate_number` = `VCERT-YYYY-NNNNNN` (copy the `CourseCertificate` pattern, prefix `VCERT`) and defaults `issued_at` to now if unset.
- Relationships: `cohortMember()` belongsTo, `finalEvaluation()` belongsTo, `issuedBy()` belongsTo(User,'issued_by'), `advisoryCouncilMember()` hasOne.
- Static `tierBadgeColors(): array` → `['distinction'=>'success','pass'=>'info']` (for Filament).
- **Static `issueFor(FinalEvaluation $evaluation, int $issuedById): self`** — the issuance entry point (Filament action is a thin wrapper):
  ```php
  $result = app(CertificationScore::class)->for($evaluation);
  abort_if($result['tier'] === 'not_certified', 422, 'Member is not eligible for certification.');
  return static::create([
      'cohort_member_id'    => $evaluation->cohort_member_id,
      'final_evaluation_id' => $evaluation->id,
      'score'               => $result['score'],
      'tier'                => $result['tier'],
      'issued_by'           => $issuedById,
      'issued_at'           => now(),
  ]);
  ```

**`AdvisoryCouncilMember`** — `use HasFactory;` `$fillable = ['user_id','validation_certificate_id','title','term_start','term_end','status','invited_by','invited_at'];` `$casts = ['term_start'=>'date','term_end'=>'date','invited_at'=>'datetime'];`
- Relationships: `user()` belongsTo, `validationCertificate()` belongsTo, `invitedBy()` belongsTo(User,'invited_by').
- Static `statusOptions(): array` → `['active'=>'Active','inactive'=>'Inactive']`.

### Additive existing-model edits (the only ones)
`app/Models/User.php`:
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

---

## Section 2: Admin (Filament)

All admin/super_admin gated, nav group `Validation Hub`.

### `FinalEvaluationResource` (extend SP3)
- Add an **"Issue Certificate"** row action:
  - `visible(fn (FinalEvaluation $r) => app(CertificationScore::class)->for($r)['tier'] !== 'not_certified' && ! \App\Models\ValidationCertificate::where('cohort_member_id', $r->cohort_member_id)->exists())` — i.e. eligible and no cert yet for that member.
  - `requiresConfirmation()`; on action: `ValidationCertificate::issueFor($record, auth()->id())` + success notification.
- The infolist gains a **Certification** section showing the live computed `score`/`tier` (preview before issuing) via `CertificationScore`.

### `ValidationCertificateResource` (new)
- `canCreate(): false`. Table: `cohortMember.user.name` (Member), `cohortMember.cohort.name` (Cohort), `certificate_number`, `score`, `tier` badge (colors from `tierBadgeColors()`), `issuedBy.name`, `issued_at`. View: infolist with frozen score/tier/number/basis.
- Row actions:
  - **Download PDF** — streams the dompdf certificate (admin can fetch any): renders `resources/views/pdf/validation-certificate.blade.php` via `Barryvdh\DomPDF\Facade\Pdf::loadView(...)->download($record->certificate_number.'.pdf')`.
  - **Invite to Council** — `visible(fn ($r) => $r->tier === 'distinction' && ! \App\Models\AdvisoryCouncilMember::where('user_id', $r->cohortMember->user_id)->exists())`; form (`title` default "Clinical Validation Advisor", `term_start` date default today, `term_end` nullable); action creates `AdvisoryCouncilMember` (`user_id` from `$r->cohortMember->user_id`, `validation_certificate_id` = `$r->id`, `invited_by` = auth id, `invited_at` = now, status active) + notification.

### `AdvisoryCouncilMemberResource` (new)
- `canCreate(): false` (members arrive via the Invite action). Table: `user.name`, `title`, term range, `status` badge (active=success, inactive=gray), `invitedBy.name`, `invited_at`. **Edit:** `title`, `term_start`, `term_end`, `status`. View: infolist.

---

## Section 3: Practitioner Portal

### Certificates page (extend existing)
- `app/Http/Controllers/Practitioner/CertificateController.php` `index()`: in addition to `courseCertificates`, load `auth()->user()->validationCertificates()->with('cohortMember.cohort')->latest('issued_at')->get()` and the active `advisoryCouncilMembership`. Pass `$validationCertificates` and `$councilMembership` to the view.
- `resources/views/practitioner/certificates/index.blade.php`: add a **Validation Certificates** section (number, tier badge, score, cohort, issued date, **Download PDF** button) and — when `$councilMembership` is active — a prestige **Advisory Council** acknowledgment card (title + term).

### PDF download
- Route in the practitioner group (`routes/web.php`):
  ```php
  Route::get('/certificates/validation/{certificate}/download',
      [\App\Http\Controllers\Practitioner\CertificateController::class, 'downloadValidation'])
      ->name('certificates.validation-download');
  ```
  Full name `practitioner.certificates.validation-download`; inherits `auth` + `role:practitioner`.
- `CertificateController::downloadValidation($locale, ValidationCertificate $certificate)`:
  - `abort_unless($certificate->cohortMember?->user_id === auth()->id(), 403);`
  - `return Pdf::loadView('pdf.validation-certificate', ['certificate' => $certificate->load('cohortMember.user','cohortMember.cohort')])->download($certificate->certificate_number.'.pdf');`
- `resources/views/pdf/validation-certificate.blade.php`: a standalone print layout (NOT the practitioner Blade component) — recipient name, cohort + specialty, tier, score, certificate number, issue date, OPES branding + signature line. Plain HTML/inline CSS (dompdf-friendly).

---

## Section 4: Authorization, Testing, Factories

### Authorization
- Both new resources + the FinalEvaluation Issue action: `canAccess()` → `hasAnyRole(['super_admin','admin'])`.
- Practitioner certificate download: ownership-gated (`cohortMember.user_id === auth id`), 403 otherwise.

### Factories
- **`ValidationCertificateFactory`** — `cohort_member_id => CohortMember::factory()`, `final_evaluation_id => null`, `score => 75`, `tier => 'pass'`, `issued_by => User::factory()`, `issued_at => now()` (certificate_number auto via boot).
- **`AdvisoryCouncilMemberFactory`** — `user_id => User::factory()`, `validation_certificate_id => null`, `title => 'Clinical Validation Advisor'`, `term_start => now()->toDateString()`, `term_end => null`, `status => 'active'`, `invited_by => User::factory()`, `invited_at => now()`.

### Tests (5 classes; `RefreshDatabase` + `RolePermissionSeeder`)
- **`ValidationCertificationMigrationsTest`**: both tables + key columns + unique constraints exist (standalone `$table->unique(...)` — NOT inline-chained, per the known SQLite drop).
- **`CertificationScoreTest`** (core): outstanding + high contribution → distinction (≥85); strong + modest metrics → pass; needs_improvement + low → not_certified; contribution capped at 50 (huge metrics still ≤ 50); boundary: a score of exactly 60 → pass; `tierOptions()` has 3 entries.
- **`ValidationCertificateTest`**: `issueFor()` freezes score/tier and auto-generates `VCERT-YYYY-NNNNNN`; `issueFor()` on a not_certified evaluation aborts/throws (no cert created); `unique(cohort_member_id)` blocks a duplicate; resource `canAccess` admin true / practitioner false; `canCreate()` false.
- **`ValidationCertificateDownloadTest`**: owner GET `/en/practitioner/certificates/validation/{id}/download` → 200 + `assertDownload()` (PDF); a different practitioner → 403; non-practitioner role → 403.
- **`AdvisoryCouncilTest`**: creating a membership works; `unique(user_id)` blocks duplicates; the certificates page shows the council badge for an active member (acting as that practitioner, `assertSee` the title); resource admin-gated.

### Success criteria
| Area | Metric |
|---|---|
| Data model | 2 new tables, 0 column changes |
| Scoring | `CertificationScore` service, fully unit-tested |
| Admin | ValidationCertificateResource + AdvisoryCouncilMemberResource + Issue/Invite/Download actions + FinalEvaluation extension |
| Portal | certificates-page extension + dompdf download route + council badge |
| Tests | 5 classes, ~18 tests, 0 failures; existing 407 stay green |
| Milestone | **Clinical Validation Hub complete (SP1–SP4)** |

---

## Appendix: scoring reference table

| Rating | Rating pts | Example metrics | Contribution pts | Score | Tier |
|---|---|---|---|---|---|
| outstanding | 50 | 8 accepted, 10 sessions, 4 retests | min(50, 40+10+8)=50 | 100 | distinction |
| strong | 38 | 3 accepted, 5 sessions, 1 retest | min(50, 15+5+2)=22 | 60 | pass |
| satisfactory | 25 | 2 accepted, 3 sessions, 0 retests | min(50, 10+3)=13 | 38 | not_certified |
| needs_improvement | 10 | 0 accepted, 1 session | 1 | 11 | not_certified |

## Appendix: Factory attribute reference

| Factory | Key attributes |
|---|---|
| `ValidationCertificateFactory` | cohort_member_id (CohortMember::factory), final_evaluation_id=null, score=75, tier='pass', issued_by (User::factory), issued_at=now |
| `AdvisoryCouncilMemberFactory` | user_id (User::factory), validation_certificate_id=null, title='Clinical Validation Advisor', term_start=today, status='active', invited_by (User::factory), invited_at=now |
