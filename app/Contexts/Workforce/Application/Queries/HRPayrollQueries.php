<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\PayrollRun;
class HRPayrollQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $runs = $workflowInput->paginate(\App\Models\PayrollRun::withCount('entries')->orderByDesc('period_start'), 20);
        return \App\Contexts\Shared\Application\WorkflowResult::read('hr_payrollcontroller_index_1', compact('runs'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, \App\Models\PayrollRun $run): \App\Contexts\Shared\Application\WorkflowResult
    {
        $run->load(['entries.employee', 'processor']);
        $entries = $workflowInput->paginate($run->entries()->with('employee'), 30);
        return \App\Contexts\Shared\Application\WorkflowResult::read('hr_payrollcontroller_show_1', compact('run', 'entries'));
    }
}
