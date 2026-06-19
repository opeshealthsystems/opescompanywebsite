<?php

namespace Database\Factories;

use App\Models\CohortMember;
use App\Models\User;
use App\Models\ValidationCertificate;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ValidationCertificate> */
class ValidationCertificateFactory extends Factory
{
    protected $model = ValidationCertificate::class;

    public function definition(): array
    {
        return [
            'cohort_member_id'    => CohortMember::factory(),
            'final_evaluation_id' => null,
            'score'               => 75,
            'tier'                => 'pass',
            'issued_by'           => User::factory(),
            'issued_at'           => now(),
        ];
    }
}
