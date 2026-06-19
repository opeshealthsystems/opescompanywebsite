<?php

namespace Database\Factories;

use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductReview>
 */
class ProductReviewFactory extends Factory
{
    protected $model = ProductReview::class;

    public function definition(): array
    {
        return [
            'issue_report_id' => \App\Models\IssueReport::factory(),
            'reviewer_id'     => \App\Models\User::factory(),
            'decision'        => 'accepted',
            'notes'           => fake()->sentence(),
            'reviewed_at'     => now(),
        ];
    }
}
