# Notifications N2 — Clinical Validation Hub Events Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans (inline) to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Wire branded email + in-app notifications for the 7 actionable Clinical Validation Hub lifecycle events onto the N1 notification spine.

**Architecture:** Each event is a small `App\Notifications\*` class (`via()` → `['mail','database']`, `toMail()` branded `MailMessage`, `toArray()` feed payload). Notifications fire at the **single chokepoint** for each event — the on-model state-machine methods (`IssueReport::recordClinicalReview/recordProductReview/closeIssue`, `DeveloperTask::markFixed`), the issue-submission controller, the `ValidationCertificate::issueFor` service method, and the council-invite Filament action. The reporter is `IssueReport->cohortMember->user`; the certified practitioner is `ValidationCertificate->cohortMember->user`. All recipients are logged-in portal users, so every event uses both channels.

**Tech Stack:** Laravel 13.8 / PHP 8.3 / Filament v3 / SQLite (tests). PHP binary `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`. Brand theme already published (N1).

---

## Conventions (verified against N1 + codebase)

- **Notification class shape** (matches `app/Notifications/PlacedInCohort.php`): `extends Notification implements ShouldQueue`, `use Queueable;`, constructor property-promotes the model(s), `via()` returns `['mail','database']`, `toMail()` builds a `MailMessage` (`->subject()->greeting()->line()->action()->line()`), `toArray()` returns `['type','title','body','icon','url']`.
- **Recipient chain:** `IssueReport->cohortMember->user` (CohortMember `belongsTo(User::class)`); guard with optional chaining (`?->`) exactly like N1's `$record->practitioner?->notify(...)`.
- **CTA route names (locale-prefixed — always pass `['locale' => 'en', ...]`):**
  - Issue page: `practitioner.validation.issues.show` param `issue` → `route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $issue->id])`
  - Certificates page: `practitioner.certificates` → `route('practitioner.certificates', ['locale' => 'en'])`
- **Heroicon names for the feed** (the bell renders `data['icon']` via lucide; reuse simple names): `clipboard-document-check`, `chat-bubble-left-right`, `wrench-screwdriver`, `check-circle`, `academic-cap`, `user-plus`. (The feed view maps unknown icons to a default bell — any kebab string is safe.)
- **Tests:** `Tests\Feature`, `use RefreshDatabase;`, `setUp()` seeds `RolePermissionSeeder` and forgets permission cache. Use `Notification::fake()` + `assertSentTo($user, X::class)` and `assertNotSentTo`. Build the graph with the CVH factories (`IssueReport::factory()`, `DeveloperTask::factory()`, `ValidationCertificate::factory()`, `FinalEvaluation::factory()`). For decision/transition assertions, call the model method directly.
- **Execution discipline:** PowerShell git commits use `-m "..."`. Stage only explicit file paths. Run `<php> artisan test --filter=<Class>` then the full suite before each commit.

---

## Task 1 — Issue review-pipeline notifications (submitted → clinical → product)

Three reporter-facing notifications covering the front half of the issue lifecycle.

