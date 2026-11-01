<?php

namespace App\Contexts\Validation\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Cohort;
use App\Models\User;
use App\Models\WeeklyReview;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Immutable cohort metric snapshots with editable narrative fields. */
final class AdminWeeklyReviewCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'cohort_id' => 'required|integer|exists:cohorts,id', 'week_start' => 'required|date',
            'summary' => 'nullable|string|max:10000', 'action_items' => 'nullable|string|max:10000',
            'metrics' => 'prohibited', 'week_end' => 'prohibited', 'author_id' => 'prohibited',
            'generated_at' => 'prohibited', 'id' => 'prohibited',
        ]);
        $record = DB::transaction(function () use ($data, $actor) {
            $cohort = Cohort::query()->lockForUpdate()->findOrFail($data['cohort_id']);
            $weekStart = Carbon::parse($data['week_start'])->startOfDay();
            if (WeeklyReview::query()->where('cohort_id', $cohort->id)->whereDate('week_start', $weekStart)->exists()) {
                throw ValidationException::withMessages(['week_start' => 'A weekly snapshot already exists for this cohort and week.']);
            }
            $record = WeeklyReview::create(array_merge(
                ['cohort_id' => $cohort->id, 'week_start' => $weekStart->toDateString(), 'summary' => $data['summary'] ?? null, 'action_items' => $data['action_items'] ?? null],
                WeeklyReview::snapshotData($cohort, $weekStart, $actor->id),
            ));
            DomainOutbox::append('event', 'validation.weekly_review_created.v1', ['references' => [['resource' => 'WeeklyReview', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'cohort_id' => $record->cohort_id, 'week_start' => $record->week_start->toDateString(), 'week_end' => $record->week_end->toDateString(), 'metrics' => $record->metrics], 201)->with('success', 'Weekly snapshot generated.');
    }

    public function update(WorkflowInput $input, WeeklyReview $weeklyReview): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'summary' => 'sometimes|nullable|string|max:10000', 'action_items' => 'sometimes|nullable|string|max:10000',
            'cohort_id' => 'prohibited', 'week_start' => 'prohibited', 'week_end' => 'prohibited',
            'metrics' => 'prohibited', 'author_id' => 'prohibited', 'generated_at' => 'prohibited', 'id' => 'prohibited',
        ]);
        if ($data === []) throw ValidationException::withMessages(['review' => 'Provide a summary or action items to update.']);
        $record = DB::transaction(function () use ($weeklyReview, $data, $actor) {
            $record = WeeklyReview::query()->lockForUpdate()->findOrFail($weeklyReview->id);
            $record->fill($data)->save();
            DomainOutbox::append('event', 'validation.weekly_review_updated.v1', ['references' => [['resource' => 'WeeklyReview', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'week_start' => $record->week_start->toDateString(), 'metrics' => $record->metrics])->with('success', 'Weekly review updated.');
    }
}
