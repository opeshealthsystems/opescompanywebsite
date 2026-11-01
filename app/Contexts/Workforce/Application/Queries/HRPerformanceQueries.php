<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\Department;
use App\Models\PerformanceReview;
use App\Models\User;
class HRPerformanceQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $reviews = $request->paginate(\App\Models\PerformanceReview::with(['employee', 'reviewer'])->when($request->status, fn($q) => $q->where('status', $request->status))->when($request->period, fn($q) => $q->where('review_period', $request->period))->when($request->department_id, fn($q) => $q->whereHas('employee', fn($q2) => $q2->where('department_id', $request->department_id)))->latest('review_date'), 25);
        $employees = $request->collect(\App\Models\User::whereNotNull('employee_id')->orderBy('name'));
        $departments = $request->collect(\App\Models\Department::orderBy('name'));
        return \App\Contexts\Shared\Application\WorkflowResult::read('hr_performancecontroller_index_1', compact('reviews', 'employees', 'departments'));
    }
}
