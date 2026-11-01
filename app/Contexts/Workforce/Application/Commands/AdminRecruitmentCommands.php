<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Shared recruitment lifecycle commands for API clients and the admin UI. */
final class AdminRecruitmentCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function invalid(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    private function applicationTransition(WorkflowInput $input, JobApplication $application, string $transition): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($application, $actor, $transition, $input) {
            $record = JobApplication::query()->lockForUpdate()->findOrFail($application->id);
            $body = [];
            if ($transition === 'interview') $body = $input->validate(['interview_date' => 'required|date']);
            $target = match ($transition) {
                'shortlist' => 'shortlisted', 'interview' => 'interviewed', 'offer' => 'offered', 'hire' => 'hired', 'reject' => 'rejected',
            };
            $allowed = match ($transition) {
                'shortlist' => ['received', 'reviewing'], 'interview' => ['shortlisted'], 'offer' => ['interviewed'], 'hire' => ['offered'],
                'reject' => ['received', 'reviewing', 'shortlisted', 'interviewed', 'offered'],
            };
            if ($transition === 'interview' && $record->status === $target && $record->interview_date?->toDateString() === $body['interview_date']) return $record;
            if ($record->status === $target) return $record;
            if (!in_array($record->status, $allowed, true)) $this->invalid('status', 'This application cannot make the requested transition from its current status.');
            $changes = ['status' => $target];
            if ($transition === 'interview') $changes['interview_date'] = $body['interview_date'];
            $record->update($changes);
            $event = match ($transition) {
                'shortlist' => 'workforce.recruitment_application_shortlisted.v1', 'interview' => 'workforce.recruitment_interview_scheduled.v1',
                'offer' => 'workforce.recruitment_offer_made.v1', 'hire' => 'workforce.recruitment_application_hired.v1',
                'reject' => 'workforce.recruitment_application_rejected.v1',
            };
            DomainOutbox::append('event', $event, ['references' => [['resource' => 'JobApplication', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status, 'interview_date' => $record->interview_date?->toDateString()])->with('success', 'Recruitment application updated.');
    }

    public function shortlist(WorkflowInput $input, JobApplication $application): WorkflowResult { return $this->applicationTransition($input, $application, 'shortlist'); }
    public function scheduleInterview(WorkflowInput $input, JobApplication $application): WorkflowResult { return $this->applicationTransition($input, $application, 'interview'); }
    public function makeOffer(WorkflowInput $input, JobApplication $application): WorkflowResult { return $this->applicationTransition($input, $application, 'offer'); }
    public function hire(WorkflowInput $input, JobApplication $application): WorkflowResult { return $this->applicationTransition($input, $application, 'hire'); }
    public function reject(WorkflowInput $input, JobApplication $application): WorkflowResult { return $this->applicationTransition($input, $application, 'reject'); }

    public function closeOpening(WorkflowInput $input, JobOpening $opening): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($opening, $actor) {
            $record = JobOpening::query()->lockForUpdate()->findOrFail($opening->id);
            if ($record->status === 'closed') return $record;
            if (!in_array($record->status, ['open', 'paused'], true)) $this->invalid('status', 'Only open or paused job openings can be closed.');
            $record->update(['status' => 'closed']);
            DomainOutbox::append('event', 'workforce.recruitment_opening_closed.v1', ['references' => [['resource' => 'JobOpening', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Job opening closed.');
    }
}
