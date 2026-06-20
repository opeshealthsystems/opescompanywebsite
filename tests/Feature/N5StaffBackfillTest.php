<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N5StaffBackfillTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_license_expiry_warning_adds_a_feed_row_for_the_customer(): void
    {
        Mail::fake();
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        License::create([
            'user_id'      => $customer->id,
            'issued_by'    => User::factory()->create()->id,
            'product_slug' => 'ohos',
            'product_name' => 'OPES Health OS',
            'license_key'  => 'LIC-TEST-0001',
            'plan'         => 'pro',
            'seats'        => 5,
            'status'       => 'active',
            'start_date'   => now()->subYear()->toDateString(),
            'end_date'     => now()->addDays(30)->toDateString(), // matches the 30-day threshold
            'currency'     => 'XAF',
        ]);

        Artisan::call('licenses:send-expiry-warnings');

        $this->assertContains(
            'licensing.expiry',
            $customer->fresh()->notifications->pluck('data.type')->all(),
        );
    }
}