**Files:**
- Create: `app/Notifications/IssueSubmitted.php`
- Create: `app/Notifications/IssueClinicalDecision.php`
- Create: `app/Notifications/IssueProductDecision.php`
- Modify: `app/Http/Controllers/Practitioner/Validation/IssueReportController.php` (capture created issue + notify)
- Modify: `app/Models/IssueReport.php` (`recordClinicalReview`, `recordProductReview`)
- Test: `tests/Feature/N2IssuePipelineTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\CohortMember;
use App\Models\IssueReport;
use App\Models\User;
use App\Notifications\IssueClinicalDecision;
use App\Notifications\IssueProductDecision;
use App\Notifications\IssueSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N2IssuePipelineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function reporterMember(): CohortMember
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM']);

        return CohortMember::factory()->create(['user_id' => $user->id]);
    }

    public function test_submitting_an_issue_notifies_the_reporter(): void
    {
        Notification::fake();
        $member = $this->reporterMember();
        [$product, $module, $workflow] = $this->scopedCatalogFor($member);

        $this->actingAs($member->user)->post('/en/practitioner/validation/issues', [
            'validation_product_id'  => $product->id,
            'validation_module_id'   => $module->id,
            'validation_workflow_id' => $workflow->id,
            'title'                  => 'Vitals panel rounds wrong',
            'issue_type'             => 'bug',
            'severity'               => 'high',
            'description'            => str_repeat('a', 30),
            'steps_to_reproduce'     => str_repeat('b', 30),
            'expected_result'        => 'correct',
            'actual_result'          => 'wrong',
            'clinical_impact'        => 'moderate',
        ])->assertRedirect();

        Notification::assertSentTo($member->user, IssueSubmitted::class);
    }

    public function test_clinical_decision_notifies_the_reporter(): void
    {
        Notification::fake();
        $member = $this->reporterMember();
        $issue  = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'clinical_review']);

        $issue->recordClinicalReview(User::factory()->create()->id, 'approved_for_product_review', 'Looks valid.');

        Notification::assertSentTo($member->user, IssueClinicalDecision::class);
    }

    public function test_product_decision_notifies_the_reporter(): void
    {
        Notification::fake();
        $member = $this->reporterMember();
        $issue  = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'product_review']);

        $issue->recordProductReview(User::factory()->create()->id, 'sent_to_development', 'Routing to dev.');

        Notification::assertSentTo($member->user, IssueProductDecision::class);
    }

    /** Build a cohort-scoped product/module/workflow + attach a test case so the workflow is in scope. */
    private function scopedCatalogFor(CohortMember $member): array
    {
        $product  = \App\Models\ValidationProduct::factory()->create();
        $module   = \App\Models\ValidationModule::factory()->create(['validation_product_id' => $product->id]);
        $workflow = \App\Models\ValidationWorkflow::factory()->create(['validation_module_id' => $module->id]);
        $testCase = \App\Models\ValidationTestCase::factory()->create(['validation_workflow_id' => $workflow->id]);
        $member->cohort->testCases()->attach($testCase->id);

        return [$product, $module, $workflow];
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=N2IssuePipelineTest`
Expected: FAIL — `Class "App\Notifications\IssueSubmitted" not found`.

- [ ] **Step 3: Create `app/Notifications/IssueSubmitted.php`**

```php
<?php

namespace App\Notifications;

use App\Models\IssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public IssueReport $issue) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('We received your issue report')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your report "' . $this->issue->title . '" was submitted and is awaiting clinical review.')
            ->action('View issue', route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]))
            ->line('Thank you for helping validate OPES Health software.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.issue_submitted',
            'title' => 'Issue report received',
            'body'  => 'Your report "' . $this->issue->title . '" is awaiting clinical review.',
            'icon'  => 'clipboard-document-check',
            'url'   => route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]),
        ];
    }
}
```

- [ ] **Step 4: Create `app/Notifications/IssueClinicalDecision.php`**

```php
<?php

namespace App\Notifications;

use App\Models\IssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueClinicalDecision extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public IssueReport $issue, public string $decision, public ?string $notes = null) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    private function headline(): string
    {
        return match ($this->decision) {
            'approved_for_product_review' => 'Your issue passed clinical review',
            'needs_more_information'      => 'Your issue needs more information',
            'rejected'                    => 'Your issue was not accepted',
            default                        => 'Update on your issue',
        };
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->headline())
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Clinical review of your report "' . $this->issue->title . '" is complete: ' . $this->headline() . '.');

        if ($this->notes) {
            $mail->line('Reviewer note: ' . $this->notes);
        }

        return $mail->action('View issue', route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.issue_clinical_decision',
            'title' => $this->headline(),
            'body'  => 'Clinical review complete for "' . $this->issue->title . '".',
            'icon'  => 'chat-bubble-left-right',
            'url'   => route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]),
        ];
    }
}
```

