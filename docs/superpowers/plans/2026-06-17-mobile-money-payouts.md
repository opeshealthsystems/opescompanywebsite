# Mobile-Money Payout Integration (MTN MoMo + Orange Money) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Pay practitioners their paid-programme compensation via Cameroon mobile money — admin-triggered MTN MoMo disbursements (implemented), Orange Money (scaffolded), with poll-based confirmation and a manual fallback.

**Architecture:** Extends the existing `App\Services\Payouts\PayoutGateway` seam. A new `PayoutGatewayManager` selects a driver per payout from the network detected on the practitioner's `payout_number`. The Filament "Pay now" action initiates a disbursement (idempotent reference); a scheduled `payouts:poll` command confirms it. All HTTP is tested via `Http::fake()` — no live calls; credentials are user-supplied in `.env`.

**Tech Stack:** Laravel 13, PHP 8.3.30, Filament v3, Laravel HTTP client, PHPUnit (in-memory sqlite).

**Conventions:**
- PHP binary: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`
- Migrate: `& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan migrate`
- Tests: `& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" vendor/bin/phpunit --no-coverage`
- Commit messages: PowerShell here-string `git commit -m @'...'@`; end with `Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>`.
- Baseline before this plan: **223 tests green** on `main`.

**Existing code this builds on:**
- `app/Services/Payouts/PayoutGateway.php` — `disburse(PractitionerApplication $application, float $amount, string $currency, array $options = []): PayoutResult` and `status(PractitionerApplication $application): string`.
- `app/Services/Payouts/PayoutResult.php` — `::settled(?string $ref)`, `::pending(?string $ref, ?string $msg)`, `::failed(string $msg)`; public `bool $success`, `string $status`, `?string $reference`, `?string $message`.
- `app/Services/Payouts/ManualPayoutGateway.php` (default), `MtnMomoPayoutGateway.php` / `OrangeMoneyPayoutGateway.php` (stubs that throw).
- `config/payouts.php` — `driver`, `currency`, `mtn_momo` (`base_url`, `subscription_key`, `api_user`, `api_key`, `environment`), `orange_money` (`base_url`, `client_id`, `client_secret`, `merchant_key`, `environment`).
- `app/Providers/AppServiceProvider.php` — binds `PayoutGateway::class` to the configured driver (keep: this is the "default/manual" driver).
- `app/Models/PractitionerApplication.php` — `payout_status` (`not_applicable|pending|paid`), `payout_amount`, `payout_currency`, `payout_reference`, `paid_at`; `markApproved()`, `isPaidProgram()`, `payoutStatusOptions()`; relations `practitioner()` (User), `program()`.
- `app/Models/PractitionerProfile.php` — belongsTo `user()`; `App\Models\User` hasOne `practitionerProfile()`.
- `app/Filament/Resources/PractitionerApplicationResource.php` — `record_payout` action routing through `app(PayoutGateway::class)->disburse(...)`.
- `app/Support/AdminNotifier.php` — `AdminNotifier::notify($title, $body, ?$url, array $roles)`.

---

## Task 1: `payout_number` on practitioner profiles

**Files:**
- Create: `database/migrations/2026_06_19_100000_add_payout_number_to_practitioner_profiles.php`
- Modify: `app/Models/PractitionerProfile.php`
- Modify: `app/Http/Controllers/Practitioner/ProfileController.php` (validation + save)
- Modify: `resources/views/practitioner/profile.blade.php` (input field)
- Test: `tests/Feature/PractitionerPayoutNumberTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PractitionerPayoutNumberTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function practitioner(): User
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM']);
        return $user;
    }

    public function test_practitioner_can_save_payout_number(): void
    {
        $user = $this->practitioner();

        $this->actingAs($user)
            ->put('/en/practitioner/profile', [
                'name'           => $user->name,
                'profession'     => 'doctor',
                'payout_number'  => '+237 677 123 456',
            ])
            ->assertRedirect();

        $this->assertSame('237677123456', $user->practitionerProfile->fresh()->payout_number);
    }

    public function test_payout_number_rejects_non_numeric(): void
    {
        $user = $this->practitioner();

        $this->actingAs($user)
            ->put('/en/practitioner/profile', [
                'name'          => $user->name,
                'profession'    => 'doctor',
                'payout_number' => 'not-a-number',
            ])
            ->assertSessionHasErrors('payout_number');
    }
}
```

- [ ] **Step 2: Run it — expect FAIL** (`Undefined column payout_number` / no validation).

Run: `& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" vendor/bin/phpunit --no-coverage --filter PractitionerPayoutNumberTest`

