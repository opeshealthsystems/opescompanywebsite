<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ValidationDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function practitioner(): User
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        return $user;
    }

    public function test_placed_practitioner_sees_cohort_dashboard(): void
    {
        $user   = $this->practitioner();
        $cohort = Cohort::factory()->create(['name' => 'July Pharmacy Cohort']);
        CohortMember::factory()->create(['cohort_id' => $cohort->id, 'user_id' => $user->id, 'status' => 'active']);

        $this->actingAs($user)
            ->get('/en/practitioner/validation')
            ->assertOk()
            ->assertSee('July Pharmacy Cohort');
    }

    public function test_unplaced_practitioner_sees_not_placed_message(): void
    {
        $user = $this->practitioner();

        $this->actingAs($user)
            ->get('/en/practitioner/validation')
            ->assertOk()
            ->assertSee('not been placed', false);
    }

    public function test_non_practitioner_forbidden(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->actingAs($user)
            ->get('/en/practitioner/validation')
            ->assertForbidden();
    }
}
