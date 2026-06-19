<?php

namespace Database\Factories;

use App\Models\ValidationProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ValidationProduct>
 */
class ValidationProductFactory extends Factory
{
    protected $model = ValidationProduct::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->unique()->words(2, true),
            'code'        => fake()->unique()->slug(2),
            'description' => fake()->sentence(),
            'is_active'   => true,
        ];
    }
}
