<?php
namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\SupplierBill;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class FinancialOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = true;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    protected function getStats(): array
    {
        $thisMonth = now()->startOfMonth();

        // Invoice totals are computed from invoice_items (no stored `total` on invoices)
        $monthRevenue = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.status', 'paid')
            ->where('invoices.updated_at', '>=', $thisMonth)
            ->sum('invoice_items.total');

        $outstanding = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->whereNotIn('invoices.status', ['paid', 'cancelled', 'draft'])
            ->sum('invoice_items.total');

        $pendingExpenses = Expense::where('status', 'pending')->count();

        $overduePayables = SupplierBill::whereNotIn('status', ['paid', 'draft'])
            ->whereDate('due_date', '<', today())
            ->count();

        return [
            Stat::make('Revenue (This Month)', 'XAF '.number_format((float) $monthRevenue, 0))
                ->description('From paid invoices')
                ->color('success')
                ->icon('heroicon-o-banknotes'),
            Stat::make('Outstanding Invoices', 'XAF '.number_format((float) $outstanding, 0))
                ->description('Unpaid invoices')
                ->color($outstanding > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-document-text'),
            Stat::make('Pending Expenses', (string) $pendingExpenses)
                ->description('Awaiting approval')
                ->color($pendingExpenses > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-receipt-percent'),
            Stat::make('Overdue Payables', (string) $overduePayables)
                ->description('Supplier bills past due')
                ->color($overduePayables > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-inbox-stack'),
        ];
    }
}
