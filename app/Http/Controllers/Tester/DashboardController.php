<?php
namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TesterAssignment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $allAssignments = TesterAssignment::where('assigned_to', $user->id)->get();

        $active    = $allAssignments->whereIn('status', ['pending', 'in_progress'])->sortBy('due_date');
        $completed = $allAssignments->whereIn('status', ['completed', 'cancelled'])
                                    ->sortByDesc('updated_at')->take(5);
        $overdue   = $allAssignments->filter(fn ($a) => $a->isOverdue());

        $totalAssigned   = $allAssignments->count();
        $activeCount     = $active->count();
        $completedCount  = $allAssignments->where('status', 'completed')->count();
        $overdueCount    = $overdue->count();
        $bugReportsCount = Ticket::where('user_id', $user->id)
                                 ->where('type', 'bug_report')
                                 ->count();

        return view('tester.dashboard', compact(
            'user', 'active', 'completed',
            'totalAssigned', 'activeCount', 'completedCount', 'overdueCount', 'bugReportsCount'
        ));
    }
}
