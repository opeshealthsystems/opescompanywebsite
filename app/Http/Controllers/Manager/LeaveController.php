<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $dept = Department::where('head_id', $user->id)->first();

        $query = LeaveRequest::with('employee')
            ->when($dept, fn ($q) => $q->whereHas('employee', fn ($q2) => $q2->where('department_id', $dept->id)))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest();

        $leaves = $query->paginate(20)->withQueryString();

        return view('manager.leave.index', compact('leaves', 'dept'));
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
        $leave->update([
            'status' => 'rejected',
            'notes'  => $request->note,
        ]);

        return back()->with('success', 'Leave request rejected.');
    }
}
