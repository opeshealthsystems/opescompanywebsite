<?php

namespace App\Contexts\Support\Application\Commands;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Support\Facades\Auth;
class SupportTicketCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function assignToMe(\App\Contexts\Shared\Application\WorkflowInput $request, string $locale, \App\Models\Ticket $ticket): \App\Contexts\Shared\Application\WorkflowResult
    {
        $ticket->update(['assigned_to' => \App\Contexts\Shared\Application\CurrentWorkflow::id(), 'status' => 'in_progress']);
        return \App\Contexts\Shared\Application\WorkflowResult::next('support_ticketcontroller_assignToMe_1', ['locale' => $request->locale(), 'ticket' => $ticket->id])->with('success', 'Ticket assigned to you.');
    }
    public function reply(\App\Contexts\Shared\Application\WorkflowInput $request, string $locale, \App\Models\Ticket $ticket): \App\Contexts\Shared\Application\WorkflowResult
    {
        $validated = $request->validate(['body' => 'required|string|max:10000', 'is_internal' => 'boolean']);
        \App\Models\TicketReply::create(['ticket_id' => $ticket->id, 'user_id' => \App\Contexts\Shared\Application\CurrentWorkflow::id(), 'body' => $validated['body'], 'is_internal' => $validated['is_internal'] ?? false]);
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }
        return \App\Contexts\Shared\Application\WorkflowResult::next('support_ticketcontroller_reply_1', ['locale' => $request->locale(), 'ticket' => $ticket->id])->with('success', 'Reply added.');
    }
    public function updateStatus(\App\Contexts\Shared\Application\WorkflowInput $request, string $locale, \App\Models\Ticket $ticket): \App\Contexts\Shared\Application\WorkflowResult
    {
        $validated = $request->validate(['status' => 'required|in:open,in_progress,pending_customer,resolved,closed']);
        $ticket->update(['status' => $validated['status']]);
        return \App\Contexts\Shared\Application\WorkflowResult::next('support_ticketcontroller_updateStatus_1', ['locale' => $request->locale(), 'ticket' => $ticket->id])->with('success', 'Status updated.');
    }
}
