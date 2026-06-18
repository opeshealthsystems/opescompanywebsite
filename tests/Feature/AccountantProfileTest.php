<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountantProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeAccountant(): User
    {
        $user = User::factory()->create();
        $user->assignRole('accountant');
        return $user;
    }

    public function test_accountant_profile_page_loads(): void
    {
        $accountant = $this->makeAccountant();
        $response = $this->actingAs($accountant)->get("/en/accountant/profile");
        $response->assertStatus(200);
        $response->assertSee($accountant->name);
    }

    public function test_accountant_profile_update_saves_fields(): void
    {
        $accountant = $this->makeAccountant();
        $response = $this->actingAs($accountant)->patch("/en/accountant/profile", [
            'name'                      => $accountant->name,
            'accounting_specialization' => 'payroll',
            'bio'                       => 'I handle payroll.',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('accountant_profiles', [
            'user_id'                   => $accountant->id,
            'accounting_specialization' => 'payroll',
        ]);
    }

    public function test_non_accountant_cannot_access_accountant_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $response = $this->actingAs($user)->get("/en/accountant/profile");
        $response->assertStatus(403);
    }
}
