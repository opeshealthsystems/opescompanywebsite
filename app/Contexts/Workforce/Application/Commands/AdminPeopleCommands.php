<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Contexts\Shared\Infrastructure\DomainMail;
use App\Models\Risk;
use App\Models\TrainingRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Shared admin commands for risk mitigation and staff training completion. */
final class AdminPeopleCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    public function mitigateRisk(WorkflowInput $input, Risk $risk): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($risk, $actor) {
            $record = Risk::query()->lockForUpdate()->findOrFail($risk->id);
            if ($record->status === 'mitigated') return $record;
            if ($record->status !== 'open') throw ValidationException::withMessages(['status' => 'Only open risks can be marked mitigated.']);
            $record->update(['status' => 'mitigated']);
            DomainOutbox::append('event', 'risk.mitigation_recorded.v1', ['references' => [['resource' => 'Risk', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Risk marked mitigated.');
    }

    public function completeTraining(WorkflowInput $input, TrainingRecord $trainingRecord): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($trainingRecord, $actor) {
            $record = TrainingRecord::query()->lockForUpdate()->findOrFail($trainingRecord->id);
            if ($record->status === 'completed') return $record;
            if (!in_array($record->status, ['planned', 'in_progress', 'expired'], true)) throw ValidationException::withMessages(['status' => 'This training record cannot be completed from its current state.']);
            $record->update(['status' => 'completed', 'completed_at' => $record->completed_at ?? today()]);
            DomainOutbox::append('event', 'workforce.training_completed.v1', ['references' => [['resource' => 'TrainingRecord', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status, 'completed_at' => $record->completed_at?->toDateString()])->with('success', 'Training marked complete.');
    }

    public function sendWelcome(WorkflowInput $input, User $user): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($user, $actor) {
            $record = User::query()->lockForUpdate()->findOrFail($user->id);
            DomainMail::to($record->email)->queue(new \App\Mail\WelcomeEmail($record));
            $record->notify(new \App\Notifications\FeedEntry('account.welcome', 'Welcome to OPES', 'Welcome to OPES Health Systems.', 'sparkles', null));
            DomainOutbox::append('event', 'identity.welcome_sent.v1', ['references' => [['resource' => 'User', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => 'queued'])->with('success', 'Welcome message queued.');
    }
}
