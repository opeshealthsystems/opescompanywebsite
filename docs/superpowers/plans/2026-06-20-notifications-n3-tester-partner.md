# Notifications N3 — Tester + Partner Events Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans (inline) to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Wire branded email (+ in-app where the recipient has a login) notifications for the 7 tester/partner application lifecycle events onto the N1 spine.

**Architecture:** Tester and partner applications are **standalone records** (no `User` relation) edited through Filament — admins change the `status` field, there is no approve/reject button and no auto-created login. So:
- **Received** notifications fire in the public **submit controllers** (`TesterApplicationController@submit`, `PartnerApplicationController@submit`) as **on-demand mail** to the applicant's email.
- **Approved / Rejected** notifications fire via **model observers** (`#[ObservedBy]`) watching `status` transitions on `updated` — mirroring N1's `UserObserver` for `AccountDeactivated`.
- **New tester assignment** fires via an observer on `TesterAssignment` `created` — its recipient (`assigned_to`) is a real tester `User`, so it uses email + in-app.

**Dynamic channel rule:** every applicant-facing notification declares
`via()` = `$notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail','database']`.
Applicants without a login get **email only**; if a `User` already exists for that email (e.g. an approved tester who has a portal account) they additionally get the in-app feed row. Because `toMail()` cannot read `$notifiable->name` for an anonymous recipient, **the display name is passed into the notification constructor**.

**Tech Stack:** Laravel 13.8 / PHP 8.3 / Filament v3 / SQLite (tests). PHP binary `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`. Brand mail theme already published (N1). Suite currently green at 458.

---

## Conventions (verified)

- **Notification shape:** `extends Notification implements ShouldQueue`, `use Queueable;`, constructor promotes scalars/models, dynamic `via()` as above, `toMail()` branded `MailMessage`, `toArray()` only matters for the database channel (still safe to define).
- **On-demand send:** `\Illuminate\Support\Facades\Notification::route('mail', $email)->notify($notification);`
- **Deliver-to-user-if-exists helper** (in the application observers):
  ```php
  $user = \App\Models\User::where('email', $app->email)->first();
  $user
      ? $user->notify($notification)
      : \Illuminate\Support\Facades\Notification::route('mail', $app->email)->notify($notification);
  ```
