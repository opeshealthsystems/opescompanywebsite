<?php

namespace App\Contexts\Programmes\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Models\PractitionerProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminPractitionerRecordCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function changes(array $data, string $field): void
    {
        if ($data === []) throw ValidationException::withMessages([$field => 'Provide at least one field to update.']);
    }

    public function updateProfile(WorkflowInput $input, PractitionerProfile $profile): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'profession' => 'sometimes|required|string|in:'.implode(',', array_keys(PractitionerProfile::professionOptions())),
            'specialty' => 'sometimes|nullable|string|max:120', 'registration_number' => 'sometimes|nullable|string|max:60',
            'years_of_experience' => 'sometimes|nullable|integer|min:0|max:60', 'workplace_name' => 'sometimes|nullable|string|max:150',
            'workplace_city' => 'sometimes|nullable|string|max:80', 'workplace_country' => 'sometimes|nullable|string|max:80',
            'bio' => 'sometimes|nullable|string|max:2000', 'opes_testimonial' => 'sometimes|nullable|string|max:1000',
            'user_id' => 'prohibited', 'is_verified' => 'prohibited', 'payout_number' => 'prohibited', 'id' => 'prohibited',
        ]);
        $this->changes($data, 'profile');
        $record = DB::transaction(function () use ($profile, $data, $actor) {
            $record = PractitionerProfile::query()->lockForUpdate()->findOrFail($profile->id);
            $record->update($data);
            DomainOutbox::append('event', 'practitioners.profile_updated.v1', ['references' => [['resource' => 'PractitionerProfile', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function updateFinding(WorkflowInput $input, PractitionerFinding $finding): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'overall_rating' => 'sometimes|nullable|integer|min:1|max:5', 'wait_time_rating' => 'sometimes|nullable|integer|min:1|max:5',
            'data_integrity_rating' => 'sometimes|nullable|integer|min:1|max:5', 'usability_rating' => 'sometimes|nullable|integer|min:1|max:5',
            'findings_text' => 'sometimes|nullable|string|max:5000',
            'video_url' => ['sometimes', 'nullable', 'url', 'max:500', 'regex:~^https?://(?:www\.|player\.)?(?:youtube\.com/|youtu\.be/|vimeo\.com/)~i'],
            'application_id' => 'prohibited', 'practitioner_id' => 'prohibited', 'is_published' => 'prohibited', 'screenshot_path' => 'prohibited', 'id' => 'prohibited',
        ]);
        $this->changes($data, 'finding');
        $record = DB::transaction(function () use ($finding, $data, $actor) {
            $record = PractitionerFinding::query()->lockForUpdate()->findOrFail($finding->id);
            $record->update($data);
            DomainOutbox::append('event', 'practitioners.finding_updated.v1', ['references' => [['resource' => 'PractitionerFinding', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function updateApplicationNotes(WorkflowInput $input, PractitionerApplication $application): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['admin_notes' => 'sometimes|nullable|string|max:10000', 'status' => 'prohibited', 'program_id' => 'prohibited', 'practitioner_id' => 'prohibited', 'id' => 'prohibited']);
        $this->changes($data, 'application');
        $record = DB::transaction(function () use ($application, $data, $actor) {
            $record = PractitionerApplication::query()->lockForUpdate()->findOrFail($application->id);
            $record->update($data);
            DomainOutbox::append('event', 'practitioners.application_notes_updated.v1', ['references' => [['resource' => 'PractitionerApplication', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }
}
