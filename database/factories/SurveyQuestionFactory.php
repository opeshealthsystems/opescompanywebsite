<?php

namespace Database\Factories;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SurveyQuestion>
 */
class SurveyQuestionFactory extends Factory
{
    protected $model = SurveyQuestion::class;

    public function definition(): array
    {
        return [
            'survey_id'   => Survey::factory(),
            'question'    => fake()->sentence() . '?',
            'type'        => 'text',
            'is_required' => true,
            'sort_order'  => 0,
        ];
    }

    public function rating(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'rating',
        ]);
    }

    public function multipleChoice(): static
    {
        return $this->state(fn (array $attributes) => [
            'type'    => 'multiple_choice',
            'options' => [fake()->word(), fake()->word(), fake()->word()],
        ]);
    }

    public function yesNo(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'yes_no',
        ]);
    }
}
