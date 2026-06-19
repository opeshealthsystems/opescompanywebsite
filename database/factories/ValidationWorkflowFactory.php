<?php

namespace Database\Factories;

use App\Models\ValidationModule;
use App\Models\ValidationWorkflow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ValidationWorkflow>
 */
class ValidationWorkflowFactory extends Factory
{
    protected $model = ValidationWorkflow::class;

    public function definition(): array
    {
        return [
            'validation_module_id' => ValidationModule::factory(),
            'name'                 => fake()->words(2, true),
            'code'                 => fake()->unique()->slug(2),
            'description'          => fake()->sentence(),
            'is_active'            => true,
        ];
    }
}
