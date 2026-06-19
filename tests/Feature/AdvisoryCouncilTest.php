<?php

namespace Tests\Feature;

use App\Models\AdvisoryCouncilMember;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdvisoryCouncilTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_unique_membership_per_user(): void
    {
        $user = User::factory()->create();
        AdvisoryCouncilMember::factory()->create(['user_id' => $user->id]);

        $this->expectException(QueryException::class);
        AdvisoryCouncilMember::factory()->create(['user_id' => $user->id]);
    }

    public function test_active_member_sees_council_badge_on_certificates_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        AdvisoryCouncilMember::factory()->create([
            'user_id' => $user->id, 'status' => 'active', 'title' => 'Clinical Validation Advisor',
        ]);

        $this->actingAs($user)
            ->get('/en/practitioner/certificates')
            ->assertOk()
            ->assertSee('Clinical Validation Advisor');
    }
}
