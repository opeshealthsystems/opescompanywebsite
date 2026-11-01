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

    public function createRisk(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'title' => 'required|string|max:250',
            'description' => 'nullable|string|max:5000',
            'category' => 'required|in:'.implode(',', array_keys(Risk::categoryOptions())),
            'owner_id' => 'required|integer|exists:users,id',
            'likelihood' => 'required|in:'.implode(',', array_keys(Risk::likelihoodOptions())),
            'impact' => 'required|in:'.implode(',', array_keys(Risk::impactOptions())),
            'review_date' => 'nullable|date',
            'mitigation_plan' => 'nullable|string|max:5000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'risk_score' => 'prohibited',
        ]);

        $record = DB::transaction(function () use ($data, $actor) {
            $year = now()->year;
            $sequenceName = 'risk:'.$year;
            DB::table('business_sequences')->insertOrIgnore(['name' => $sequenceName, 'last_value' => 0]);
            $sequence = DB::table('business_sequences')->where('name', $sequenceName)->lockForUpdate()->first();
            $previous = (int) $sequence->last_value;
            if ($previous === 0) {
                $prefix = 'RISK-'.$year.'-';
                $previous = Risk::query()->where('reference', 'like', $prefix.'%')->pluck('reference')
                    ->map(fn ($reference) => ctype_digit(substr($reference, strlen($prefix))) ? (int) substr($reference, strlen($prefix)) : 0)->max() ?? 0;
            }
            $next = $previous + 1;
            DB::table('business_sequences')->where('name', $sequenceName)->update(['last_value' => $next]);
            $record = Risk::create($data + [
                'reference' => 'RISK-'.$year.'-'.str_pad((string) $next, 3, '0', STR_PAD_LEFT),
                'risk_score' => Risk::computeScore($data['likelihood'], $data['impact']),
                'status' => 'open',
            ]);
            DomainOutbox::append('event', 'risk.created.v1', [
                'references' => [['resource' => 'Risk', 'id' => $record->id]],
                'changed_fields' => array_keys($data),
            ], $actor->id);
            return $record;
        }, 3);

        return WorkflowResult::payload(['id' => $record->id, 'reference' => $record->reference, 'status' => $record->status, 'risk_score' => $record->risk_score], 201)->with('success', 'Risk recorded.');
    }

    public function updateRisk(WorkflowInput $input, Risk $risk): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'title' => 'sometimes|required|string|max:250',
            'description' => 'sometimes|nullable|string|max:5000',
            'category' => 'sometimes|required|in:'.implode(',', array_keys(Risk::categoryOptions())),
            'owner_id' => 'sometimes|required|integer|exists:users,id',
            'likelihood' => 'sometimes|required|in:'.implode(',', array_keys(Risk::likelihoodOptions())),
            'impact' => 'sometimes|required|in:'.implode(',', array_keys(Risk::impactOptions())),
            'review_date' => 'sometimes|nullable|date',
            'mitigation_plan' => 'sometimes|nullable|string|max:5000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'risk_score' => 'prohibited',
        ]);
        if ($data === []) throw ValidationException::withMessages(['risk' => 'Provide at least one risk field to update.']);

        $record = DB::transaction(function () use ($risk, $data, $actor) {
            $record = Risk::query()->lockForUpdate()->findOrFail($risk->id);
            if ($record->status !== 'open') throw ValidationException::withMessages(['status' => 'Only open risks can be edited.']);
            $record->fill($data);
            $record->risk_score = Risk::computeScore($record->likelihood, $record->impact);
            $record->save();
            DomainOutbox::append('event', 'risk.updated.v1', [
                'references' => [['resource' => 'Risk', 'id' => $record->id]],
                'changed_fields' => array_keys($data),
            ], $actor->id);
            return $record;
        }, 3);

        return WorkflowResult::payload(['id' => $record->id, 'reference' => $record->reference, 'status' => $record->status, 'risk_score' => $record->risk_score])->with('success', 'Risk updated.');
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
