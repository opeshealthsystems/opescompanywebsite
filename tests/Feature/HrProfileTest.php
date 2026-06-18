<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeHr(): User
    {
        $user = User::factory()->create();
        $user->assignRole('hr');
        return $user;
    }

    public function test_hr_profile_page_loads(): void
    {
        $hr = $this->makeHr();
        $response = $this->actingAs($hr)->get("/en/hr/profile");
        $response->assertStatus(200);
        $response->assertSee($hr->name);
    }

    public function test_hr_profile_update_saves_user_fields(): void
    {
        $hr = $this->makeHr();
        $response = $this->actingAs($hr)->patch("/en/hr/profile", [
            'name'  => 'Updated HR Name',
            'phone' => '+237600000000',
            'emergency_contact_name' => 'Jane Doe',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id'   => $hr->id,
            'name' => 'Updated HR Name',
        ]);
    }

    public function test_non_hr_cannot_access_hr_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $response = $this->actingAs($user)->get("/en/hr/profile");
        $response->assertStatus(403);
    }
}
