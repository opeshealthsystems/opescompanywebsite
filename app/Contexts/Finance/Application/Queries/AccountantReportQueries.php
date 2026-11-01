<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\PayrollRun;
use Illuminate\Support\Carbon;
class AccountantReportQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $months = collect(range(0, 11))->map(fn($i) => \Illuminate\Support\Carbon::now()->subMonths($i)->format('Y-m'))->reverse()->values();
        $itemTotals = \App\Models\InvoiceItem::selectRaw('invoice_id, SUM(total) as subtotal')->groupBy('invoice_id');
        $revenue = \Illuminate\Support\Facades\DB::table('invoices')->leftJoinSub($itemTotals, 'item_totals', 'item_totals.invoice_id', '=', 'invoices.id')
            ->where('invoices.status', 'paid')->whereNotNull('invoices.paid_at')
            ->selectRaw(\App\Contexts\Shared\Infrastructure\ReportingSql::month('invoices.paid_at')." as month, SUM(COALESCE(item_totals.subtotal, 0) + ROUND(COALESCE(item_totals.subtotal, 0) * invoices.tax_rate / 100)) as total")
            ->groupBy('month')->orderBy('month')->pluck('total', 'month');
        $payrollCost = \App\Models\PayrollRun::where('status', 'completed')->selectRaw(\App\Contexts\Shared\Infrastructure\ReportingSql::month('period_end')." as month, SUM(total_net) as total")->groupBy('month')->orderBy('month')->pluck('total', 'month');
        $expenses = \App\Models\Expense::where('status', 'approved')->selectRaw(\App\Contexts\Shared\Infrastructure\ReportingSql::month('expense_date')." as month, SUM(amount) as total")->groupBy('month')->orderBy('month')->pluck('total', 'month');
        $ar = $workflowInput->collect(\App\Models\Invoice::whereIn('status', ['sent', 'overdue'])->with(['customer', 'items'])->orderBy('due_date'))->groupBy(function ($inv) {
            $due = $inv->due_date ? \Illuminate\Support\Carbon::parse($inv->due_date) : \Illuminate\Support\Carbon::now();
            $age = max(0, (int) now()->diffInDays($due, false) * -1);
            if ($age <= 30) {
                return '0–30d';
            }
            if ($age <= 60) {
                return '31–60d';
            }
            return '60d+';
        });
        return \App\Contexts\Shared\Application\WorkflowResult::read('accountant_reportcontroller_index_1', compact('months', 'revenue', 'payrollCost', 'expenses', 'ar'));
    }
}
