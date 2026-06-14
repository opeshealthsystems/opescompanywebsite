# Customer Portal — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a customer self-registration and login system, a customer profile with healthcare facility data, and a customer portal dashboard — giving `customer`-role users a dedicated authenticated area separate from the Filament admin panel.

**Architecture:** Auth routes (`/login`, `/register`, `/logout`) are non-locale-prefixed so Laravel's `Authenticate` middleware can redirect to `route('login')` without needing locale context. Customer portal routes are locale-prefixed (`/{locale}/customer/...`) and guarded by `auth` + `role:customer` middleware. A `customer_profiles` table stores facility-specific data in a 1-to-1 relationship with `users`. Two new Blade layout components (`layouts.auth` and `layouts.customer`) keep the portal visually separate from the marketing site, while sharing the same dark-theme CSS.

**Tech Stack:** Laravel 13, PHP 8.3, Blade components, Tailwind CSS v4 (`@theme` in `app.css`), Spatie Laravel Permission (roles already seeded), MySQL, PHPUnit 11 / SQLite for tests

---

## File Map

### New files
- `database/migrations/2026_06_13_210000_create_customer_profiles_table.php`
- `app/Models/CustomerProfile.php`
- `app/Http/Controllers/Auth/RegisterController.php`
- `app/Http/Controllers/Auth/LoginController.php`
- `app/Http/Controllers/Customer/DashboardController.php`
- `app/Http/Controllers/Customer/ProfileController.php`
- `resources/views/components/layouts/auth.blade.php`
- `resources/views/components/layouts/customer.blade.php`
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/customer/dashboard.blade.php`
- `resources/views/customer/profile.blade.php`
- `tests/Feature/CustomerPortalTest.php`

### Modified files
- `app/Models/User.php` — add `customerProfile()` hasOne relationship
- `routes/web.php` — add auth routes + customer portal routes
- `app/Http/Middleware/RequireRole.php` — use `route('login')` instead of `'/login'`
- `resources/css/app.css` — add `auth-*` and `customer-*` CSS classes

---

## Task 1: CustomerProfile Migration + Model + User Relationship

**Files:**
- Create: `database/migrations/2026_06_13_210000_create_customer_profiles_table.php`
- Create: `app/Models/CustomerProfile.php`
- Modify: `app/Models/User.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/CustomerPortalTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_customer_profiles_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('customer_profiles'));
    }

    public function test_user_can_have_customer_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $profile = CustomerProfile::create([
            'user_id'       => $user->id,
            'facility_name' => 'Central Hospital Douala',
            'facility_type' => 'hospital',
            'country'       => 'CM',
            'city'          => 'Douala',
        ]);

        $this->assertDatabaseHas('customer_profiles', [
            'user_id'       => $user->id,
            'facility_name' => 'Central Hospital Douala',
        ]);

        $this->assertEquals($user->id, $user->customerProfile->user_id);
        $this->assertEquals($profile->id, $user->customerProfile->id);
    }
}
```

- [ ] **Step 2: Run the test — expect FAIL**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/CustomerPortalTest.php --filter=test_customer_profiles_table_exists
```

Expected: FAIL — `Table customer_profiles doesn't exist`

- [ ] **Step 3: Create the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('facility_name')->nullable();
            $table->string('facility_type')->nullable();
            $table->string('country')->default('CM');
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
```

- [ ] **Step 4: Create `app/Models/CustomerProfile.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'facility_name',
        'facility_type',
        'country',
        'city',
        'address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 5: Add `customerProfile()` relationship to `app/Models/User.php`**

Add the import and method. After the `casts()` method, add:

```php
use Illuminate\Database\Eloquent\Relations\HasOne;

// Add to the User class:
public function customerProfile(): HasOne
{
    return $this->hasOne(CustomerProfile::class);
}
```

Also add `use App\Models\CustomerProfile;` — or use the FQCN in the return type only. The cleanest approach: add a `use` statement in the imports block:

```php
use Illuminate\Database\Eloquent\Relations\HasOne;
```

And add the method to the class body:

```php
public function customerProfile(): HasOne
{
    return $this->hasOne(CustomerProfile::class);
}
```

- [ ] **Step 6: Run the migration**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan migrate
```

Expected: `Migrated: 2026_06_13_210000_create_customer_profiles_table`

- [ ] **Step 7: Run the tests — expect PASS**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/CustomerPortalTest.php
```

Expected: 2 tests pass.

- [ ] **Step 8: Run full suite — no regressions**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test
```

Expected: All 27 existing tests + 2 new = 29 pass.

- [ ] **Step 9: Commit**

```bash
cd /c/laragon/www/ohs && git add database/migrations/2026_06_13_210000_create_customer_profiles_table.php app/Models/CustomerProfile.php app/Models/User.php tests/Feature/CustomerPortalTest.php
git commit -m "feat: add customer_profiles table, model, and User hasOne relationship"
```

---

## Task 2: Auth Routes + Customer Portal Routes + RequireRole Fix

**Files:**
- Modify: `routes/web.php`
- Modify: `app/Http/Middleware/RequireRole.php`

- [ ] **Step 1: Write the failing route test**

Add to `tests/Feature/CustomerPortalTest.php`:

```php
public function test_login_route_exists(): void
{
    $response = $this->get('/login');
    $this->assertNotEquals(404, $response->status());
}

public function test_register_route_exists(): void
{
    $response = $this->get('/register');
    $this->assertNotEquals(404, $response->status());
}