- **Observer registration:** `#[\Illuminate\Database\Eloquent\Attributes\ObservedBy(\App\Observers\XObserver::class)]` on the model class (Laravel 11+; same as N1's `User`).
- **CTA routes (always pass `['locale' => 'en', ...]`):**
  - Home: `route('home', ['locale' => 'en'])` — "Visit OPES".
  - Tester portal: `route('tester.dashboard', ['locale' => 'en'])` — "Open tester portal".
  - Assignment: `route('tester.assignments.show', ['locale' => 'en', 'id' => $assignment->id])` — "View assignment".
- **Status vocab:** TesterApplication `['pending','accepted','rejected','active']`; PartnerApplication `['pending','reviewing','approved','rejected']`. Approval = tester→`accepted`, partner→`approved`.
- **Models lack `HasFactory`** — build records in tests with `::create([...])` supplying the validated columns.
- **Tests:** `Notification::fake()`. Use `assertSentOnDemand(X::class)` for email-only applicant sends, `assertSentTo($user, X::class)` when a `User` receives it. `setUp()` seeds `RolePermissionSeeder` + forgets permission cache.
- **Execution discipline:** PowerShell git `-m "..."`; stage explicit paths; `--filter` then full suite before each commit.

---

## Task 1 — Tester application lifecycle (received → approved → rejected)

**Files:**
- Create: `app/Notifications/TesterApplicationReceived.php`
- Create: `app/Notifications/TesterApplicationApproved.php`
- Create: `app/Notifications/TesterApplicationRejected.php`
- Create: `app/Observers/TesterApplicationObserver.php`
- Modify: `app/Models/TesterApplication.php` (add `#[ObservedBy]`)
- Modify: `app/Http/Controllers/TesterApplicationController.php` (fire received)
- Test: `tests/Feature/N3TesterNotificationsTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\TesterApplication;
use App\Models\User;
use App\Notifications\TesterApplicationApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N3TesterNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function applicationData(array $overrides = []): array
    {
        return array_merge([
            'name'             => 'Dr Ada Eze',
            'email'            => 'ada@example.com',
            'profession'       => 'doctor',
            'country'          => 'CM',
            'years_experience' => 6,
            'motivation'       => str_repeat('m', 40),
            'status'           => 'pending',
        ], $overrides);
    }

    public function test_submitting_a_tester_application_emails_the_applicant(): void
    {
        Notification::fake();

        $this->post('/en/join-testers', [
            'name'             => 'Dr Ada Eze',
            'email'            => 'ada@example.com',
            'profession'       => 'doctor',
            'country'          => 'CM',
            'years_experience' => 6,
            'motivation'       => str_repeat('m', 40),
        ])->assertRedirect();

        Notification::assertSentOnDemand(\App\Notifications\TesterApplicationReceived::class);
    }

    // Note: TesterApplicationReceived / TesterApplicationRejected are referenced by FQCN inline
    // (no top-of-file import needed); only TesterApplicationApproved is imported above.

    public function test_accepting_an_application_notifies_an_existing_user_in_app(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'ada@example.com']);
        $user->assignRole('tester');
        $app  = TesterApplication::create($this->applicationData());

        $app->update(['status' => 'accepted']);

        Notification::assertSentTo($user, TesterApplicationApproved::class);
    }

    public function test_rejecting_an_application_emails_the_applicant_on_demand(): void
    {
        Notification::fake();
        $app = TesterApplication::create($this->applicationData(['email' => 'noaccount@example.com']));

        $app->update(['status' => 'rejected']);

        Notification::assertSentOnDemand(\App\Notifications\TesterApplicationRejected::class);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=N3TesterNotificationsTest`
Expected: FAIL — `Class "App\Notifications\TesterApplicationReceived" not found`.

- [ ] **Step 3: Create `app/Notifications/TesterApplicationReceived.php`**

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TesterApplicationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $name) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('We received your tester application')
            ->greeting('Hello ' . $this->name . ',')
            ->line('Thank you for applying to the OPES Health tester program. Your application is under review.')
            ->line('We will be in touch once a decision has been made.')
            ->action('Visit OPES', route('home', ['locale' => 'en']));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'tester.application_received',
            'title' => 'Tester application received',
            'body'  => 'Your tester application is under review.',
            'icon'  => 'beaker',
            'url'   => route('home', ['locale' => 'en']),
        ];
    }
}
```

- [ ] **Step 4: Create `app/Notifications/TesterApplicationApproved.php`**

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TesterApplicationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $name) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("You're approved as an OPES tester")
            ->greeting('Hello ' . $this->name . ',')
            ->line('Congratulations! Your tester application has been approved.')
            ->line('You can now access the OPES tester portal to receive and complete testing assignments.')
            ->action('Open tester portal', route('tester.dashboard', ['locale' => 'en']));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'tester.application_approved',
            'title' => 'Tester application approved',
            'body'  => 'Welcome to the OPES tester program.',
            'icon'  => 'beaker',
            'url'   => route('tester.dashboard', ['locale' => 'en']),
        ];
    }
}
```

- [ ] **Step 5: Create `app/Notifications/TesterApplicationRejected.php`**

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TesterApplicationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $name, public ?string $reason = null) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Update on your tester application')
            ->greeting('Hello ' . $this->name . ',')
            ->line('Thank you for your interest in the OPES Health tester program. After review, we are unable to move forward with your application at this time.');

        if ($this->reason) {
            $mail->line('Note: ' . $this->reason);
        }

        return $mail->line('We appreciate the time you took to apply and wish you the best.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'tester.application_rejected',
            'title' => 'Tester application update',
            'body'  => 'Your tester application was not accepted.',
            'icon'  => 'beaker',
            'url'   => null,
        ];
    }
}
```

- [ ] **Step 6: Create `app/Observers/TesterApplicationObserver.php`**

```php
<?php

namespace App\Observers;

use App\Models\TesterApplication;
use App\Models\User;
use App\Notifications\TesterApplicationApproved;
use App\Notifications\TesterApplicationRejected;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Notification;

