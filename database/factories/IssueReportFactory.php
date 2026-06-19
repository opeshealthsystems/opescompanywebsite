<?php

namespace Database\Factories;

use App\Models\IssueReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IssueReport>
 */
class IssueReportFactory extends Factory
{
    protected $model = IssueReport::class;

    public function definition(): array
    {
        return [
            'cohort_member_id'        => \App\Models\CohortMember::factory(),
            'daily_test_session_id'   => null,
            'validation_product_id'   => \App\Models\ValidationProduct::factory(),
            'validation_module_id'    => \App\Models\ValidationModule::factory(),
            'validation_workflow_id'  => \App\Models\ValidationWorkflow::factory(),
            'validation_test_case_id' => null,
            'title'                   => fake()->sentence(4),
            'issue_type'              => 'bug',
            'severity'                => 'medium',
            'description'             => fake()->paragraph(),
            'steps_to_reproduce'      => fake()->paragraph(),
            'expected_result'         => fake()->sentence(),
            'actual_result'           => fake()->sentence(),
            'clinical_impact'         => fake()->sentence(),
            'recommendation'          => null,
            'attachments'             => null,
            'status'                  => 'submitted',
        ];
    }
}
