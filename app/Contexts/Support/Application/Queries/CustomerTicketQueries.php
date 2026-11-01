<?php

namespace App\Contexts\Support\Application\Queries;

use App\Mail\TicketCreated;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
class CustomerTicketQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $tickets = $workflowInput->paginate(\App\Models\Ticket::where('user_id', $user->id)->orderByDesc('created_at'), 15);
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_ticketcontroller_index_1', compact('tickets'));
    }
    public function create(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_ticketcontroller_create_1', []);
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $id = (int) $request->route('id');
        $ticket = \App\Models\Ticket::with('publicReplies.author')->findOrFail($id);
        $this->authorize('view', $ticket);
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_ticketcontroller_show_1', compact('ticket'));
    }
}
