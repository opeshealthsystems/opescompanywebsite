<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $reviews = PerformanceReview::with(['employee', 'reviewer'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->period, fn ($q) => $q->where('review_period', $request->period))
            ->when($request->department_id, fn ($q) => $q->whereHas('employee', fn ($q2) => $q2->where('department_id', $request->department_id)))
            ->latest('review_date')
            ->paginate(25)
            ->withQueryString();

        $employees   = User::whereNotNull('employee_id')->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('hr.performance.index', compact('reviews', 'employees', 'departments'));
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
            'reviewer_id'          => auth()->id(),
            'status'               => 'draft',
            'overall_rating'       => 0,
            'goals_rating'         => 0,
            'teamwork_rating'      => 0,
            'technical_rating'     => 0,
            'communication_rating' => 0,
        ]);

        return back()->with('success', 'Performance review initiated.');
    }
}
