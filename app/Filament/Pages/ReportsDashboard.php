<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use App\Models\License;
use App\Models\Ticket;
use App\Models\TesterAssignment;
use App\Models\User;
use Filament\Pages\Page;

class ReportsDashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reports';
    protected static ?string $title           = 'Reports Dashboard';
    protected static ?string $slug            = 'reports-dashboard';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int    $navigationSort  = 50;
    protected static string  $view            = 'filament.pages.reports-dashboard';

    public array $metrics = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo('view_reports') ?? false;
    }

    public function mount(): void
    {
        $this->metrics = $this->buildMetrics();
    }

    protected function buildMetrics(): array
    {
        $now        = now();
        $monthStart = $now->copy()->startOfMonth();

        $totalCustomers    = User::role('customer')->count();
        $newCustomers      = User::role('customer')->where('created_at', '>=', $monthStart)->count();

        $activeLicenses    = License::where('status', 'active')->count();
        $expiringSoon      = License::where('status', 'active')
            ->where('end_date', '>=', $now)
            ->where('end_date', '<=', $now->copy()->addDays(30))
            ->count();

        $paidThisMonth       = Invoice::where('status', 'paid')->where('paid_at', '>=', $monthStart)->count();
        $outstandingInvoices = Invoice::whereIn('status', ['sent', 'overdue'])->count();
        $overdueInvoices     = Invoice::where('status', 'overdue')->count();

        $openTickets       = Ticket::whereIn('status', ['open', 'in_progress', 'pending_customer'])->count();
        $resolvedThisMonth = Ticket::where('status', 'resolved')->where('resolved_at', '>=', $monthStart)->count();
        $openBugReports    = Ticket::where('type', 'bug_report')->whereIn('status', ['open', 'in_progress', 'pending_customer'])->count();

        $pendingAssignments  = TesterAssignment::where('status', 'pending')->count();
        $activeAssignments   = TesterAssignment::where('status', 'in_progress')->count();
        $completedThisMonth  = TesterAssignment::where('status', 'completed')->where('updated_at', '>=', $monthStart)->count();

        $recentTickets  = Ticket::with('customer')->whereIn('status', ['open', 'in_progress', 'pending_customer'])->orderByDesc('created_at')->limit(5)->get();
        $recentInvoices = Invoice::with('customer')->whereIn('status', ['sent', 'overdue'])->orderByDesc('created_at')->limit(5)->get();

        return compact(
            'totalCustomers', 'newCustomers',
            'activeLicenses', 'expiringSoon',
            'paidThisMonth', 'outstandingInvoices', 'overdueInvoices',
            'openTickets', 'resolvedThisMonth', 'openBugReports',
            'pendingAssignments', 'activeAssignments', 'completedThisMonth',
            'recentTickets', 'recentInvoices'
        );
    }
}
