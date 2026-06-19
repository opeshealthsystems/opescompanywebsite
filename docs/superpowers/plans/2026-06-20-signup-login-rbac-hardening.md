# Signup + Login RBAC Hardening — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development or superpowers:executing-plans. Steps use checkbox (`- [ ]`) tracking.

**Goal:** Give public signup a safe account-type selector (Facility/Company · Individual · Practitioner), with sensitive roles/positions hidden AND server-blocked; fix real login gaps (deactivated-user bypass, no throttle, wrong staff redirects).

**Architecture:** A single `RegisterController` validates an `account_type` against a server-side allowlist and maps it to a role internally — the client never sends a role. Tester/Partner remain admin-reviewed "Apply" flows (unchanged). Login gains an `is_active` gate, rate-limiting, and correct role-based redirects.

**Tech Stack:** Laravel 13.8 / PHP 8.3 / Spatie Permission v8 / Blade. PHP binary: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`. 424 tests currently green.

**Security invariant (non-negotiable):** Only `customer` and `practitioner` are ever self-assignable. `super_admin, admin, support, manager, hr, accountant` and the employee `position` field are never exposed in or accepted by public signup. Role is derived server-side from a validated allowlist key — never read from request input.

---

## Account-type → role allowlist (single source of truth)
```php
private const ACCOUNT_TYPES = [
    'facility'     => 'customer',     // Healthcare Facility / Company
    'individual'   => 'customer',     // Individual
    'practitioner' => 'practitioner', // Clinician
];
```
`account_type` is validated `Rule::in(array_keys(self::ACCOUNT_TYPES))`; the role is `self::ACCOUNT_TYPES[$accountType]`. A forged `account_type=admin` (or any `role=...` field) fails validation / is ignored.

---

## Task 1: Unified RegisterController + allowlist

**Files:** Modify `app/Http/Controllers/Auth/RegisterController.php`; Delete `app/Http/Controllers/Auth/PractitionerRegisterController.php` (folded in); Test `tests/Feature/RegisterRbacTest.php`.

- [ ] **Step 1: Write the failing test** `tests/Feature/RegisterRbacTest.php`
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RegisterRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_facility_signup_creates_customer_with_profile(): void
    {
        $this->post('/register', [
            'account_type' => 'facility', 'name' => 'Douala General', 'email' => 'f@x.cm',
            'password' => 'password123', 'password_confirmation' => 'password123',
            'facility_name' => 'Douala General', 'facility_type' => 'hospital', 'country' => 'CM',
        ])->assertRedirect();

        $user = User::where('email', 'f@x.cm')->first();
        $this->assertTrue($user->hasRole('customer'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertEquals('hospital', $user->customerProfile->facility_type);
    }

    public function test_individual_signup_creates_customer(): void
    {
        $this->post('/register', [
            'account_type' => 'individual', 'name' => 'Jane', 'email' => 'i@x.cm',
            'password' => 'password123', 'password_confirmation' => 'password123', 'country' => 'CM',
        ])->assertRedirect();
        $this->assertTrue(User::where('email', 'i@x.cm')->first()->hasRole('customer'));
    }

    public function test_practitioner_signup_creates_practitioner_with_profile(): void
    {
        $this->post('/register', [
            'account_type' => 'practitioner', 'name' => 'Dr Ada', 'email' => 'p@x.cm',
            'password' => 'password123', 'password_confirmation' => 'password123',
            'profession' => 'doctor',
        ])->assertRedirect();
        $user = User::where('email', 'p@x.cm')->first();
        $this->assertTrue($user->hasRole('practitioner'));
        $this->assertEquals('doctor', $user->practitionerProfile->profession);
    }

    public function test_forged_sensitive_account_type_is_rejected(): void
    {
        $this->post('/register', [
            'account_type' => 'admin', 'name' => 'Evil', 'email' => 'e@x.cm',
            'password' => 'password123', 'password_confirmation' => 'password123', 'country' => 'CM',
        ])->assertSessionHasErrors('account_type');
        $this->assertDatabaseMissing('users', ['email' => 'e@x.cm']);
    }

    public function test_forged_role_field_is_ignored(): void
    {
        $this->post('/register', [
            'account_type' => 'individual', 'role' => 'admin', 'roles' => ['super_admin'],
            'name' => 'Sneaky', 'email' => 's@x.cm',
            'password' => 'password123', 'password_confirmation' => 'password123', 'country' => 'CM',
        ])->assertRedirect();
        $user = User::where('email', 's@x.cm')->first();
        $this->assertTrue($user->hasRole('customer'));
        $this->assertFalse($user->hasAnyRole(['admin', 'super_admin']));
    }
}
```