public function test_customer_dashboard_redirects_unauthenticated_to_login(): void
{
    $response = $this->get('/en/customer/dashboard');
    $response->assertRedirect('/login');
}
```

- [ ] **Step 2: Run the tests — expect FAIL**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/CustomerPortalTest.php --filter=test_login_route_exists
```

Expected: FAIL — 404

- [ ] **Step 3: Replace the full content of `routes/web.php`**

```php
<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Bare root → default locale
Route::get('/', fn () => redirect('/'.config('locale.default')));
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// ── Auth (non-locale-prefixed so Laravel's Authenticate middleware can redirect to route('login')) ──
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class,   'show'])->name('login');
    Route::post('/login',   [LoginController::class,   'authenticate'])->name('login.post');
    Route::get('/register', [RegisterController::class,'show'])->name('register');
    Route::post('/register',[RegisterController::class,'register'])->name('register.post');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Locale-prefixed routes ──────────────────────────────────────────────────
Route::prefix('{locale}')
    ->where(['locale' => implode('|', config('locale.supported'))])
    ->middleware('setlocale')
    ->group(function () {

        // Public marketing pages
        Route::get('/',              [HomeController::class,    'index'])->name('home');
        Route::get('/products',      [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{slug}',[ProductController::class,'show'])->name('product.show');
        Route::get('/contact',       [ContactController::class, 'show'])->name('contact');
        Route::post('/contact',      [ContactController::class, 'submit'])->name('contact.submit');
        Route::get('/solutions',     fn () => view('pages.solutions'))->name('solutions');
        Route::get('/about',         fn () => view('pages.about'))->name('about');
        Route::get('/blog',          [BlogController::class,   'index'])->name('blog');
        Route::get('/blog/{slug}',   [BlogController::class,   'show'])->name('blog.show');
        Route::get('/partnerships',  fn () => view('pages.partnerships'))->name('partnerships');

        // Customer portal (auth + customer role required)
        Route::middleware(['auth', 'role:customer'])
            ->prefix('customer')
            ->name('customer.')
            ->group(function () {
                Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
                Route::get('/profile',   [ProfileController::class,  'show'])->name('profile');
                Route::put('/profile',   [ProfileController::class,  'update'])->name('profile.update');
            });
    });
```

- [ ] **Step 4: Update `app/Http/Middleware/RequireRole.php` to use named route**

Change `redirect('/login')` to `redirect()->route('login')`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->hasAnyRole($roles)) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
```

- [ ] **Step 5: Run the route tests — expect PASS**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/CustomerPortalTest.php --filter=test_login_route_exists
```

