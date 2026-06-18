<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesterProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeTester(): User
    {
        $user = User::factory()->create();
        $user->assignRole('tester');
        return $user;
    }

    public function test_tester_profile_page_loads(): void
    {
        $tester = $this->makeTester();
        $response = $this->actingAs($tester)->get("/en/tester/profile");
        $response->assertStatus(200);
        $response->assertSee($tester->name);
    }

    public function test_tester_profile_update_saves_user_fields(): void
    {
        $tester = $this->makeTester();
        $response = $this->actingAs($tester)->patch("/en/tester/profile", [
            'name'  => 'Updated Tester',
            'phone' => '+1234567890',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $tester->id, 'name' => 'Updated Tester']);
    }

    public function test_tester_profile_update_saves_tester_profile_fields(): void
    {
        $tester = $this->makeTester();
        $response = $this->actingAs($tester)->patch("/en/tester/profile", [
            'name'              => $tester->name,
            'testing_specialty' => 'web',
            'portfolio_url'     => 'https://example.com',
            'bio'               => 'I test web apps.',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('tester_profiles', [
            'user_id'           => $tester->id,
            'testing_specialty' => 'web',
            'bio'               => 'I test web apps.',
        ]);
    }

    public function test_non_tester_cannot_access_tester_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $response = $this->actingAs($user)->get("/en/tester/profile");
        $response->assertStatus(403);
    }
}