- [ ] **Step 5: Create `app/Notifications/IssueProductDecision.php`**

```php
<?php

namespace App\Notifications;

use App\Models\IssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueProductDecision extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public IssueReport $issue, public string $decision, public ?string $notes = null) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    private function headline(): string
    {
        return match ($this->decision) {
            'accepted'           => 'Your issue was accepted',
            'sent_to_development' => 'Your issue was sent to development',
            'duplicate'          => 'Your issue was marked a duplicate',
            'rejected'           => 'Your issue was not accepted',
            default               => 'Update on your issue',
        };
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->headline())
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Product review of your report "' . $this->issue->title . '" is complete: ' . $this->headline() . '.');

        if ($this->notes) {
            $mail->line('Reviewer note: ' . $this->notes);
        }

        return $mail->action('View issue', route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.issue_product_decision',
            'title' => $this->headline(),
            'body'  => 'Product review complete for "' . $this->issue->title . '".',
            'icon'  => 'wrench-screwdriver',
            'url'   => route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]),
        ];
    }
}
```

- [ ] **Step 6: Wire `IssueSubmitted` into the controller**

In `app/Http/Controllers/Practitioner/Validation/IssueReportController.php` `store()`, replace the `IssueReport::create(...)` statement (lines ~107-111) with a captured instance + notify:

```php
        $issue = IssueReport::create(array_merge($validated, [
            'cohort_member_id' => $member->id,
            'attachments'      => $paths ?: null,
            'status'           => 'submitted',
        ]));

        $member->user?->notify(new \App\Notifications\IssueSubmitted($issue));
```

- [ ] **Step 7: Wire `IssueClinicalDecision` + `IssueProductDecision` into the model methods**

In `app/Models/IssueReport.php`, at the end of `recordClinicalReview()` (after `$this->save();`):

```php
        $this->cohortMember?->user?->notify(new \App\Notifications\IssueClinicalDecision($this, $decision, $notes));
```

At the end of `recordProductReview()` (after the `if ($decision === 'sent_to_development')` block, still inside the method — only reached when status was `product_review`):

```php
        $this->cohortMember?->user?->notify(new \App\Notifications\IssueProductDecision($this, $decision, $notes));
```

- [ ] **Step 8: Run test to verify it passes**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=N2IssuePipelineTest`
Expected: PASS (3 tests).

- [ ] **Step 9: Run full suite**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test`
Expected: all green (450 prior + 3 new = 453, 0 failures).

- [ ] **Step 10: Commit**

```bash
git add app/Notifications/IssueSubmitted.php app/Notifications/IssueClinicalDecision.php app/Notifications/IssueProductDecision.php app/Http/Controllers/Practitioner/Validation/IssueReportController.php app/Models/IssueReport.php tests/Feature/N2IssuePipelineTest.php
git commit -m "feat(notifications): notify reporters on issue submit + clinical/product decisions (N2)"
```

---

## Task 2 — Issue resolution notifications (ready-for-retest → closed)

