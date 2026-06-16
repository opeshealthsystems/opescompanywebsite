<?php

namespace Database\Factories;

use App\Models\PractitionerBugReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PractitionerBugReportFactory extends Factory
{
    protected $model = PractitionerBugReport::class;

    public function definition(): array
    {
        return [
            'practitioner_id'    => User::factory(),
            'title'              => $this->faker->sentence(5),
            'severity'           => 'medium',
            'description'        => $this->faker->paragraph(),
            'steps_to_reproduce' => $this->faker->paragraph(),
            'status'             => 'open',
        ];
    }

    public function responded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'         => 'resolved',
            'admin_response' => $this->faker->paragraph(),
            'responded_at'   => now(),
        ]);
    }
}
