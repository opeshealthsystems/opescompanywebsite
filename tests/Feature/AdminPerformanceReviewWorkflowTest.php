<?php

namespace Tests\Feature;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPerformanceReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_performance_reviews_share_draft_crud_and_guarded_transitions(): void
    {
        foreach (['admin', 'support'] as $role) Role::findOrCreate($role, 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $employee = User::factory()->create(['is_active' => true]);
        $employee->assignRole('support');
        $this->actingAs($admin);
        $data = ['user_id' => $employee->id, 'reviewer_id' => $admin->id, 'review_period' => 'Fictional 2026 cycle', 'review_date' => '2026-11-01',
            'overall_rating' => 4, 'goals_rating' => 4, 'teamwork_rating' => 3, 'technical_rating' => 4, 'communication_rating' => 4,
            'strengths' => 'Fictional fixture.', 'areas_for_improvement' => null, 'goals_for_next_period' => null, 'employee_comments' => null];

        AdminWorkflowAdapter::run('admin.workforce.performanceReviews.create', $data);
        $review = PerformanceReview::query()->where('user_id', $employee->id)->firstOrFail();
        $this->assertSame('draft', $review->status);
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.performance_review_created.v1', 'actor_id' => $admin->id]);

        $result = app(WorkflowDispatcher::class)->execute('admin.workforce.performanceReviews.index', new WorkflowInput(['status' => 'draft'], actor: $admin));
        $this->assertSame($review->id, $result->data['items'][0]['id']);
        AdminWorkflowAdapter::run('admin.workforce.performanceReviews.update', [...$data, 'strengths' => 'Updated fictional fixture.'], ['review' => $review->id]);
        $this->assertDatabaseHas('performance_reviews', ['id' => $review->id, 'strengths' => 'Updated fictional fixture.']);

        AdminWorkflowAdapter::run('admin.workforce.performanceReviews.submit', parameters: ['review' => $review->id]);
        $this->assertDatabaseHas('performance_reviews', ['id' => $review->id, 'status' => 'submitted']);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        AdminWorkflowAdapter::run('admin.workforce.performanceReviews.update', [...$data, 'strengths' => 'Too late.'], ['review' => $review->id]);
    }
}
