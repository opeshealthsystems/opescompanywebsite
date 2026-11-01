<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminLeaveQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function item(LeaveRequest $leave): array
    {
        return $leave->only(['id', 'user_id', 'leave_type_id', 'type', 'start_date', 'end_date', 'total_days', 'reason', 'status', 'approved_by', 'approved_at', 'notes', 'created_at', 'updated_at'])
            + ['employee' => $leave->employee?->only(['id', 'name', 'email']), 'leave_type' => $leave->leaveType?->only(['id', 'name', 'code']),
                'approver' => $leave->approver?->only(['id', 'name'])];
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return LeaveRequest::query()->with(['employee', 'leaveType', 'approver'])->latest();
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:pending,approved,rejected,cancelled', 'user_id' => 'sometimes|integer|exists:users,id',
            'from' => 'sometimes|date', 'to' => 'sometimes|date']);
        $query = $this->query($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['user_id'])) $query->where('user_id', $filters['user_id']);
        if (isset($filters['from'])) $query->where('start_date', '>=', $filters['from']);
        if (isset($filters['to'])) $query->where('end_date', '<=', $filters['to']);
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (LeaveRequest $leave) => $this->item($leave))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, LeaveRequest $leave): WorkflowResult
    {
        $this->admin($input);
        $leave->load(['employee', 'leaveType', 'approver']);
        return WorkflowResult::payload(['leave_request' => $this->item($leave)]);
    }

    public function leaveBalances(WorkflowInput $input, User $employee): WorkflowResult
    {
        $this->admin($input);
        abort_unless($employee->is_active && $employee->hasAnyRole(['super_admin', 'admin', 'support', 'staff', 'employee', 'tester']), 404);
        $query = $this->leaveBalanceQuery($input, $employee);
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (LeaveBalance $balance) => $balance->only(['id', 'user_id', 'year', 'type', 'entitled_days', 'used_days']) + ['remaining_days' => $balance->remainingDays()])->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function leaveBalanceQuery(WorkflowInput $input, User $employee): Builder
    {
        $this->admin($input);
        abort_unless($employee->is_active && $employee->hasAnyRole(['super_admin', 'admin', 'support', 'staff', 'employee', 'tester']), 404);

        return LeaveBalance::query()->where('user_id', $employee->id)->orderByDesc('year')->orderBy('type');
    }

    public function leaveBalance(WorkflowInput $input, LeaveBalance $leaveBalance): WorkflowResult
    {
        $this->admin($input);
        $leaveBalance->load('user');
        abort_unless($leaveBalance->user?->is_active && $leaveBalance->user->hasAnyRole(['super_admin', 'admin', 'support', 'staff', 'employee', 'tester']), 404);
        return WorkflowResult::payload(['leave_balance' => $leaveBalance->only(['id', 'user_id', 'year', 'type', 'entitled_days', 'used_days']) + ['remaining_days' => $leaveBalance->remainingDays()]]);
    }
}
