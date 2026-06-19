<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\FinalEvaluation;
use App\Models\WeeklyReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationReportingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_weekly_review_relationships_and_casts(): void
    {
        $review = WeeklyReview::factory()->create(['metrics' => ['sessions' => 3]]);
        $this->assertIsArray($review->fresh()->metrics);
        $this->assertEquals(3, $review->fresh()->metrics['sessions']);
        $this->assertInstanceOf(Cohort::class, $review->cohort);
        $this->assertNotNull($review->author);
        $this->assertTrue($review->cohort->weeklyReviews->contains($review));
    }

    public function test_final_evaluation_relationships_and_options(): void
    {
        $eval = FinalEvaluation::factory()->create();
        $this->assertInstanceOf(CohortMember::class, $eval->cohortMember);
        $this->assertNotNull($eval->evaluator);
        $this->assertEquals($eval->id, $eval->cohortMember->finalEvaluation->id);
        $this->assertArrayHasKey('strong', FinalEvaluation::ratingOptions());
        $this->assertCount(4, FinalEvaluation::ratingOptions());
    }
}
