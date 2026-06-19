<?php

namespace Database\Factories;

use App\Models\Cohort;
use App\Models\User;
use App\Models\WeeklyReview;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WeeklyReview> */
class WeeklyReviewFactory extends Factory
{
    protected $model = WeeklyReview::class;

    public function definition(): array
    {
        $start = now()->startOfWeek();
        return [
            'cohort_id'    => Cohort::factory(),
            'week_start'   => $start->toDateString(),
            'week_end'     => $start->copy()->addDays(6)->toDateString(),
            'metrics'      => [],
            'summary'      => fake()->sentence(),
            'action_items' => null,
            'author_id'    => User::factory(),
            'generated_at' => now(),
        ];
    }
}