class TesterApplicationObserver
{
    public function updated(TesterApplication $app): void
    {
        if (! $app->wasChanged('status')) {
            return;
        }

        match ($app->status) {
            'accepted' => $this->notifyApplicant($app, new TesterApplicationApproved($app->name)),
            'rejected' => $this->notifyApplicant($app, new TesterApplicationRejected($app->name, $app->admin_notes)),
            default    => null,
        };
    }

    private function notifyApplicant(TesterApplication $app, BaseNotification $notification): void
    {
        $user = User::where('email', $app->email)->first();

        $user
            ? $user->notify($notification)
            : Notification::route('mail', $app->email)->notify($notification);
    }
}
```

- [ ] **Step 7: Register the observer on the model**

In `app/Models/TesterApplication.php`, add the attribute import and annotation:

```php
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(\App\Observers\TesterApplicationObserver::class)]
class TesterApplication extends Model
```

- [ ] **Step 8: Fire `TesterApplicationReceived` from the controller**

In `app/Http/Controllers/TesterApplicationController.php` `submit()`, after the `$app = TesterApplication::create(...)` block and before the admin `Notification::make()` block, add:

```php
        \Illuminate\Support\Facades\Notification::route('mail', $app->email)
            ->notify(new \App\Notifications\TesterApplicationReceived($app->name));
```

- [ ] **Step 9: Run test to verify it passes**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=N3TesterNotificationsTest`
Expected: PASS (3 tests).

- [ ] **Step 10: Run full suite**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test`
Expected: all green (458 + 3 = 461).

- [ ] **Step 11: Commit**

```bash
git add app/Notifications/TesterApplicationReceived.php app/Notifications/TesterApplicationApproved.php app/Notifications/TesterApplicationRejected.php app/Observers/TesterApplicationObserver.php app/Models/TesterApplication.php app/Http/Controllers/TesterApplicationController.php tests/Feature/N3TesterNotificationsTest.php
git commit -m "feat(notifications): tester application received/approved/rejected emails (N3)"
```

---

## Task 2 — New tester assignment

**Files:**
- Create: `app/Notifications/NewTesterAssignment.php`
- Create: `app/Observers/TesterAssignmentObserver.php`
- Modify: `app/Models/TesterAssignment.php` (add `#[ObservedBy]`)
- Test: `tests/Feature/N3AssignmentNotificationTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\TesterAssignment;
use App\Models\User;
use App\Notifications\NewTesterAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N3AssignmentNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_creating_an_assignment_notifies_the_tester(): void
    {
        Notification::fake();
        $tester = User::factory()->create();
        $tester->assignRole('tester');
        $admin  = User::factory()->create();

        TesterAssignment::create([
            'assigned_to'  => $tester->id,
            'assigned_by'  => $admin->id,
            'product_slug' => 'ohos',
            'product_name' => 'OPES Health OS',
            'title'        => 'Test the triage workflow',
            'status'       => 'pending',
        ]);

        Notification::assertSentTo($tester, NewTesterAssignment::class);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=N3AssignmentNotificationTest`
Expected: FAIL — `Class "App\Notifications\NewTesterAssignment" not found`.

- [ ] **Step 3: Create `app/Notifications/NewTesterAssignment.php`**

```php
<?php

namespace App\Notifications;

use App\Models\TesterAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTesterAssignment extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TesterAssignment $assignment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('You have a new testing assignment')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been assigned a new testing task: "' . $this->assignment->title . '" on ' . $this->assignment->product_name . '.');

        if ($this->assignment->due_date) {
            $mail->line('Due date: ' . $this->assignment->due_date->format('M j, Y') . '.');
        }

        return $mail->action('View assignment', route('tester.assignments.show', ['locale' => 'en', 'id' => $this->assignment->id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'tester.new_assignment',
            'title' => 'New testing assignment',
            'body'  => $this->assignment->title . ' · ' . $this->assignment->product_name,
            'icon'  => 'clipboard-document-list',
            'url'   => route('tester.assignments.show', ['locale' => 'en', 'id' => $this->assignment->id]),
        ];
    }
}
```

- [ ] **Step 4: Create `app/Observers/TesterAssignmentObserver.php`**

