<?php

namespace App\Contexts\Shared\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\AttendanceRecord;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\JobOpening;
use App\Models\Lead;
use App\Models\License;
use App\Models\LeaveRequest;
use App\Models\TesterAssignment;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class AdminOperationalReportQueries extends WorkflowHandler
{
    private function authorizeAdministrator(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    public function overview(WorkflowInput $input): WorkflowResult
    {
        $this->authorizeAdministrator($input);

        $invoicedTotal = DB::table('invoice_items')->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->whereIn('invoices.status', ['sent', 'paid', 'overdue'])->sum('invoice_items.total');
        $paidTotal = DB::table('invoice_items')->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.status', 'paid')->sum('invoice_items.total');
        $overdueCount = Invoice::where('status', 'overdue')->count();
        $overdueTotal = DB::table('invoice_items')->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.status', 'overdue')->sum('invoice_items.total');
        $activeLicenses = License::where('status', 'active')->count();
        $expiringLicenses = License::where('status', 'active')->where('end_date', '<=', now()->addDays(30))->where('end_date', '>=', now())->count();
        $openTickets = Ticket::whereIn('status', ['open', 'in_progress'])->count();
        $resolvedThisMonth = Ticket::where('status', 'resolved')->whereMonth('updated_at', now()->month)->count();
        $newLeads = Lead::where('created_at', '>=', now()->subDays(30))->count();
        $qualifiedLeads = Lead::where('status', 'qualified')->count();
        $licensesByProduct = License::where('status', 'active')->selectRaw('product_name, count(*) as total')
            ->groupBy('product_name')->orderByDesc('total')->limit(8)->get();
        $recentInvoices = Invoice::with('customer')->whereIn('status', ['sent', 'overdue'])->orderByDesc('due_date')->limit(6)->get();

        return WorkflowResult::payload(compact('invoicedTotal', 'paidTotal', 'overdueCount', 'overdueTotal', 'activeLicenses', 'expiringLicenses',
            'openTickets', 'resolvedThisMonth', 'newLeads', 'qualifiedLeads', 'licensesByProduct', 'recentInvoices'));
    }

    public function dashboard(WorkflowInput $input): WorkflowResult
    {
        $this->authorizeAdministrator($input);
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $metrics = [
            'totalCustomers' => User::role('customer')->count(),
            'newCustomers' => User::role('customer')->where('created_at', '>=', $monthStart)->count(),
            'activeLicenses' => License::where('status', 'active')->count(),
            'expiringSoon' => License::where('status', 'active')->where('end_date', '>=', $now)->where('end_date', '<=', $now->copy()->addDays(30))->count(),
            'paidThisMonth' => Invoice::where('status', 'paid')->where('paid_at', '>=', $monthStart)->count(),
            'outstandingInvoices' => Invoice::whereIn('status', ['sent', 'overdue'])->count(),
            'overdueInvoices' => Invoice::where('status', 'overdue')->count(),
            'openTickets' => Ticket::whereIn('status', ['open', 'in_progress', 'pending_customer'])->count(),
            'resolvedThisMonth' => Ticket::where('status', 'resolved')->where('resolved_at', '>=', $monthStart)->count(),
            'openBugReports' => Ticket::where('type', 'bug_report')->whereIn('status', ['open', 'in_progress', 'pending_customer'])->count(),
            'pendingAssignments' => TesterAssignment::where('status', 'pending')->count(),
            'activeAssignments' => TesterAssignment::where('status', 'in_progress')->count(),
            'completedThisMonth' => TesterAssignment::where('status', 'completed')->where('updated_at', '>=', $monthStart)->count(),
            'recentTickets' => Ticket::with('customer')->whereIn('status', ['open', 'in_progress', 'pending_customer'])->orderByDesc('created_at')->limit(5)->get(),
            'recentInvoices' => Invoice::with('customer')->whereIn('status', ['sent', 'overdue'])->orderByDesc('created_at')->limit(5)->get(),
        ];

        return WorkflowResult::payload(['metrics' => $metrics]);
    }

    public function contractsExpiry(WorkflowInput $input): WorkflowResult
    {
        $this->authorizeAdministrator($input);
        $today = Carbon::today();
        $rows = Contract::whereIn('status', ['active', 'pending_renewal'])->whereNotNull('end_date')->orderBy('end_date')->get()
            ->map(function (Contract $contract) use ($today): array {
                $endDate = Carbon::parse($contract->end_date);
                return ['reference' => $contract->reference, 'type' => $contract->type, 'status' => $contract->status,
                    'value' => (float) $contract->value, 'currency' => $contract->currency ?? 'XAF',
                    'end_date' => $endDate->format('d M Y'), 'days_left' => (int) $today->diffInDays($endDate, false),
                    'auto_renew' => (bool) $contract->auto_renew];
            })->all();
        $buckets = [
            'expired' => ['label' => 'Already Expired', 'rows' => array_values(array_filter($rows, fn ($row) => $row['days_left'] < 0))],
            'week' => ['label' => 'Expiring This Week', 'rows' => array_values(array_filter($rows, fn ($row) => $row['days_left'] >= 0 && $row['days_left'] <= 7))],
            'month' => ['label' => 'Expiring This Month', 'rows' => array_values(array_filter($rows, fn ($row) => $row['days_left'] > 7 && $row['days_left'] <= 30))],
            'quarter' => ['label' => 'Expiring in 30–90 Days', 'rows' => array_values(array_filter($rows, fn ($row) => $row['days_left'] > 30 && $row['days_left'] <= 90))],
            'later' => ['label' => 'Beyond 90 Days', 'rows' => array_values(array_filter($rows, fn ($row) => $row['days_left'] > 90))],
        ];

        return WorkflowResult::payload(['buckets' => $buckets]);
    }

    public function crmSummary(WorkflowInput $input): WorkflowResult
    {
        $this->authorizeAdministrator($input);
        return WorkflowResult::payload([
            'open_leads' => Lead::whereNotIn('status', ['converted', 'lost', 'disqualified'])->count(),
            'pipeline_value' => (float) \App\Models\Deal::whereNotIn('stage', ['closed_won', 'closed_lost'])->sum('value'),
            'won_this_month' => (float) \App\Models\Deal::where('stage', 'closed_won')->where('updated_at', '>=', now()->startOfMonth())->sum('value'),
            'contracts_expiring' => Contract::where('status', 'active')->whereNotNull('end_date')->whereDate('end_date', '<=', now()->addDays(30))->count(),
        ]);
    }

    public function peopleSummary(WorkflowInput $input): WorkflowResult
    {
        $this->authorizeAdministrator($input);
        return WorkflowResult::payload([
            'headcount' => User::whereHas('roles', fn ($query) => $query->where('name', '!=', 'customer'))->count(),
            'pending_leave' => LeaveRequest::where('status', 'pending')->count(),
            'absent_today' => AttendanceRecord::whereDate('date', today())->where('status', 'absent')->count(),
            'open_positions' => JobOpening::where('status', 'open')->count(),
        ]);
    }
}
