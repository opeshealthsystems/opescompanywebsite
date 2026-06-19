<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationCohortTest extends TestCase
{
    use RefreshDatabase;

    public function test_program_has_cohorts_and_validation_scope_filters(): void
    {
        $validation = PractitionerProgram::factory()->create(['program_type' => 'validation']);
        $general    = PractitionerProgram::factory()->create(['program_type' => 'general']);
        $cohort     = Cohort::factory()->create(['practitioner_program_id' => $validation->id]);

        $this->assertTrue($validation->cohorts->contains($cohort));
        $ids = PractitionerProgram::validation()->pluck('id');
        $this->assertTrue($ids->contains($validation->id));
        $this->assertFalse($ids->contains($general->id));
    }

    public function test_duplicate_membership_violates_unique_constraint(): void
    {
        $cohort = Cohort::factory()->create();
        $user   = User::factory()->create();
        CohortMember::factory()->create(['cohort_id' => $cohort->id, 'user_id' => $user->id]);

        $this->expectException(QueryException::class);
        CohortMember::factory()->create(['cohort_id' => $cohort->id, 'user_id' => $user->id]);
    }

    public function test_user_active_cohort_memberships(): void
    {
        $user = User::factory()->create();
        CohortMember::factory()->create(['user_id' => $user->id, 'status' => 'active']);
        CohortMember::factory()->create(['user_id' => $user->id, 'status' => 'removed']);

        $this->assertEquals(1, $user->cohortMembers()->where('status', 'active')->count());
    }
}
