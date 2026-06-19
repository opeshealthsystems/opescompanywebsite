<?php

namespace Tests\Feature;

use App\Filament\Resources\WeeklyReviewResource;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\IssueReport;
use App\Models\User;
use App\Models\WeeklyReview;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class WeeklyReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_snapshot_data_freezes_week_metrics(): void
    {
        $admin  = User::factory()->create();
        $cohort = Cohort::factory()->create();
        $member = CohortMember::factory()->create(['cohort_id' => $cohort->id]);
        IssueReport::factory()->create(['cohort_member_id' => $member->id, 'created_at' => now()]);

        $data = WeeklyReview::snapshotData($cohort, now()->startOfWeek(), $admin->id);

        $this->assertEquals(1, $data['metrics']['issues_submitted']);
        $this->assertEquals($admin->id, $data['author_id']);
        $this->assertNotNull($data['week_end']);
        $this->assertNotNull($data['generated_at']);
    }

    public function test_unique_cohort_week(): void
    {
        $cohort = Cohort::factory()->create();
        $start  = now()->startOfWeek()->toDateString();
        WeeklyReview::factory()->create(['cohort_id' => $cohort->id, 'week_start' => $start]);

        $this->expectException(QueryException::class);
        WeeklyReview::factory()->create(['cohort_id' => $cohort->id, 'week_start' => $start]);
    }

    public function test_resource_admin_gated(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $this->assertTrue(WeeklyReviewResource::canAccess());

        $prac = User::factory()->create();
        $prac->assignRole('practitioner');
        $this->actingAs($prac);
        $this->assertFalse(WeeklyReviewResource::canAccess());
    }
}
