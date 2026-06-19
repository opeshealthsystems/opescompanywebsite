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
        $cohortMember = CohortMember::factory();

        return [
            'cohort_member_id'  => $cohortMember,
            'issue_report_id'   => function (array $attributes) {
                $memberId = $attributes['cohort_member_id'] ?? null;

                if ($memberId) {
                    $existing = IssueReport::where('cohort_member_id', $memberId)->first();
                    if ($existing) {
                        return $existing->id;
                    }

                    return IssueReport::factory()->create(['cohort_member_id' => $memberId])->id;
                }

                return IssueReport::factory();
            },
            'developer_task_id' => null,
            'result'            => 'passed',
            'notes'             => fake()->sentence(),
            'attachments'       => null,
            'retested_at'       => now(),
        ];
    }
}
