<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\PayrollRun;
use App\Models\SupplierBill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class AdminFinanceReportQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function year(WorkflowInput $input): int
    {
        return (int) ($input->validate(['year' => 'sometimes|integer|min:2000|max:2100'])['year'] ?? now()->year);
    }

    private function agingBuckets(string $itemKey): array
    {
        return [
            'current' => ['label' => 'Current (not yet due)', $itemKey => [], 'total' => 0],
            '1_30' => ['label' => '1–30 Days Overdue', $itemKey => [], 'total' => 0],
            '31_60' => ['label' => '31–60 Days Overdue', $itemKey => [], 'total' => 0],
            '61_90' => ['label' => '61–90 Days Overdue', $itemKey => [], 'total' => 0],
            'over_90' => ['label' => 'Over 90 Days Overdue', $itemKey => [], 'total' => 0],
        ];
    }

    private function bucketFor(?int $daysOverdue): string
    {
        if ($daysOverdue === null || $daysOverdue <= 0) return 'current';
        if ($daysOverdue <= 30) return '1_30';
        if ($daysOverdue <= 60) return '31_60';
        if ($daysOverdue <= 90) return '61_90';
        return 'over_90';
    }

    public function accountsPayableAging(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $buckets = $this->agingBuckets('bills');
        $bills = SupplierBill::query()->with('vendor:id,name')->whereNotIn('status', ['paid', 'draft'])->get();
        foreach ($bills as $bill) {
            $due = $bill->due_date ? Carbon::parse($bill->due_date) : null;
            $days = $due ? (int) round(Carbon::today()->diffInDays($due, false) * -1) : null;
            $item = ['reference' => $bill->reference, 'vendor' => $bill->vendor?->name ?? $bill->vendor_name ?? '—',
                'bill_number' => $bill->bill_number ?? '—', 'amount' => (float) $bill->total, 'currency' => $bill->currency ?? 'XAF',
                'status' => $bill->status, 'due_date' => $due?->format('d M Y') ?? '—', 'days_overdue' => $days];
            $key = $this->bucketFor($days); $buckets[$key]['bills'][] = $item; $buckets[$key]['total'] += $item['amount'];
        }
        return WorkflowResult::payload(['buckets' => $buckets, 'total_outstanding' => (float) SupplierBill::whereNotIn('status', ['paid', 'draft'])->sum('total')]);
    }

    public function accountsReceivableAging(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $buckets = $this->agingBuckets('invoices');
        $invoices = Invoice::query()->whereNotIn('status', ['paid', 'cancelled', 'draft'])->get();
        foreach ($invoices as $invoice) {
            $due = $invoice->due_date ? Carbon::parse($invoice->due_date) : null;
            $days = $due ? (int) round(Carbon::today()->diffInDays($due, false) * -1) : null;
            $item = ['reference' => $invoice->invoice_number, 'amount' => (float) $invoice->grand_total, 'currency' => $invoice->currency ?? 'XAF',
                'status' => $invoice->status, 'due_date' => $due?->format('d M Y') ?? '—', 'days_overdue' => $days];
            $key = $this->bucketFor($days); $buckets[$key]['invoices'][] = $item; $buckets[$key]['total'] += $item['amount'];
        }
        $outstanding = (float) DB::table('invoice_items')->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->whereNotIn('invoices.status', ['paid', 'cancelled', 'draft'])->sum('invoice_items.total');
        return WorkflowResult::payload(['buckets' => $buckets, 'total_outstanding' => $outstanding]);
    }

    public function cashFlow(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input); $year = $this->year($input); $months = []; $cumulative = 0.0;
        for ($month = 1; $month <= 12; $month++) {
            $cashIn = (float) DB::table('invoice_items')->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->where('invoices.status', 'paid')->whereYear('invoices.created_at', $year)->whereMonth('invoices.created_at', $month)->sum('invoice_items.total');
            $expenses = (float) Expense::where('status', 'paid')->whereYear('expense_date', $year)->whereMonth('expense_date', $month)->sum('amount');
            $payroll = (float) PayrollRun::where('status', 'completed')->whereYear('period_start', $year)->whereMonth('period_start', $month)->sum('total_net');
            $net = $cashIn - $expenses - $payroll; $cumulative += $net;
            $months[$month] = ['month' => date('F', mktime(0, 0, 0, $month, 1)), 'cash_in' => $cashIn, 'cash_out_exp' => $expenses,
                'cash_out_pay' => $payroll, 'cash_out' => $expenses + $payroll, 'net_cash' => $net, 'cumulative' => $cumulative];
        }
        return WorkflowResult::payload(['year' => $year, 'months' => $months, 'cumulative' => $cumulative]);
    }

    public function budgetVsActual(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input); $year = $this->year($input);
        $actuals = Expense::query()->select('category')->selectRaw('SUM(amount) as actual_spent')->whereIn('status', ['approved', 'paid'])
            ->whereYear('expense_date', $year)->groupBy('category')->pluck('actual_spent', 'category');
        $rows = Budget::query()->where('year', $year)->orderBy('category')->get()->map(function (Budget $budget) use ($actuals) {
            $actual = (float) ($actuals[$budget->category] ?? 0); $allocated = (float) $budget->allocated_amount;
            return ['id' => $budget->id, 'category' => $budget->category, 'department' => $budget->department, 'currency' => $budget->currency,
                'allocated_amount' => $allocated, 'actual_spent' => $actual, 'variance' => $allocated - $actual,
                'utilization' => $allocated === 0.0 ? null : round(($actual / $allocated) * 100, 1)];
        })->all();
        return WorkflowResult::payload(['year' => $year, 'items' => $rows]);
    }

    public function payrollHeadcount(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input); $year = $this->year($input);
        $runs = PayrollRun::where('status', 'completed')->whereYear('period_start', $year)->withCount('entries')->orderBy('period_start')->get();
        $items = $runs->map(fn ($run) => ['reference' => $run->reference, 'period' => $run->period_start->format('M Y'), 'headcount' => $run->entries_count,
            'gross' => (float) $run->total_gross, 'net' => (float) $run->total_net,
            'avg_net' => $run->entries_count > 0 ? (float) $run->total_net / $run->entries_count : 0, 'currency' => $run->currency])->all();
        $staff = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'admin', 'support', 'tester']))->where('is_active', true)->count();
        return WorkflowResult::payload(['year' => $year, 'runs' => $items, 'total_staff' => $staff, 'total_payroll_net' => $runs->sum('total_net')]);
    }

    public function profitAndLoss(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input); $year = $this->year($input); $months = []; $revenueTotal = 0.0; $expenseTotal = 0.0; $payrollTotal = 0.0;
        for ($month = 1; $month <= 12; $month++) {
            $revenue = (float) DB::table('invoice_items')->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->where('invoices.status', 'paid')->whereYear('invoices.created_at', $year)->whereMonth('invoices.created_at', $month)->sum('invoice_items.total');
            $expenses = (float) Expense::whereIn('status', ['approved', 'paid'])->whereYear('expense_date', $year)->whereMonth('expense_date', $month)->sum('amount');
            $payroll = (float) PayrollRun::where('status', 'completed')->whereYear('period_start', $year)->whereMonth('period_start', $month)->sum('total_net');
            $costs = $expenses + $payroll; $net = $revenue - $costs;
            $revenueTotal += $revenue; $expenseTotal += $expenses; $payrollTotal += $payroll;
            $months[$month] = ['month' => date('F', mktime(0, 0, 0, $month, 1)), 'revenue' => $revenue, 'expenses' => $expenses, 'payroll' => $payroll, 'total_costs' => $costs, 'net' => $net];
        }
        return WorkflowResult::payload(['year' => $year, 'months' => $months, 'total_revenue' => $revenueTotal, 'total_expenses' => $expenseTotal,
            'total_payroll' => $payrollTotal, 'total_costs' => $expenseTotal + $payrollTotal, 'net' => $revenueTotal - $expenseTotal - $payrollTotal]);
    }

    public function overview(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input); $monthStart = now()->startOfMonth();
        $monthRevenue = DB::table('invoice_items')->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')->where('invoices.status', 'paid')->where('invoices.updated_at', '>=', $monthStart)->sum('invoice_items.total');
        $outstanding = DB::table('invoice_items')->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')->whereNotIn('invoices.status', ['paid', 'cancelled', 'draft'])->sum('invoice_items.total');
        return WorkflowResult::payload(['revenue_this_month' => (float) $monthRevenue, 'outstanding_invoices' => (float) $outstanding,
            'pending_expenses' => Expense::where('status', 'pending')->count(),
            'overdue_payables' => SupplierBill::whereNotIn('status', ['paid', 'draft'])->whereDate('due_date', '<', today())->count()]);
    }
}
