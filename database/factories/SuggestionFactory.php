<?php

namespace Database\Factories;

use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Suggestion>
 */
class SuggestionFactory extends Factory
{
    protected $model = Suggestion::class;

    public function definition(): array
    {
        return [
            'user_id'  => User::factory(),
            'title'    => fake()->sentence(5),
            'category' => fake()->randomElement(array_keys(Suggestion::categoryOptions())),
            'body'     => fake()->paragraph(),
            'status'   => 'pending',
        ];
    }

    public function responded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'         => 'accepted',
            'admin_response' => fake()->paragraph(),
            'responded_at'   => now(),
        ]);
    }
}
