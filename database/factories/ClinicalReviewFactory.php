<?php

namespace Database\Factories;

use App\Models\ClinicalReview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClinicalReview>
 */
class ClinicalReviewFactory extends Factory
{
    protected $model = ClinicalReview::class;

    public function definition(): array
    {
        return [
            'issue_report_id' => \App\Models\IssueReport::factory(),
            'reviewer_id'     => \App\Models\User::factory(),
            'decision'        => 'approved_for_product_review',
            'notes'           => fake()->sentence(),
            'reviewed_at'     => now(),
        ];
    }
}
