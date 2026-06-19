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

    public function test_register_page_renders_with_selector_and_apply_links(): void
    {
        // Regression: the Tester/Partner links use locale-prefixed routes and must
        // render without a UrlGenerationException.
        $this->get('/register')
            ->assertOk()
            ->assertSee('Account Type')
            ->assertSee('Apply as a Tester');
    }
}