**Files:**
- Create: `app/Notifications/IssueReadyForRetest.php`
- Create: `app/Notifications/IssueClosed.php`
- Modify: `app/Models/DeveloperTask.php` (`markFixed`)
- Modify: `app/Models/IssueReport.php` (`closeIssue`)
- Test: `tests/Feature/N2IssueResolutionTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\CohortMember;
use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\User;
use App\Notifications\IssueClosed;
use App\Notifications\IssueReadyForRetest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N2IssueResolutionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function reporterMember(): CohortMember
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');

        return CohortMember::factory()->create(['user_id' => $user->id]);
    }

    public function test_marking_a_dev_task_fixed_notifies_the_reporter_to_retest(): void
    {
        Notification::fake();
        $member = $this->reporterMember();
        $issue  = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'sent_to_development']);
        $task   = DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'in_progress']);

        $task->markFixed('Patched the rounding bug.');

        Notification::assertSentTo($member->user, IssueReadyForRetest::class);
    }

    public function test_marking_fixed_on_a_non_dev_issue_does_not_notify(): void
    {
        Notification::fake();
        $member = $this->reporterMember();
        // Issue already closed → markFixed must NOT resurrect it or notify.
        $issue  = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'closed']);
        $task   = DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'in_progress']);

        $task->markFixed();

        Notification::assertNotSentTo($member->user, IssueReadyForRetest::class);
    }

    public function test_closing_an_issue_notifies_the_reporter(): void
    {
        Notification::fake();
        $member = $this->reporterMember();
        $issue  = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'accepted']);

        $issue->closeIssue();

        Notification::assertSentTo($member->user, IssueClosed::class);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=N2IssueResolutionTest`
Expected: FAIL — `Class "App\Notifications\IssueReadyForRetest" not found`.

- [ ] **Step 3: Create `app/Notifications/IssueReadyForRetest.php`**

```php
<?php

namespace App\Notifications;

use App\Models\IssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueReadyForRetest extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public IssueReport $issue) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your reported issue has been fixed — please retest')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The development team has fixed your reported issue "' . $this->issue->title . '".')
            ->line('Please verify the fix and submit a retest result from the issue page.')
            ->action('Retest now', route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.issue_ready_for_retest',
            'title' => 'Fixed — please retest',
            'body'  => 'Your issue "' . $this->issue->title . '" is ready for retest.',
            'icon'  => 'wrench-screwdriver',
            'url'   => route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]),
        ];
    }
}
```

- [ ] **Step 4: Create `app/Notifications/IssueClosed.php`**

```php
<?php

namespace App\Notifications;

use App\Models\IssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueClosed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public IssueReport $issue) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your issue has been closed')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your reported issue "' . $this->issue->title . '" has been closed.')
            ->line('Thank you for your contribution to validating OPES Health software.')
            ->action('View issue', route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.issue_closed',
            'title' => 'Issue closed',
            'body'  => 'Your issue "' . $this->issue->title . '" has been closed.',
            'icon'  => 'check-circle',
            'url'   => route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]),
        ];
    }
}
```

- [ ] **Step 5: Wire `IssueReadyForRetest` into `DeveloperTask::markFixed`**

In `app/Models/DeveloperTask.php` `markFixed()`, inside the `if ($issue && in_array(...))` block, after `$issue->update(['status' => 'ready_for_retest']);` add the notify (only fires on the real transition, never for terminal issues):

```php
        if ($issue && in_array($issue->status, ['sent_to_development', 'retest_failed'], true)) {
            $issue->update(['status' => 'ready_for_retest']);
            $issue->cohortMember?->user?->notify(new \App\Notifications\IssueReadyForRetest($issue));
        }
```

- [ ] **Step 6: Wire `IssueClosed` into `IssueReport::closeIssue`**

In `app/Models/IssueReport.php` `closeIssue()`:

```php
    public function closeIssue(): void
    {
        $this->update(['status' => 'closed']);
        $this->cohortMember?->user?->notify(new \App\Notifications\IssueClosed($this));
    }
```

- [ ] **Step 7: Run test to verify it passes**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=N2IssueResolutionTest`
Expected: PASS (3 tests).

- [ ] **Step 8: Run full suite**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test`
Expected: all green (453 + 3 = 456, 0 failures).

- [ ] **Step 9: Commit**

```bash
git add app/Notifications/IssueReadyForRetest.php app/Notifications/IssueClosed.php app/Models/DeveloperTask.php app/Models/IssueReport.php tests/Feature/N2IssueResolutionTest.php
git commit -m "feat(notifications): notify reporters on ready-for-retest + issue closed (N2)"
```

---

## Task 3 — Certification notifications (certificate issued → council invitation)

