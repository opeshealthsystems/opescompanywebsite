<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\PayrollRun;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class ProfitAndLoss extends Page
{
    protected static ?string $title           = 'P&L Statement';
    protected static ?string $navigationIcon  = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'P&L Statement';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 6;
    protected static string $view = 'filament.pages.profit-and-loss';

    public int $selectedYear;

    public function mount(): void
    {
        $this->selectedYear = now()->year;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getReportData(): array
    {
        $months = [];
        $totalRevenue = 0;
        $totalExpenses = 0;
        $totalPayroll = 0;

        for ($m = 1; $m <= 12; $m++) {
            $revenue = (float) DB::table('invoice_items')
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->where('invoices.status', 'paid')
                ->whereYear('invoices.created_at', $this->selectedYear)
                ->whereMonth('invoices.created_at', $m)
                ->sum('invoice_items.total');

            $expenses = (float) Expense::whereIn('status', ['approved', 'paid'])
                ->whereYear('expense_date', $this->selectedYear)
                ->whereMonth('expense_date', $m)
                ->sum('amount');

            $payroll = (float) PayrollRun::where('status', 'completed')
                ->whereYear('period_start', $this->selectedYear)
                ->whereMonth('period_start', $m)
                ->sum('total_net');

            $totalCosts = $expenses + $payroll;
            $net = $revenue - $totalCosts;

            $totalRevenue += $revenue;
            $totalExpenses += $expenses;
            $totalPayroll += $payroll;

            $months[$m] = [
                'month'       => date('F', mktime(0, 0, 0, $m, 1)),
                'revenue'     => $revenue,
                'expenses'    => $expenses,
                'payroll'     => $payroll,
                'total_costs' => $totalCosts,
                'net'         => $net,
            ];
        }

        return [
            'months'         => $months,
            'total_revenue'  => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'total_payroll'  => $totalPayroll,
            'total_costs'    => $totalExpenses + $totalPayroll,
            'net'            => $totalRevenue - $totalExpenses - $totalPayroll,
        ];
    }

    protected function getViewData(): array
    {
        return ['report' => $this->getReportData()];
    }
}
