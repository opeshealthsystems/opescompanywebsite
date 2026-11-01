<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\LeaveRequest;
use App\Models\PayrollRun;
use App\Models\User;
use Illuminate\Support\Carbon;
class HRDashboardQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $totalEmployees = \App\Models\User::whereNotNull('employee_id')->count();
        $pendingLeave = \App\Models\LeaveRequest::where('status', 'pending')->count();
        $activePayroll = \App\Models\PayrollRun::whereIn('status', ['draft', 'processing'])->first();
        $expiringContracts = $workflowInput->collect(\App\Models\EmployeeProfile::where('contract_end_date', '<=', \Illuminate\Support\Carbon::now()->addDays(30))->where('contract_end_date', '>=', \Illuminate\Support\Carbon::now())->with('user'));
        $recentEmployees = $workflowInput->collect(\App\Models\User::whereNotNull('employee_id')->latest()->take(5));
        $recentLeave = $workflowInput->collect(\App\Models\LeaveRequest::where('status', 'pending')->with('employee')->latest()->take(5));
        $depts = $workflowInput->collect(\App\Models\Department::withCount('members'));
        return \App\Contexts\Shared\Application\WorkflowResult::read('hr_dashboardcontroller_index_1', compact('totalEmployees', 'pendingLeave', 'activePayroll', 'expiringContracts', 'recentEmployees', 'recentLeave', 'depts'));
    }
}
