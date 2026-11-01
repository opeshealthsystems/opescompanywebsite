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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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

    private function openingValues(WorkflowInput $input, bool $update = false): array
    {
        $presence = $update ? 'sometimes|required|' : 'required|';
        $values = $input->validate([
            'title' => $presence.'string|max:200',
            'department_id' => 'sometimes|nullable|integer|exists:departments,id',
            'type' => ['sometimes', $update ? 'required' : 'required', Rule::in(array_keys(JobOpening::typeOptions()))],
            'location' => 'sometimes|nullable|string|max:150',
            'description' => 'sometimes|nullable|string|max:20000',
            'requirements' => 'sometimes|nullable|string|max:20000',
            'openings_count' => 'sometimes|integer|min:1|max:1000',
            'posted_at' => 'sometimes|nullable|date',
            'closes_at' => 'sometimes|nullable|date|after_or_equal:posted_at',
            'salary_range' => 'sometimes|nullable|string|max:100',
            'status' => 'prohibited', 'created_by' => 'prohibited', 'id' => 'prohibited',
        ]);
        return $values;
    }

    private function applicationValues(WorkflowInput $input, bool $update = false): array
    {
        $values = $input->validate([
            'job_opening_id' => ($update ? 'sometimes|' : 'required|').'integer|exists:job_openings,id',
            'applicant_name' => ($update ? 'sometimes|required|' : 'required|').'string|max:200',
            'email' => ($update ? 'sometimes|required|' : 'required|').'email:rfc|max:150',
            'phone' => 'sometimes|nullable|string|max:50',
            'applied_at' => 'sometimes|nullable|date',
            'interview_date' => 'sometimes|nullable|date',
            'notes' => 'sometimes|nullable|string|max:20000',
            'resume_path' => 'sometimes|nullable|string|max:300',
            'resume' => 'sometimes|file|mimes:pdf|max:10240',
            'status' => 'prohibited', 'id' => 'prohibited',
        ]);
        if ($input->hasFile('resume') && array_key_exists('resume_path', $values) && $values['resume_path'] !== null) {
            throw ValidationException::withMessages(['resume_path' => 'Send a resume file or an existing resume path, not both.']);
        }
        if ($input->hasFile('resume')) {
            $values['resume_path'] = $input->file('resume')->storePublicly('resumes', 'public');
        } elseif (array_key_exists('resume_path', $values) && $values['resume_path'] !== null) {
            $path = $values['resume_path'];
            $mime = str_starts_with($path, 'resumes/') && !str_contains($path, '..') && !str_contains($path, '\\')
                && Storage::disk('public')->exists($path)
                ? Storage::disk('public')->mimeType($path) : null;
            if (!is_string($mime) || $mime !== 'application/pdf') {
                throw ValidationException::withMessages(['resume_path' => 'Select a PDF resume stored in the resumes directory.']);
            }
        }
        return $values;
    }

    public function createOpening(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->openingValues($input);
        $record = DB::transaction(function () use ($data, $actor) {
            $record = JobOpening::create($data + ['status' => 'open', 'created_by' => $actor->id]);
            DomainOutbox::append('event', 'workforce.recruitment_opening_created.v1', ['references' => [['resource' => 'JobOpening', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function updateOpening(WorkflowInput $input, JobOpening $opening): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->openingValues($input, true);
        if (!$data) throw ValidationException::withMessages(['opening' => 'Provide at least one editable job-opening field.']);
        $record = DB::transaction(function () use ($opening, $data, $actor) {
            $record = JobOpening::query()->lockForUpdate()->findOrFail($opening->id);
            if ($record->status === 'closed' || $record->status === 'filled') throw ValidationException::withMessages(['opening' => 'Closed or filled job openings cannot be edited.']);
            $record->update($data);
            DomainOutbox::append('event', 'workforce.recruitment_opening_updated.v1', ['references' => [['resource' => 'JobOpening', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function deleteOpening(WorkflowInput $input, JobOpening $opening): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($opening, $actor) {
            $record = JobOpening::query()->lockForUpdate()->findOrFail($opening->id);
            if ($record->applications()->exists()) throw ValidationException::withMessages(['opening' => 'Job openings with applications must be retained. Close the opening instead.']);
            $id = $record->id;
            $record->delete();
            DomainOutbox::append('event', 'workforce.recruitment_opening_deleted.v1', ['references' => [['resource' => 'JobOpening', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }

    public function createApplication(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->applicationValues($input);
        $opening = JobOpening::query()->findOrFail($data['job_opening_id']);
        if ($opening->status !== 'open') throw ValidationException::withMessages(['job_opening_id' => 'Applications can only be recorded against an open job opening.']);
        $data['status'] = 'received';
        $record = DB::transaction(function () use ($data, $actor) {
            $record = JobApplication::create($data);
            DomainOutbox::append('event', 'workforce.recruitment_application_recorded.v1', ['references' => [['resource' => 'JobApplication', 'id' => $record->id], ['resource' => 'JobOpening', 'id' => $record->job_opening_id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function updateApplication(WorkflowInput $input, JobApplication $application): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->applicationValues($input, true);
        if (!$data) throw ValidationException::withMessages(['application' => 'Provide at least one editable application field.']);
        $record = DB::transaction(function () use ($application, $data, $actor) {
            $record = JobApplication::query()->lockForUpdate()->findOrFail($application->id);
            if (in_array($record->status, ['hired', 'rejected'], true)) throw ValidationException::withMessages(['application' => 'Hired or rejected applications cannot be edited.']);
            $record->update($data);
            DomainOutbox::append('event', 'workforce.recruitment_application_updated.v1', ['references' => [['resource' => 'JobApplication', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function deleteApplication(WorkflowInput $input, JobApplication $application): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($application, $actor) {
            $record = JobApplication::query()->lockForUpdate()->findOrFail($application->id);
            if ($record->status === 'hired') throw ValidationException::withMessages(['application' => 'Hired applications must be retained as employment records.']);
            $id = $record->id;
            $record->delete();
            DomainOutbox::append('event', 'workforce.recruitment_application_deleted.v1', ['references' => [['resource' => 'JobApplication', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
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
