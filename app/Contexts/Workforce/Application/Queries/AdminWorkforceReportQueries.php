<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Department;
use App\Models\JobOpening;
use App\Models\LeaveRequest;
use App\Models\User;

final class AdminWorkforceReportQueries extends WorkflowHandler
{
    public function summary(WorkflowInput $input): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        $departments = Department::query()->where('is_active', true)->withCount('members')->orderByDesc('members_count')->get();
        $unassigned = User::query()->whereNull('department_id')->whereHas('roles', fn ($query) => $query->where('name', '!=', 'customer'))->count();
        $departmentRows = $departments->map(fn ($department) => ['name' => $department->name, 'count' => $department->members_count])->all();
        if ($unassigned > 0) $departmentRows[] = ['name' => 'Unassigned', 'count' => $unassigned];

        $requests = LeaveRequest::query()->whereMonth('start_date', now()->month)->whereYear('start_date', now()->year)->get(['status']);
        $summary = [
            'headcount_by_department' => $departmentRows,
            'total_headcount' => User::query()->whereHas('roles', fn ($query) => $query->where('name', '!=', 'customer'))->count(),
            'open_positions' => JobOpening::query()->where('status', 'open')->count(),
            'leave' => [
                'total_requests' => $requests->count(),
                'approved' => $requests->where('status', 'approved')->count(),
                'pending' => $requests->where('status', 'pending')->count(),
                'rejected' => $requests->where('status', 'rejected')->count(),
                'period' => now()->format('Y-m'),
            ],
        ];

        return WorkflowResult::payload(['summary' => $summary]);
    }
}
