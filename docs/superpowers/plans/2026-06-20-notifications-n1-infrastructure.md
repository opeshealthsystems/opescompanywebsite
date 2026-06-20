# Notifications N1 — Infrastructure Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development or superpowers:executing-plans. Steps use checkbox (`- [ ]`) tracking.

**Goal:** Build the reusable notification spine — Laravel Notifications (email + in-app), a branded mail theme, the `notifications` table, a shared bell + feed across the 6 web portals — proven by 2 pilot events.

**Architecture:** Each event = an `App\Notifications\*` class with `via() = ['mail','database']`, a branded `MailMessage` (`toMail`), and a feed payload (`toArray`). In-app items persist to the standard `notifications` table and render through a shared `<x-notification-bell>` + a notifications page. Pilot events: `PlacedInCohort` (wired into `PlaceInCohortAction`) and `AccountDeactivated` (wired via a `UserObserver`).

**Tech Stack:** Laravel 13.8 / PHP 8.3 / Filament v3 / Blade. PHP binary: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`. Spec: `docs/superpowers/specs/2026-06-20-notification-system-design.md`. Baseline: 441 green. `User` already uses `Notifiable`.

---

## Conventions
- Notifications implement `ShouldQueue` + `use Queueable` (app already queues mail).
- Web portal routes are locale-prefixed → `route('name', ['locale' => app()->getLocale()])`.
- Tests: `Tests\Feature`, `use RefreshDatabase;`, `setUp()` → `parent::setUp(); app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions(); $this->seed(\Database\Seeders\RolePermissionSeeder::class);`. Use `Notification::fake()` for send assertions.
- Git: explicit paths only; never `git add -A`; never `taskkill`; don't touch MySQL/Apache.

## File map
| File | Task |
|---|---|
| `database/migrations/2026_06_20_500001_create_notifications_table.php` | 1 |
| `config/mail.php` (theme), `resources/views/vendor/mail/html/themes/opes.css`, `resources/views/vendor/mail/html/header.blade.php` | 2 |
| `app/Notifications/PlacedInCohort.php`, `app/Notifications/AccountDeactivated.php` | 3 |
| `app/Observers/UserObserver.php`, `app/Models/User.php` (ObservedBy), `app/Filament/Resources/PractitionerApplicationResource/Actions/PlaceInCohortAction.php` (notify) | 3 |
| `app/Http/Controllers/NotificationController.php`, `routes/web.php` | 4 |
| `resources/views/notifications/index.blade.php` | 4 |
| `resources/views/components/notification-bell.blade.php` + 6 portal layouts | 5 |

---

## Task 1: notifications table

**Files:** Create `database/migrations/2026_06_20_500001_create_notifications_table.php`; Test `tests/Feature/NotificationsTableTest.php`.

- [ ] **Step 1: Write the failing test**
```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NotificationsTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('notifications'));
        foreach (['id', 'type', 'notifiable_type', 'notifiable_id', 'data', 'read_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('notifications', $c), "notifications.$c");
        }
    }
}
```

- [ ] **Step 2: Run — FAIL.** `<php> artisan test --filter=NotificationsTableTest`

- [ ] **Step 3: Create the migration** (standard Laravel notifications schema)
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
```

- [ ] **Step 4: Run — PASS.** Full suite — 0 failures (~442).

- [ ] **Step 5: Commit**
```bash
git add database/migrations/2026_06_20_500001_create_notifications_table.php tests/Feature/NotificationsTableTest.php
git commit -m "feat(notifications): add notifications table"
```

---

## Task 2: branded mail theme

