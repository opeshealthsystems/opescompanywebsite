<?php

namespace Tests\Feature;

use App\Filament\Resources\PractitionerApplicationResource\Actions\PlaceInCohortAction;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationCohortTest2 extends TestCase
{
    use RefreshDatabase;

    public function test_place_action_returns_filament_action(): void
    {
        $this->assertInstanceOf(\Filament\Tables\Actions\Action::class, PlaceInCohortAction::make());
    }

    public function test_placing_creates_cohort_member(): void
    {
        $program = PractitionerProgram::factory()->create(['program_type' => 'validation']);
        $cohort  = Cohort::factory()->create(['practitioner_program_id' => $program->id, 'status' => 'active']);
        $user    = User::factory()->create();

        CohortMember::create([
            'cohort_id' => $cohort->id, 'user_id' => $user->id, 'status' => 'active', 'placed_at' => now(),
        ]);

        $this->assertDatabaseHas('cohort_members', [
            'cohort_id' => $cohort->id, 'user_id' => $user->id, 'status' => 'active',
        ]);
    }

    public function test_membership_uniqueness_blocks_double_placement(): void
    {
        $program = PractitionerProgram::factory()->create(['program_type' => 'validation']);
        $cohort  = Cohort::factory()->create(['practitioner_program_id' => $program->id, 'status' => 'active']);
        $user    = User::factory()->create();

        CohortMember::create(['cohort_id' => $cohort->id, 'user_id' => $user->id, 'status' => 'active', 'placed_at' => now()]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        CohortMember::create(['cohort_id' => $cohort->id, 'user_id' => $user->id, 'status' => 'active', 'placed_at' => now()]);
    }
}
