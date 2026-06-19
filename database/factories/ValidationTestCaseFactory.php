<?php

namespace Database\Factories;

use App\Models\ValidationTestCase;
use App\Models\ValidationWorkflow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ValidationTestCase>
 */
class ValidationTestCaseFactory extends Factory
{
    protected $model = ValidationTestCase::class;

    public function definition(): array
    {
        return [
            'validation_workflow_id' => ValidationWorkflow::factory(),
            'title'                  => fake()->sentence(3),
            'description'            => fake()->sentence(),
            'steps'                  => fake()->sentence(),
            'expected_result'        => fake()->sentence(),
            'is_active'              => true,
        ];
    }
}