- [ ] **Step 2: Run — expect FAIL** (`--filter=RegisterRbacTest`): `account_type=admin` currently passes (ignored field) and creates a customer, so `test_forged_sensitive_account_type_is_rejected` fails (no validation yet); practitioner via `/register` fails (no profession handling).

- [ ] **Step 3: Replace `app/Http/Controllers/Auth/RegisterController.php`**
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PractitionerWelcome;
use App\Mail\WelcomeEmail;
use App\Models\PractitionerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /** Public account types mapped to their role. Role is NEVER taken from request input. */
    private const ACCOUNT_TYPES = [
        'facility'     => 'customer',
        'individual'   => 'customer',
        'practitioner' => 'practitioner',
    ];

    public function show()
    {
        return view('auth.register', [
            'professions' => PractitionerProfile::professionOptions(),
        ]);
    }

    public function register(Request $request)
    {
        $accountType = $request->input('account_type');

        $rules = [
            'account_type' => ['required', Rule::in(array_keys(self::ACCOUNT_TYPES))],
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users|max:150',
            'password'     => 'required|string|min:8|confirmed',
            'phone'        => 'nullable|string|max:30',
            'locale'       => 'nullable|string|in:en,fr',
        ];

        if ($accountType === 'facility') {
            $rules += [
                'facility_name' => 'required|string|max:100',
                'facility_type' => 'required|string|max:60',
                'country'       => 'required|string|max:60',
                'city'          => 'nullable|string|max:60',
            ];
        } elseif ($accountType === 'individual') {
            $rules += [
                'country' => 'required|string|max:60',
                'city'    => 'nullable|string|max:60',
            ];
        } elseif ($accountType === 'practitioner') {
            $rules += [
                'profession'        => 'required|string|in:' . implode(',', array_keys(PractitionerProfile::professionOptions())),
                'specialty'         => 'nullable|string|max:120',
                'workplace_name'    => 'nullable|string|max:150',
                'workplace_country' => 'nullable|string|max:80',
            ];
        }

        $validated = $request->validate($rules);

        // Role derived server-side from the validated allowlist key — client cannot choose it.
        $role = self::ACCOUNT_TYPES[$validated['account_type']];

        $user = DB::transaction(function () use ($validated, $role) {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => $validated['password'],
                'phone'     => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            $user->assignRole($role);

            if ($role === 'practitioner') {
                $user->practitionerProfile()->create([
                    'profession'        => $validated['profession'],
                    'specialty'         => $validated['specialty'] ?? null,
                    'workplace_name'    => $validated['workplace_name'] ?? null,
                    'workplace_country' => $validated['workplace_country'] ?? 'CM',
                ]);
            } else {
                $user->customerProfile()->create([
                    'facility_name' => $validated['facility_name'] ?? null,
                    'facility_type' => $validated['facility_type'] ?? null,
                    'country'       => $validated['country'],
                    'city'          => $validated['city'] ?? null,
                ]);
            }

            return $user;
        });

        Auth::login($user);

        $locale = $validated['locale'] ?? 'en';

        if ($role === 'practitioner') {
            Mail::to($user->email)->queue(new PractitionerWelcome($user));
            return redirect()->route('practitioner.dashboard', ['locale' => $locale]);
        }

        Mail::to($user->email)->queue(new WelcomeEmail($user));
        return redirect()->route('customer.dashboard', ['locale' => $locale]);
    }
}
```

- [ ] **Step 4: Run — expect PASS** (`--filter=RegisterRbacTest`). Full suite — fix any practitioner-register test that referenced the old controller (Task 2 handles routes).

- [ ] **Step 5: Commit** (after Task 2 routes, to keep the suite green — see Task 2 Step 5).

---

## Task 2: Register view selector + route cleanup

**Files:** Modify `resources/views/auth/register.blade.php`; Modify `routes/web.php` (redirect old practitioner route, drop its POST); Delete `app/Http/Controllers/Auth/PractitionerRegisterController.php`.

- [ ] **Step 1: Routes** — in `routes/web.php`, replace the two practitioner-register routes (`practitioner.register` GET + `practitioner.register.post` POST) with a single redirect so old links still work:
```php
    Route::get('/practitioners/register', fn () => redirect()->route('register'))->name('practitioner.register');
