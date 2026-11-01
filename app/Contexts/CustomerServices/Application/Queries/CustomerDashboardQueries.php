<?php

namespace App\Contexts\CustomerServices\Application\Queries;

use App\Models\License;
use App\Models\Ticket;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
class CustomerDashboardQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $profile = $user->customerProfile;
        $activeLicenses = \App\Models\License::where('user_id', $user->id)->where('status', 'active')->count();
        $openTickets = \App\Models\Ticket::where('user_id', $user->id)->whereNotIn('status', ['closed', 'resolved'])->count();
        $pendingInvoices = \App\Models\Invoice::where('customer_id', $user->id)->whereIn('status', ['sent', 'overdue'])->count();
        $recentTickets = $workflowInput->collect(\App\Models\Ticket::where('user_id', $user->id)->orderByDesc('updated_at')->limit(3));
        $allProducts = array_merge(config('products', []), config('products_specialist', []));
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_dashboardcontroller_index_1', compact('user', 'profile', 'activeLicenses', 'openTickets', 'pendingInvoices', 'recentTickets', 'allProducts'));
    }
}
