<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\Department;
use App\Models\PerformanceReview;
use App\Models\User;
class ManagerPerformanceQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $dept = \App\Models\Department::where('head_id', $user->id)->first();
        $reviews = $request->paginate(\App\Models\PerformanceReview::with('employee')->when($dept, fn($q) => $q->whereHas('employee', fn($q2) => $q2->where('department_id', $dept->id)), fn($emptyScope) => $emptyScope->whereRaw('1 = 0'))->when($request->status, fn($q) => $q->where('status', $request->status))->latest('review_date'), 20);
        $teamMembers = $dept ? $request->collect(\App\Models\User::where('department_id', $dept->id)->orderBy('name')) : collect();
        return \App\Contexts\Shared\Application\WorkflowResult::read('manager_performancecontroller_index_1', compact('reviews', 'dept', 'teamMembers'));
    }
}
