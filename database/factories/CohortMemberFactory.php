<?php

namespace Database\Factories;

use App\Models\CohortMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CohortMember>
 */
class CohortMemberFactory extends Factory
{
    protected $model = CohortMember::class;

    public function definition(): array
    {
        return [
            'cohort_id'  => \App\Models\Cohort::factory(),
            'user_id'    => \App\Models\User::factory(),
            'status'     => 'active',
            'placed_at'  => now(),
        ];
    }
}
