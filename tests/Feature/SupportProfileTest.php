<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeSupport(): User
    {
        $user = User::factory()->create();
        $user->assignRole('support');
        return $user;
    }

    public function test_support_profile_page_loads(): void
    {
        $support = $this->makeSupport();
        $response = $this->actingAs($support)->get("/en/support/profile");
        $response->assertStatus(200);
        $response->assertSee($support->name);
    }

    public function test_support_profile_update_saves_fields(): void
    {
        $support = $this->makeSupport();
        $response = $this->actingAs($support)->patch("/en/support/profile", [
            'name'                  => $support->name,
            'ticket_specialization' => 'technical',
            'shift'                 => 'morning',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('support_profiles', [
            'user_id'               => $support->id,
            'ticket_specialization' => 'technical',
            'shift'                 => 'morning',
        ]);
    }

    public function test_non_support_cannot_access_support_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $response = $this->actingAs($user)->get("/en/support/profile");
        $response->assertStatus(403);
    }
}
