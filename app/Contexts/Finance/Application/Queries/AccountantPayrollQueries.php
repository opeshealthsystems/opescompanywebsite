<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Models\Department;
use App\Models\PayrollEntry;
use App\Models\PayrollRun;
use Illuminate\Support\Facades\DB;
class AccountantPayrollQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $runs = $workflowInput->paginate(\App\Models\PayrollRun::withCount('entries')->orderByDesc('period_start'), 20);
        return \App\Contexts\Shared\Application\WorkflowResult::read('accountant_payrollcontroller_index_1', compact('runs'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, \App\Models\PayrollRun $run): \App\Contexts\Shared\Application\WorkflowResult
    {
        $run->load('processor');
        $byDept = $workflowInput->collect(\App\Models\PayrollEntry::where('payroll_run_id', $run->id)->join('users', 'payroll_entries.user_id', '=', 'users.id')->join('departments', 'users.department_id', '=', 'departments.id')->selectRaw('departments.name as dept_name, SUM(payroll_entries.net_salary) as total_net, SUM(payroll_entries.gross_salary) as total_gross, COUNT(*) as count')->groupBy('departments.id', 'departments.name')->orderByDesc('total_net'));
        $entries = $workflowInput->paginate($run->entries()->with('employee'), 30);
        return \App\Contexts\Shared\Application\WorkflowResult::read('accountant_payrollcontroller_show_1', compact('run', 'byDept', 'entries'));
    }
}
