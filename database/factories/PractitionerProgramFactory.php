<?php

namespace Database\Factories;

use App\Models\PractitionerProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PractitionerProgram>
 */
class PractitionerProgramFactory extends Factory
{
    protected $model = PractitionerProgram::class;

    public function definition(): array
    {
        $starts = fake()->dateTimeBetween('now', '+1 month');

        return [
            'title'            => fake()->sentence(4),
            'description'      => fake()->paragraph(),
            'type'            => 'volunteer',
            'compensation'    => null,
            'max_participants' => null,
            'status'          => 'open',
            'starts_at'       => $starts,
            'ends_at'         => fake()->dateTimeBetween($starts, '+3 months'),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'type'         => 'paid',
            'compensation' => fake()->numerify('##0,000 XAF'),
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
