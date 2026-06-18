<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $dept = Department::where('head_id', $user->id)->first();

        $reviews = PerformanceReview::with('employee')
            ->when($dept, fn ($q) => $q->whereHas('employee', fn ($q2) => $q2->where('department_id', $dept->id)))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest('review_date')
            ->paginate(20)
            ->withQueryString();

        $teamMembers = $dept
            ? User::where('department_id', $dept->id)->orderBy('name')->get()
            : collect();

        return view('manager.performance.index', compact('reviews', 'dept', 'teamMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'review_period' => 'required|string|max:50',
            'review_date'   => 'required|date',
            'strengths'     => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'goals_for_next_period' => 'nullable|string',
        ]);

        PerformanceReview::create([
            ...$validated,
            'reviewer_id'     => auth()->id(),
            'status'          => 'draft',
            'overall_rating'  => 0,
            'goals_rating'    => 0,
            'teamwork_rating' => 0,
            'technical_rating' => 0,
            'communication_rating' => 0,
        ]);

        return back()->with('success', 'Performance review created.');
    }
}
