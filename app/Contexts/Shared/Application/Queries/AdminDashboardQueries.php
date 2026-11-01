<?php

namespace App\Contexts\Shared\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Deal;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\License;
use App\Models\TesterAssignment;
use App\Models\Ticket;
use App\Models\User;

final class AdminDashboardQueries extends WorkflowHandler
{
    public function summary(WorkflowInput $input): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin', 'support']), 403);

        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $summary = [
            'customers' => [
                'total' => User::role('customer')->count(),
                'new_this_month' => User::role('customer')->where('created_at', '>=', $monthStart)->count(),
            ],
            'tickets' => [
                'open' => Ticket::whereIn('status', ['open', 'in_progress', 'pending_customer'])->count(),
                'resolved_this_month' => Ticket::where('status', 'resolved')->where('resolved_at', '>=', $monthStart)->count(),
            ],
            'licenses' => [
                'active' => License::where('status', 'active')->count(),
                'expiring_in_30_days' => License::where('status', 'active')->where('end_date', '>=', $now)->where('end_date', '<=', $now->copy()->addDays(30))->count(),
            ],
            'tester_assignments' => [
                'pending' => TesterAssignment::where('status', 'pending')->count(),
                'active' => TesterAssignment::where('status', 'in_progress')->count(),
            ],
            'support_staff' => User::role(['super_admin', 'admin', 'support'])->count(),
        ];

        if ($actor->hasPermissionTo('manage_accounting')) {
            $summary['finance'] = [
                'outstanding_invoices' => Invoice::whereIn('status', ['sent', 'overdue'])->count(),
                'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
            ];
        }

        if ($actor->hasAnyRole(['super_admin', 'admin'])) {
            $summary['crm'] = [
                'new_leads' => Lead::where('status', 'new')->count(),
                'qualified_leads' => Lead::where('status', 'qualified')->count(),
                'open_pipeline_value' => Deal::whereNotIn('stage', ['closed_won', 'closed_lost'])->sum('value'),
            ];
        }

        return WorkflowResult::payload(['summary' => $summary, 'generated_at' => $now->toIso8601String()]);
    }
}
