<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Workforce\Application\LeaveDecision;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Admin leave decisions call the same guarded application service as HR/manager flows. */
final class AdminLeaveCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function leaveValues(WorkflowInput $input, bool $update = false): array
    {
        $p = $update ? 'sometimes|' : '';
        $data = $input->validate([
            'user_id' => $p.'required|integer|exists:users,id', 'leave_type_id' => $p.'nullable|integer|exists:leave_types,id',
            'type' => $p.'required|in:'.implode(',', array_keys(LeaveRequest::typeOptions())),
            'start_date' => $p.'required|date', 'end_date' => $p.'required|date|after_or_equal:start_date',
            'total_days' => $p.'required|numeric|min:0.5|max:365', 'reason' => $p.'nullable|string|max:5000',
            'notes' => 'sometimes|nullable|string|max:5000',
            'status' => 'prohibited', 'approved_by' => 'prohibited', 'approved_at' => 'prohibited', 'id' => 'prohibited',
        ]);
        if (isset($data['user_id']) && !User::query()->whereKey($data['user_id'])->where('is_active', true)
            ->whereHas('roles', fn ($roles) => $roles->whereIn('name', ['super_admin', 'admin', 'support', 'staff', 'employee', 'tester']))->exists()) {
            throw ValidationException::withMessages(['user_id' => 'Select an active employee account.']);
        }
        if (!empty($data['leave_type_id']) && !LeaveType::query()->whereKey($data['leave_type_id'])->where('is_active', true)->exists()) {
            throw ValidationException::withMessages(['leave_type_id' => 'Select an active leave type.']);
        }
        return $data;
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->leaveValues($input);
        $record = DB::transaction(function () use ($data, $actor) {
            $leave = LeaveRequest::create($data + ['status' => 'pending']);
            \App\Contexts\Shared\Infrastructure\DomainOutbox::append('event', 'workforce.leave_request_created.v1', ['references' => [['resource' => 'LeaveRequest', 'id' => $leave->id]]], $actor->id);
            return $leave;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status], 201)->with('success', 'Leave request created.');
    }

    private function employeeWithLeaveBalanceAccess(WorkflowInput $input, User $employee): User
    {
        $this->admin($input);
        abort_unless($employee->is_active && $employee->hasAnyRole(['super_admin', 'admin', 'support', 'staff', 'employee', 'tester']), 404);
        return $employee;
    }

    public function createLeaveBalance(WorkflowInput $input, User $employee): WorkflowResult
    {
        $actor = $this->admin($input);
        $this->employeeWithLeaveBalanceAccess($input, $employee);
        $data = $input->validate([
            'year' => 'required|integer|min:1900|max:2200',
            'type' => 'required|in:'.implode(',', array_keys(LeaveRequest::typeOptions())),
            'entitled_days' => 'required|numeric|min:0|max:365',
            'user_id' => 'prohibited', 'used_days' => 'prohibited', 'remaining' => 'prohibited', 'id' => 'prohibited',
        ]);
        $record = DB::transaction(function () use ($employee, $data, $actor) {
            $parent = User::query()->lockForUpdate()->findOrFail($employee->id);
            if (LeaveBalance::query()->where('user_id', $parent->id)->where('year', $data['year'])->where('type', $data['type'])->exists()) {
                throw ValidationException::withMessages(['type' => 'A leave balance already exists for this employee, year and leave type.']);
            }
            $used = LeaveRequest::query()->where('user_id', $parent->id)->where('type', $data['type'])->where('status', 'approved')
                ->whereYear('start_date', $data['year'])->sum('total_days');
            $balance = $parent->leaveBalances()->create($data + ['used_days' => $used]);
            \App\Contexts\Shared\Infrastructure\DomainOutbox::append('event', 'workforce.leave_balance_created.v1', ['references' => [['resource' => 'User', 'id' => $parent->id], ['resource' => 'LeaveBalance', 'id' => $balance->id]]], $actor->id);
            return $balance;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function updateLeaveBalance(WorkflowInput $input, LeaveBalance $leaveBalance): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['entitled_days' => 'required|numeric|min:0|max:365', 'year' => 'prohibited', 'type' => 'prohibited',
            'user_id' => 'prohibited', 'used_days' => 'prohibited', 'remaining' => 'prohibited', 'id' => 'prohibited']);
        $record = DB::transaction(function () use ($leaveBalance, $data, $actor) {
            $record = LeaveBalance::query()->lockForUpdate()->findOrFail($leaveBalance->id);
            $used = max((float) $record->used_days, (float) LeaveRequest::query()->where('user_id', $record->user_id)->where('type', $record->type)
                ->where('status', 'approved')->whereYear('start_date', $record->year)->sum('total_days'));
            if ((float) $data['entitled_days'] < $used) {
                throw ValidationException::withMessages(['entitled_days' => 'Entitlement cannot be lower than leave already used.']);
            }
            $record->update($data);
            \App\Contexts\Shared\Infrastructure\DomainOutbox::append('event', 'workforce.leave_balance_updated.v1', ['references' => [['resource' => 'LeaveBalance', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function deleteLeaveBalance(WorkflowInput $input, LeaveBalance $leaveBalance): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($leaveBalance, $actor) {
            $record = LeaveBalance::query()->lockForUpdate()->findOrFail($leaveBalance->id);
            $approved = LeaveRequest::query()->where('user_id', $record->user_id)->where('type', $record->type)->where('status', 'approved')->whereYear('start_date', $record->year)->exists();
            if ($approved || (float) $record->used_days > 0) {
                throw ValidationException::withMessages(['leave_balance' => 'A leave balance with approved leave history or used days must be retained.']);
            }
            $id = $record->id;
            $record->delete();
            \App\Contexts\Shared\Infrastructure\DomainOutbox::append('event', 'workforce.leave_balance_deleted.v1', ['references' => [['resource' => 'LeaveBalance', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['id' => $id, 'status' => 'deleted']);
    }

    public function update(WorkflowInput $input, LeaveRequest $leave): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->leaveValues($input, true);
        if ($data === []) throw ValidationException::withMessages(['leave' => 'Provide at least one leave request field to update.']);
        $record = DB::transaction(function () use ($leave, $data, $actor) {
            $record = LeaveRequest::query()->lockForUpdate()->findOrFail($leave->id);
            if ($record->status !== 'pending') throw ValidationException::withMessages(['status' => 'Only pending leave requests can be edited.']);
            $record->update($data);
            \App\Contexts\Shared\Infrastructure\DomainOutbox::append('event', 'workforce.leave_request_updated.v1', ['references' => [['resource' => 'LeaveRequest', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Leave request updated.');
    }

    public function approve(WorkflowInput $input, LeaveRequest $leave): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = app(LeaveDecision::class)->decide($actor, $leave->id, 'approved');
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Leave request approved.');
    }

    public function reject(WorkflowInput $input, LeaveRequest $leave): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = app(LeaveDecision::class)->decide($actor, $leave->id, 'rejected');
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Leave request rejected.');
    }

    public function bulkApprove(WorkflowInput $input): WorkflowResult { return $this->bulk($input, 'approved'); }
    public function bulkReject(WorkflowInput $input): WorkflowResult { return $this->bulk($input, 'rejected'); }

    private function bulk(WorkflowInput $input, string $decision): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['ids' => 'required|array|min:1|max:100', 'ids.*' => 'required|integer|distinct']);
        $result = DB::transaction(function () use ($actor, $decision, $data) {
            $records = LeaveRequest::query()->whereIn('id', $data['ids'])->orderBy('id')->lockForUpdate()->get();
            if ($records->count() !== count($data['ids'])) throw ValidationException::withMessages(['ids' => 'One or more leave requests do not exist.']);
            foreach ($records as $record) {
                if ($record->status !== 'pending') throw ValidationException::withMessages(['ids' => 'Every selected leave request must still be pending.']);
            }
            foreach ($records as $record) app(LeaveDecision::class)->decide($actor, $record->id, $decision);
            return ['count' => $records->count(), 'ids' => $records->modelKeys(), 'decision' => $decision];
        }, 3);
        return WorkflowResult::payload($result)->with('success', $result['count'].' leave request(s) '.$decision.'.');
    }
}
