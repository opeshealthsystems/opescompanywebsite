<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\PayrollRun;
use Illuminate\Support\Carbon;
class AccountantDashboardQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $now = \Illuminate\Support\Carbon::now();
        $month = $now->month;
        $year = $now->year;
        $revenueThisMonth = $workflowInput->collect(\App\Models\Invoice::where('status', 'paid')->whereMonth('paid_at', $month)->whereYear('paid_at', $year))->sum('grand_total');
        $outstanding = $workflowInput->collect(\App\Models\Invoice::whereIn('status', ['sent', 'overdue']))->sum('grand_total');
        $overdueCount = \App\Models\Invoice::where('status', 'overdue')->count();
        $lastPayroll = \App\Models\PayrollRun::where('status', 'completed')->orderByDesc('period_end')->first();
        $expensesMtd = \App\Models\Expense::where('status', 'approved')->whereMonth('expense_date', $month)->whereYear('expense_date', $year)->sum('amount') ?: 0;
        $overdueInvoices = $workflowInput->collect(\App\Models\Invoice::where('status', 'overdue')->with('customer')->orderBy('due_date')->take(5));
        $recentPayments = $workflowInput->collect(\App\Models\Invoice::where('status', 'paid')->with('customer')->orderByDesc('paid_at')->take(5));
        return \App\Contexts\Shared\Application\WorkflowResult::read('accountant_dashboardcontroller_index_1', compact('revenueThisMonth', 'outstanding', 'overdueCount', 'lastPayroll', 'expensesMtd', 'overdueInvoices', 'recentPayments'));
    }
}
