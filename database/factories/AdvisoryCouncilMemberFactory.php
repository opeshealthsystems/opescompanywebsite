<?php

namespace Database\Factories;

use App\Models\AdvisoryCouncilMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AdvisoryCouncilMember> */
class AdvisoryCouncilMemberFactory extends Factory
{
    protected $model = AdvisoryCouncilMember::class;

    public function definition(): array
    {
        return [
            'user_id'                   => User::factory(),
            'validation_certificate_id' => null,
            'title'                     => 'Clinical Validation Advisor',
            'term_start'                => now()->toDateString(),
            'term_end'                  => null,
            'status'                    => 'active',
            'invited_by'                => User::factory(),
            'invited_at'                => now(),
        ];
    }
}
