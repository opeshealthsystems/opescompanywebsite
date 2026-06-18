<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeManager(): User
    {
        $user = User::factory()->create();
        $user->assignRole('manager');
        return $user;
    }

    public function test_manager_profile_page_loads(): void
    {
        $manager = $this->makeManager();
        $response = $this->actingAs($manager)->get("/en/manager/profile");
        $response->assertStatus(200);
        $response->assertSee($manager->name);
    }

    public function test_manager_profile_update_saves_user_fields(): void
    {
        $manager = $this->makeManager();
        $response = $this->actingAs($manager)->patch("/en/manager/profile", [
            'name'  => 'Updated Manager',
            'phone' => '+237600000001',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $manager->id, 'name' => 'Updated Manager']);
    }

    public function test_manager_profile_update_saves_manager_profile_fields(): void
    {
        $manager = $this->makeManager();
        $response = $this->actingAs($manager)->patch("/en/manager/profile", [
            'name'             => $manager->name,
            'management_level' => 'team_lead',
            'bio'              => 'I manage teams.',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('manager_profiles', [
            'user_id'          => $manager->id,
            'management_level' => 'team_lead',
        ]);
    }

    public function test_non_manager_cannot_access_manager_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $response = $this->actingAs($user)->get("/en/manager/profile");
        $response->assertStatus(403);
    }
}
