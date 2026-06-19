<?php

namespace Database\Factories;

use App\Models\CohortMember;
use App\Models\IssueReport;
use App\Models\Retest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Retest> */
class RetestFactory extends Factory
{
    protected $model = Retest::class;

    public function definition(): array
    {
        return [
            'issue_report_id'   => IssueReport::factory(),
            'developer_task_id' => null,
            'cohort_member_id'  => CohortMember::factory(),
            'result'            => 'passed',
            'notes'             => fake()->sentence(),
            'attachments'       => null,
            'retested_at'       => now(),
        ];
    }
}
