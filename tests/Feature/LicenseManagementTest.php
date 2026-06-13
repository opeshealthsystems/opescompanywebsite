<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class LicenseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_licenses_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('licenses'));
    }

    public function test_license_can_be_created(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $admin    = User::factory()->create();
        $admin->assignRole('admin');

        $license = License::create([
            'user_id'      => $customer->id,
            'issued_by'    => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'license_key'  => 'OPES-' . strtoupper(substr(md5('test'), 0, 16)),
            'plan'         => 'professional',
            'seats'        => 5,
            'status'       => 'active',
            'start_date'   => now()->toDateString(),
            'end_date'     => now()->addYear()->toDateString(),
            'price'        => 150000,
            'currency'     => 'XAF',
        ]);

        $this->assertDatabaseHas('licenses', [
            'product_slug' => 'opescare',
            'status'       => 'active',
            'user_id'      => $customer->id,
        ]);

        $this->assertEquals($customer->id, $license->customer->id);
        $this->assertEquals($admin->id, $license->issuer->id);
    }

    public function test_license_key_is_unique(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $admin    = User::factory()->create();
        $admin->assignRole('admin');

        $key = 'OPES-UNIQUE-KEY-12345';

        License::create([
            'user_id'      => $customer->id,
            'issued_by'    => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'license_key'  => $key,
            'plan'         => 'standard',
            'seats'        => 1,
            'status'       => 'active',
            'start_date'   => now()->toDateString(),
            'end_date'     => now()->addYear()->toDateString(),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        License::create([
            'user_id'      => $customer->id,
            'issued_by'    => $admin->id,
            'product_slug' => 'opes-emr',
            'product_name' => 'OPES EMR',
            'license_key'  => $key,
            'plan'         => 'standard',
            'seats'        => 1,
            'status'       => 'active',
            'start_date'   => now()->toDateString(),
            'end_date'     => now()->addYear()->toDateString(),
        ]);
    }

    public function test_license_generate_key_produces_unique_strings(): void
    {
        $key1 = License::generateKey();
        $key2 = License::generateKey();

        $this->assertNotEquals($key1, $key2);
        $this->assertStringStartsWith('OPES-', $key1);
    }

    public function test_license_is_expiring_soon(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $admin    = User::factory()->create();
        $admin->assignRole('admin');

        $license = License::create([
            'user_id'      => $customer->id,
            'issued_by'    => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'license_key'  => License::generateKey(),
            'plan'         => 'standard',
            'seats'        => 1,
            'status'       => 'active',
            'start_date'   => now()->subMonth()->toDateString(),
            'end_date'     => now()->addDays(15)->toDateString(),
        ]);

        $this->assertTrue($license->isExpiringSoon());
    }

    public function test_manage_licenses_permission_exists(): void
    {
        $this->assertDatabaseHas('permissions', ['name' => 'manage_licenses']);
    }

    public function test_admin_has_manage_licenses_permission(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->assertTrue($admin->hasPermissionTo('manage_licenses'));
    }
}
