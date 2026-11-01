<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Models\Department;
use App\Models\PerformanceReview;
use App\Models\User;
class ManagerPerformanceCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function store(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $validated = $request->validate(['user_id' => 'required|exists:users,id', 'review_period' => 'required|string|max:50', 'review_date' => 'required|date', 'strengths' => 'nullable|string', 'areas_for_improvement' => 'nullable|string', 'goals_for_next_period' => 'nullable|string']);
        $departments = Department::where('head_id', $request->user()->id)->pluck('id');
        abort_unless(User::whereKey($validated['user_id'])->whereIn('department_id', $departments)->exists(), 403);
        \App\Models\PerformanceReview::create([...$validated, 'reviewer_id' => \App\Contexts\Shared\Application\CurrentWorkflow::id(), 'status' => 'draft', 'overall_rating' => 0, 'goals_rating' => 0, 'teamwork_rating' => 0, 'technical_rating' => 0, 'communication_rating' => 0]);
        return \App\Contexts\Shared\Application\WorkflowResult::next('manager_performancecontroller_store_1')->with('success', 'Performance review created.');
    }
}
