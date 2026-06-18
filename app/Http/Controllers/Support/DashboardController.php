<?php
namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $me = Auth::id();

        $myOpenCount      = Ticket::where('assigned_to', $me)
            ->whereIn('status', ['open', 'in_progress', 'pending_customer'])->count();
        $myResolvedToday  = Ticket::where('assigned_to', $me)
            ->where('status', 'resolved')
            ->whereDate('resolved_at', today())->count();
        $unassignedCount  = Ticket::whereNull('assigned_to')
            ->whereIn('status', ['open', 'in_progress'])->count();
        $slaBreachedCount = Ticket::where('assigned_to', $me)
            ->where('sla_resolution_due_at', '<', now())
            ->whereNotIn('status', ['resolved', 'closed'])->count();

        $priorityOrder = "CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END";

        $myQueue = Ticket::where('assigned_to', $me)
            ->whereIn('status', ['open', 'in_progress', 'pending_customer'])
            ->with('customer')
            ->orderByRaw($priorityOrder)
            ->take(8)->get();

        $unassigned = Ticket::whereNull('assigned_to')
            ->whereIn('status', ['open'])
            ->with('customer')
            ->orderByRaw($priorityOrder)
            ->take(5)->get();

        return view('support.dashboard', compact(
            'myOpenCount', 'myResolvedToday', 'unassignedCount',
            'slaBreachedCount', 'myQueue', 'unassigned'
        ));
    }
}