Expected: PASS (routes return non-404 — will be 500 until controllers exist, but that's not 404)

Actually the test uses `assertNotEquals(404, ...)` so even a 500 (no controller yet) passes this assertion.

- [ ] **Step 6: Verify `test_customer_dashboard_redirects_unauthenticated_to_login` passes**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/CustomerPortalTest.php --filter=test_customer_dashboard_redirects_unauthenticated_to_login
```

Expected: PASS — unauthenticated request redirects to `/login`

- [ ] **Step 7: Run full suite**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test
```

**Important:** The `RbacTest` tests that define inline routes using `\Illuminate\Support\Facades\Route::get('/test-admin-only', ...)` may now conflict with the new named `login` route. If any existing test fails, investigate before proceeding.

Expected: All 29+ tests pass.

- [ ] **Step 8: Commit**

```bash
cd /c/laragon/www/ohs && git add routes/web.php app/Http/Middleware/RequireRole.php tests/Feature/CustomerPortalTest.php
git commit -m "feat: add auth routes, customer portal routes, fix RequireRole to use named login route"
```

---

## Task 3: RegisterController + LoginController

**Files:**
- Create: `app/Http/Controllers/Auth/RegisterController.php`
- Create: `app/Http/Controllers/Auth/LoginController.php`

- [ ] **Step 1: Write the failing tests**

Add to `tests/Feature/CustomerPortalTest.php`:

```php
public function test_customer_can_register(): void
{
    $response = $this->post('/register', [
        'name'                  => 'Dr. Ambe John',
        'email'                 => 'ambe@centralhospital.cm',
        'password'              => 'Secret1234!',
        'password_confirmation' => 'Secret1234!',
        'phone'                 => '+237612000000',
        'facility_name'         => 'Central Hospital Douala',
        'facility_type'         => 'hospital',
        'country'               => 'CM',
        'city'                  => 'Douala',
        'locale'                => 'en',
    ]);

    $this->assertDatabaseHas('users', ['email' => 'ambe@centralhospital.cm']);

    $user = User::where('email', 'ambe@centralhospital.cm')->first();
    $this->assertTrue($user->hasRole('customer'));
    $this->assertDatabaseHas('customer_profiles', [
        'user_id'       => $user->id,
        'facility_name' => 'Central Hospital Douala',
        'facility_type' => 'hospital',
    ]);

    $response->assertRedirect('/en/customer/dashboard');
}

public function test_registration_requires_email_uniqueness(): void
{
    User::factory()->create(['email' => 'duplicate@test.cm']);

    $response = $this->post('/register', [
        'name'                  => 'Another User',
        'email'                 => 'duplicate@test.cm',
        'password'              => 'Secret1234!',
        'password_confirmation' => 'Secret1234!',
        'country'               => 'CM',
        'locale'                => 'en',
    ]);

    $response->assertSessionHasErrors('email');
}

public function test_customer_can_login(): void
{
    $user = User::factory()->create([
        'email'    => 'customer@test.cm',
        'password' => bcrypt('Secret1234!'),
    ]);
    $user->assignRole('customer');

    $response = $this->post('/login', [
        'email'    => 'customer@test.cm',
        'password' => 'Secret1234!',
        'locale'   => 'en',
    ]);

    $response->assertRedirect('/en/customer/dashboard');
    $this->assertAuthenticatedAs($user);
}

public function test_staff_login_redirects_to_admin(): void
{
    $admin = User::factory()->create([
        'email'    => 'staff@opes.cm',
        'password' => bcrypt('Secret1234!'),
    ]);
    $admin->assignRole('admin');

    $response = $this->post('/login', [
        'email'    => 'staff@opes.cm',
        'password' => 'Secret1234!',
        'locale'   => 'en',
    ]);

    $response->assertRedirect('/admin');
    $this->assertAuthenticatedAs($admin);
}

public function test_login_fails_with_wrong_password(): void
{
    User::factory()->create([
        'email'    => 'user@test.cm',
        'password' => bcrypt('CorrectPass!'),
    ]);

    $response = $this->post('/login', [
        'email'    => 'user@test.cm',
        'password' => 'WrongPass!',
        'locale'   => 'en',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
}

public function test_authenticated_customer_can_logout(): void
{
    $user = User::factory()->create();
    $user->assignRole('customer');

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect('/');

    $this->assertGuest();
}
```

- [ ] **Step 2: Run the tests — expect FAIL**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/CustomerPortalTest.php --filter=test_customer_can_register
```

Expected: FAIL — controller class not found

- [ ] **Step 3: Create `app/Http/Controllers/Auth/RegisterController.php`**

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:users|max:150',
            'password'      => 'required|string|min:8|confirmed',
            'phone'         => 'nullable|string|max:30',
            'facility_name' => 'nullable|string|max:100',
            'facility_type' => 'nullable|string|max:60',
            'country'       => 'required|string|max:60',
            'city'          => 'nullable|string|max:60',
            'locale'        => 'nullable|string|in:en,fr',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => $validated['password'],
            'phone'     => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        $user->assignRole('customer');

        $user->customerProfile()->create([
            'facility_name' => $validated['facility_name'] ?? null,
            'facility_type' => $validated['facility_type'] ?? null,
            'country'       => $validated['country'],
            'city'          => $validated['city'] ?? null,
        ]);

        Auth::login($user);

        $locale = $validated['locale'] ?? 'en';
        return redirect()->route('customer.dashboard', ['locale' => $locale]);
    }
}
```

- [ ] **Step 4: Create `app/Http/Controllers/Auth/LoginController.php`**

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->hasAnyRole(['super_admin', 'admin', 'support'])) {
            return redirect('/admin');
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

- [ ] **Step 5: Run the controller tests — expect PASS**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/CustomerPortalTest.php
```

Expected: All customer portal tests pass (including the new auth tests).

- [ ] **Step 6: Run the full suite**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test
```

Expected: All tests pass.

- [ ] **Step 7: Commit**

```bash
cd /c/laragon/www/ohs && git add app/Http/Controllers/Auth/RegisterController.php app/Http/Controllers/Auth/LoginController.php tests/Feature/CustomerPortalTest.php
git commit -m "feat: add RegisterController and LoginController with role-based redirect"
```

---

## Task 4: DashboardController + ProfileController

**Files:**
- Create: `app/Http/Controllers/Customer/DashboardController.php`
- Create: `app/Http/Controllers/Customer/ProfileController.php`

- [ ] **Step 1: Write the failing tests**

Add to `tests/Feature/CustomerPortalTest.php`:

```php
public function test_authenticated_customer_can_access_dashboard(): void
{
    $user = User::factory()->create();
    $user->assignRole('customer');

    $this->actingAs($user)
        ->get('/en/customer/dashboard')
        ->assertOk()
        ->assertSee($user->name);
}

public function test_staff_user_cannot_access_customer_dashboard(): void
{
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get('/en/customer/dashboard')
        ->assertForbidden();
}

public function test_customer_can_update_profile(): void
{
    $user = User::factory()->create(['name' => 'Old Name', 'phone' => null]);
    $user->assignRole('customer');
    $user->customerProfile()->create(['country' => 'CM']);

    $response = $this->actingAs($user)->put('/en/customer/profile', [
        'name'          => 'New Name',
        'phone'         => '+237612345678',
        'facility_name' => 'Updated Clinic',
        'facility_type' => 'clinic',
        'country'       => 'CM',
        'city'          => 'Yaounde',
        'address'       => '12 Rue de l\'Hopital',
    ]);

    $response->assertRedirect('/en/customer/profile');
    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name', 'phone' => '+237612345678']);
    $this->assertDatabaseHas('customer_profiles', ['user_id' => $user->id, 'facility_name' => 'Updated Clinic']);
}
```

- [ ] **Step 2: Run the tests — expect FAIL**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/CustomerPortalTest.php --filter=test_authenticated_customer_can_access_dashboard
```

Expected: FAIL — controller class not found

- [ ] **Step 3: Create `app/Http/Controllers/Customer/DashboardController.php`**

```php
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $profile = $user->customerProfile;

        return view('customer.dashboard', compact('user', 'profile'));
    }
}
```

- [ ] **Step 4: Create `app/Http/Controllers/Customer/ProfileController.php`**

```php
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user    = Auth::user();
        $profile = $user->customerProfile ?? $user->customerProfile()->create(['country' => 'CM']);

        return view('customer.profile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'phone'         => 'nullable|string|max:30',
            'facility_name' => 'nullable|string|max:100',
            'facility_type' => 'nullable|string|max:60',
            'country'       => 'required|string|max:60',
            'city'          => 'nullable|string|max:60',
            'address'       => 'nullable|string|max:200',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'phone' => $validated['phone'] ?? null,
        ]);

        $profileData = [
            'facility_name' => $validated['facility_name'] ?? null,
            'facility_type' => $validated['facility_type'] ?? null,
            'country'       => $validated['country'],
            'city'          => $validated['city'] ?? null,
            'address'       => $validated['address'] ?? null,
        ];

        if ($user->customerProfile) {
            $user->customerProfile->update($profileData);
        } else {
            $user->customerProfile()->create(array_merge($profileData, ['user_id' => $user->id]));
        }

        return redirect()
            ->route('customer.profile', ['locale' => app()->getLocale()])
            ->with('success', 'Profile updated successfully.');
    }
}
```

- [ ] **Step 5: Run the tests — expect FAIL (views don't exist yet)**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/CustomerPortalTest.php --filter=test_authenticated_customer_can_access_dashboard
```

