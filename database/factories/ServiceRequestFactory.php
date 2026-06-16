<?php

namespace Database\Factories;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequest>
 */
class ServiceRequestFactory extends Factory
{
    protected $model = ServiceRequest::class;

    public function definition(): array
    {
        return [
            'customer_id'    => User::factory(),
            'type'           => fake()->randomElement(array_keys(ServiceRequest::typeOptions())),
            'description'    => fake()->paragraph(),
            'preferred_date' => fake()->dateTimeBetween('now', '+2 months'),
            'location'       => fake()->address(),
            'status'         => 'pending',
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'         => 'confirmed',
            'confirmed_date' => fake()->dateTimeBetween('now', '+2 months'),
        ]);
    }
}
