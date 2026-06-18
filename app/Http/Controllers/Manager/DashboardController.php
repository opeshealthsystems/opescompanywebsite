<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\PerformanceReview;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $dept = Department::where('head_id', $user->id)->first();

        $teamSize = $dept ? User::where('department_id', $dept->id)->count() : 0;

        $pendingLeave = LeaveRequest::where('status', 'pending')
            ->when($dept, fn ($q) => $q->whereHas('employee', fn ($q2) => $q2->where('department_id', $dept->id)))
            ->count();

        $reviewsDue = PerformanceReview::where('status', 'draft')
            ->when($dept, fn ($q) => $q->whereHas('employee', fn ($q2) => $q2->where('department_id', $dept->id)))
            ->count();

        $recentLeave = LeaveRequest::where('status', 'pending')
            ->when($dept, fn ($q) => $q->whereHas('employee', fn ($q2) => $q2->where('department_id', $dept->id)))
            ->with('employee')
            ->latest()
            ->take(5)
            ->get();

        $team = $dept
            ? User::where('department_id', $dept->id)->take(8)->get()
            : collect();

        $upcomingReviews = PerformanceReview::where('status', 'draft')
            ->when($dept, fn ($q) => $q->whereHas('employee', fn ($q2) => $q2->where('department_id', $dept->id)))
            ->with('employee')
            ->latest('review_date')
            ->take(4)
            ->get();

        return view('manager.dashboard', compact(
            'user', 'dept', 'teamSize', 'pendingLeave', 'reviewsDue',
            'recentLeave', 'team', 'upcomingReviews'
        ));
    }
}