Expected: FAIL with `View [customer.dashboard] not found`

- [ ] **Step 6: Create placeholder views to make tests pass**

Create `resources/views/customer/dashboard.blade.php` (temporary, will be replaced in Task 6):

```html
<!DOCTYPE html>
<html><body>
<h1>{{ $user->name }}</h1>
<p>Customer Dashboard</p>
</body></html>
```

Create `resources/views/customer/profile.blade.php` (temporary, will be replaced in Task 6):

```html
<!DOCTYPE html>
<html><body>
<h1>Profile</h1>
<p>{{ $user->name }}</p>
</body></html>
```

- [ ] **Step 7: Run the tests — expect PASS**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test tests/Feature/CustomerPortalTest.php
```

Expected: All tests pass.

- [ ] **Step 8: Run full suite**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test
```

Expected: All tests pass.

- [ ] **Step 9: Commit**

```bash
cd /c/laragon/www/ohs && git add app/Http/Controllers/Customer/ resources/views/customer/ tests/Feature/CustomerPortalTest.php
git commit -m "feat: add DashboardController and ProfileController for customer portal"
```

---

## Task 5: Auth Layout + Auth Views (Login + Register)

**Files:**
- Create: `resources/views/components/layouts/auth.blade.php`
- Create: `resources/views/auth/login.blade.php`
- Create: `resources/views/auth/register.blade.php`
- Modify: `resources/css/app.css` (add `auth-*` CSS)

The views use the OPES dark theme: background `#0F172A`, card `#1E293B`, accent `#00C896`, text `#e2e8f0`.

- [ ] **Step 1: Create `resources/views/components/layouts/auth.blade.php`**

```html
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'OPES Health Systems' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <a href="{{ url('/') }}" class="auth-brand">
            <span class="auth-brand-opes">OPES</span>
            <span class="auth-brand-name"> Health Systems</span>
        </a>
        {{ $slot }}
        <p class="auth-footer-note">
            &copy; {{ date('Y') }} OPES Health Systems SARL — Douala, Cameroon
        </p>
    </div>
    <script src="https://unpkg.com/lucide@0.511.0/dist/umd/lucide.min.js" crossorigin="anonymous"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
```

- [ ] **Step 2: Create `resources/views/auth/login.blade.php`**

```html
<x-layouts.auth title="Sign In">
    <div class="auth-card">
        <h1 class="auth-heading">Welcome back</h1>
        <p class="auth-subheading">Sign in to your OPES customer portal</p>

        @if ($errors->any())
            <div class="auth-error-box">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="auth-form">
            @csrf
            <input type="hidden" name="locale" value="{{ request()->segment(1) === 'fr' ? 'fr' : 'en' }}">

            <div class="auth-field">
                <label for="email" class="auth-label">Email address</label>
                <input
                    id="email" name="email" type="email"
                    class="auth-input @error('email') auth-input-error @enderror"
                    value="{{ old('email') }}"
                    required autofocus autocomplete="email"
                    placeholder="you@facility.cm"
                >
            </div>

            <div class="auth-field">
                <label for="password" class="auth-label">Password</label>
                <input
                    id="password" name="password" type="password"
                    class="auth-input"
                    required autocomplete="current-password"
                    placeholder="••••••••"
                >
            </div>

            <div class="auth-remember">
                <label class="auth-check-label">
                    <input type="checkbox" name="remember" class="auth-check"> Remember me
                </label>
            </div>

            <button type="submit" class="auth-btn">Sign In</button>
        </form>

        <p class="auth-switch">
            Don't have an account?
            <a href="{{ route('register') }}" class="auth-link">Create one</a>
        </p>
    </div>
</x-layouts.auth>
```

- [ ] **Step 3: Create `resources/views/auth/register.blade.php`**

