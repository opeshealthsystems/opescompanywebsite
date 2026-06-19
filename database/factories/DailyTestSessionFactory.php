<?php

namespace Database\Factories;

use App\Models\DailyTestSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyTestSession>
 */
class DailyTestSessionFactory extends Factory
{
    protected $model = DailyTestSession::class;

    public function definition(): array
    {
        return [
            'cohort_member_id'       => \App\Models\CohortMember::factory(),
            'validation_product_id'  => \App\Models\ValidationProduct::factory(),
            'validation_module_id'   => \App\Models\ValidationModule::factory(),
            'validation_workflow_id' => \App\Models\ValidationWorkflow::factory(),
            'facility_context'       => fake()->company(),
            'date'                   => now()->toDateString(),
            'start_time'             => '09:00',
            'end_time'               => '11:00',
            'tasks_completed'        => 3,
            'screenshots'            => null,
            'comments'               => fake()->sentence(),
        ];
    }
}
