<?php

namespace App\Contexts\Support\Application\Commands;

use App\Mail\TicketCreated;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
class CustomerTicketCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function store(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $validated = $request->validate(['subject' => 'required|string|max:255', 'description' => 'required|string|max:10000', 'type' => 'required|in:support,billing,technical,bug_report,other', 'priority' => 'required|in:low,medium,high,urgent']);
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $ticket = \App\Models\Ticket::create(array_merge($validated, ['user_id' => $user->id, 'status' => 'open']));
        \App\Contexts\Shared\Infrastructure\DomainMail::to($user->email)->queue(new \App\Mail\TicketCreated($ticket));
        $user->notify(new \App\Notifications\FeedEntry('support.ticket_created', 'Support ticket created', 'Your ticket "' . $ticket->subject . '" was created.', 'lifebuoy', route('customer.tickets.show', ['locale' => 'en', 'id' => $ticket->id])));
        $admins = \App\Models\User::role('admin')->get();
        if ($admins->isNotEmpty()) {
            \Filament\Notifications\Notification::make()->title('New ticket: ' . $ticket->subject)->body($user->name . ' · ' . ucfirst($ticket->priority) . ' priority · ' . ucfirst($ticket->type))->icon('heroicon-o-ticket')->iconColor($ticket->priority === 'urgent' ? 'danger' : 'warning')->sendToDatabase($admins);
        }
        return \App\Contexts\Shared\Application\WorkflowResult::next('customer_ticketcontroller_store_1', ['locale' => $request->locale()])->with('success', 'Your support ticket has been submitted. We\'ll be in touch shortly.');
    }
    public function reply(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $id = (int) $request->route('id');
        $ticket = \App\Models\Ticket::findOrFail($id);
        $this->authorize('reply', $ticket);
        abort_unless($ticket->isOpen(), 403, 'This ticket is closed.');
        $validated = $request->validate(['body' => 'required|string|max:10000']);
        \App\Models\TicketReply::create(['ticket_id' => $ticket->id, 'user_id' => $user->id, 'body' => $validated['body'], 'is_internal' => false]);
        if ($ticket->status === 'pending_customer') {
            $ticket->update(['status' => 'in_progress']);
        }
        $admins = \App\Models\User::role('admin')->get();
        if ($admins->isNotEmpty()) {
            \Filament\Notifications\Notification::make()->title('Customer reply on #' . $ticket->id)->body($user->name . ': ' . \Illuminate\Support\Str::limit($validated['body'], 80))->icon('heroicon-o-chat-bubble-left-right')->iconColor('info')->sendToDatabase($admins);
        }
        return \App\Contexts\Shared\Application\WorkflowResult::next('customer_ticketcontroller_reply_1', ['locale' => $request->locale(), 'id' => $ticket->id])->with('success', 'Your reply has been added.');
    }
}