```
(Remove the `practitioner.register.post` route and the `use App\Http\Controllers\Auth\PractitionerRegisterController;` import if present.)

- [ ] **Step 2: Delete** `app/Http/Controllers/Auth/PractitionerRegisterController.php` (its logic now lives in `RegisterController`; the `PractitionerWelcome` mailable is reused).

- [ ] **Step 3: Replace `resources/views/auth/register.blade.php`** with the account-type selector + conditional field groups (plain JS toggle, no new deps). The selector offers Facility/Company · Individual · Practitioner; Tester/Partner appear as "Apply" links, not account types.
```blade
<x-layouts.auth title="Create Account">
    <div class="auth-card auth-card-wide">
        <h1 class="auth-heading">Create your account</h1>
        <p class="auth-subheading">Join OPES Health Systems — digitising healthcare in Cameroon</p>

        @if ($errors->any())
            <div class="auth-error-box">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" class="auth-form" id="registerForm">
            @csrf
            <input type="hidden" name="locale" value="{{ request()->segment(1) === 'fr' ? 'fr' : 'en' }}">

            <p class="auth-section-label">I am registering as</p>
            <div class="auth-grid-2">
                <div class="auth-field">
                    <label for="account_type" class="auth-label">Account Type *</label>
                    <select id="account_type" name="account_type" class="auth-input auth-select" required onchange="syncAccountType()">
                        @php $sel = old('account_type', 'facility'); @endphp
                        <option value="facility"     {{ $sel==='facility' ? 'selected' : '' }}>Healthcare Facility / Company</option>
                        <option value="individual"   {{ $sel==='individual' ? 'selected' : '' }}>Individual</option>
                        <option value="practitioner" {{ $sel==='practitioner' ? 'selected' : '' }}>Practitioner (Clinician)</option>
                    </select>
                </div>
            </div>

            <p class="auth-section-label" style="margin-top:1.5rem">Contact Information</p>
            <div class="auth-grid-2">
                <div class="auth-field">
                    <label for="name" class="auth-label" id="nameLabel">Full Name / Organisation *</label>
                    <input id="name" name="name" type="text" class="auth-input @error('name') auth-input-error @enderror" value="{{ old('name') }}" required>
                </div>
                <div class="auth-field">
                    <label for="email" class="auth-label">Email Address *</label>
                    <input id="email" name="email" type="email" class="auth-input @error('email') auth-input-error @enderror" value="{{ old('email') }}" required>
                </div>
                <div class="auth-field">
                    <label for="password" class="auth-label">Password *</label>
                    <input id="password" name="password" type="password" class="auth-input" required minlength="8" placeholder="Min. 8 characters">
                </div>
                <div class="auth-field">
                    <label for="password_confirmation" class="auth-label">Confirm Password *</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="auth-input" required>
                </div>
                <div class="auth-field">
                    <label for="phone" class="auth-label">Phone</label>
                    <input id="phone" name="phone" type="tel" class="auth-input" value="{{ old('phone') }}" placeholder="+237 6XX XXX XXX">
                </div>
            </div>

            {{-- Facility (company) fields --}}
            <div data-group="facility">
                <p class="auth-section-label" style="margin-top:1.5rem">Facility Information</p>
                <div class="auth-grid-2">
                    <div class="auth-field">
                        <label for="facility_name" class="auth-label">Facility Name *</label>
                        <input id="facility_name" name="facility_name" type="text" class="auth-input" value="{{ old('facility_name') }}" placeholder="Central Hospital Douala">
                    </div>
                    <div class="auth-field">
                        <label for="facility_type" class="auth-label">Facility Type *</label>
                        <select id="facility_type" name="facility_type" class="auth-input auth-select">
                            <option value="">— Select type —</option>
                            @foreach(['hospital'=>'Hospital','clinic'=>'Clinic','laboratory'=>'Laboratory','pharmacy'=>'Pharmacy','radiology'=>'Radiology Centre','nursing_home'=>'Nursing Home','other'=>'Other'] as $val => $label)
                                <option value="{{ $val }}" {{ old('facility_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Location (facility + individual) --}}
            <div data-group="facility individual">
                <div class="auth-grid-2">
                    <div class="auth-field">
                        <label for="country" class="auth-label">Country *</label>
                        <input id="country" name="country" type="text" class="auth-input" value="{{ old('country', 'CM') }}" placeholder="CM">
                    </div>
                    <div class="auth-field">
                        <label for="city" class="auth-label">City</label>
                        <input id="city" name="city" type="text" class="auth-input" value="{{ old('city') }}" placeholder="Douala">
                    </div>
                </div>
            </div>

            {{-- Practitioner fields --}}
            <div data-group="practitioner">
                <p class="auth-section-label" style="margin-top:1.5rem">Professional Information</p>
                <div class="auth-grid-2">
                    <div class="auth-field">
                        <label for="profession" class="auth-label">Profession *</label>
                        <select id="profession" name="profession" class="auth-input auth-select">
                            <option value="">— Select —</option>
                            @foreach($professions as $val => $label)
                                <option value="{{ $val }}" {{ old('profession') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="auth-field">
                        <label for="specialty" class="auth-label">Specialty</label>
                        <input id="specialty" name="specialty" type="text" class="auth-input" value="{{ old('specialty') }}">
                    </div>
                    <div class="auth-field">
                        <label for="workplace_name" class="auth-label">Workplace</label>
                        <input id="workplace_name" name="workplace_name" type="text" class="auth-input" value="{{ old('workplace_name') }}">
                    </div>
                    <div class="auth-field">
                        <label for="workplace_country" class="auth-label">Workplace Country</label>
                        <input id="workplace_country" name="workplace_country" type="text" class="auth-input" value="{{ old('workplace_country', 'CM') }}">
                    </div>
                </div>
            </div>

            <button type="submit" class="auth-btn" style="margin-top:1.5rem">Create Account</button>
        </form>

        <p class="auth-switch" style="margin-top:1rem">
            Want to test our software or partner with us?
            <a href="{{ route('join-testers') }}" class="auth-link">Apply as a Tester</a> ·
            <a href="{{ route('become-a-partner') }}" class="auth-link">Become a Partner</a>
        </p>
        <p class="auth-switch">
            Already have an account? <a href="{{ route('login') }}" class="auth-link">Sign in</a>
        </p>
    </div>

    <script>
        function syncAccountType() {
            var t = document.getElementById('account_type').value;
            document.querySelectorAll('[data-group]').forEach(function (el) {
                el.style.display = el.getAttribute('data-group').split(' ').indexOf(t) !== -1 ? '' : 'none';
            });
            document.getElementById('nameLabel').textContent = (t === 'facility') ? 'Organisation Name *' : 'Full Name *';
        }
        document.addEventListener('DOMContentLoaded', syncAccountType);
    </script>
</x-layouts.auth>
```

- [ ] **Step 4: Run** `--filter=RegisterRbacTest` → PASS. Then full suite — expect 0 failures (~429). If a pre-existing `PractitionerPortalTest`/auth test posts to `/practitioners/register`, update it to `/register` with `account_type=practitioner`.

- [ ] **Step 5: Commit Tasks 1+2 together**
```bash
git add app/Http/Controllers/Auth/RegisterController.php resources/views/auth/register.blade.php routes/web.php tests/Feature/RegisterRbacTest.php
git rm app/Http/Controllers/Auth/PractitionerRegisterController.php
git commit -m "feat(auth): unified signup with account-type selector and server-side role allowlist"
```

---

## Task 3: Login hardening (is_active + throttle + staff redirects)

**Files:** Modify `app/Http/Controllers/Auth/LoginController.php`; Test `tests/Feature/LoginHardeningTest.php`.

- [ ] **Step 1: Write the failing test** `tests/Feature/LoginHardeningTest.php`
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class LoginHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_deactivated_user_cannot_log_in(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password123'), 'is_active' => false]);
        $user->assignRole('customer');

        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_active_customer_logs_in(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password123'), 'is_active' => true]);
        $user->assignRole('customer');

        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_staff_roles_redirect_to_their_portals(): void
    {
        foreach (['manager' => 'manager/dashboard', 'hr' => 'hr/dashboard', 'accountant' => 'accountant/dashboard'] as $role => $path) {
            $u = User::factory()->create(['password' => Hash::make('password123'), 'is_active' => true]);
            $u->assignRole($role);
            $this->post('/login', ['email' => $u->email, 'password' => 'password123'])
                ->assertRedirectContains($path);
            $this->post('/logout');
        }
    }
}
```
> `assertRedirectContains` exists in Laravel 11+; if unavailable, assert `->assertRedirect()` and follow with a `->assertSee` on the portal, or check `redirect()->getTargetUrl()` contains the path.

- [ ] **Step 2: Run — expect FAIL** (`--filter=LoginHardeningTest`): deactivated user currently logs in; manager/hr/accountant currently redirect to `customer/dashboard`.

- [ ] **Step 3: Replace `app/Http/Controllers/Auth/LoginController.php`**
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'locale'   => 'nullable|string|in:en,fr',
        ]);

        $locale = $credentials['locale'] ?? 'en';
        unset($credentials['locale']);

        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact support.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        if ($user->hasAnyRole(['super_admin', 'admin', 'support'])) {
            return redirect('/admin');
        }

        $portalRoutes = [
            'practitioner' => 'practitioner.dashboard',
            'tester'       => 'tester.dashboard',
            'manager'      => 'manager.dashboard',
            'hr'           => 'hr.dashboard',
            'accountant'   => 'accountant.dashboard',
        ];
        foreach ($portalRoutes as $role => $routeName) {
            if ($user->hasRole($role)) {
                return redirect()->route($routeName, ['locale' => $locale]);
            }
        }

        return redirect()->route('customer.dashboard', ['locale' => $locale]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
```

- [ ] **Step 4: Run — expect PASS** (`--filter=LoginHardeningTest`). Full suite — 0 failures (~432).

- [ ] **Step 5: Commit**
```bash
git add app/Http/Controllers/Auth/LoginController.php tests/Feature/LoginHardeningTest.php
git commit -m "fix(auth): block deactivated logins, throttle attempts, route staff to their portals"
```

---

## Final verification
- Full suite: `<php> artisan test` — expect ~432 passing, 0 failures.
- Manual smoke: `/register` shows the account-type selector; picking each type reveals the right fields; Facility→customer+facility profile, Individual→customer, Practitioner→practitioner+profile, all redirect to the right dashboard. POSTing `account_type=admin` → validation error, no account. Deactivated user can't log in; manager/hr/accountant land on their own portals; 6 bad login attempts → throttle message. Tester/Partner "Apply" links still go to `/join-testers` and `/become-a-partner`.

## Out of scope (flagged, not done)
- Email verification on signup (N1) — users are still auto-logged-in. Separate decision.
