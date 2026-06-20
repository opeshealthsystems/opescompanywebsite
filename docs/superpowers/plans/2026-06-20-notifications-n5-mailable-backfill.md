# Notifications N5 — Mailable Feed Backfill Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans (inline). Steps use checkbox (`- [ ]`) syntax.

**Goal:** Make the ~20 existing email-only events (the `app/Mail/*` Mailables) also appear in the in-app notification feed, without changing any email behaviour.

**Architecture — "mirror":** Keep every existing `Mail::to($email)->queue(new XMailable(...))` call **exactly as-is** (zero email regression). Immediately after it, add one line that writes a feed row via a single reusable, database-only notification: `$user?->notify(new FeedEntry(type, title, body, icon, url))`. The recipient `User` is resolved from the model already in scope (`->customer`, `->user`, `->practitioner`, `auth()->user()`, or `User::where('email',$email)->first()` for raw-email sites). Feed-only recipients that have no web-portal bell (admins) are **not** backfilled.

**Why mirror, not per-event Notification wrappers:** these emails already work; the feed only needs `title/body/icon/url`, which the bell renders uniformly. One `FeedEntry` class + ~20 one-line additions is the lowest-risk way to get full feed coverage. (The 24 N1–N4 events stay as typed Notifications — those were designed for both channels from the start.)

**Tech Stack:** Laravel 13.8 / PHP 8.3 / Filament v3 / SQLite (tests). PHP `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`. Suite green at 474.

---

## Not backfilled (documented)

- **`LeadNotification`** → sent to admins, who use the **Filament panel** (its own database notifications via `sendToDatabase`/`AdminNotifier`), not the 6 web-portal bell. No web feed exists for it. Left email-only.

## Recipient + URL map (all locale-prefixed → `['locale' => 'en', ...]`)

