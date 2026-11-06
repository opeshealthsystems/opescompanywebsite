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
        if ($workflowInput->api) {
            $entries->setCollection($entries->getCollection()->map(fn ($entry) => [
                'id' => $entry->id, 'payroll_run_id' => $entry->payroll_run_id, 'user_id' => $entry->user_id,
                'gross_salary' => $entry->gross_salary, 'deductions' => $entry->deductions,
                'total_deductions' => $entry->total_deductions, 'net_salary' => $entry->net_salary,
                'currency' => $entry->currency, 'status' => $entry->status,
                'employee' => $entry->employee ? ['id' => $entry->employee->id, 'name' => $entry->employee->name] : null,
            ]));
            return \App\Contexts\Shared\Application\WorkflowResult::read('accountant_payrollcontroller_show_1', [
                'run' => [
                    'id' => $run->id, 'reference' => $run->reference, 'period_start' => $run->period_start,
                    'period_end' => $run->period_end, 'status' => $run->status, 'total_gross' => $run->total_gross,
                    'total_net' => $run->total_net, 'currency' => $run->currency, 'notes' => $run->notes,
                    'processed_by' => $run->processed_by, 'completed_at' => $run->completed_at,
                    'processor' => $run->processor ? ['id' => $run->processor->id, 'name' => $run->processor->name] : null,
                ],
                'byDept' => $byDept->map(fn ($row) => (array) $row)->all(), 'entries' => $entries,
            ]);
        }
        return \App\Contexts\Shared\Application\WorkflowResult::read('accountant_payrollcontroller_show_1', compact('run', 'byDept', 'entries'));
    }
}
