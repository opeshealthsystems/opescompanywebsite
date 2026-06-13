<?php

namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TesterAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        $assignments = TesterAssignment::where('assigned_to', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('tester.assignments.index', compact('assignments'));
    }

    public function show(Request $request)
    {
        $user       = Auth::user();
        $id         = (int) $request->route('id');
        $assignment = TesterAssignment::with('bugReports')->findOrFail($id);

        abort_if((int) $assignment->assigned_to !== $user->id, 403);

        return view('tester.assignments.show', compact('assignment'));
    }

    public function updateStatus(Request $request)
    {
        $user       = Auth::user();
        $id         = (int) $request->route('id');
        $assignment = TesterAssignment::findOrFail($id);

        abort_if((int) $assignment->assigned_to !== $user->id, 403);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $assignment->update(['status' => $validated['status']]);

        return redirect()
            ->route('tester.assignments.show', ['locale' => app()->getLocale(), 'id' => $assignment->id])
            ->with('success', 'Assignment status updated.');
    }

    public function storeBugReport(Request $request)
    {
        $user       = Auth::user();
        $id         = (int) $request->route('id');
        $assignment = TesterAssignment::findOrFail($id);

        abort_if((int) $assignment->assigned_to !== $user->id, 403);
        abort_unless($assignment->isActive(), 403, 'Cannot file a bug report on a completed or cancelled assignment.');

        $validated = $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'priority'    => 'required|in:low,medium,high,urgent',
        ]);

        Ticket::create(array_merge($validated, [
            'user_id'              => $user->id,
            'type'                 => 'bug_report',
            'status'               => 'open',
            'tester_assignment_id' => $assignment->id,
        ]));

        return redirect()
            ->route('tester.assignments.show', ['locale' => app()->getLocale(), 'id' => $assignment->id])
            ->with('success', 'Bug report filed and sent to support.');
    }
}
