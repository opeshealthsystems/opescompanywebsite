<?php

namespace App\Contexts\Support\Application\Queries;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Support\Facades\Auth;
class SupportTicketQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function query(\App\Contexts\Shared\Application\WorkflowInput $input): \Illuminate\Database\Eloquent\Builder
    {
        $actor = $input->user();
        abort_unless($actor instanceof \App\Models\User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin', 'support']), 403);

        return \App\Models\Ticket::query()->with(['customer', 'assignee'])
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->orderByDesc('created_at');
    }

    public function index(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $query = $this->query($request);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('mine')) {
            $query->where('assigned_to', \App\Contexts\Shared\Application\CurrentWorkflow::id());
        }
        $tickets = $request->paginate($query, 25);
        return \App\Contexts\Shared\Application\WorkflowResult::read('support_ticketcontroller_index_1', compact('tickets'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, string $locale, \App\Models\Ticket $ticket): \App\Contexts\Shared\Application\WorkflowResult
    {
        $ticket->load(['customer', 'assignee', 'publicReplies.author', 'testerAssignment']);
        return \App\Contexts\Shared\Application\WorkflowResult::read('support_ticketcontroller_show_1', compact('ticket'));
    }
}