```html
<x-layouts.auth title="Create Account">
    <div class="auth-card auth-card-wide">
        <h1 class="auth-heading">Create your account</h1>
        <p class="auth-subheading">Join OPES Health Systems — digitising healthcare in Cameroon</p>

        @if ($errors->any())
            <div class="auth-error-box">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" class="auth-form">
            @csrf
            <input type="hidden" name="locale" value="{{ request()->segment(1) === 'fr' ? 'fr' : 'en' }}">

            <p class="auth-section-label">Contact Information</p>
            <div class="auth-grid-2">
                <div class="auth-field">
                    <label class="auth-label">Full Name *</label>
                    <input name="name" type="text" class="auth-input @error('name') auth-input-error @enderror"
                        value="{{ old('name') }}" required placeholder="Dr. Ambe John">
                </div>
                <div class="auth-field">
                    <label class="auth-label">Email Address *</label>
                    <input name="email" type="email" class="auth-input @error('email') auth-input-error @enderror"
                        value="{{ old('email') }}" required placeholder="you@hospital.cm">
                </div>
                <div class="auth-field">
                    <label class="auth-label">Password *</label>
                    <input name="password" type="password" class="auth-input"
                        required minlength="8" placeholder="Min. 8 characters">
                </div>
                <div class="auth-field">
                    <label class="auth-label">Confirm Password *</label>
                    <input name="password_confirmation" type="password" class="auth-input"
                        required placeholder="Repeat password">
                </div>
                <div class="auth-field">
                    <label class="auth-label">Phone</label>
                    <input name="phone" type="tel" class="auth-input"
                        value="{{ old('phone') }}" placeholder="+237 6XX XXX XXX">
                </div>
            </div>

            <p class="auth-section-label" style="margin-top:1.5rem">Facility Information</p>
            <div class="auth-grid-2">
                <div class="auth-field">
                    <label class="auth-label">Facility Name</label>
                    <input name="facility_name" type="text" class="auth-input"
                        value="{{ old('facility_name') }}" placeholder="Central Hospital Douala">
                </div>
                <div class="auth-field">
                    <label class="auth-label">Facility Type</label>
                    <select name="facility_type" class="auth-input auth-select">
                        <option value="">— Select type —</option>
                        @foreach(['hospital'=>'Hospital','clinic'=>'Clinic','laboratory'=>'Laboratory','pharmacy'=>'Pharmacy','radiology'=>'Radiology Centre','nursing_home'=>'Nursing Home','other'=>'Other'] as $val => $label)
                            <option value="{{ $val }}" {{ old('facility_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="auth-field">
                    <label class="auth-label">Country *</label>
                    <input name="country" type="text" class="auth-input @error('country') auth-input-error @enderror"
                        value="{{ old('country', 'CM') }}" required placeholder="CM">
                </div>
                <div class="auth-field">
                    <label class="auth-label">City</label>
                    <input name="city" type="text" class="auth-input"
                        value="{{ old('city') }}" placeholder="Douala">
                </div>
            </div>

            <button type="submit" class="auth-btn" style="margin-top:1.5rem">Create Account</button>
        </form>

        <p class="auth-switch">
            Already have an account?
            <a href="{{ route('login') }}" class="auth-link">Sign in</a>
        </p>
    </div>
</x-layouts.auth>
```

- [ ] **Step 4: Add `auth-*` CSS classes to `resources/css/app.css`**

Append to the end of `resources/css/app.css`:

```css
/* ── Auth pages ─────────────────────────────────────────── */
.auth-body {
    background: #0F172A;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
    padding: 2rem 1rem;
}
.auth-wrapper {
    width: 100%;
    max-width: 460px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}
.auth-brand { text-decoration: none; }
.auth-brand-opes { color: #00C896; font-weight: 700; font-size: 1.4rem; letter-spacing: -0.02em; }
.auth-brand-name { color: #e2e8f0; font-size: 1rem; font-weight: 400; }
.auth-card {
    width: 100%;
    background: #1E293B;
    border: 1px solid #334155;
    border-radius: 12px;
    padding: 2rem;
}
.auth-card-wide { max-width: 640px; }
.auth-heading { color: #f1f5f9; font-size: 1.5rem; font-weight: 700; margin: 0 0 0.25rem; }
.auth-subheading { color: #94a3b8; font-size: 0.875rem; margin: 0 0 1.5rem; }
.auth-error-box {
    background: rgba(239,68,68,0.1);
    border: 1px solid rgba(239,68,68,0.3);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    color: #fca5a5;
    font-size: 0.875rem;
}
.auth-error-box p { margin: 0; }
.auth-form { display: flex; flex-direction: column; gap: 1rem; }
.auth-field { display: flex; flex-direction: column; gap: 0.375rem; }
.auth-label { color: #94a3b8; font-size: 0.8125rem; font-weight: 500; }
.auth-input {
    background: #0F172A;
    border: 1px solid #334155;
    border-radius: 8px;
    color: #e2e8f0;
    font-size: 0.9375rem;
    padding: 0.625rem 0.875rem;
    width: 100%;
    transition: border-color 0.15s;
    box-sizing: border-box;
}
.auth-input:focus { outline: none; border-color: #00C896; }
.auth-input::placeholder { color: #475569; }
.auth-input-error { border-color: #ef4444; }
.auth-select { cursor: pointer; }
.auth-section-label { color: #00C896; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; margin: 0 0 0.5rem; }
.auth-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media (max-width: 560px) { .auth-grid-2 { grid-template-columns: 1fr; } }
.auth-remember { display: flex; align-items: center; }
.auth-check-label { display: flex; align-items: center; gap: 0.5rem; color: #94a3b8; font-size: 0.875rem; cursor: pointer; }
.auth-check { accent-color: #00C896; }
.auth-btn {
    background: #00C896;
    color: #0F172A;
    font-weight: 700;
    font-size: 0.9375rem;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    width: 100%;
    cursor: pointer;
    transition: background 0.15s, transform 0.1s;
}
.auth-btn:hover { background: #00b386; transform: translateY(-1px); }
.auth-switch { color: #64748b; font-size: 0.875rem; text-align: center; margin: 0; }
.auth-link { color: #00C896; text-decoration: none; font-weight: 500; }
.auth-link:hover { text-decoration: underline; }
.auth-footer-note { color: #334155; font-size: 0.75rem; text-align: center; margin: 0; }
```

- [ ] **Step 5: Build assets**

```bash
cd /c/laragon/www/ohs && npm run build 2>&1 | tail -5
```

Expected: Vite build completes with no errors.

