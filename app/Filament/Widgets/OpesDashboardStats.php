<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Lead;
use App\Models\License;
use App\Models\TesterAssignment;
use App\Models\Ticket;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OpesDashboardStats extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();

        $totalCustomers = User::role('customer')->count();
        $newCustomers = User::role('customer')->where('created_at', '>=', $monthStart)->count();

        $openTickets = Ticket::whereIn('status', ['open', 'in_progress', 'pending_customer'])->count();
        $resolvedThisMonth = Ticket::where('status', 'resolved')->where('resolved_at', '>=', $monthStart)->count();

        $activeLicenses = License::where('status', 'active')->count();
        $expiringSoon = License::where('status', 'active')
            ->where('end_date', '>=', $now)
            ->where('end_date', '<=', $now->copy()->addDays(30))
            ->count();

        $pendingAssignments = TesterAssignment::where('status', 'pending')->count();
        $activeAssignments = TesterAssignment::where('status', 'in_progress')->count();

        // Operational stats — visible to all panel users (incl. support).
        $stats = [
            Stat::make('Customers', $totalCustomers)
                ->description($newCustomers . ' new this month')
                ->icon('heroicon-o-users')
                ->color('success'),

            Stat::make('Open Tickets', $openTickets)
                ->description($resolvedThisMonth . ' resolved this month')
                ->icon('heroicon-o-ticket')
                ->color($openTickets > 10 ? 'danger' : ($openTickets > 0 ? 'warning' : 'success')),

            Stat::make('Active Licenses', $activeLicenses)
                ->description($expiringSoon . ' expiring in 30 days')
                ->icon('heroicon-o-key')
                ->color($expiringSoon > 0 ? 'warning' : 'success'),

            Stat::make('Tester Assignments', $activeAssignments . ' active')
                ->description($pendingAssignments . ' pending')
                ->icon('heroicon-o-beaker')
                ->color('info'),

            Stat::make('Support Staff', User::role(['super_admin', 'admin', 'support'])->count())
                ->description('super admins + admins + support')
                ->icon('heroicon-o-shield-check')
                ->color('info'),
        ];

        // Finance aggregate — manage_accounting permission only (accountants + admins).
        if (auth()->user()?->hasPermissionTo('manage_accounting') ?? false) {
            $outstandingInvoices = Invoice::whereIn('status', ['sent', 'overdue'])->count();
            $overdueInvoices = Invoice::where('status', 'overdue')->count();

            $stats[] = Stat::make('Outstanding Invoices', $outstandingInvoices)
                ->description($overdueInvoices . ' overdue')
                ->icon('heroicon-o-banknotes')
                ->color($overdueInvoices > 0 ? 'danger' : ($outstandingInvoices > 0 ? 'warning' : 'success'));
        }

        // CRM aggregate — admin / super_admin only.
        if (auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false) {
            $newLeads = Lead::where('status', 'new')->count();

            $stats[] = Stat::make('New Leads', $newLeads)
                ->description(Lead::where('status', 'qualified')->count() . ' qualified')
                ->icon('heroicon-o-inbox')
                ->color($newLeads > 0 ? 'warning' : 'success');
        }

        return $stats;
    }
}
