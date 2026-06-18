<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['head', 'parent', 'children'])
            ->withCount('members')
            ->orderBy('name')
            ->get();

        $employees = User::whereNotNull('employee_id')->orderBy('name')->get();

        return view('hr.departments.index', compact('departments', 'employees'));
    }

    public function updateHead(Request $request, Department $dept)
    {
        $request->validate(['head_id' => 'required|exists:users,id']);
        $dept->update(['head_id' => $request->head_id]);

        return back()->with('success', 'Department head updated.');
    }
}
