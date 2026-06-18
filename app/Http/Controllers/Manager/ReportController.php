<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $dept = Department::where('head_id', $user->id)->first();

        $teamIds = $dept
            ? User::where('department_id', $dept->id)->pluck('id')
            : collect();

        $leaveByMonth = LeaveRequest::whereIn('user_id', $teamIds)
            ->where('status', 'approved')
            ->selectRaw("DATE_FORMAT(start_date, '%Y-%m') as month, SUM(total_days) as total_days, COUNT(*) as count")
            ->groupBy('month')
            ->orderByDesc('month')
            ->take(12)
            ->get();

        $leaveByType = LeaveRequest::whereIn('user_id', $teamIds)
            ->where('status', 'approved')
            ->selectRaw('type, SUM(total_days) as total_days, COUNT(*) as count')
            ->groupBy('type')
            ->get();

        $leaveByEmployee = LeaveRequest::whereIn('user_id', $teamIds)
            ->where('status', 'approved')
            ->with('employee')
            ->selectRaw('user_id, SUM(total_days) as total_days, COUNT(*) as count')
            ->groupBy('user_id')
            ->orderByDesc('total_days')
            ->get();

        return view('manager.reports.index', compact('dept', 'leaveByMonth', 'leaveByType', 'leaveByEmployee'));
    }
}
