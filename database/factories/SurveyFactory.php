<?php

namespace Database\Factories;

use App\Models\Survey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Survey>
 */
class SurveyFactory extends Factory
{
    protected $model = Survey::class;

    public function definition(): array
    {
        return [
            'title'       => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'audience'    => 'all',
            'status'      => 'active',
        ];
    }

    public function forPractitioners(): static
    {
        return $this->state(fn (array $attributes) => [
            'audience' => 'practitioners',
        ]);
    }

    public function forCustomers(): static
    {
        return $this->state(fn (array $attributes) => [
            'audience' => 'customers',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }
}
