<?php

namespace App\Contexts\Operations\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Milestone;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Shared project milestone transitions. */
final class AdminMilestoneCommands extends WorkflowHandler
{
    public function complete(WorkflowInput $input, Milestone $milestone): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
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