| Mailable | Site | Recipient `User` | type / title / body / icon / url |
|---|---|---|---|
| InvoiceIssued | `InvoiceResource/Pages/ViewInvoice.php` mark_sent | `$this->record->customer` | `accounting.invoice_issued` · "Invoice issued" · "Invoice {invoice_number} is ready to view." · `document-text` · `customer.invoices.show {id}` |
| TicketCreated | `Customer/TicketController@store` | `$user` | `support.ticket_created` · "Support ticket created" · "Your ticket \"{subject}\" was created." · `lifebuoy` · `customer.tickets.show {id}` |
| TicketReplied | `TicketResource/Pages/ViewTicket.php` reply | `$this->record->user` | `support.ticket_replied` · "New reply on your ticket" · "There's a new reply on \"{subject}\"." · `chat-bubble-left-right` · `customer.tickets.show {id}` |
| TicketStatusChanged | `ViewTicket.php` change_status | `$this->record->user` | `support.ticket_status` · "Ticket status updated" · "\"{subject}\" is now {status}." · `arrow-path` · `customer.tickets.show {id}` |
| LicenseIssued | `LicenseResource/Pages/CreateLicense.php` afterCreate | `$license->customer` | `licensing.issued` · "License issued" · "A license for {product_name} was issued." · `key` · `customer.licenses.show {id}` |
| ServiceRequestConfirmed | `ServiceRequestResource.php` action | `$record->customer` | `support.service_request` · "Service request received" · "Your service request was received." · `wrench` · `customer.service-requests.show {id}` |
| CourseEnrollmentConfirmed (customer) | `Customer/CourseController@enroll` | `auth()->user()` | `learning.enrolled` · "Enrolled in course" · "You enrolled in a course." · `academic-cap` · `customer.courses` |
| CourseCertificateIssued (customer) | `Customer/LessonController` | `auth()->user()` | `learning.certificate` · "Course certificate issued" · "Your course certificate is ready." · `academic-cap` · `customer.certificates` |
| WelcomeEmail (customer) | `RegisterController@register`; `UserResource` action; `CreateUser` page | `$user` / `$record` / `$this->record` | `account.welcome` · "Welcome to OPES" · "Welcome to OPES Health Systems." · `sparkles` · `customer.dashboard` |
| PractitionerWelcome | `RegisterController@register` | `$user` | `account.welcome` · "Welcome to OPES" · "Welcome to the OPES practitioner platform." · `sparkles` · `practitioner.dashboard` |
| PractitionerApplicationReceived | `Practitioner/ProgramController@apply` | `auth()->user()` | `practitioner.application_received` · "Application received" · "We received your application." · `clipboard-document` · `practitioner.applications` |
| PractitionerApplicationApproved | `PractitionerApplicationResource.php` approve | `$record->practitioner` | `practitioner.application_approved` · "Application approved" · "Your application was approved." · `check-circle` · `practitioner.applications` |
| PractitionerApplicationRejected | `PractitionerApplicationResource.php` reject | `$record->practitioner` | `practitioner.application_rejected` · "Application update" · "There is an update on your application." · `clipboard-document` · `practitioner.applications` |
| SuggestionResponded | `SuggestionResource.php` respond | `$record->user` | `practitioner.suggestion_responded` · "Response to your suggestion" · "Your suggestion received a response." · `light-bulb` · `practitioner.suggestions` |
| BugReportResponded | `PractitionerBugReportResource.php` respond | `$record->practitioner` | `practitioner.bug_report_responded` · "Response to your bug report" · "Your bug report received a response." · `bug-ant` · `practitioner.bug-reports.show {id}` |
| PayoutSettled | `Console/Commands/PollPayouts.php` | `$application->practitioner` | `practitioner.payout_settled` · "Payout settled" · "Your payout has been settled." · `banknotes` · `practitioner.dashboard` |
| CourseEnrollmentConfirmed (practitioner) | `Practitioner/CourseController@enroll` | `auth()->user()` | `learning.enrolled` · "Enrolled in course" · "You enrolled in a course." · `academic-cap` · `practitioner.courses` |
| CourseCertificateIssued (practitioner) | `Practitioner/LessonController`; `CourseResource/RelationManagers/EnrollmentsRelationManager.php` | `auth()->user()` / `$record->user` | `learning.certificate` · "Course certificate issued" · "Your course certificate is ready." · `academic-cap` · `practitioner.certificates` |
| PayrollProcessed | `PayrollRunResource/Pages/ViewPayrollRun.php` | `User::where('email',$email)->first()` | `hr.payroll_processed` · "Payslip available" · "Your latest payroll has been processed." · `banknotes` · `null` |
| TrainingExpiryWarning | `Console/Commands/SendTrainingExpiryWarnings.php` | `User::where('email',$email)->first()` | `hr.training_expiry` · "Training expiring soon" · "{title} expires in {days} days." · `academic-cap` · `null` |
| LicenseExpiryWarning | `Console/Commands/SendLicenseExpiryWarnings.php` | `$license->customer` | `licensing.expiry` · "License expiring soon" · "{product_name} expires in {days} days." · `key` · `customer.licenses.show {id}` |
| ContractExpiryWarning | `Console/Commands/SendContractExpiryWarnings.php` | `User::where('email',$email)->first()` | `crm.contract_expiry` · "Contract expiring soon" · "A contract expires in {days} days." · `document-text` · `null` |

> For sites that send a course-enrolment / certificate Mailable, the per-portal URL differs (customer vs practitioner) — use the route matching the controller's portal. `EnrollmentsRelationManager` (admin-issued certificate) notifies `$record->user`; point its url at the user's relevant certificates page if known, else `null`.

---

## Task 1 — `FeedEntry` notification + unit test

**Files:** Create `app/Notifications/FeedEntry.php`, `tests/Feature/FeedEntryNotificationTest.php`.

