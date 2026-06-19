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

    public function test_login_route_exists(): void
    {
        $response = $this->get('/login');
        $this->assertNotEquals(404, $response->status());
    }

    public function test_register_route_exists(): void
    {
        $this->get('/register')->assertOk();
    }

    public function test_customer_dashboard_redirects_unauthenticated_to_login(): void
    {
        $response = $this->get('/en/customer/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_customer_can_register(): void
    {
        $response = $this->post('/register', [
            'account_type'          => 'facility',
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
            'password' => 'Secret1234!',
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
            'password' => 'Secret1234!',
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
            'password' => 'CorrectPass!',
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
}
