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

    private function reviewValues(WorkflowInput $input, bool $update = false): array
    {
        $p = $update ? 'sometimes|' : '';
        $data = $input->validate([
            'user_id' => $p.'required|integer|exists:users,id', 'reviewer_id' => $p.'required|integer|exists:users,id',
            'review_period' => $p.'required|string|max:50', 'review_date' => $p.'required|date',
            'overall_rating' => $p.'required|integer|between:1,5', 'goals_rating' => $p.'required|integer|between:1,5',
            'teamwork_rating' => $p.'required|integer|between:1,5', 'technical_rating' => $p.'required|integer|between:1,5',
            'communication_rating' => $p.'required|integer|between:1,5', 'strengths' => $p.'nullable|string|max:10000',
            'areas_for_improvement' => $p.'nullable|string|max:10000', 'goals_for_next_period' => $p.'nullable|string|max:10000',
            'employee_comments' => $p.'nullable|string|max:10000', 'status' => 'prohibited', 'acknowledged_at' => 'prohibited', 'id' => 'prohibited',
        ]);
        if (isset($data['user_id']) && !User::query()->whereKey($data['user_id'])->where('is_active', true)
            ->whereHas('roles', fn ($roles) => $roles->whereIn('name', ['super_admin', 'admin', 'support', 'staff', 'employee', 'tester']))->exists()) {
            throw ValidationException::withMessages(['user_id' => 'Select an active employee account.']);
        }
        if (isset($data['reviewer_id']) && !User::query()->whereKey($data['reviewer_id'])->where('is_active', true)
            ->whereHas('roles', fn ($roles) => $roles->whereIn('name', ['super_admin', 'admin']))->exists()) {
            throw ValidationException::withMessages(['reviewer_id' => 'Select an active administrator as reviewer.']);
        }
        return $data;
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->reviewValues($input);
        $record = DB::transaction(function () use ($data, $actor) {
            $review = PerformanceReview::create($data + ['status' => 'draft']);
            DomainOutbox::append('event', 'workforce.performance_review_created.v1', ['references' => [['resource' => 'PerformanceReview', 'id' => $review->id]]], $actor->id);
            return $review;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status], 201)->with('success', 'Performance review created.');
    }

    public function update(WorkflowInput $input, PerformanceReview $review): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->reviewValues($input, true);
        if ($data === []) throw ValidationException::withMessages(['review' => 'Provide at least one review field to update.']);
        $record = DB::transaction(function () use ($review, $data, $actor) {
            $record = PerformanceReview::query()->lockForUpdate()->findOrFail($review->id);
            if ($record->status !== 'draft') throw ValidationException::withMessages(['status' => 'Submitted or acknowledged reviews cannot be edited.']);
            $record->update($data);
            DomainOutbox::append('event', 'workforce.performance_review_updated.v1', ['references' => [['resource' => 'PerformanceReview', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Performance review updated.');
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