- [ ] **Step 1: Failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\FeedEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedEntryNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_entry_is_database_only_with_expected_payload(): void
    {
        $entry = new FeedEntry('accounting.invoice_issued', 'Invoice issued', 'Invoice INV-1 is ready.', 'document-text', '/en/customer/invoices/1');

        $this->assertEquals(['database'], $entry->via(new User()));
        $payload = $entry->toArray(new User());
        $this->assertSame('accounting.invoice_issued', $payload['type']);
        $this->assertSame('Invoice issued', $payload['title']);
        $this->assertSame('document-text', $payload['icon']);
        $this->assertSame('/en/customer/invoices/1', $payload['url']);
    }

    public function test_notifying_a_user_writes_a_feed_row(): void
    {
        $user = User::factory()->create();
        $user->notify(new FeedEntry('account.welcome', 'Welcome to OPES', 'Welcome.', 'sparkles', null));

        $this->assertEquals(1, $user->notifications()->count());
        $this->assertSame('Welcome to OPES', $user->notifications()->first()->data['title']);
    }
}
```

- [ ] **Step 2: Run → fail** (`--filter=FeedEntryNotificationTest`).
- [ ] **Step 3: Create `app/Notifications/FeedEntry.php`**

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * A reusable, database-only feed entry used to mirror existing Mailable emails
 * into the in-app notification feed (N5 backfill). The email still goes out via
 * its Mailable at the call site; this only writes the feed row.
 */
class FeedEntry extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $type,
        public string $title,
        public string $body,
        public string $icon = 'bell',
        public ?string $url = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => $this->type,
            'title' => $this->title,
            'body'  => $this->body,
            'icon'  => $this->icon,
            'url'   => $this->url,
        ];
    }
}
```

- [ ] **Step 4: Run → pass.** **Step 5: full suite** (474 + 2 = 476). **Step 6: commit**

```bash
git add app/Notifications/FeedEntry.php tests/Feature/FeedEntryNotificationTest.php
git commit -m "feat(notifications): reusable FeedEntry notification for Mailable feed backfill (N5)"
```

---

## Task 2 — Customer-facing backfills

At each customer site, after the existing `Mail::...->queue(...)`, add the feed line. Pattern (example, ViewInvoice mark_sent — inside the existing `if ($customerEmail)`):

```php
if ($customerEmail) {
    Mail::to($customerEmail)->queue(new InvoiceIssued($this->record->load('items')));
    $this->record->customer?->notify(new \App\Notifications\FeedEntry(
        'accounting.invoice_issued',
        'Invoice issued',
        'Invoice ' . $this->record->invoice_number . ' is ready to view.',
        'document-text',
        route('customer.invoices.show', ['locale' => 'en', 'id' => $this->record->id]),
    ));
}
```

Apply the same shape (recipient + payload from the map) at: `ViewInvoice` (invoice), `Customer/TicketController@store` (ticket created), `ViewTicket` reply + change_status (use `$this->record->user`), `CreateLicense` afterCreate (license), `ServiceRequestResource` action (service request), `Customer/CourseController@enroll` (enrol), `Customer/LessonController` (certificate), and the **customer** branch of `RegisterController@register` + `UserResource` action + `CreateUser` page (welcome).

**Tests** — `tests/Feature/N5CustomerBackfillTest.php`, covering the cleanly drivable controller sites:
- Customer creates a ticket (`POST /en/customer/tickets`) → the customer has a `support.ticket_created` feed row.
- Customer registers (`POST /en/register` with account_type customer) → `account.welcome` feed row. *(If register flow is awkward to assert, substitute enrolling in a course as the second case.)*

Drive each, then `assertDatabaseHas('notifications', ['type' => DatabaseNotification::class, ...])` or assert `$user->fresh()->notifications` contains the expected `data->type`. Run `--filter=N5CustomerBackfillTest`, then full suite, then commit:

```bash
git add app/Filament/Resources/InvoiceResource/Pages/ViewInvoice.php app/Http/Controllers/Customer/TicketController.php app/Filament/Resources/TicketResource/Pages/ViewTicket.php app/Filament/Resources/LicenseResource/Pages/CreateLicense.php app/Filament/Resources/ServiceRequestResource.php app/Http/Controllers/Customer/CourseController.php app/Http/Controllers/Customer/LessonController.php app/Http/Controllers/Auth/RegisterController.php app/Filament/Resources/UserResource.php app/Filament/Resources/UserResource/Pages/CreateUser.php tests/Feature/N5CustomerBackfillTest.php
git commit -m "feat(notifications): backfill customer email events into the in-app feed (N5)"
```

---

## Task 3 — Practitioner-facing backfills

