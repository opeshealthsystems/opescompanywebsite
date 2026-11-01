<?php

namespace App\Contexts\Workforce\Application;

use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** One state transition shared by every interface, under a record lock. */
final class LeaveDecision
{
    public function decide(User $actor, int|string $id, string $decision, ?string $notes = null): LeaveRequest
    {
        abort_unless(in_array($decision, ['approved', 'rejected'], true), 422);
        abort_unless($actor->is_active && $actor->hasAnyRole(['super_admin', 'admin', 'hr', 'manager']), 403);

        return DB::transaction(function () use ($actor, $id, $decision, $notes) {
            $leave = LeaveRequest::query()->lockForUpdate()->findOrFail($id);
            if (!$actor->hasAnyRole(['super_admin', 'admin', 'hr'])) {
                $departments = Department::where('head_id', $actor->id)->pluck('id');
                abort_unless($leave->employee && $departments->contains($leave->employee->department_id), 403);
                abort_if((string) $leave->user_id === (string) $actor->id, 403);
            }
            if ($leave->status === $decision) return $leave;
            if ($leave->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Only pending leave requests can be decided.']);
            }
            $leave->update(['status' => $decision, 'approved_by' => $actor->id, 'approved_at' => now(), 'notes' => $notes]);
            if ($decision === 'approved') $leave->deductFromBalance();
            \App\Contexts\Shared\Infrastructure\DomainOutbox::append('event', 'workforce.leave_decided.v1', ['references' => [
                ['resource' => 'LeaveRequest', 'id' => $leave->id, 'decision' => $decision],
            ]], $actor->id);
            return $leave;
        }, 3);
    }
}
