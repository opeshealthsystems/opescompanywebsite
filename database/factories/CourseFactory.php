<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'title'          => fake()->sentence(4),
            'slug'           => fake()->unique()->slug(),
            'description'    => fake()->paragraph(),
            'level'          => 'beginner',
            'duration_hours' => fake()->numberBetween(1, 40),
            'is_active'      => true,
            'is_featured'    => false,
            'sort_order'     => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
