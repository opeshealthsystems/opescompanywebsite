<?php

namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use App\Models\TesterAssignment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $active = TesterAssignment::where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date')
            ->get();

        $completed = TesterAssignment::where('assigned_to', $user->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        return view('tester.dashboard', compact('active', 'completed'));
    }
}
