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