**Files:**
- Create: `app/Notifications/CertificateIssued.php`
- Create: `app/Notifications/CouncilInvitation.php`
- Modify: `app/Models/ValidationCertificate.php` (`issueFor`)
- Modify: `app/Filament/Resources/ValidationCertificateResource.php` (invite action)
- Test: `tests/Feature/N2CertificationNotificationsTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\AdvisoryCouncilMember;
use App\Models\CohortMember;
use App\Models\FinalEvaluation;
use App\Models\User;
use App\Models\ValidationCertificate;
use App\Notifications\CertificateIssued;
use App\Notifications\CouncilInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N2CertificationNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function certifiableEvaluation(): FinalEvaluation
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $member = CohortMember::factory()->create(['user_id' => $user->id]);

        // rating + metrics drive CertificationScore: outstanding(50) + capped contribution(50) = 100 → distinction,
        // so issueFor() does not abort with "not eligible".
        return FinalEvaluation::factory()->create([
            'cohort_member_id' => $member->id,
            'rating'           => 'outstanding',
            'metrics'          => ['issues_accepted' => 8, 'sessions' => 10, 'retests' => 3],
        ]);
    }

    public function test_issuing_a_certificate_notifies_the_practitioner(): void
    {
        Notification::fake();
        $evaluation = $this->certifiableEvaluation();
        $admin      = User::factory()->create();

        ValidationCertificate::issueFor($evaluation, $admin->id);

        Notification::assertSentTo($evaluation->cohortMember->user, CertificateIssued::class);
    }

    public function test_council_invitation_notifies_the_practitioner(): void
    {
        Notification::fake();
        $user   = User::factory()->create();
        $member = CohortMember::factory()->create(['user_id' => $user->id]);
        $cert   = ValidationCertificate::factory()->create([
            'cohort_member_id' => $member->id,
            'tier'             => 'distinction',
        ]);

        $councilMember = AdvisoryCouncilMember::create([
            'user_id'                   => $user->id,
            'validation_certificate_id' => $cert->id,
            'title'                     => 'Clinical Validation Advisor',
            'term_start'                => now(),
            'status'                    => 'active',
            'invited_by'                => User::factory()->create()->id,
            'invited_at'                => now(),
        ]);
        $user->notify(new CouncilInvitation($councilMember));

        Notification::assertSentTo($user, CouncilInvitation::class);
    }
}
```

> The council-invite assertion drives the notification class directly (the Filament action's `->action()` closure is exercised in `IssueReportTriageTest`-style resource tests elsewhere; here we prove the class + payload). Step 6 wires the real action.

- [ ] **Step 2: Run test to verify it fails**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=N2CertificationNotificationsTest`
Expected: FAIL — `Class "App\Notifications\CertificateIssued" not found`.

- [ ] **Step 3: Create `app/Notifications/CertificateIssued.php`**

```php
<?php

namespace App\Notifications;

use App\Models\ValidationCertificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateIssued extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ValidationCertificate $certificate) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Clinical Validation certificate is ready')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Congratulations! Your Clinical Validation certificate has been issued.')
            ->line('Tier: ' . ucfirst($this->certificate->tier) . ' · Score: ' . $this->certificate->score . '/100 · Number: ' . $this->certificate->certificate_number)
            ->action('Download certificate', route('practitioner.certificates', ['locale' => 'en']))
            ->line('Thank you for your contribution to OPES Health validation.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.certificate_issued',
            'title' => 'Certificate issued',
            'body'  => ucfirst($this->certificate->tier) . ' certificate (' . $this->certificate->score . '/100) is ready to download.',
            'icon'  => 'academic-cap',
            'url'   => route('practitioner.certificates', ['locale' => 'en']),
        ];
    }
}
```

- [ ] **Step 4: Create `app/Notifications/CouncilInvitation.php`**

```php
<?php

namespace App\Notifications;

