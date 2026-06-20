<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['employeeProfile', 'department'])
            ->whereNotNull('employee_id')
            ->when($request->search, fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%')))
            ->when($request->department_id, fn ($q) => $q->where('department_id', $request->department_id))
            ->when($request->employment_type, fn ($q) => $q->whereHas('employeeProfile', fn ($q2) => $q2->where('employment_type', $request->employment_type)));

        $employees  = $query->orderBy('name')->paginate(25)->withQueryString();
        $departments = Department::orderBy('name')->get();

        return view('hr.employees.index', compact('employees', 'departments'));
    }

    public function show(User $user)
    {
        $user->load('employeeProfile', 'department');

        $leaveBalances = LeaveBalance::where('user_id', $user->id)
            ->where('year', now()->year)
            ->get();

        return view('hr.employees.show', compact('user', 'leaveBalances'));
    }
}
