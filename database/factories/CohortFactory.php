<?php

namespace Database\Factories;

use App\Models\Cohort;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cohort>
 */
class CohortFactory extends Factory
{
    protected $model = Cohort::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('now', '+1 month');

        return [
            'practitioner_program_id' => \App\Models\PractitionerProgram::factory()->state(['program_type' => 'validation']),
            'name'        => fake()->monthName().' Cohort',
            'specialty'   => fake()->randomElement(['Pharmacy', 'Nursing', 'Laboratory', 'Triage']),
            'description' => fake()->sentence(),
            'start_date'  => $start,
            'end_date'    => fake()->dateTimeBetween($start, '+3 months'),
            'max_members' => null,
            'status'      => 'active',
        ];
    }
}