use App\Models\AdvisoryCouncilMember;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CouncilInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AdvisoryCouncilMember $member) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $term = $this->member->term_start
            ? $this->member->term_start->format('M Y') . ($this->member->term_end ? ' – ' . $this->member->term_end->format('M Y') : ' onward')
            : '';

        return (new MailMessage)
            ->subject("You've been invited to the Clinical Validation Advisory Council")
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('In recognition of your validation work, you are invited to join the OPES Clinical Validation Advisory Council as ' . $this->member->title . '.')
            ->line($term ? 'Term: ' . $term . '.' : 'Welcome to the council.')
            ->action('View certificates', route('practitioner.certificates', ['locale' => 'en']))
            ->line('We look forward to your continued guidance.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.council_invitation',
            'title' => 'Advisory Council invitation',
            'body'  => 'You are invited to the Clinical Validation Advisory Council as ' . $this->member->title . '.',
            'icon'  => 'user-plus',
            'url'   => route('practitioner.certificates', ['locale' => 'en']),
        ];
    }
}
```

> Note: `AdvisoryCouncilMember` already casts `term_start`/`term_end` to `date` and `invited_at` to `datetime` (verified), so `->format()` works directly. No model edit needed.

- [ ] **Step 5: Wire `CertificateIssued` into `ValidationCertificate::issueFor`**

In `app/Models/ValidationCertificate.php` `issueFor()`, capture the created certificate and notify before returning:

```php
        $certificate = static::create([
            'cohort_member_id'    => $evaluation->cohort_member_id,
            'final_evaluation_id' => $evaluation->id,
            'score'               => $result['score'],
            'tier'                => $result['tier'],
            'issued_by'           => $issuedById,
            'issued_at'           => now(),
        ]);

        $certificate->cohortMember?->user?->notify(new \App\Notifications\CertificateIssued($certificate));

        return $certificate;
```

- [ ] **Step 6: Wire `CouncilInvitation` into the Filament invite action**

In `app/Filament/Resources/ValidationCertificateResource.php`, inside the `invite_to_council` action's `->action(function (ValidationCertificate $r, array $data) {...})`, capture the created member and notify the user:

```php
                    ->action(function (ValidationCertificate $r, array $data) {
                        $member = AdvisoryCouncilMember::create([
                            'user_id'                   => $r->cohortMember->user_id,
                            'validation_certificate_id' => $r->id,
                            'title'                     => $data['title'],
                            'term_start'                => $data['term_start'],
                            'term_end'                  => $data['term_end'] ?? null,
                            'status'                    => 'active',
                            'invited_by'                => auth()->id(),
                            'invited_at'                => now(),
                        ]);
                        $r->cohortMember->user?->notify(new \App\Notifications\CouncilInvitation($member));
                        Notification::make()->title('Practitioner invited to the Advisory Council.')->success()->send();
                    }),
```

- [ ] **Step 7: Run test to verify it passes**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=N2CertificationNotificationsTest`
Expected: PASS (2 tests).

- [ ] **Step 8: Run full suite**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test`
Expected: all green (456 + 2 = 458, 0 failures).

- [ ] **Step 9: Commit**

```bash
git add app/Notifications/CertificateIssued.php app/Notifications/CouncilInvitation.php app/Models/ValidationCertificate.php app/Filament/Resources/ValidationCertificateResource.php tests/Feature/N2CertificationNotificationsTest.php
git commit -m "feat(notifications): notify practitioners on certificate issued + council invitation (N2)"
```

---

## Final verification (N2)

1. **Full suite green:** `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test` — expect 450 prior + 8 new = ~458, 0 failures.
2. **Manual trace (optional):** the 7 events fire at: issue submit (portal), clinical decision, product decision, dev-task fixed→retest, issue close, certificate issue, council invite — each notifying the reporter/practitioner via mail + feed.
3. **Then:** finishing-a-development-branch → merge `feat/notifications-n2` to main (verify suite on merged main), and continue to N3.
