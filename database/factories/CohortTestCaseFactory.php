<?php

namespace Database\Factories;

use App\Models\CohortTestCase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CohortTestCase>
 */
class CohortTestCaseFactory extends Factory
{
    protected $model = CohortTestCase::class;

    public function definition(): array
    {
        return [
            'cohort_id'               => \App\Models\Cohort::factory(),
            'validation_test_case_id' => \App\Models\ValidationTestCase::factory(),
            'due_date'                => null,
        ];
    }
}
