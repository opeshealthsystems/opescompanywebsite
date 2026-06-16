<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\PayrollRun;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class CashFlow extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationLabel = 'Cash Flow';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 7;
    protected static string $view = 'filament.pages.cash-flow';

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
        $cumulativeCash = 0;

        for ($m = 1; $m <= 12; $m++) {
            $cashIn = (float) DB::table('invoice_items')
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->where('invoices.status', 'paid')
                ->whereYear('invoices.created_at', $this->selectedYear)
                ->whereMonth('invoices.created_at', $m)
                ->sum('invoice_items.total');

            $cashOutExpenses = (float) Expense::where('status', 'paid')
                ->whereYear('expense_date', $this->selectedYear)
                ->whereMonth('expense_date', $m)
                ->sum('amount');

            $cashOutPayroll = (float) PayrollRun::where('status', 'completed')
                ->whereYear('period_start', $this->selectedYear)
                ->whereMonth('period_start', $m)
                ->sum('total_net');

            $cashOut = $cashOutExpenses + $cashOutPayroll;
            $netCash = $cashIn - $cashOut;
            $cumulativeCash += $netCash;

            $months[$m] = [
                'month'           => date('F', mktime(0, 0, 0, $m, 1)),
                'cash_in'         => $cashIn,
                'cash_out_exp'    => $cashOutExpenses,
                'cash_out_pay'    => $cashOutPayroll,
                'cash_out'        => $cashOut,
                'net_cash'        => $netCash,
                'cumulative'      => $cumulativeCash,
            ];
        }

        return ['months' => $months, 'cumulative' => $cumulativeCash];
    }

    protected function getViewData(): array
    {
        return ['report' => $this->getReportData()];
    }
}
