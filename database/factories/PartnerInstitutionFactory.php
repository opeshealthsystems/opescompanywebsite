<?php

namespace Database\Factories;

use App\Models\PartnerInstitution;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PartnerInstitution>
 */
class PartnerInstitutionFactory extends Factory
{
    protected $model = PartnerInstitution::class;

    public function definition(): array
    {
        return [
            'name'              => fake()->company(),
            'type'              => fake()->randomElement(array_keys(PartnerInstitution::typeOptions())),
            'country'           => 'CM',
            'city'              => fake()->city(),
            'website'           => fake()->url(),
            'description'       => fake()->paragraph(),
            'partnership_since' => fake()->numberBetween(2010, 2025),
            'is_featured'       => false,
            'is_active'         => true,
            'sort_order'        => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
