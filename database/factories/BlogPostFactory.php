<?php

namespace Database\Factories;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BlogPost>
 */
class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'title'        => $title,
            'title_fr'     => null,
            'slug'         => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'excerpt'      => fake()->sentence(12),
            'excerpt_fr'   => null,
            'body'         => '<p>' . fake()->paragraphs(3, true) . '</p>',
            'body_fr'      => null,
            'cover_image'  => null,
            'reading_time' => fake()->numberBetween(1, 10),
            'category'     => fake()->randomElement(['Digital Health', 'Nutrition', 'Mental Health', 'Fitness']),
            'author'       => 'OPES Health Systems',
            'published'    => false,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published'    => true,
            'published_at' => now(),
        ]);
    }
}