- [ ] **Step 3: Migration**

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('practitioner_profiles', function (Blueprint $table) {
            $table->string('payout_number', 20)->nullable()->after('registration_number');
        });
    }
    public function down(): void
    {
        Schema::table('practitioner_profiles', function (Blueprint $table) {
            $table->dropColumn('payout_number');
        });
    }
};
```

- [ ] **Step 4: Model** — add `'payout_number'` to `PractitionerProfile::$fillable`.

- [ ] **Step 5: Controller** — in `ProfileController::update()` (read the existing method first), add to the validation array:

```php
'payout_number' => ['nullable', 'string', 'regex:/^[0-9 +]{6,20}$/'],
```

and when filling the profile, normalise + store:

```php
$profile->payout_number = isset($validated['payout_number'])
    ? \App\Services\Payouts\MobileMoneyNetwork::normalise($validated['payout_number'])
    : $profile->payout_number;
```

> NOTE: `MobileMoneyNetwork::normalise()` is created in Task 2. If implementing strictly in order, temporarily inline `preg_replace('/\D+/', '', $validated['payout_number'])` here and replace with the helper call in Task 2. The test expects `'+237 677 123 456'` → `'237677123456'`.

- [ ] **Step 6: View** — in `resources/views/practitioner/profile.blade.php`, add a text input bound to `old('payout_number', $user->practitionerProfile->payout_number)` labelled "Mobile-money number (for paid-programme payouts)". Match the existing dark-theme form field markup in that file.

- [ ] **Step 7: Run the test — expect PASS.**

- [ ] **Step 8: Commit**

```
git add database/migrations/2026_06_19_100000_add_payout_number_to_practitioner_profiles.php app/Models/PractitionerProfile.php app/Http/Controllers/Practitioner/ProfileController.php resources/views/practitioner/profile.blade.php tests/Feature/PractitionerPayoutNumberTest.php
git commit -m @'feat(payouts): capture practitioner mobile-money payout number ...'@
```

---

## Task 2: `MobileMoneyNetwork` detection helper

**Files:**
- Create: `app/Services/Payouts/MobileMoneyNetwork.php`
- Test: `tests/Unit/MobileMoneyNetworkTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php
namespace Tests\Unit;

use App\Services\Payouts\MobileMoneyNetwork;
use PHPUnit\Framework\TestCase;

class MobileMoneyNetworkTest extends TestCase
{
    public function test_normalise_strips_spaces_plus_and_country_code(): void
    {
        $this->assertSame('677123456', MobileMoneyNetwork::normalise('+237 677 123 456'));
        $this->assertSame('699000111', MobileMoneyNetwork::normalise('237699000111'));
        $this->assertSame('680111222', MobileMoneyNetwork::normalise('680 111 222'));
    }

    public function test_detect_mtn_numbers(): void
    {
        $this->assertSame('mtn', MobileMoneyNetwork::detect('+237 677 12 34 56')); // 67x
        $this->assertSame('mtn', MobileMoneyNetwork::detect('650111222'));        // 650-654
        $this->assertSame('mtn', MobileMoneyNetwork::detect('680111222'));        // 680-689
    }

    public function test_detect_orange_numbers(): void
    {
        $this->assertSame('orange', MobileMoneyNetwork::detect('+237 699 12 34 56')); // 69x
        $this->assertSame('orange', MobileMoneyNetwork::detect('655111222'));         // 655-659
        $this->assertSame('orange', MobileMoneyNetwork::detect('640111222'));         // 640-649
    }

    public function test_detect_unknown_returns_null(): void
    {
        $this->assertNull(MobileMoneyNetwork::detect('620111222')); // other operator
        $this->assertNull(MobileMoneyNetwork::detect(''));
    }
}
```

- [ ] **Step 2: Run it — expect FAIL** (class missing).

- [ ] **Step 3: Implement**

```php
<?php
namespace App\Services\Payouts;

