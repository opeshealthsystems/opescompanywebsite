<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\PayrollRun;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now   = Carbon::now();
        $month = $now->month;
        $year  = $now->year;

        $revenueThisMonth = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', $month)
            ->whereYear('paid_at', $year)
            ->get()
            ->sum('grand_total');

        $outstanding = Invoice::whereIn('status', ['sent', 'overdue'])
            ->get()
            ->sum('grand_total');

        $overdueCount = Invoice::where('status', 'overdue')->count();

        $lastPayroll = PayrollRun::where('status', 'completed')
            ->orderByDesc('period_end')
            ->first();

        $expensesMtd = Expense::where('status', 'approved')
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount') ?: 0;

        $overdueInvoices = Invoice::where('status', 'overdue')
            ->with('customer')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $recentPayments = Invoice::where('status', 'paid')
            ->with('customer')
            ->orderByDesc('paid_at')
            ->take(5)
            ->get();

        return view('accountant.dashboard', compact(
            'revenueThisMonth', 'outstanding', 'overdueCount',
            'lastPayroll', 'expensesMtd', 'overdueInvoices', 'recentPayments'
        ));
    }
}
