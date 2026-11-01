<?php

namespace App\Contexts\Operations\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Shared project milestone transitions. */
final class AdminMilestoneCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function values(WorkflowInput $input, bool $update = false): array
    {
        $p = $update ? 'sometimes|' : '';
        return $input->validate([
            'title' => $p.'required|string|max:250', 'description' => 'sometimes|nullable|string|max:10000',
            'due_date' => 'sometimes|nullable|date', 'status' => $update ? 'sometimes|in:pending,in_progress,overdue' : 'sometimes|in:pending,in_progress,overdue',
            'project_id' => 'prohibited', 'completed_at' => 'prohibited', 'id' => 'prohibited',
        ]);
    }

    public function create(WorkflowInput $input, Project $project): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input);
        $record = DB::transaction(function () use ($project, $data, $actor) {
            $parent = Project::query()->lockForUpdate()->findOrFail($project->id);
            $record = $parent->milestones()->create($data + ['status' => 'pending']);
            DomainOutbox::append('event', 'operations.milestone_created.v1', ['references' => [['resource' => 'Project', 'id' => $parent->id], ['resource' => 'Milestone', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function update(WorkflowInput $input, Project $project, Milestone $milestone): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input, true);
        if ($data === []) throw ValidationException::withMessages(['milestone' => 'Provide at least one milestone field to update.']);
        $record = DB::transaction(function () use ($project, $milestone, $data, $actor) {
            $parent = Project::query()->lockForUpdate()->findOrFail($project->id);
            $record = Milestone::query()->lockForUpdate()->findOrFail($milestone->id);
            if ((int) $record->project_id !== (int) $parent->id) abort(404);
            if ($record->status === 'completed' && isset($data['status']) && $data['status'] !== 'completed') {
                throw ValidationException::withMessages(['status' => 'A completed milestone cannot be reopened.']);
            }
            $record->update($data);
            DomainOutbox::append('event', 'operations.milestone_updated.v1', ['references' => [['resource' => 'Project', 'id' => $parent->id], ['resource' => 'Milestone', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function delete(WorkflowInput $input, Project $project, Milestone $milestone): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($project, $milestone, $actor) {
            $parent = Project::query()->lockForUpdate()->findOrFail($project->id);
            $record = Milestone::query()->lockForUpdate()->findOrFail($milestone->id);
            if ((int) $record->project_id !== (int) $parent->id) abort(404);
            if ($record->status === 'completed' || $record->completed_at !== null) throw ValidationException::withMessages(['milestone' => 'Completed milestone history must be retained.']);
            $id = $record->id; $record->delete();
            DomainOutbox::append('event', 'operations.milestone_deleted.v1', ['references' => [['resource' => 'Milestone', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['id' => $id, 'status' => 'deleted']);
    }

    public function complete(WorkflowInput $input, Milestone $milestone): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($milestone, $actor) {
            $record = Milestone::query()->lockForUpdate()->findOrFail($milestone->id);
            if ($record->status === 'completed') return $record;
            if (!in_array($record->status, ['pending', 'in_progress', 'overdue'], true)) {
                throw ValidationException::withMessages(['status' => 'This milestone cannot be completed from its current state.']);
            }
            $record->update(['status' => 'completed', 'completed_at' => now()]);
            DomainOutbox::append('event', 'operations.milestone_completed.v1', ['references' => [['resource' => 'Milestone', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);

        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status, 'completed_at' => $record->completed_at?->toISOString()])->with('success', 'Milestone completed.');
    }
}