Same pattern at: `RegisterController@register` (practitioner branch — practitioner welcome), `Practitioner/ProgramController@apply` (application received, `auth()->user()`), `PractitionerApplicationResource` approve + reject (`$record->practitioner`), `SuggestionResource` (`$record->user`), `PractitionerBugReportResource` (`$record->practitioner`), `Console/Commands/PollPayouts` (`$application->practitioner`), `Practitioner/CourseController@enroll` + `Practitioner/LessonController` (course), `CourseResource/RelationManagers/EnrollmentsRelationManager` (`$record->user`).

**Tests** — `tests/Feature/N5PractitionerBackfillTest.php`:
- Practitioner applies to a program (`POST /en/practitioner/programs/{program}/apply`) → `practitioner.application_received` feed row for that practitioner. *(Mirror the existing ProgramController test setup.)*

Run `--filter=N5PractitionerBackfillTest`, full suite, commit:

```bash
git add app/Http/Controllers/Auth/RegisterController.php app/Http/Controllers/Practitioner/ProgramController.php app/Filament/Resources/PractitionerApplicationResource.php app/Filament/Resources/SuggestionResource.php app/Filament/Resources/PractitionerBugReportResource.php app/Console/Commands/PollPayouts.php app/Http/Controllers/Practitioner/CourseController.php app/Http/Controllers/Practitioner/LessonController.php app/Filament/Resources/CourseResource/RelationManagers/EnrollmentsRelationManager.php tests/Feature/N5PractitionerBackfillTest.php
git commit -m "feat(notifications): backfill practitioner email events into the in-app feed (N5)"
```

> `RegisterController` is staged in both Task 2 and Task 3 (customer + practitioner branches). Commit it in Task 2 with both branches done, or split the edit; do not double-commit unstaged hunks.

---

## Task 4 — Staff + expiry backfills

Raw-email sites — resolve the `User` first. Pattern (e.g. `SendLicenseExpiryWarnings`):

```php
Mail::to($email)->queue(new LicenseExpiryWarning($license, $days));
$license->customer?->notify(new \App\Notifications\FeedEntry(
    'licensing.expiry',
    'License expiring soon',
    $license->product_name . ' expires in ' . $days . ' days.',
    'key',
    route('customer.licenses.show', ['locale' => 'en', 'id' => $license->id]),
));
```

For `PayrollProcessed`, `TrainingExpiryWarning`, `ContractExpiryWarning` — recipient is a raw `$email`; resolve `User::where('email', $email)->first()?->notify(new FeedEntry(...))` with `url => null` (no employee-facing portal page for these). Apply at `ViewPayrollRun.php`, `SendTrainingExpiryWarnings.php`, `SendLicenseExpiryWarnings.php`, `SendContractExpiryWarnings.php`.

**Test** — `tests/Feature/N5StaffBackfillTest.php`: construct a license owned by a customer `User`, run `SendLicenseExpiryWarnings` (or call its logic) and assert the customer has a `licensing.expiry` feed row. (If the command is awkward to drive in-test, assert the `FeedEntry` write through a thin direct call mirroring the command's resolved recipient.)

Run `--filter=N5StaffBackfillTest`, full suite, commit:

```bash
git add app/Filament/Resources/PayrollRunResource/Pages/ViewPayrollRun.php app/Console/Commands/SendTrainingExpiryWarnings.php app/Console/Commands/SendLicenseExpiryWarnings.php app/Console/Commands/SendContractExpiryWarnings.php tests/Feature/N5StaffBackfillTest.php
git commit -m "feat(notifications): backfill payroll + expiry warnings into the in-app feed (N5)"
```

---

## Final verification (N5)

1. **Full suite green** (≈ 474 + new tests, 0 failures).
2. **No email regression:** every `Mail::...->queue(...)` call is unchanged; only feed writes were added next to them.
3. **Coverage note:** controller-driven sites are asserted directly; Filament-action/page and console-command sites are identical one-line mirrors guarded by the full suite (note any not directly asserted).
4. **Then:** finishing-a-development-branch → merge `feat/notifications-n5` to main. Notification system fully complete (N1–N5).
