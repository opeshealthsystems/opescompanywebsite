<?php

namespace Database\Factories;

use App\Models\CohortMember;
use App\Models\FinalEvaluation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FinalEvaluation> */
class FinalEvaluationFactory extends Factory
{
    protected $model = FinalEvaluation::class;

    public function definition(): array
    {
        return [
            'cohort_member_id' => CohortMember::factory(),
            'metrics'          => [],
            'assessment'       => fake()->paragraph(),
            'rating'           => 'strong',
            'recommendation'   => fake()->sentence(),
            'evaluator_id'     => User::factory(),
            'evaluated_at'     => now(),
        ];
    }
}