class MobileMoneyNetwork
{
    /** Strip spaces/+, drop the 237 country code, return the local 9-digit MSISDN. */
    public static function normalise(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number) ?? '';
        if (str_starts_with($digits, '237') && strlen($digits) > 9) {
            $digits = substr($digits, 3);
        }
        return $digits;
    }

    /** @return 'mtn'|'orange'|null */
    public static function detect(string $number): ?string
    {
        $n = self::normalise($number);
        if (strlen($n) < 3) {
            return null;
        }
        $p2 = substr($n, 0, 2);
        $p3 = (int) substr($n, 0, 3);

        if ($p2 === '67' || ($p3 >= 650 && $p3 <= 654) || ($p3 >= 680 && $p3 <= 689)) {
            return 'mtn';
        }
        if ($p2 === '69' || ($p3 >= 655 && $p3 <= 659) || ($p3 >= 640 && $p3 <= 649)) {
            return 'orange';
        }
        return null;
    }
}
```

- [ ] **Step 4: Run the test — expect PASS.** Then replace the temporary inline normalisation in `ProfileController` (Task 1, Step 5) with `MobileMoneyNetwork::normalise(...)` if not already done, and re-run `PractitionerPayoutNumberTest`.

- [ ] **Step 5: Commit**

```
git add app/Services/Payouts/MobileMoneyNetwork.php tests/Unit/MobileMoneyNetworkTest.php app/Http/Controllers/Practitioner/ProfileController.php
git commit -m @'feat(payouts): add Cameroon mobile-money network detection helper ...'@
```

---

## Task 3: Disbursement fields on `practitioner_applications`

**Files:**
- Create: `database/migrations/2026_06_19_100001_add_disbursement_fields_to_practitioner_applications.php`
- Modify: `app/Models/PractitionerApplication.php`
- Test: `tests/Feature/PractitionerDisbursementFieldsTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php
namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PractitionerDisbursementFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_disbursement_fields_are_fillable_and_cast(): void
    {
        $program = PractitionerProgram::factory()->paid()->create();
        $app = PractitionerApplication::factory()->create([
            'practitioner_id'       => User::factory()->create()->id,
            'program_id'            => $program->id,
            'status'                => 'approved',
            'payout_status'         => 'pending',
            'payout_provider'       => 'mtn',
            'payout_initiated_at'   => now(),
            'payout_failure_reason' => null,
        ]);

        $this->assertSame('mtn', $app->fresh()->payout_provider);
        $this->assertNotNull($app->fresh()->payout_initiated_at);
    }
}
```

- [ ] **Step 2: Run it — expect FAIL.**

- [ ] **Step 3: Migration**

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('practitioner_applications', function (Blueprint $table) {
            $table->string('payout_provider', 20)->nullable()->after('payout_reference');
            $table->timestamp('payout_initiated_at')->nullable()->after('payout_provider');
            $table->string('payout_failure_reason', 255)->nullable()->after('payout_initiated_at');
        });
    }
    public function down(): void
    {
        Schema::table('practitioner_applications', function (Blueprint $table) {
            $table->dropColumn(['payout_provider', 'payout_initiated_at', 'payout_failure_reason']);
        });
    }
};
```

- [ ] **Step 4: Model** — add `'payout_provider'`, `'payout_initiated_at'`, `'payout_failure_reason'` to `$fillable`; add `'payout_initiated_at' => 'datetime'` to `$casts`. Update `payoutStatusOptions()` to include `'failed' => 'Failed'`.

- [ ] **Step 5: Run the test — expect PASS.**

- [ ] **Step 6: Commit**

```
git add database/migrations/2026_06_19_100001_add_disbursement_fields_to_practitioner_applications.php app/Models/PractitionerApplication.php tests/Feature/PractitionerDisbursementFieldsTest.php
git commit -m @'feat(payouts): add disbursement tracking fields to applications ...'@
```

---

## Task 4: `PayoutGatewayManager` (per-network driver selection)

**Files:**
- Create: `app/Services/Payouts/PayoutGatewayManager.php`
- Modify: `app/Providers/AppServiceProvider.php` (register the manager as a singleton)
- Test: `tests/Feature/PayoutGatewayManagerTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php
namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use App\Services\Payouts\ManualPayoutGateway;
use App\Services\Payouts\MtnMomoPayoutGateway;
use App\Services\Payouts\OrangeMoneyPayoutGateway;
use App\Services\Payouts\PayoutGatewayManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutGatewayManagerTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): PayoutGatewayManager
    {
        return app(PayoutGatewayManager::class);
    }

    public function test_driver_for_network(): void
    {
        $this->assertInstanceOf(MtnMomoPayoutGateway::class, $this->manager()->driverFor('mtn'));
        $this->assertInstanceOf(OrangeMoneyPayoutGateway::class, $this->manager()->driverFor('orange'));
        $this->assertInstanceOf(ManualPayoutGateway::class, $this->manager()->driverFor('manual'));
        $this->assertInstanceOf(ManualPayoutGateway::class, $this->manager()->driverFor(null));
    }

    public function test_resolve_network_from_profile_number(): void
    {
        $user = User::factory()->create();
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM', 'payout_number' => '677123456']);
        $program = PractitionerProgram::factory()->paid()->create();
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $user->id, 'program_id' => $program->id]);

        $this->assertSame('mtn', $this->manager()->resolveNetwork($app));
    }

    public function test_resolve_network_override_wins(): void
    {
        $user = User::factory()->create();
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM', 'payout_number' => '677123456']);
        $program = PractitionerProgram::factory()->paid()->create();
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $user->id, 'program_id' => $program->id]);

        $this->assertSame('orange', $this->manager()->resolveNetwork($app, 'orange'));
    }

    public function test_resolve_network_falls_back_to_manual_without_number(): void
    {
        $user = User::factory()->create();
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM']);
        $program = PractitionerProgram::factory()->paid()->create();
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $user->id, 'program_id' => $program->id]);

        $this->assertSame('manual', $this->manager()->resolveNetwork($app));
    }
}
```

