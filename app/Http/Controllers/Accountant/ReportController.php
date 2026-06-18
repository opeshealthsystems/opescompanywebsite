<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\PayrollRun;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $months = collect(range(0, 11))->map(fn ($i) => Carbon::now()->subMonths($i)->format('Y-m'))->reverse()->values();

        $revenue = Invoice::where('status', 'paid')
            ->get()
            ->groupBy(fn ($i) => \Carbon\Carbon::parse($i->paid_at)->format('Y-m'))
            ->map(fn ($group) => $group->sum('grand_total'));

        $payrollCost = PayrollRun::where('status', 'completed')
            ->selectRaw("DATE_FORMAT(period_end, '%Y-%m') as month, SUM(total_net) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $expenses = Expense::where('status', 'approved')
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $ar = Invoice::whereIn('status', ['sent', 'overdue'])
            ->with(['customer', 'items'])
            ->orderBy('due_date')
            ->get()
            ->groupBy(function ($inv) {
                $due = $inv->due_date ? Carbon::parse($inv->due_date) : Carbon::now();
                $age = max(0, (int) now()->diffInDays($due, false) * -1);
                if ($age <= 30) return '0–30d';
                if ($age <= 60) return '31–60d';
                return '60d+';
            });

        return view('accountant.reports.index', compact('months', 'revenue', 'payrollCost', 'expenses', 'ar'));
    }
}