**Files:** Run `vendor:publish`; Modify `config/mail.php`; Create `resources/views/vendor/mail/html/themes/opes.css`; Modify `resources/views/vendor/mail/html/header.blade.php`. (Verified by Task 3's render test.)

- [ ] **Step 1: Publish the mail components**
```
<php> artisan vendor:publish --tag=laravel-mail
```
This creates `resources/views/vendor/mail/html/*` and `markdown/*`.

- [ ] **Step 2: Register the OPES theme** in `config/mail.php` — set the `markdown` block:
```php
    'markdown' => [
        'theme' => 'opes',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],
```
(If a `markdown` key already exists, change its `theme` to `'opes'` and ensure the `paths` entry is present.)

- [ ] **Step 3: Create `resources/views/vendor/mail/html/themes/opes.css`** — copy the published `themes/default.css` content, then override the brand tokens. The essential overrides (append at the end of the copied file so they win):
```css
/* OPES brand overrides */
.header a, .header { background-color: #0f172a; }
.header a { color: #00C896 !important; font-size: 20px; font-weight: 700; text-decoration: none; }
.button-primary, .button-success, .button { background-color: #00C896 !important; border-color: #00C896 !important; box-shadow: none; }
.body { background-color: #f8fafc; }
.content-cell h1 { color: #0f172a; }
.footer { color: #94a3b8; }
```
(The base file content stays; these rules override colors to the OPES palette.)

- [ ] **Step 4: Brand the header** — replace `resources/views/vendor/mail/html/header.blade.php` content with:
```blade
@props(['url'])
<tr>
<td class="header" style="background-color:#0f172a;padding:24px 0;text-align:center;">
<a href="{{ $url }}" style="color:#00C896;font-size:20px;font-weight:700;text-decoration:none;">
OPES Health Systems
</a>
</td>
</tr>
```

- [ ] **Step 5: Commit**
```bash
git add config/mail.php resources/views/vendor/mail/html/themes/opes.css resources/views/vendor/mail/html/header.blade.php resources/views/vendor/mail/html/footer.blade.php resources/views/vendor/mail/html/button.blade.php resources/views/vendor/mail/html/message.blade.php resources/views/vendor/mail/html/panel.blade.php resources/views/vendor/mail/html/subcopy.blade.php resources/views/vendor/mail/html/table.blade.php resources/views/vendor/mail/html/layout.blade.php resources/views/vendor/mail/markdown
git commit -m "feat(notifications): add OPES-branded mail theme"
```
(Stage whatever `vendor:publish` created under `resources/views/vendor/mail/` — list the actual files from `git status`; the set above is the standard publish output.)

---

## Task 3: pilot notifications + wiring

**Files:** Create `app/Notifications/PlacedInCohort.php`, `app/Notifications/AccountDeactivated.php`, `app/Observers/UserObserver.php`; Modify `app/Models/User.php` (ObservedBy), `app/Filament/Resources/PractitionerApplicationResource/Actions/PlaceInCohortAction.php`; Test `tests/Feature/PilotNotificationsTest.php`.

- [ ] **Step 1: Write the failing test**
```php
<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\User;
use App\Notifications\AccountDeactivated;
use App\Notifications\PlacedInCohort;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PilotNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_deactivating_a_user_notifies_them(): void
    {
        Notification::fake();
        $user = User::factory()->create(['is_active' => true]);

        $user->update(['is_active' => false]);

        Notification::assertSentTo($user, AccountDeactivated::class);
    }

    public function test_creating_inactive_user_does_not_notify(): void
    {
        Notification::fake();
        User::factory()->create(['is_active' => false]); // creation, not deactivation
        Notification::assertNothingSent();
    }

    public function test_placed_in_cohort_notification_channels_and_payload(): void
    {
        $cohort = Cohort::factory()->create(['name' => 'July Pharmacy', 'specialty' => 'Pharmacy']);
        $user   = User::factory()->create();

        $notification = new PlacedInCohort($cohort);
        $this->assertEquals(['mail', 'database'], $notification->via($user));

        $array = $notification->toArray($user);
        $this->assertEquals('validation.placed_in_cohort', $array['type']);
        $this->assertArrayHasKey('url', $array);

        // Branded mail renders (proves the OPES theme is wired).
        $html = $notification->toMail($user)->render();
        $this->assertStringContainsString('OPES Health Systems', $html);
    }
}
```

- [ ] **Step 2: Run — FAIL** (`--filter=PilotNotificationsTest`).

- [ ] **Step 3: Create `app/Notifications/PlacedInCohort.php`**
```php
<?php

namespace App\Notifications;

use App\Models\Cohort;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
            ->subject("You've been placed in a validation cohort")
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been placed in the ' . $this->cohort->name . ' cohort (' . $this->cohort->specialty . ').')
            ->line('You can now log daily test sessions and report issues from your Validation Hub.')
            ->action('Open the Validation Hub', route('practitioner.validation.dashboard', ['locale' => 'en']))
            ->line('Thank you for helping validate OPES Health software.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.placed_in_cohort',
            'title' => 'Placed in a validation cohort',
            'body'  => 'You joined the ' . $this->cohort->name . ' cohort.',
            'icon'  => 'clipboard-check',
            'url'   => route('practitioner.validation.dashboard', ['locale' => 'en']),
        ];
    }
}
```

- [ ] **Step 4: Create `app/Notifications/AccountDeactivated.php`**
```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDeactivated extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your OPES account has been deactivated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your OPES Health Systems account has been deactivated and you can no longer sign in.')
            ->line('If you believe this is a mistake, please contact support@opeshealthsystems.com.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'account.deactivated',
            'title' => 'Account deactivated',
            'body'  => 'Your account has been deactivated.',
            'icon'  => 'lock-closed',
            'url'   => null,
        ];
    }
}
```

- [ ] **Step 5: Create `app/Observers/UserObserver.php`**
```php
<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\AccountDeactivated;

class UserObserver
{
    public function updated(User $user): void
    {
        // Fire only on a true→false transition of is_active (deactivation),
        // not on creation or unrelated updates.
        if ($user->wasChanged('is_active') && ! $user->is_active) {
            $user->notify(new AccountDeactivated());
        }
    }
}
```

- [ ] **Step 6: Register the observer on `app/Models/User.php`** — add the attribute above the class (and the import):
```php
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
```
```php
#[ObservedBy(\App\Observers\UserObserver::class)]
class User extends Authenticatable implements FilamentUser
```

- [ ] **Step 7: Wire `PlacedInCohort` into the placement action** — in `app/Filament/Resources/PractitionerApplicationResource/Actions/PlaceInCohortAction.php`, after `CohortMember::create([...])`, notify the practitioner:
```php
                        \App\Models\User::find($record->practitioner_id)
                            ?->notify(new \App\Notifications\PlacedInCohort(Cohort::find($data['cohort_id'])));
```
(`Cohort` is already imported in that file; if not, use the FQN `\App\Models\Cohort::find(...)`.)

- [ ] **Step 8: Run — PASS** (`--filter=PilotNotificationsTest`). Full suite — 0 failures (~445). Watch for any existing test that updates a user's `is_active` true→false and now triggers a real notification — if one fails, add `Notification::fake()` to it.

- [ ] **Step 9: Commit**
```bash
git add app/Notifications/PlacedInCohort.php app/Notifications/AccountDeactivated.php app/Observers/UserObserver.php app/Models/User.php app/Filament/Resources/PractitionerApplicationResource/Actions/PlaceInCohortAction.php tests/Feature/PilotNotificationsTest.php
git commit -m "feat(notifications): add PlacedInCohort and AccountDeactivated pilot notifications"
```

---

## Task 4: NotificationController + routes + feed page

**Files:** Create `app/Http/Controllers/NotificationController.php`, `resources/views/notifications/index.blade.php`; Modify `routes/web.php`; Test `tests/Feature/NotificationFeedTest.php`.

- [ ] **Step 1: Write the failing test**
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AccountDeactivated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class NotificationFeedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function userWithNotification(): User
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $user->notify(new AccountDeactivated()); // real send → row in notifications
        return $user;
    }

    public function test_feed_lists_notifications(): void
    {
        $user = $this->userWithNotification();
        $this->actingAs($user)->get('/en/notifications')
            ->assertOk()
            ->assertSee('Account deactivated');
    }

    public function test_mark_all_read(): void
    {
        $user = $this->userWithNotification();
        $this->assertEquals(1, $user->unreadNotifications()->count());

        $this->actingAs($user)->post('/en/notifications/read-all')->assertRedirect();

        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_guest_cannot_access_feed(): void
    {
        $this->get('/en/notifications')->assertRedirect('/login');
    }
}
```

- [ ] **Step 2: Run — FAIL** (`--filter=NotificationFeedTest`).

- [ ] **Step 3: Add routes** to `routes/web.php` — inside the `{locale}` group, after the public practitioner directory routes (around line 118, before the customer portal group):
```php
        // Shared in-app notifications (any authenticated portal user)
        Route::middleware('auth')->group(function () {
            Route::get('/notifications',            [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
            Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
            Route::post('/notifications/read-all',  [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
        });
```

- [ ] **Step 4: Create `app/Http/Controllers/NotificationController.php`**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead($locale, string $id, Request $request)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        return $url ? redirect($url) : back();
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
```

- [ ] **Step 5: Create `resources/views/notifications/index.blade.php`** (standalone authenticated page; uses plain HTML so it works for every role)
```blade
<x-layouts.app title="Notifications">
    <div class="max-w-3xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Notifications</h1>
            <form method="POST" action="{{ route('notifications.read-all', ['locale' => app()->getLocale()]) }}">
                @csrf
                <button class="text-sm text-emerald-600 hover:underline">Mark all read</button>
            </form>
        </div>

        <div class="space-y-3">
            @forelse($notifications as $n)
                <div class="bg-white border {{ $n->read_at ? 'border-slate-200' : 'border-emerald-300' }} rounded-xl p-4 flex items-start gap-3">
                    <div class="flex-1">
                        <h3 class="font-semibold text-slate-900">{{ $n->data['title'] ?? 'Notification' }}</h3>
                        <p class="text-sm text-slate-500 mt-0.5">{{ $n->data['body'] ?? '' }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ $n->created_at?->diffForHumans() }}</p>
                    </div>
                    @if(($n->data['url'] ?? null) || ! $n->read_at)
                        <form method="POST" action="{{ route('notifications.read', ['locale' => app()->getLocale(), 'id' => $n->id]) }}">
                            @csrf
                            <button class="text-xs px-3 py-1.5 bg-emerald-600 text-white rounded-lg">{{ ($n->data['url'] ?? null) ? 'Open' : 'Mark read' }}</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="bg-white border border-slate-200 rounded-xl p-10 text-center text-slate-400">No notifications yet.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $notifications->links() }}</div>
    </div>
</x-layouts.app>
```
> If no `x-layouts.app` component exists, use the simplest existing public layout wrapper (check `resources/views/components/layouts/`). The page must render for any authenticated role, so use a role-agnostic layout (not a portal-specific one). If none is suitable, wrap in a minimal `<x-layouts.public>` or inline `<!DOCTYPE html>` with `@vite`.

- [ ] **Step 6: Run — PASS** (`--filter=NotificationFeedTest`). If the layout component name is wrong, fix the wrapper and re-run. Full suite — 0 failures (~448).

- [ ] **Step 7: Commit**
```bash
git add app/Http/Controllers/NotificationController.php resources/views/notifications/index.blade.php routes/web.php tests/Feature/NotificationFeedTest.php
git commit -m "feat(notifications): add in-app notification feed (controller, routes, page)"
```

---

## Task 5: notification bell in the 6 portal layouts

**Files:** Create `resources/views/components/notification-bell.blade.php`; Modify the 6 layouts in `resources/views/components/layouts/`; Test `tests/Feature/NotificationBellTest.php`.

- [ ] **Step 1: Write the failing test**
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AccountDeactivated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class NotificationBellTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_practitioner_dashboard_shows_bell_with_unread_count(): void
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM']);
        $user->notify(new AccountDeactivated());

        $this->actingAs($user)->get('/en/practitioner/dashboard')
            ->assertOk()
            ->assertSee('notification-bell'); // the component's wrapper id/class
    }
}
```

- [ ] **Step 2: Run — FAIL** (`--filter=NotificationBellTest`).

- [ ] **Step 3: Create `resources/views/components/notification-bell.blade.php`**
```blade
@php
    $user = auth()->user();
    $unread = $user ? $user->unreadNotifications()->count() : 0;
    $recent = $user ? $user->notifications()->latest()->take(6)->get() : collect();
    $locale = app()->getLocale();
@endphp
<div class="notification-bell" x-data="{ open: false }" style="position:relative;display:inline-block;">
    <button type="button" @click="open = !open" style="position:relative;background:none;border:none;cursor:pointer;color:inherit;">
        <i data-lucide="bell" style="width:20px;height:20px"></i>
        @if($unread > 0)
            <span style="position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;font-size:10px;font-weight:700;border-radius:99px;padding:1px 5px;">{{ $unread > 9 ? '9+' : $unread }}</span>
        @endif
    </button>
    <div x-show="open" @click.outside="open = false" x-cloak
         style="position:absolute;right:0;margin-top:8px;width:320px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.12);z-index:50;overflow:hidden;">
        <div style="padding:12px 16px;border-bottom:1px solid #f1f5f9;font-weight:600;color:#0f172a;">Notifications</div>
        @forelse($recent as $n)
            <a href="{{ route('notifications.read', ['locale' => $locale, 'id' => $n->id]) }}"
               onclick="event.preventDefault(); this.closest('form')?.submit();" style="display:block;text-decoration:none;">
            <form method="POST" action="{{ route('notifications.read', ['locale' => $locale, 'id' => $n->id]) }}" style="margin:0;">
                @csrf
                <button type="submit" style="display:block;width:100%;text-align:left;background:{{ $n->read_at ? '#fff' : '#ecfdf5' }};border:none;border-bottom:1px solid #f1f5f9;padding:10px 16px;cursor:pointer;">
                    <span style="font-size:13px;font-weight:600;color:#0f172a;display:block;">{{ $n->data['title'] ?? 'Notification' }}</span>
                    <span style="font-size:12px;color:#64748b;display:block;">{{ \Illuminate\Support\Str::limit($n->data['body'] ?? '', 60) }}</span>
                </button>
            </form>
            </a>
        @empty
            <div style="padding:16px;color:#94a3b8;font-size:13px;text-align:center;">No notifications.</div>
        @endforelse
        <a href="{{ route('notifications.index', ['locale' => $locale]) }}" style="display:block;padding:10px 16px;text-align:center;color:#00C896;font-size:13px;font-weight:600;text-decoration:none;">View all</a>
    </div>
</div>
```
> Note: the nested `<a>`+`<form>` is redundant — keep just the `<form><button>` per item (a POST to mark-read then redirect to the item URL). Drop the outer `<a>` wrapper when implementing; the form-button is the click target. (Alpine `x-data`/`x-show` is already available in these portal layouts; if not, a CSS `:focus-within` dropdown works too.)

- [ ] **Step 4: Include `<x-notification-bell />` in each of the 6 portal layouts** — in the header actions area (next to the user name / profile link) of:
  - `resources/views/components/layouts/practitioner.blade.php`
  - `resources/views/components/layouts/customer.blade.php`
  - `resources/views/components/layouts/tester.blade.php`
  - `resources/views/components/layouts/manager.blade.php`
  - `resources/views/components/layouts/hr.blade.php`
  - `resources/views/components/layouts/accountant.blade.php`

  Read each layout, find the `portal-actions` / user-menu area (e.g. the `<span>{{ auth()->user()->name }}</span>` block), and insert `<x-notification-bell />` immediately before it.

- [ ] **Step 5: Run — PASS** (`--filter=NotificationBellTest`). Sanity: load one more portal (e.g. customer) in a quick assertion if desired. Full suite — 0 failures (~449).

- [ ] **Step 6: Commit**
```bash
git add resources/views/components/notification-bell.blade.php resources/views/components/layouts/practitioner.blade.php resources/views/components/layouts/customer.blade.php resources/views/components/layouts/tester.blade.php resources/views/components/layouts/manager.blade.php resources/views/components/layouts/hr.blade.php resources/views/components/layouts/accountant.blade.php tests/Feature/NotificationBellTest.php
git commit -m "feat(notifications): add notification bell to the six web portal layouts"
```

---

## Final verification
- Full suite: `<php> artisan test` — expect ~449, 0 failures.
- Manual smoke (dev server): place a practitioner in a cohort → they get an email + an in-app bell item linking to the Validation Hub; deactivate a user in Filament → they receive the deactivation email + feed item; the bell shows the unread count and dropdown in each portal; the notifications page lists items and mark-read works.

## Post-completion
- Run `superpowers:finishing-a-development-branch` to merge.
- Then proceed to N2 (Validation Hub events) — a separate plan using this spine.