- [ ] **Step 2: Run it — expect FAIL.**

- [ ] **Step 3: Implement the manager**

```php
<?php
namespace App\Services\Payouts;

use App\Models\PractitionerApplication;
use Illuminate\Contracts\Container\Container;

class PayoutGatewayManager
{
    public function __construct(private Container $app) {}

    public function driverFor(?string $network): PayoutGateway
    {
        return match ($network) {
            'mtn'    => $this->app->make(MtnMomoPayoutGateway::class),
            'orange' => $this->app->make(OrangeMoneyPayoutGateway::class),
            default  => $this->app->make(PayoutGateway::class), // manual / configured default
        };
    }

    /** Network for this application: explicit override, else detected from the payout number, else 'manual'. */
    public function resolveNetwork(PractitionerApplication $application, ?string $override = null): string
    {
        if ($override) {
            return $override;
        }
        $number = $application->practitioner?->practitionerProfile?->payout_number;
        if (! $number) {
            return 'manual';
        }
        return MobileMoneyNetwork::detect($number) ?? 'manual';
    }
}
```

- [ ] **Step 4: Register** in `AppServiceProvider::register()` (after the existing `PayoutGateway` bind):

```php
$this->app->singleton(PayoutGatewayManager::class, fn ($app) => new PayoutGatewayManager($app));
```

(Add `use App\Services\Payouts\PayoutGatewayManager;` to the imports.)

- [ ] **Step 5: Run the test — expect PASS.**

- [ ] **Step 6: Commit**

```
git add app/Services/Payouts/PayoutGatewayManager.php app/Providers/AppServiceProvider.php tests/Feature/PayoutGatewayManagerTest.php
git commit -m @'feat(payouts): add PayoutGatewayManager for per-network driver selection ...'@
```

---

## Task 5: Implement `MtnMomoPayoutGateway` against the MoMo sandbox

**Files:**
- Modify: `app/Services/Payouts/MtnMomoPayoutGateway.php`
- Test: `tests/Feature/MtnMomoPayoutGatewayTest.php`

- [ ] **Step 1: Write the failing test (mocked HTTP)**

```php
<?php
namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use App\Services\Payouts\MtnMomoPayoutGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MtnMomoPayoutGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('payouts.mtn_momo', [
            'base_url'         => 'https://sandbox.momodeveloper.mtn.com',
            'subscription_key' => 'test-sub-key',
            'api_user'         => 'test-api-user',
            'api_key'          => 'test-api-key',
            'environment'      => 'sandbox',
        ]);
    }

    private function paidApplication(string $number = '677123456'): PractitionerApplication
    {
        $user = User::factory()->create();
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM', 'payout_number' => $number]);
        $program = PractitionerProgram::factory()->paid()->create();
        return PractitionerApplication::factory()->create([
            'practitioner_id' => $user->id,
            'program_id'      => $program->id,
            'status'          => 'approved',
            'payout_status'   => 'pending',
        ]);
    }

    public function test_disburse_initiates_transfer_and_marks_pending(): void
    {
        Http::fake([
            '*/disbursement/token/'        => Http::response(['access_token' => 'tok-123', 'expires_in' => 3600], 200),
            '*/disbursement/v1_0/transfer' => Http::response('', 202),
        ]);

        $app = $this->paidApplication();
        $result = (new MtnMomoPayoutGateway())->disburse($app, 50000, 'XAF');

        $this->assertTrue($result->success);
        $this->assertSame('pending', $result->status);
        $this->assertNotNull($result->reference);

        $fresh = $app->fresh();
        $this->assertSame('pending', $fresh->payout_status);
        $this->assertSame('mtn', $fresh->payout_provider);
        $this->assertSame($result->reference, $fresh->payout_reference);
        $this->assertNotNull($fresh->payout_initiated_at);

        Http::assertSent(fn ($request) =>
            str_contains($request->url(), '/disbursement/v1_0/transfer')
            && $request->hasHeader('X-Reference-Id', $result->reference)
            && $request->hasHeader('X-Target-Environment', 'sandbox')
        );
    }

    public function test_status_maps_successful_to_paid(): void
    {
        Http::fake([
            '*/disbursement/token/'           => Http::response(['access_token' => 'tok-123', 'expires_in' => 3600], 200),
            '*/disbursement/v1_0/transfer/*'  => Http::response(['status' => 'SUCCESSFUL'], 200),
        ]);

        $app = $this->paidApplication();
        $app->update(['payout_reference' => 'ref-abc', 'payout_provider' => 'mtn']);

        $this->assertSame('paid', (new MtnMomoPayoutGateway())->status($app));
    }

    public function test_status_maps_failed(): void
    {
        Http::fake([
            '*/disbursement/token/'          => Http::response(['access_token' => 'tok-123', 'expires_in' => 3600], 200),
            '*/disbursement/v1_0/transfer/*' => Http::response(['status' => 'FAILED', 'reason' => 'PAYEE_NOT_FOUND'], 200),
        ]);

        $app = $this->paidApplication();
        $app->update(['payout_reference' => 'ref-abc', 'payout_provider' => 'mtn']);

        $this->assertSame('failed', (new MtnMomoPayoutGateway())->status($app));
    }
}
```

