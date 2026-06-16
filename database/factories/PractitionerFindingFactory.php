<?php

namespace Database\Factories;

use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PractitionerFinding>
 */
class PractitionerFindingFactory extends Factory
{
    protected $model = PractitionerFinding::class;

    public function definition(): array
    {
        return [
            'application_id'        => PractitionerApplication::factory(),
            'practitioner_id'       => User::factory(),
            'wait_time_rating'      => fake()->numberBetween(1, 5),
            'data_integrity_rating' => fake()->numberBetween(1, 5),
            'usability_rating'      => fake()->numberBetween(1, 5),
            'overall_rating'        => fake()->numberBetween(1, 5),
            'findings_text'         => fake()->paragraph(),
            'video_url'             => fake()->url(),
            'is_published'          => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }
}