```php
<?php

namespace App\Observers;

use App\Models\TesterAssignment;
use App\Notifications\NewTesterAssignment;

class TesterAssignmentObserver
{
    public function created(TesterAssignment $assignment): void
    {
        $assignment->tester?->notify(new NewTesterAssignment($assignment));
    }
}
```

- [ ] **Step 5: Register the observer on the model**

In `app/Models/TesterAssignment.php`, add:

```php
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
// ...existing use lines...

#[ObservedBy(\App\Observers\TesterAssignmentObserver::class)]
class TesterAssignment extends Model
```

- [ ] **Step 6: Run test to verify it passes**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=N3AssignmentNotificationTest`
Expected: PASS (1 test).

- [ ] **Step 7: Run full suite**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test`
Expected: all green (461 + 1 = 462). Watch for any existing test that creates a `TesterAssignment` and asserts mail state — if one fails because a notification now fires, it should be using `Notification::fake()`; this is expected and harmless with the array mailer. If a real failure appears, inspect that test.

- [ ] **Step 8: Commit**

```bash
git add app/Notifications/NewTesterAssignment.php app/Observers/TesterAssignmentObserver.php app/Models/TesterAssignment.php tests/Feature/N3AssignmentNotificationTest.php
git commit -m "feat(notifications): notify tester on new assignment (N3)"
```

---

## Task 3 — Partner application lifecycle (received → approved → rejected)

Partners never receive a portal login, so all three are **email-only on-demand** sends. `via()` still uses the dynamic rule for safety.

**Files:**
- Create: `app/Notifications/PartnerApplicationReceived.php`
- Create: `app/Notifications/PartnerApplicationApproved.php`
- Create: `app/Notifications/PartnerApplicationRejected.php`
- Create: `app/Observers/PartnerApplicationObserver.php`
- Modify: `app/Models/PartnerApplication.php` (add `#[ObservedBy]`)
- Modify: `app/Http/Controllers/PartnerApplicationController.php` (fire received)
- Test: `tests/Feature/N3PartnerNotificationsTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\PartnerApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N3PartnerNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function applicationData(array $overrides = []): array
    {
        return array_merge([
            'organization_name' => 'Acme Health',
            'contact_name'      => 'Bola Smith',
            'email'             => 'partner@example.com',
            'country'           => 'CM',
            'partner_type'      => 'hospital',
            'description'       => str_repeat('d', 40),
            'status'            => 'pending',
        ], $overrides);
    }

    public function test_submitting_a_partner_application_emails_the_applicant(): void
    {
        Notification::fake();

        $this->post('/en/become-a-partner', [
            'organization_name' => 'Acme Health',
            'contact_name'      => 'Bola Smith',
            'email'             => 'partner@example.com',
            'country'           => 'CM',
            'partner_type'      => 'hospital',
            'description'       => str_repeat('d', 40),
        ])->assertRedirect();

        Notification::assertSentOnDemand(\App\Notifications\PartnerApplicationReceived::class);
    }

    public function test_approving_a_partner_application_emails_the_applicant(): void
    {
        Notification::fake();
        $app = PartnerApplication::create($this->applicationData());

        $app->update(['status' => 'approved']);

        Notification::assertSentOnDemand(\App\Notifications\PartnerApplicationApproved::class);
    }

    public function test_rejecting_a_partner_application_emails_the_applicant(): void
    {
        Notification::fake();
        $app = PartnerApplication::create($this->applicationData());

        $app->update(['status' => 'rejected']);

        Notification::assertSentOnDemand(\App\Notifications\PartnerApplicationRejected::class);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=N3PartnerNotificationsTest`
Expected: FAIL — `Class "App\Notifications\PartnerApplicationReceived" not found`.

