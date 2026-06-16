<?php

namespace Database\Factories;

use App\Models\PractitionerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PractitionerProfile>
 */
class PractitionerProfileFactory extends Factory
{
    protected $model = PractitionerProfile::class;

    public function definition(): array
    {
        return [
            'user_id'             => User::factory(),
            'profession'          => fake()->randomElement(array_keys(PractitionerProfile::professionOptions())),
            'specialty'           => fake()->words(2, true),
            'workplace_name'      => fake()->company(),
            'workplace_city'      => fake()->city(),
            'workplace_country'   => 'CM',
            'registration_number' => strtoupper(fake()->bothify('REG-####??')),
            'years_of_experience' => fake()->numberBetween(1, 30),
            'bio'                 => fake()->paragraph(),
            'opes_testimonial'    => fake()->sentence(),
            'is_verified'         => false,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
        ]);
    }
}
