<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\LeaveRequest;
use App\Models\PayrollRun;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = User::whereNotNull('employee_id')->count();

        $pendingLeave = LeaveRequest::where('status', 'pending')->count();

        $activePayroll = PayrollRun::whereIn('status', ['draft', 'processing'])->first();

        $expiringContracts = EmployeeProfile::where('contract_end_date', '<=', Carbon::now()->addDays(30))
            ->where('contract_end_date', '>=', Carbon::now())
            ->with('user')
            ->get();

        $recentEmployees = User::whereNotNull('employee_id')
            ->latest()
            ->take(5)
            ->get();

        $recentLeave = LeaveRequest::where('status', 'pending')
            ->with('employee')
            ->latest()
            ->take(5)
            ->get();

        $depts = Department::withCount('members')->get();

        return view('hr.dashboard', compact(
            'totalEmployees', 'pendingLeave', 'activePayroll',
            'expiringContracts', 'recentEmployees', 'recentLeave', 'depts'
        ));
    }
}