- [ ] **Step 3: Create `app/Notifications/PartnerApplicationReceived.php`**

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PartnerApplicationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $contactName, public string $organizationName) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('We received your partnership application')
            ->greeting('Hello ' . $this->contactName . ',')
            ->line('Thank you for your interest in partnering with OPES Health Systems on behalf of ' . $this->organizationName . '.')
            ->line('Your application is under review and our team will be in touch.')
            ->action('Visit OPES', route('home', ['locale' => 'en']));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'partner.application_received',
            'title' => 'Partnership application received',
            'body'  => 'Your partnership application is under review.',
            'icon'  => 'users',
            'url'   => route('home', ['locale' => 'en']),
        ];
    }
}
```

- [ ] **Step 4: Create `app/Notifications/PartnerApplicationApproved.php`**

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PartnerApplicationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $contactName, public string $organizationName) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your partnership with OPES is approved')
            ->greeting('Hello ' . $this->contactName . ',')
            ->line('We are delighted to approve the partnership with ' . $this->organizationName . '.')
            ->line('Our partnerships team will reach out shortly with next steps.')
            ->action('Visit OPES', route('home', ['locale' => 'en']));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'partner.application_approved',
            'title' => 'Partnership approved',
            'body'  => 'Your partnership with OPES has been approved.',
            'icon'  => 'users',
            'url'   => route('home', ['locale' => 'en']),
        ];
    }
}
```

- [ ] **Step 5: Create `app/Notifications/PartnerApplicationRejected.php`**

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PartnerApplicationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $contactName, public string $organizationName, public ?string $reason = null) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Update on your partnership application')
            ->greeting('Hello ' . $this->contactName . ',')
            ->line('Thank you for your interest in partnering with OPES Health Systems. After review, we are unable to move forward with the application from ' . $this->organizationName . ' at this time.');

        if ($this->reason) {
            $mail->line('Note: ' . $this->reason);
        }

        return $mail->line('We appreciate your interest and wish ' . $this->organizationName . ' continued success.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'partner.application_rejected',
            'title' => 'Partnership application update',
            'body'  => 'Your partnership application was not accepted.',
            'icon'  => 'users',
            'url'   => null,
        ];
    }
}
```

- [ ] **Step 6: Create `app/Observers/PartnerApplicationObserver.php`**

```php
<?php

namespace App\Observers;

use App\Models\PartnerApplication;
use App\Notifications\PartnerApplicationApproved;
use App\Notifications\PartnerApplicationRejected;
use Illuminate\Support\Facades\Notification;

class PartnerApplicationObserver
{
    public function updated(PartnerApplication $app): void
    {
        if (! $app->wasChanged('status')) {
            return;
        }

        $notification = match ($app->status) {
            'approved' => new PartnerApplicationApproved($app->contact_name, $app->organization_name),
            'rejected' => new PartnerApplicationRejected($app->contact_name, $app->organization_name, $app->admin_notes),
            default    => null,
        };

        if ($notification) {
            Notification::route('mail', $app->email)->notify($notification);
        }
    }
}
```

- [ ] **Step 7: Register the observer on the model**

In `app/Models/PartnerApplication.php`:

```php
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(\App\Observers\PartnerApplicationObserver::class)]
class PartnerApplication extends Model
```

- [ ] **Step 8: Fire `PartnerApplicationReceived` from the controller**

In `app/Http/Controllers/PartnerApplicationController.php` `submit()`, after `$app = PartnerApplication::create(...)` and before the admin `Notification::make()` block:

```php
        \Illuminate\Support\Facades\Notification::route('mail', $app->email)
            ->notify(new \App\Notifications\PartnerApplicationReceived($app->contact_name, $app->organization_name));
```

- [ ] **Step 9: Run test to verify it passes**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=N3PartnerNotificationsTest`
Expected: PASS (3 tests).

- [ ] **Step 10: Run full suite**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test`
Expected: all green (462 + 3 = 465).

- [ ] **Step 11: Commit**

```bash
git add app/Notifications/PartnerApplicationReceived.php app/Notifications/PartnerApplicationApproved.php app/Notifications/PartnerApplicationRejected.php app/Observers/PartnerApplicationObserver.php app/Models/PartnerApplication.php app/Http/Controllers/PartnerApplicationController.php tests/Feature/N3PartnerNotificationsTest.php
git commit -m "feat(notifications): partner application received/approved/rejected emails (N3)"
```

---

## Final verification (N3)

1. **Full suite green:** expect 458 prior + 7 new = ~465, 0 failures.
2. **Channel correctness:** applicant-only events email on-demand (no feed row); tester-with-login and assignment events also create a feed row.
3. **Then:** finishing-a-development-branch → merge `feat/notifications-n3` to main (verify suite on merged main), and continue to N4.
