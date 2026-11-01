<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Models\Department;
use App\Models\PerformanceReview;
use App\Models\User;
class HRPerformanceCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function store(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $validated = $request->validate(['user_id' => 'required|exists:users,id', 'review_period' => 'required|string|max:50', 'review_date' => 'required|date', 'strengths' => 'nullable|string', 'areas_for_improvement' => 'nullable|string', 'goals_for_next_period' => 'nullable|string']);
        \App\Models\PerformanceReview::create([...$validated, 'reviewer_id' => \App\Contexts\Shared\Application\CurrentWorkflow::id(), 'status' => 'draft', 'overall_rating' => 0, 'goals_rating' => 0, 'teamwork_rating' => 0, 'technical_rating' => 0, 'communication_rating' => 0]);
        return \App\Contexts\Shared\Application\WorkflowResult::next('hr_performancecontroller_store_1')->with('success', 'Performance review initiated.');
    }
}
