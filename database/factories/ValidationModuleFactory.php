<?php

namespace Database\Factories;

use App\Models\ValidationModule;
use App\Models\ValidationProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ValidationModule>
 */
class ValidationModuleFactory extends Factory
{
    protected $model = ValidationModule::class;

    public function definition(): array
    {
        return [
            'validation_product_id' => ValidationProduct::factory(),
            'name'                  => fake()->words(2, true),
            'code'                  => fake()->unique()->slug(2),
            'description'           => fake()->sentence(),
            'is_active'             => true,
        ];
    }
}