- [ ] **Step 6: Run full test suite**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test
```

Expected: All tests pass.

- [ ] **Step 7: Commit**

```bash
cd /c/laragon/www/ohs && git add resources/views/components/layouts/auth.blade.php resources/views/auth/ resources/css/app.css
git commit -m "feat: add auth layout component and login/register views with dark theme"
```

---

## Task 6: Customer Portal Layout + Dashboard View + Profile View

**Files:**
- Create: `resources/views/components/layouts/customer.blade.php`
- Modify: `resources/views/customer/dashboard.blade.php` (replace placeholder)
- Modify: `resources/views/customer/profile.blade.php` (replace placeholder)
- Modify: `resources/css/app.css` (add `customer-*` CSS)

- [ ] **Step 1: Create `resources/views/components/layouts/customer.blade.php`**

```html
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Customer Portal' }} — OPES Health Systems</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cp-body">
    <nav class="cp-nav">
        <a href="{{ route('customer.dashboard', ['locale' => app()->getLocale()]) }}" class="cp-nav-brand">
            <span class="cp-brand-opes">OPES</span>
            <span class="cp-brand-name"> Portal</span>
        </a>
        <div class="cp-nav-links">
            <a href="{{ route('customer.dashboard', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.dashboard') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="layout-dashboard" style="width:16px;height:16px"></i> Dashboard
            </a>
            <a href="#" class="cp-nav-link cp-nav-link-disabled" title="Coming soon">
                <i data-lucide="key" style="width:16px;height:16px"></i> Licenses
            </a>
            <a href="#" class="cp-nav-link cp-nav-link-disabled" title="Coming soon">
                <i data-lucide="ticket" style="width:16px;height:16px"></i> Support
            </a>
        </div>
        <div class="cp-nav-user">
            <span class="cp-nav-username">{{ auth()->user()->name }}</span>
            <a href="{{ route('customer.profile', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.profile') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="user" style="width:16px;height:16px"></i>
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="cp-logout-btn" title="Sign out">
                    <i data-lucide="log-out" style="width:16px;height:16px"></i>
                </button>
            </form>
        </div>
    </nav>

    @if (session('success'))
        <div class="cp-flash-success">{{ session('success') }}</div>
    @endif

    <main class="cp-main">
        <div class="cp-container">
            {{ $slot }}
        </div>
    </main>

    <script src="https://unpkg.com/lucide@0.511.0/dist/umd/lucide.min.js" crossorigin="anonymous"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
```

- [ ] **Step 2: Replace `resources/views/customer/dashboard.blade.php`**

```html
<x-layouts.customer title="Dashboard">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">Welcome, {{ $user->name }}</h1>
            <p class="cp-page-subtitle">
                {{ $profile?->facility_name ?? 'Your OPES Health Systems account' }}
                @if($profile?->city) · {{ $profile->city }} @endif
                @if($profile?->country) · {{ $profile->country }} @endif
            </p>
        </div>
        <a href="{{ route('customer.profile', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">
            <i data-lucide="settings" style="width:15px;height:15px"></i> Edit Profile
        </a>
    </div>

    <div class="cp-stats-row">
        <div class="cp-stat-card">
            <div class="cp-stat-icon" style="background:rgba(0,200,150,0.1)">
                <i data-lucide="key" style="width:20px;height:20px;color:#00C896"></i>
            </div>
            <div>
                <p class="cp-stat-value">0</p>
                <p class="cp-stat-label">Active Licenses</p>
            </div>
        </div>
        <div class="cp-stat-card">
            <div class="cp-stat-icon" style="background:rgba(26,111,232,0.1)">
                <i data-lucide="ticket" style="width:20px;height:20px;color:#1A6FE8"></i>
            </div>
            <div>
                <p class="cp-stat-value">0</p>
                <p class="cp-stat-label">Open Tickets</p>
            </div>
        </div>
        <div class="cp-stat-card">
            <div class="cp-stat-icon" style="background:rgba(234,179,8,0.1)">
                <i data-lucide="bug" style="width:20px;height:20px;color:#eab308"></i>
            </div>
            <div>
                <p class="cp-stat-value">0</p>
                <p class="cp-stat-label">Bug Reports</p>
            </div>
        </div>
    </div>

    <div class="cp-section-grid">
        <div class="cp-section-card">
            <div class="cp-section-header">
                <h2 class="cp-section-title">
                    <i data-lucide="key" style="width:18px;height:18px;color:#00C896"></i> My Licenses
                </h2>
                <a href="{{ route('contact', ['locale' => app()->getLocale()]) }}" class="cp-btn-primary">
                    Request License
                </a>
            </div>
            <div class="cp-empty-state">
                <i data-lucide="package-open" style="width:40px;height:40px;color:#334155"></i>
                <p>No active licenses yet.</p>
                <p style="font-size:0.8125rem">Contact us to purchase software licenses for your facility.</p>
            </div>
        </div>

        <div class="cp-section-card">
            <div class="cp-section-header">
                <h2 class="cp-section-title">
                    <i data-lucide="ticket" style="width:18px;height:18px;color:#1A6FE8"></i> Support Tickets
                </h2>
                <span class="cp-badge-coming-soon">Coming soon</span>
            </div>
            <div class="cp-empty-state">
                <i data-lucide="message-circle" style="width:40px;height:40px;color:#334155"></i>
                <p>No open tickets.</p>
                <p style="font-size:0.8125rem">Ticket system launching soon — contact us directly for urgent issues.</p>
            </div>
        </div>
    </div>

    <div class="cp-help-card">
        <i data-lucide="life-buoy" style="width:24px;height:24px;color:#00C896"></i>
        <div>
            <p class="cp-help-title">Need help?</p>
            <p class="cp-help-text">Our team is available Mon–Fri 8 am – 6 pm (WAT). Email
                <a href="mailto:support@opeshealthsystems.com" class="auth-link">support@opeshealthsystems.com</a>
                or visit our <a href="{{ route('contact', ['locale' => app()->getLocale()]) }}" class="auth-link">contact page</a>.
            </p>
        </div>
    </div>
