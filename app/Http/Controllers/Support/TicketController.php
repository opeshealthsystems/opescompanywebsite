<?php
namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with('customer')
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('mine')) {
            $query->where('assigned_to', Auth::id());
        }

        $tickets = $query->paginate(25)->withQueryString();

        return view('support.tickets.index', compact('tickets'));
    }

    public function show(string $locale, Ticket $ticket)
    {
        $ticket->load(['customer', 'assignee', 'publicReplies.author', 'testerAssignment']);
        return view('support.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, string $locale, Ticket $ticket)
    {
        $validated = $request->validate([
            'body'        => 'required|string|max:10000',
            'is_internal' => 'boolean',
        ]);

        TicketReply::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => Auth::id(),
            'body'        => $validated['body'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return redirect()
            ->route('support.tickets.show', ['locale' => app()->getLocale(), 'ticket' => $ticket->id])
            ->with('success', 'Reply added.');
    }

    public function updateStatus(Request $request, string $locale, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,pending_customer,resolved,closed',
        ]);

        $ticket->update(['status' => $validated['status']]);

        return redirect()
            ->route('support.tickets.show', ['locale' => app()->getLocale(), 'ticket' => $ticket->id])
            ->with('success', 'Status updated.');
    }

    public function assignToMe(Request $request, string $locale, Ticket $ticket)
    {
        $ticket->update(['assigned_to' => Auth::id(), 'status' => 'in_progress']);

        return redirect()
            ->route('support.tickets.show', ['locale' => app()->getLocale(), 'ticket' => $ticket->id])
            ->with('success', 'Ticket assigned to you.');
    }
}
