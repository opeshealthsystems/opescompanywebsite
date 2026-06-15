<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Mail\TicketCreated;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $tickets = Ticket::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('customer.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('customer.tickets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'type'        => 'required|in:support,billing,technical,bug_report,other',
            'priority'    => 'required|in:low,medium,high,urgent',
        ]);

        $user   = Auth::user();
        $ticket = Ticket::create(array_merge($validated, [
            'user_id' => $user->id,
            'status'  => 'open',
        ]));

        Mail::to($user->email)->queue(new TicketCreated($ticket));

        return redirect()
            ->route('customer.tickets', ['locale' => app()->getLocale()])
            ->with('success', 'Your support ticket has been submitted. We\'ll be in touch shortly.');
    }

    public function show(Request $request)
    {
        $user   = Auth::user();
        $id     = (int) $request->route('id');
        $ticket = Ticket::with('publicReplies.author')->findOrFail($id);

        abort_if((int) $ticket->user_id !== $user->id, 403);

        return view('customer.tickets.show', compact('ticket'));
    }

    public function reply(Request $request)
    {
        $user   = Auth::user();
        $id     = (int) $request->route('id');
        $ticket = Ticket::findOrFail($id);

        abort_if((int) $ticket->user_id !== $user->id, 403);
        abort_unless($ticket->isOpen(), 403, 'This ticket is closed.');

        $validated = $request->validate([
            'body' => 'required|string|max:10000',
        ]);

        TicketReply::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'body'        => $validated['body'],
            'is_internal' => false,
        ]);

        if ($ticket->status === 'pending_customer') {
            $ticket->update(['status' => 'in_progress']);
        }

        return redirect()
            ->route('customer.tickets.show', ['locale' => app()->getLocale(), 'id' => $ticket->id])
            ->with('success', 'Your reply has been added.');
    }
}
