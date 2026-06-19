<?php

namespace Database\Factories;

use App\Models\DeveloperTask;
use App\Models\IssueReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DeveloperTask> */
class DeveloperTaskFactory extends Factory
{
    protected $model = DeveloperTask::class;

    public function definition(): array
    {
        return [
            'issue_report_id'  => IssueReport::factory(),
            'assigned_to'      => null,
            'title'            => fake()->sentence(4),
            'priority'         => 'medium',
            'status'           => 'open',
            'resolution_notes' => null,
            'started_at'       => null,
            'fixed_at'         => null,
        ];
    }
}
