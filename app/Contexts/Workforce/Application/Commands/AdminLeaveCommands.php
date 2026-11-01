<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Workforce\Application\LeaveDecision;
use App\Models\LeaveRequest;
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