- [ ] **Step 2: Run it — expect FAIL** (stub throws RuntimeException).

- [ ] **Step 3: Implement the driver**

```php
<?php
namespace App\Services\Payouts;

use App\Models\PractitionerApplication;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MtnMomoPayoutGateway implements PayoutGateway
{
    private function config(): array
    {
        return config('payouts.mtn_momo');
    }

    private function token(): string
    {
        $cfg = $this->config();

        $response = Http::withBasicAuth($cfg['api_user'], $cfg['api_key'])
            ->withHeaders(['Ocp-Apim-Subscription-Key' => $cfg['subscription_key']])
            ->post(rtrim($cfg['base_url'], '/').'/disbursement/token/');

        $response->throw();

        return $response->json('access_token');
    }

    public function disburse(PractitionerApplication $application, float $amount, string $currency, array $options = []): PayoutResult
    {
        $cfg = $this->config();

        // Idempotent reference: reuse an existing one so retries don't double-pay.
        $reference = $application->payout_reference ?: (string) Str::uuid();

        $number = $application->practitioner?->practitionerProfile?->payout_number;
        if (! $number) {
            return PayoutResult::failed('Practitioner has no mobile-money number on file.');
        }

        $application->update([
            'payout_reference'    => $reference,
            'payout_provider'     => 'mtn',
            'payout_amount'       => $amount,
            'payout_currency'     => $currency,
            'payout_initiated_at' => now(),
        ]);

        $response = Http::withToken($this->token())
            ->withHeaders([
                'X-Reference-Id'            => $reference,
                'X-Target-Environment'      => $cfg['environment'],
                'Ocp-Apim-Subscription-Key' => $cfg['subscription_key'],
            ])
            ->post(rtrim($cfg['base_url'], '/').'/disbursement/v1_0/transfer', [
                'amount'       => (string) $amount,
                'currency'     => $currency,
                'externalId'   => (string) $application->id,
                'payee'        => ['partyIdType' => 'MSISDN', 'partyId' => MobileMoneyNetwork::normalise($number)],
                'payerMessage' => 'OPES practitioner payout',
                'payeeNote'    => 'OPES payout '.$reference,
            ]);

        if ($response->status() !== 202) {
            $application->update(['payout_status' => 'failed', 'payout_failure_reason' => 'MoMo transfer rejected ('.$response->status().')']);
            return PayoutResult::failed('MoMo transfer rejected ('.$response->status().').');
        }

        $application->update(['payout_status' => 'pending']);

        return PayoutResult::pending($reference, 'MoMo transfer queued.');
    }

    public function status(PractitionerApplication $application): string
    {
        $cfg = $this->config();

        $response = Http::withToken($this->token())
            ->withHeaders([
                'X-Target-Environment'      => $cfg['environment'],
                'Ocp-Apim-Subscription-Key' => $cfg['subscription_key'],
            ])
            ->get(rtrim($cfg['base_url'], '/').'/disbursement/v1_0/transfer/'.$application->payout_reference);

        return match (strtoupper((string) $response->json('status'))) {
            'SUCCESSFUL' => 'paid',
            'FAILED'     => 'failed',
            default      => 'pending',
        };
    }
}
```

- [ ] **Step 4: Run the test — expect PASS.**

- [ ] **Step 5: Commit**

```
git add app/Services/Payouts/MtnMomoPayoutGateway.php tests/Feature/MtnMomoPayoutGatewayTest.php
git commit -m @'feat(payouts): implement MTN MoMo disbursement driver (sandbox) ...'@
```

---

## Task 6: Confirm Orange scaffold throws until implemented

