<?php

namespace App\Filament\Widgets;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OpesDashboardStats extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $summary = app(WorkflowDispatcher::class)->execute(
            'admin.dashboard.summary',
            new WorkflowInput(actor: auth()->user(), language: app()->getLocale()),
        )->data['summary'];

        // Operational stats — visible to all panel users (incl. support).
        $stats = [
            Stat::make('Customers', $summary['customers']['total'])
                ->description($summary['customers']['new_this_month'] . ' new this month')
                ->icon('heroicon-o-users')
                ->color('success'),

            Stat::make('Open Tickets', $summary['tickets']['open'])
                ->description($summary['tickets']['resolved_this_month'] . ' resolved this month')
                ->icon('heroicon-o-ticket')
                ->color($summary['tickets']['open'] > 10 ? 'danger' : ($summary['tickets']['open'] > 0 ? 'warning' : 'success')),

            Stat::make('Active Licenses', $summary['licenses']['active'])
                ->description($summary['licenses']['expiring_in_30_days'] . ' expiring in 30 days')
                ->icon('heroicon-o-key')
                ->color($summary['licenses']['expiring_in_30_days'] > 0 ? 'warning' : 'success'),

            Stat::make('Tester Assignments', $summary['tester_assignments']['active'] . ' active')
                ->description($summary['tester_assignments']['pending'] . ' pending')
                ->icon('heroicon-o-beaker')
                ->color('info'),

            Stat::make('Support Staff', $summary['support_staff'])
                ->description('super admins + admins + support')
                ->icon('heroicon-o-shield-check')
                ->color('info'),
        ];

        // Finance aggregate — manage_accounting permission only (accountants + admins).
        if (isset($summary['finance'])) {
            $outstandingInvoices = $summary['finance']['outstanding_invoices'];
            $overdueInvoices = $summary['finance']['overdue_invoices'];

            $stats[] = Stat::make('Outstanding Invoices', $outstandingInvoices)
                ->description($overdueInvoices . ' overdue')
                ->icon('heroicon-o-banknotes')
                ->color($overdueInvoices > 0 ? 'danger' : ($outstandingInvoices > 0 ? 'warning' : 'success'));
        }

        // CRM aggregate — admin / super_admin only.
        if (isset($summary['crm'])) {
            $newLeads = $summary['crm']['new_leads'];

            $stats[] = Stat::make('New Leads', $newLeads)
                ->description($summary['crm']['qualified_leads'] . ' qualified')
                ->icon('heroicon-o-inbox')
                ->color($newLeads > 0 ? 'warning' : 'success');
        }

        return $stats;
    }
}
