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
