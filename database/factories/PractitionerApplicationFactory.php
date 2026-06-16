<?php

namespace Database\Factories;

use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PractitionerApplication>
 */
class PractitionerApplicationFactory extends Factory
{
    protected $model = PractitionerApplication::class;

    public function definition(): array
    {
        return [
            'practitioner_id' => User::factory(),
            'program_id'      => PractitionerProgram::factory(),
            'motivation'      => fake()->paragraph(),
            'status'          => 'pending',
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => 'approved',
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => 'rejected',
            'reviewed_at' => now(),
        ]);
    }
}