**Files:**
- Modify: `app/Services/Payouts/OrangeMoneyPayoutGateway.php` (keep stub; ensure message)
- Test: `tests/Feature/OrangeMoneyPayoutGatewayTest.php`

- [ ] **Step 1: Write the test**

```php
<?php
namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use App\Services\Payouts\OrangeMoneyPayoutGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrangeMoneyPayoutGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_orange_driver_throws_until_implemented(): void
    {
        $user = User::factory()->create();
        $program = PractitionerProgram::factory()->paid()->create();
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $user->id, 'program_id' => $program->id]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Orange Money payout driver is not yet implemented');

        (new OrangeMoneyPayoutGateway())->disburse($app, 50000, 'XAF');
    }
}
```

- [ ] **Step 2: Run it — expect PASS** (the existing stub already throws with this message; if the wording differs, align the stub's exception message to contain "Orange Money payout driver is not yet implemented").

- [ ] **Step 3: Commit**

```
git add app/Services/Payouts/OrangeMoneyPayoutGateway.php tests/Feature/OrangeMoneyPayoutGatewayTest.php
git commit -m @'test(payouts): assert Orange driver throws until API spec provided ...'@
```

---

## Task 7: "Pay now" Filament action + idempotency guard

**Files:**
- Modify: `app/Filament/Resources/PractitionerApplicationResource.php`
- Test: `tests/Feature/PayNowActionTest.php`

- [ ] **Step 1: Write the failing test** (drive the action logic via a helper on the resource OR test the manager+driver path the action uses; here we test the underlying flow the action calls, plus the double-pay guard)

```php
<?php
namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use App\Services\Payouts\PayoutGatewayManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayNowActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('payouts.mtn_momo', [
            'base_url' => 'https://sandbox.momodeveloper.mtn.com',
            'subscription_key' => 'k', 'api_user' => 'u', 'api_key' => 'a', 'environment' => 'sandbox',
        ]);
    }

    private function paidApp(): PractitionerApplication
    {
        $user = User::factory()->create();
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM', 'payout_number' => '677123456']);
        $program = PractitionerProgram::factory()->paid()->create();
        return PractitionerApplication::factory()->create([
            'practitioner_id' => $user->id, 'program_id' => $program->id,
            'status' => 'approved', 'payout_status' => 'pending',
        ]);
    }

    public function test_pay_now_initiates_via_resolved_driver(): void
    {
        Http::fake([
            '*/disbursement/token/'        => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            '*/disbursement/v1_0/transfer' => Http::response('', 202),
        ]);

        $app = $this->paidApp();
        $manager = app(PayoutGatewayManager::class);
        $network = $manager->resolveNetwork($app);
        $result  = $manager->driverFor($network)->disburse($app, 50000, 'XAF');

        $this->assertSame('mtn', $network);
        $this->assertSame('pending', $result->status);
        $this->assertSame('mtn', $app->fresh()->payout_provider);
    }

    public function test_already_paid_application_is_not_repaid(): void
    {
        $app = $this->paidApp();
        $app->update(['payout_status' => 'paid', 'paid_at' => now(), 'payout_amount' => 50000]);

        // The resource guard: an application already paid must be rejected before any driver call.
        $this->assertTrue($app->fresh()->payout_status === 'paid');
        // Guard helper (added to the model in this task):
        $this->assertFalse($app->fresh()->isPayable());
    }
}
```

- [ ] **Step 2: Run it — expect FAIL** (`isPayable()` missing).

- [ ] **Step 3: Add the guard to `PractitionerApplication`**

```php
public function isPayable(): bool
{
    return $this->isPaidProgram()
        && $this->status === 'approved'
        && $this->payout_status !== 'paid';
}
```

- [ ] **Step 4: Replace the `record_payout` action** in `PractitionerApplicationResource` with a "Pay now" action that uses the manager. Read the current action first; replace its `->form([...])->action(...)` with:

```php
Tables\Actions\Action::make('pay_now')
    ->label('Pay now')
    ->icon('heroicon-o-banknotes')
    ->color('success')
    ->visible(fn (PractitionerApplication $record) => $record->isPayable())
    ->form([
        Forms\Components\TextInput::make('payout_amount')->label('Amount')->numeric()->required(),
        Forms\Components\TextInput::make('payout_currency')->label('Currency')->default(config('payouts.currency', 'XAF'))->maxLength(3)->required(),
        Forms\Components\Select::make('network_override')
            ->label('Network')
            ->options(['mtn' => 'MTN MoMo', 'orange' => 'Orange Money', 'manual' => 'Manual / offline'])
            ->helperText('Leave blank to auto-detect from the practitioner\'s number.'),
    ])
    ->action(function (PractitionerApplication $record, array $data) {
        if (! $record->isPayable()) {
            \Filament\Notifications\Notification::make()->title('Not payable')->danger()->send();
            return;
        }
        $manager = app(\App\Services\Payouts\PayoutGatewayManager::class);
        $network = $manager->resolveNetwork($record, $data['network_override'] ?? null);
        try {
            $result = $manager->driverFor($network)->disburse(
                $record,
                (float) $data['payout_amount'],
                $data['payout_currency'] ?? config('payouts.currency', 'XAF'),
            );
        } catch (\Throwable $e) {
            \Filament\Notifications\Notification::make()->title('Payout failed')->body($e->getMessage())->danger()->send();
            return;
        }
        \Filament\Notifications\Notification::make()
            ->title($result->status === 'paid' ? 'Payout settled' : ($result->success ? 'Payout initiated' : 'Payout failed'))
            ->body($result->message)
            ->{$result->success ? 'success' : 'danger'}()
            ->send();
    }),
```

- [ ] **Step 5: Run the test — expect PASS.**

- [ ] **Step 6: Commit**

```
git add app/Filament/Resources/PractitionerApplicationResource.php app/Models/PractitionerApplication.php tests/Feature/PayNowActionTest.php
git commit -m @'feat(payouts): Pay-now admin action with per-network driver + double-pay guard ...'@
```

---

## Task 8: `payouts:poll` confirmation command + schedule + notifications

**Files:**
- Create: `app/Console/Commands/PollPayouts.php`
- Modify: `routes/console.php` (schedule) — or `app/Console/Kernel.php` if present
- Create: `app/Mail/PayoutSettled.php` + `resources/views/mail/payout-settled.blade.php`
- Test: `tests/Feature/PollPayoutsTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php
namespace Tests\Feature;

use App\Mail\PayoutSettled;
use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PollPayoutsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('payouts.mtn_momo', [
            'base_url' => 'https://sandbox.momodeveloper.mtn.com',
            'subscription_key' => 'k', 'api_user' => 'u', 'api_key' => 'a', 'environment' => 'sandbox',
        ]);
        Mail::fake();
    }

    private function pendingMtnPayout(): PractitionerApplication
    {
        $user = User::factory()->create();
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM', 'payout_number' => '677123456']);
        $program = PractitionerProgram::factory()->paid()->create();
        return PractitionerApplication::factory()->create([
            'practitioner_id' => $user->id, 'program_id' => $program->id,
            'status' => 'approved', 'payout_status' => 'pending',
            'payout_provider' => 'mtn', 'payout_reference' => 'ref-1', 'payout_initiated_at' => now(),
        ]);
    }

    public function test_poll_marks_successful_payout_paid_and_mails_practitioner(): void
    {
        Http::fake([
            '*/disbursement/token/'          => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            '*/disbursement/v1_0/transfer/*' => Http::response(['status' => 'SUCCESSFUL'], 200),
        ]);
        $app = $this->pendingMtnPayout();

        $this->artisan('payouts:poll')->assertExitCode(0);

        $fresh = $app->fresh();
        $this->assertSame('paid', $fresh->payout_status);
        $this->assertNotNull($fresh->paid_at);
        Mail::assertQueued(PayoutSettled::class);
    }

    public function test_poll_marks_failed_payout(): void
    {
        Http::fake([
            '*/disbursement/token/'          => Http::response(['access_token' => 't', 'expires_in' => 3600], 200),
            '*/disbursement/v1_0/transfer/*' => Http::response(['status' => 'FAILED', 'reason' => 'PAYEE_NOT_FOUND'], 200),
        ]);
        $app = $this->pendingMtnPayout();

        $this->artisan('payouts:poll')->assertExitCode(0);

        $fresh = $app->fresh();
        $this->assertSame('failed', $fresh->payout_status);
        $this->assertNotNull($fresh->payout_failure_reason);
    }
}
```

- [ ] **Step 2: Run it — expect FAIL** (command + mailable missing).

- [ ] **Step 3: Mailable**

```php
<?php
namespace App\Mail;

use App\Models\PractitionerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayoutSettled extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public function __construct(public PractitionerApplication $application) {}
    public function envelope(): Envelope { return new Envelope(subject: 'Your OPES payout has been sent'); }
    public function content(): Content { return new Content(view: 'mail.payout-settled'); }
}
```

`resources/views/mail/payout-settled.blade.php` — branded HTML: greet `$application->practitioner->name`, state amount `{{ number_format((float) $application->payout_amount, 2) }} {{ $application->payout_currency }}` sent via `{{ strtoupper($application->payout_provider) }}`, reference `{{ $application->payout_reference }}`.

- [ ] **Step 4: Command**

```php
<?php
namespace App\Console\Commands;

use App\Mail\PayoutSettled;
use App\Models\PractitionerApplication;
use App\Services\Payouts\PayoutGatewayManager;
use App\Support\AdminNotifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class PollPayouts extends Command
{
    protected $signature = 'payouts:poll';
    protected $description = 'Poll pending mobile-money payouts and update their status.';

    public function handle(PayoutGatewayManager $manager): int
    {
        $pending = PractitionerApplication::query()
            ->where('payout_status', 'pending')
            ->whereNotNull('payout_reference')
            ->whereIn('payout_provider', ['mtn', 'orange'])
            ->get();

        foreach ($pending as $application) {
            try {
                $status = $manager->driverFor($application->payout_provider)->status($application);
            } catch (\Throwable $e) {
                $this->warn("Payout {$application->id}: {$e->getMessage()}");
                continue;
            }

            if ($status === 'paid') {
                $application->update(['payout_status' => 'paid', 'paid_at' => now()]);
                Mail::to($application->practitioner->email)->queue(new PayoutSettled($application));
                AdminNotifier::notify('Payout settled', $application->practitioner->name.' was paid '.$application->payout_amount.' '.$application->payout_currency, null);
            } elseif ($status === 'failed') {
                $application->update([
                    'payout_status'         => 'failed',
                    'payout_failure_reason' => $application->payout_failure_reason ?: 'Provider reported FAILED',
                ]);
                AdminNotifier::notify('Payout failed', 'Payout for '.$application->practitioner->name.' failed.', null, ['super_admin', 'admin', 'support']);
            }
        }

        return self::SUCCESS;
    }
}
```

- [ ] **Step 5: Schedule** — in `routes/console.php` append:

```php
use Illuminate\Support\Facades\Schedule;
Schedule::command('payouts:poll')->everyFiveMinutes()->withoutOverlapping();
```

(If the project uses `app/Console/Kernel.php` instead, register it there; check which exists.)

- [ ] **Step 6: Run the test — expect PASS.**

- [ ] **Step 7: Commit**

```
git add app/Console/Commands/PollPayouts.php app/Mail/PayoutSettled.php resources/views/mail/payout-settled.blade.php routes/console.php tests/Feature/PollPayoutsTest.php
git commit -m @'feat(payouts): poll command confirms disbursements and notifies on settle/fail ...'@
```

---

## Task 9: `.env.example` keys + full-suite verification

**Files:**
- Modify: `.env.example`
- Test: full suite

- [ ] **Step 1:** Append to `.env.example` (read it first; append at end):

```
# Mobile-money payouts (practitioner compensation). Default driver is "manual".
PAYOUT_DRIVER=manual
PAYOUT_CURRENCY=XAF
# MTN MoMo Disbursements (sandbox first) — fill from your MoMo developer account
MTN_MOMO_BASE_URL=https://sandbox.momodeveloper.mtn.com
MTN_MOMO_SUBSCRIPTION_KEY=
MTN_MOMO_API_USER=
MTN_MOMO_API_KEY=
MTN_MOMO_ENVIRONMENT=sandbox
# Orange Money (fill once API access confirmed)
ORANGE_MONEY_BASE_URL=
ORANGE_MONEY_CLIENT_ID=
ORANGE_MONEY_CLIENT_SECRET=
ORANGE_MONEY_MERCHANT_KEY=
ORANGE_MONEY_ENVIRONMENT=sandbox
```

- [ ] **Step 2:** Run the FULL suite — expect all green (baseline 223 + new tests).

Run: `& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" vendor/bin/phpunit --no-coverage`
Expected: `result: passed`, 0 failures.

- [ ] **Step 3: Commit**

```
git add .env.example
git commit -m @'docs(payouts): document mobile-money env keys ...'@
```

---

## Verification

- All new tests pass; full suite green.
- `PAYOUT_DRIVER=manual` (default) → behaviour unchanged; "Pay now" only shows for paid+approved+unpaid applications.
- MoMo driver verified only against `Http::fake()` — **user** must do the live sandbox check with real credentials before switching `PAYOUT_DRIVER`.
- Orange driver throws until its API spec is supplied (Task 6 asserts this).

## Manual Follow-ups (user-owned, outside this plan)
1. Provide Orange Money Cameroon API docs/sandbox → implement `OrangeMoneyPayoutGateway` (mirror Task 5).
2. Create a MoMo developer sandbox account; provision API user/key; put credentials in `.env`.
3. Run an end-to-end sandbox disbursement; confirm `payouts:poll` flips status to paid.
4. Confirm the Cameroon MTN/Orange prefix ranges in `MobileMoneyNetwork` against the authoritative numbering plan; adjust if needed.
