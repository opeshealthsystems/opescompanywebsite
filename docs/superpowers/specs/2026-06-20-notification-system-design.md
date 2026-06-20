# Platform Notification System — Design Spec

> **For agentic workers:** REQUIRED SUB-SKILL: Use `superpowers:subagent-driven-development` or `superpowers:executing-plans` to implement the resulting plan(s) task-by-task.

**Goal:** Deliver branded **email + in-app** notifications for every actionable event across the OPES platform. Build a reusable notification spine (N1), then wire event batches (N2–N4) onto it; the 21 existing email-only Mailables are backfilled into the feed later (N5).

**Architecture:** Laravel Notifications with `mail` + `database` channels. One **branded mail theme** (customised Laravel markdown components) renders every notification email — no per-event HTML. In-app notifications persist to the standard `notifications` table and surface through a shared **bell + feed** in the web portal layouts. Each event is a small `App\Notifications\*` class supplying its email (`toMail`) and feed payload (`toArray`).

**Tech stack:** Laravel 13.8 / PHP 8.3 / Filament v3 (admin keeps its own alerts) / Blade + Tailwind / MySQL (prod) / SQLite (tests). Brand: dark header `#0f172a`, accent `#00C896`.

---

## Decomposition (multi-sub-project initiative)

| Sub-project | Scope | Status |
|---|---|---|
| **N1 — Infrastructure** (this spec's build target) | Notifications setup, `notifications` table, branded mail theme, shared bell + feed UI in web portals, base convention, 2 pilot events | build first |
| **N2 — Validation Hub events** (~8) | placed-in-cohort, issue submitted, clinical decision, product decision, ready-for-retest, closed, certificate issued, council invite | next plan |
| **N3 — Tester + Partner** (~7) | tester app received/approved/rejected + assignment; partner app received/approved/rejected | next plan |
| **N4 — HR + Accounting + CRM + Auth** | leave submitted/approved/rejected; payment receipt / quote sent / credit-note; demo-request confirmation; branded password reset; account-deactivated | next plan |
| **N5 — Backfill (optional)** | add `database` channel to the 21 existing Mailables so old events join the feed | optional |

Each of N2–N4 is a batch of `Notification` classes + their wiring + tests, plugging into N1. Messaging for all of them is drafted in **Section 4** of this spec.

**Decisions locked:** full coverage of all gaps; email + in-app; new events via Notifications now; existing 21 Mailables stay email-only until N5.

---

## Section 1: N1 — Infrastructure

### 1.1 Branded mail theme (one-time, DRY)
- Publish Laravel's mail components: `php artisan vendor:publish --tag=laravel-mail` → `resources/views/vendor/mail/html/*`.
- Customise `themes/` + the `header`, `footer`, `button`, `panel` components to the OPES brand (dark `#0f172a` header band with `#00C896` wordmark, green CTA button, slate footer with company line). Register the theme in `config/mail.php` (`'markdown' => ['theme' => 'opes']`) or per-message `->theme('opes')`.
- Result: every notification email built with `MailMessage` (`->greeting()->line()->action()->line()`) renders branded automatically. **Zero per-event HTML files.**

### 1.2 Notification convention
Each event is a class in `app/Notifications/`, e.g.:
```php
class PlacedInCohort extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Cohort $cohort) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have been placed in a validation cohort')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('You have been placed in the '.$this->cohort->name.' cohort ('.$this->cohort->specialty.').')
            ->action('Open the Validation Hub', route('practitioner.validation.dashboard', ['locale' => 'en']))
            ->line('Thank you for helping validate OPES Health software.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.placed_in_cohort',
            'title' => 'Placed in cohort',
            'body'  => 'You joined the '.$this->cohort->name.' cohort.',
            'icon'  => 'clipboard-check',
            'url'   => route('practitioner.validation.dashboard', ['locale' => 'en']),
        ];
    }
}
```
- Sent via `$user->notify(new PlacedInCohort($cohort))` at the trigger site (controller or on-model action), or `Notification::send($users, ...)` for multiple recipients.
- `ShouldQueue` so sends don't block the request (the app already queues mail).
- A consistent `toArray()` shape — `type, title, body, icon, url` — so the feed renders uniformly.

### 1.3 Storage
- `php artisan notifications:table` → migration creating the standard `notifications` table (uuid id, type, notifiable morph, data json, read_at, timestamps). `User` already uses `Notifiable` (Laravel default) — confirm the trait is present; add if missing.

### 1.4 Recipient routing
- Per event (each Notification is sent to the specific user[s]). Admin-facing alerts keep using the existing `AdminNotifier` (Filament) — not replaced here.

---

## Section 2: In-app feed UI

### 2.1 Routes (web portal group, locale-prefixed)
```php
Route::get('/notifications',                 [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/{id}/read',      [NotificationController::class, 'markRead'])->name('notifications.read');
Route::post('/notifications/read-all',       [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
```
Placed once in the shared authenticated web area so all portal roles (practitioner, tester, customer, manager, hr, accountant) reach them. Names resolve under the existing `{locale}` group.

### 2.2 `App\Http\Controllers\NotificationController`
- `index()`: paginate `auth()->user()->notifications()`; render `notifications.index`.
- `markRead($locale, $id)`: mark the one notification read; redirect to its `data['url']` (or back).
- `markAllRead()`: `auth()->user()->unreadNotifications->markAsRead()`; back with flash.

### 2.3 Bell component
- `resources/views/components/notification-bell.blade.php` — a bell icon with the unread count (`auth()->user()->unreadNotifications()->count()`), a dropdown of the latest 6 (title, body, relative time, link), and a "View all" → notifications page + "Mark all read".
- Included in each web portal layout header (`components/layouts/{practitioner,tester,customer,manager,hr,accountant}.blade.php`) via `<x-notification-bell />`. One component, six include points.

### 2.4 Notifications page
- `resources/views/notifications/index.blade.php` — wraps the appropriate portal layout (or a shared minimal auth layout), lists paginated notifications with icon/title/body/time, read/unread styling, per-item "mark read" + "mark all read".

---

## Section 3: N1 pilot events (full build in N1)

Two real events wired end-to-end to prove email + database + feed:

| Event | Class | Recipient | Trigger site |
|---|---|---|---|
| Placed in cohort | `App\Notifications\PlacedInCohort` | the placed practitioner (User) | `PlaceInCohortAction` (creates CohortMember) |
| Account deactivated | `App\Notifications\AccountDeactivated` | the user being deactivated | when `UserResource` edit sets `is_active=false` (an EditUser `afterSave` hook or model observer) |

**Messaging — PlacedInCohort:** subject "You've been placed in a validation cohort"; greeting by name; body: which cohort + specialty + that they can now log daily sessions and report issues; CTA "Open the Validation Hub".
**Messaging — AccountDeactivated:** subject "Your OPES account has been deactivated"; body: the account is deactivated and they can no longer sign in; contact support to restore; no CTA (or "Contact support" mailto). (Pairs with the login `is_active` block already shipped.)

---

## Section 4: Event catalog + drafted messaging (N2–N4)

Each row: **recipient · trigger · subject · body summary · CTA**. (Emails use the branded MailMessage; feed uses the same title/body.)

### N2 — Clinical Validation Hub
1. **Issue submitted** · reporter · on `IssueReport` create · "We received your issue report" · "Your report '{title}' was submitted and is awaiting clinical review." · "View issue".
2. **Clinical review decision** · reporter · `recordClinicalReview` · "Your issue passed clinical review" / "needs more information" / "was not accepted" (by decision) · the decision + reviewer note · "View issue".
3. **Product decision** · reporter · `recordProductReview` · "Your issue was {accepted|sent to development|marked duplicate|rejected}" · decision + note · "View issue".
4. **Ready for retest** · reporter · `DeveloperTask::markFixed` → issue `ready_for_retest` · "Your reported issue has been fixed — please retest" · ask them to verify the fix and submit a retest · "Retest now".
5. **Issue closed** · reporter · `closeIssue` · "Your issue has been closed" · outcome summary · "View issue".
6. **Certificate issued** · practitioner · `ValidationCertificate::issueFor` · "Your Clinical Validation certificate is ready" · tier + score + download link · "Download certificate".
7. **Council invitation** · practitioner · AdvisoryCouncilMember created · "You've been invited to the Clinical Validation Advisory Council" · title + term + acknowledgement · "View certificates".
8. **(admin) New issue submitted** · admins · on submit · keep `AdminNotifier` (Filament) — no email change.

### N3 — Tester + Partner
9. **Tester application received** · applicant · `TesterApplicationController@submit` · "We received your tester application" · under review · "Visit OPES".
10. **Tester application approved** · applicant (now tester) · admin approves · "You're approved as an OPES tester" · how to access the tester portal · "Open tester portal".
11. **Tester application rejected** · applicant · admin rejects · "Update on your tester application" · courteous decline + reason if any · none.
12. **New tester assignment** · tester · `TesterAssignment` assigned · "You have a new testing assignment" · product + due date · "View assignment".
13. **Partner application received** · applicant · `PartnerApplicationController@submit` · "We received your partnership application" · under review · "Visit OPES".
14. **Partner application approved** · applicant · admin approves · "Your partnership with OPES is approved" · next steps · "Visit OPES".
15. **Partner application rejected** · applicant · admin rejects · "Update on your partnership application" · courteous decline · none.

### N4 — HR + Accounting + CRM + Auth
16. **Leave request submitted** · manager + HR · employee submits leave · "New leave request from {employee}" · dates + type · "Review in admin".
17. **Leave approved** · employee · manager/HR approves · "Your leave request was approved" · dates · "View leave".
18. **Leave rejected** · employee · manager/HR rejects · "Your leave request was declined" · dates + reason · "View leave".
19. **Payment received / receipt** · customer · payment recorded on invoice · "We received your payment" · amount + invoice # · "View invoice".
20. **Quote sent** · customer/lead · quote issued · "Your quote from OPES Health Systems" · quote summary · "View quote".
21. **Credit note issued** · customer · credit note created · "A credit note has been issued" · amount + reference · "View".
22. **Demo request confirmation** · requester · `LeadController`/demo form submit · "Thanks for requesting a demo" · we'll be in touch · "Visit OPES". (Admin still gets `LeadNotification`.)
23. **Branded password reset** · user · `Password::sendResetLink` · "Reset your OPES password" · branded version of the reset link (custom `ResetPassword` notification using the OPES theme) · "Reset password".
24. **Account deactivated** · already built in N1 pilot.

> Recipients with no portal login (tester/partner applicants pre-approval, leads) receive **email only** (no in-app feed). The `database` channel applies to events whose recipient is a logged-in portal user.

---

## Section 5: Testing

- **N1:** `Notification::fake()` — placing a practitioner in a cohort notifies that user with `PlacedInCohort`; deactivating a user notifies them. A real (non-faked) send writes a row to `notifications`; the feed page renders it; `markRead` flips `read_at`; `markAllRead` clears unread. Bell unread count reflects state. Branded mail renders (assert `MailMessage` subject/lines, or render the theme without error).
- **N2–N4 (their own plans):** each event's trigger sends the right Notification to the right recipient (`Notification::fake()` + `assertSentTo`), and the `toArray` payload shape is correct.
- Existing suite (441) stays green.

### Success criteria (N1)
| Area | Metric |
|---|---|
| Channels | mail + database working via Laravel Notifications |
| Theme | one branded mail theme; notifications render branded, no per-event HTML |
| Feed | bell + dropdown + page across the 6 web portal layouts; mark-read / mark-all-read |
| Pilot | PlacedInCohort + AccountDeactivated wired, emailed, fed, tested |
| Regression | existing 441 tests green |

---

## Appendix: recipient-has-no-login rule
Tester/partner applicants (pre-approval) and CRM leads are not portal users → those notifications use **`via() = ['mail']` only**. Once a tester is approved (gains a login), subsequent events (assignments) use `['mail','database']`.
