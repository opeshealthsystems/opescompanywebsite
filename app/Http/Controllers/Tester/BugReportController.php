<?php
namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class BugReportController extends Controller
{
    public function index()
    {
        $reports = Ticket::where('user_id', Auth::id())
            ->where('type', 'bug_report')
            ->with('testerAssignment')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('tester.bug-reports.index', compact('reports'));
    }
}