</x-layouts.customer>
```

- [ ] **Step 3: Replace `resources/views/customer/profile.blade.php`**

```html
<x-layouts.customer title="My Profile">
    <div class="cp-page-header">
        <h1 class="cp-page-title">My Profile</h1>
        <p class="cp-page-subtitle">Manage your account and facility information</p>
    </div>

    <form method="POST" action="{{ route('customer.profile.update', ['locale' => app()->getLocale()]) }}" class="cp-form">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="auth-error-box">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <div class="cp-section-card">
            <h2 class="cp-section-title" style="margin-bottom:1.5rem">
                <i data-lucide="user" style="width:18px;height:18px;color:#00C896"></i> Contact Details
            </h2>
            <div class="auth-grid-2">
                <div class="auth-field">
                    <label class="auth-label">Full Name *</label>
                    <input name="name" type="text" class="auth-input @error('name') auth-input-error @enderror"
                        value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="auth-field">
                    <label class="auth-label">Email Address</label>
                    <input type="email" class="auth-input" value="{{ $user->email }}" disabled
                        style="opacity:0.5;cursor:not-allowed" title="Contact support to change email">
                </div>
                <div class="auth-field">
                    <label class="auth-label">Phone</label>
                    <input name="phone" type="tel" class="auth-input"
                        value="{{ old('phone', $user->phone) }}" placeholder="+237 6XX XXX XXX">
                </div>
            </div>
        </div>

        <div class="cp-section-card" style="margin-top:1.5rem">
            <h2 class="cp-section-title" style="margin-bottom:1.5rem">
                <i data-lucide="building-2" style="width:18px;height:18px;color:#00C896"></i> Facility Information
            </h2>
            <div class="auth-grid-2">
                <div class="auth-field">
                    <label class="auth-label">Facility Name</label>
                    <input name="facility_name" type="text" class="auth-input"
                        value="{{ old('facility_name', $profile->facility_name) }}" placeholder="Central Hospital Douala">
                </div>
                <div class="auth-field">
                    <label class="auth-label">Facility Type</label>
                    <select name="facility_type" class="auth-input auth-select">
                        <option value="">— Select —</option>
                        @foreach(['hospital'=>'Hospital','clinic'=>'Clinic','laboratory'=>'Laboratory','pharmacy'=>'Pharmacy','radiology'=>'Radiology Centre','nursing_home'=>'Nursing Home','other'=>'Other'] as $val => $label)
                            <option value="{{ $val }}" {{ old('facility_type', $profile->facility_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="auth-field">
                    <label class="auth-label">Country *</label>
                    <input name="country" type="text" class="auth-input @error('country') auth-input-error @enderror"
                        value="{{ old('country', $profile->country) }}" required>
                </div>
                <div class="auth-field">
                    <label class="auth-label">City</label>
                    <input name="city" type="text" class="auth-input"
                        value="{{ old('city', $profile->city) }}" placeholder="Douala">
                </div>
                <div class="auth-field" style="grid-column: 1 / -1">
                    <label class="auth-label">Address</label>
                    <input name="address" type="text" class="auth-input"
                        value="{{ old('address', $profile->address) }}" placeholder="Street address">
                </div>
            </div>
        </div>

        <div style="margin-top:1.5rem; display:flex; gap:1rem; align-items:center">
            <button type="submit" class="auth-btn" style="width:auto; padding:0.75rem 2rem">Save Changes</button>
            <a href="{{ route('customer.dashboard', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">
                Cancel
            </a>
        </div>
    </form>
</x-layouts.customer>
```

- [ ] **Step 4: Add `customer-portal` CSS to `resources/css/app.css`**

Append to the end of `resources/css/app.css`:

```css
/* ── Customer Portal ─────────────────────────────────────── */
.cp-body { background: #0F172A; min-height: 100vh; font-family: 'Inter', sans-serif; }
.cp-nav {
    background: #1E293B;
    border-bottom: 1px solid #334155;
    display: flex;
    align-items: center;
    padding: 0 2rem;
    height: 60px;
    gap: 2rem;
    position: sticky;
    top: 0;
    z-index: 50;
}
.cp-nav-brand { text-decoration: none; flex-shrink: 0; }
.cp-brand-opes { color: #00C896; font-weight: 700; font-size: 1.15rem; }
.cp-brand-name { color: #94a3b8; font-size: 0.875rem; }
.cp-nav-links { display: flex; gap: 0.25rem; flex: 1; }
.cp-nav-user { display: flex; align-items: center; gap: 0.5rem; margin-left: auto; }
.cp-nav-username { color: #94a3b8; font-size: 0.8125rem; }
.cp-nav-link {
    display: flex; align-items: center; gap: 0.375rem;
    color: #94a3b8; font-size: 0.875rem; text-decoration: none;
    padding: 0.375rem 0.75rem; border-radius: 6px;
    transition: color 0.15s, background 0.15s;
}
.cp-nav-link:hover { color: #e2e8f0; background: rgba(255,255,255,0.05); }
.cp-nav-link-active { color: #00C896 !important; background: rgba(0,200,150,0.1) !important; }
.cp-nav-link-disabled { opacity: 0.4; cursor: default; pointer-events: none; }
.cp-logout-btn {
    background: none; border: none; cursor: pointer;
    color: #64748b; padding: 0.375rem; border-radius: 6px;
    display: flex; align-items: center;
    transition: color 0.15s, background 0.15s;
}
.cp-logout-btn:hover { color: #ef4444; background: rgba(239,68,68,0.1); }
.cp-flash-success {
    background: rgba(0,200,150,0.1); border-bottom: 1px solid rgba(0,200,150,0.3);
    color: #00C896; text-align: center; padding: 0.75rem; font-size: 0.875rem;
}
.cp-main { padding: 2rem 0; }
.cp-container { max-width: 1100px; margin: 0 auto; padding: 0 2rem; }
.cp-page-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;
}
.cp-page-title { color: #f1f5f9; font-size: 1.5rem; font-weight: 700; margin: 0; }
.cp-page-subtitle { color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0; }
.cp-stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem; }
@media (max-width: 640px) { .cp-stats-row { grid-template-columns: 1fr; } }
.cp-stat-card {
    background: #1E293B; border: 1px solid #334155; border-radius: 10px;
    padding: 1.25rem; display: flex; align-items: center; gap: 1rem;
}
.cp-stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.cp-stat-value { color: #f1f5f9; font-size: 1.5rem; font-weight: 700; margin: 0; }
.cp-stat-label { color: #64748b; font-size: 0.8125rem; margin: 0; }
.cp-section-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
@media (max-width: 768px) { .cp-section-grid { grid-template-columns: 1fr; } }
.cp-section-card {
    background: #1E293B; border: 1px solid #334155; border-radius: 12px; padding: 1.5rem;
}
.cp-section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
.cp-section-title {
    color: #e2e8f0; font-size: 1rem; font-weight: 600; margin: 0;
    display: flex; align-items: center; gap: 0.5rem;
}
.cp-empty-state {
    display: flex; flex-direction: column; align-items: center;
    gap: 0.5rem; padding: 2rem 1rem; color: #475569; font-size: 0.875rem; text-align: center;
}
.cp-help-card {
    background: rgba(0,200,150,0.05); border: 1px solid rgba(0,200,150,0.15);
    border-radius: 10px; padding: 1.25rem 1.5rem;
    display: flex; align-items: flex-start; gap: 1rem;
}
.cp-help-title { color: #e2e8f0; font-weight: 600; font-size: 0.9375rem; margin: 0 0 0.25rem; }
.cp-help-text { color: #64748b; font-size: 0.875rem; margin: 0; }
.cp-badge-coming-soon {
    background: rgba(100,116,139,0.15); color: #64748b;
    font-size: 0.6875rem; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.06em; padding: 0.25rem 0.625rem; border-radius: 20px;
}
.cp-btn-primary {
    background: #00C896; color: #0F172A; font-weight: 600; font-size: 0.8125rem;
    text-decoration: none; border-radius: 6px; padding: 0.5rem 1rem;
    display: inline-flex; align-items: center; gap: 0.375rem;
    transition: background 0.15s;
}
.cp-btn-primary:hover { background: #00b386; }
.cp-btn-outline {
    color: #94a3b8; font-size: 0.8125rem; text-decoration: none;
    border: 1px solid #334155; border-radius: 6px; padding: 0.5rem 1rem;
    display: inline-flex; align-items: center; gap: 0.375rem;
    transition: border-color 0.15s, color 0.15s;
    background: none; cursor: pointer;
}
.cp-btn-outline:hover { border-color: #475569; color: #e2e8f0; }
.cp-form { max-width: 800px; }
```

- [ ] **Step 5: Build assets**

```bash
cd /c/laragon/www/ohs && npm run build 2>&1 | tail -5
```

- [ ] **Step 6: Run full test suite**

```bash
/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe artisan test
```

Expected: All tests pass.

- [ ] **Step 7: Commit**

```bash
cd /c/laragon/www/ohs && git add resources/views/components/layouts/customer.blade.php resources/views/customer/ resources/css/app.css
git commit -m "feat: add customer portal layout, dashboard, and profile views"
```

---

## Self-Review

### 1. Spec coverage

| Requirement | Covered |
|---|---|
| Customer self-registration | ✅ Task 3 RegisterController, Task 5 register view |
| Customer login with role-based redirect | ✅ Task 3 LoginController |
| Customer dashboard | ✅ Task 4 DashboardController, Task 6 dashboard view |
| Profile management (facility info) | ✅ Task 4 ProfileController + CustomerProfile model, Task 6 profile view |
| `customer_profiles` table with facility data | ✅ Task 1 migration + model |
| Auth layout (standalone, branded) | ✅ Task 5 `layouts.auth` |
| Customer portal layout (nav, sidebar-free) | ✅ Task 6 `layouts.customer` |
| Named `login` route (required by Laravel `auth` middleware) | ✅ Task 2 routes |
| Staff redirected to `/admin` on login | ✅ Task 3 LoginController |
| Customers redirected to `/{locale}/customer/dashboard` on login | ✅ Task 3 |
| Tests covering registration, login, access control, profile update | ✅ Task 2, 3, 4 tests |

### 2. Placeholder scan

No TBD, no TODO, no placeholder steps.

### 3. Type consistency

- `customerProfile()` returns `HasOne` in User — used as `$user->customerProfile` in controllers/views.
- `CustomerProfile::create()` uses same `$fillable` keys as migration columns.
- `route('customer.dashboard', ['locale' => ...])` matches the route name `customer.dashboard` defined in `routes/web.php`.
