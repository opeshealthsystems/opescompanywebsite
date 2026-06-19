<?php

namespace Tests\Feature;

use App\Filament\Resources\FinalEvaluationResource;
use App\Models\CohortMember;
use App\Models\FinalEvaluation;
use App\Models\IssueReport;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class FinalEvaluationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_snapshot_data_freezes_member_metrics(): void
    {
        $admin  = User::factory()->create();
        $member = CohortMember::factory()->create();
        IssueReport::factory()->create(['cohort_member_id' => $member->id]);

        $data = FinalEvaluation::snapshotData($member, $admin->id);

        $this->assertEquals(1, $data['metrics']['issues_found']);
        $this->assertArrayHasKey('member_name', $data['metrics']);
        $this->assertEquals($admin->id, $data['evaluator_id']);
        $this->assertNotNull($data['evaluated_at']);
    }

    public function test_unique_per_member(): void
    {
        $member = CohortMember::factory()->create();
        FinalEvaluation::factory()->create(['cohort_member_id' => $member->id]);

        $this->expectException(QueryException::class);
        FinalEvaluation::factory()->create(['cohort_member_id' => $member->id]);
    }

    public function test_resource_admin_gated(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $this->assertTrue(FinalEvaluationResource::canAccess());

        $prac = User::factory()->create();
        $prac->assignRole('practitioner');
        $this->actingAs($prac);
        $this->assertFalse(FinalEvaluationResource::canAccess());
    }

    public function test_rating_options_are_the_allowed_set(): void
    {
        // Rating membership is enforced at the UI (Filament Select); this pins the
        // canonical allowed set both the resource form and the Evaluate action use.
        $this->assertSame(
            ['outstanding', 'strong', 'satisfactory', 'needs_improvement'],
            array_keys(FinalEvaluation::ratingOptions())
        );
    }
}
