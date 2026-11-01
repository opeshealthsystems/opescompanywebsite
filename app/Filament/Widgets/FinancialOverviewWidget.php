<?php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

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
        $summary = app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.finance.reports.overview',
            new \App\Contexts\Shared\Application\WorkflowInput(actor: auth()->user(), language: app()->getLocale()),
        )->data;

        return [
            Stat::make('Revenue (This Month)', 'XAF '.number_format($summary['revenue_this_month'], 0))
                ->description('From paid invoices')->color('success')->icon('heroicon-o-banknotes'),
            Stat::make('Outstanding Invoices', 'XAF '.number_format($summary['outstanding_invoices'], 0))
                ->description('Unpaid invoices')->color($summary['outstanding_invoices'] > 0 ? 'warning' : 'success')->icon('heroicon-o-document-text'),
            Stat::make('Pending Expenses', (string) $summary['pending_expenses'])
                ->description('Awaiting approval')->color($summary['pending_expenses'] > 0 ? 'warning' : 'success')->icon('heroicon-o-receipt-percent'),
            Stat::make('Overdue Payables', (string) $summary['overdue_payables'])
                ->description('Supplier bills past due')->color($summary['overdue_payables'] > 0 ? 'danger' : 'success')->icon('heroicon-o-inbox-stack'),
        ];
    }
}
