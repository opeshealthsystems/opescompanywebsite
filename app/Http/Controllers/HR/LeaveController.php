<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['employee', 'employee.department'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->department_id, fn ($q) => $q->whereHas('employee', fn ($q2) => $q2->where('department_id', $request->department_id)))
            ->when($request->from, fn ($q) => $q->where('start_date', '>=', $request->from))
            ->when($request->to,   fn ($q) => $q->where('end_date', '<=', $request->to))
            ->latest();

        $leaves      = $query->paginate(25)->withQueryString();
        $departments = Department::orderBy('name')->get();

        return view('hr.leave.index', compact('leaves', 'departments'));
    }

    public function approve(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes'       => $request->note,
        ]);
        $leave->deductFromBalance();

        return back()->with('success', 'Leave request approved.');
    }

    public function reject(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->update(['status' => 'rejected', 'notes' => $request->note]);

        return back()->with('success', 'Leave request rejected.');
    }
}
