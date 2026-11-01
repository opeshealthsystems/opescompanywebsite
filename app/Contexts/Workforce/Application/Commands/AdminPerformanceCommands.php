<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Shared administrative performance-review transitions. */
final class AdminPerformanceCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    public function submit(WorkflowInput $input, PerformanceReview $review): WorkflowResult
    {
        return $this->transition($input, $review, 'draft', 'submitted', null, 'workforce.performance_review_submitted.v1');
    }

    public function acknowledge(WorkflowInput $input, PerformanceReview $review): WorkflowResult
    {
        return $this->transition($input, $review, 'submitted', 'acknowledged', now(), 'workforce.performance_review_acknowledged.v1');
    }

    private function transition(WorkflowInput $input, PerformanceReview $review, string $from, string $to, mixed $acknowledgedAt, string $event): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($review, $actor, $from, $to, $acknowledgedAt, $event) {
            $record = PerformanceReview::query()->lockForUpdate()->findOrFail($review->id);
            if ($record->status === $to) return $record;
            if ($record->status !== $from) throw ValidationException::withMessages(['status' => 'This performance review cannot make the requested transition from its current state.']);
            $changes = ['status' => $to];
            if ($acknowledgedAt !== null) $changes['acknowledged_at'] = $acknowledgedAt;
            $record->update($changes);
            DomainOutbox::append('event', $event, ['references' => [['resource' => 'PerformanceReview', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status, 'acknowledged_at' => $record->acknowledged_at?->toISOString()])->with('success', 'Performance review updated.');
    }
}
