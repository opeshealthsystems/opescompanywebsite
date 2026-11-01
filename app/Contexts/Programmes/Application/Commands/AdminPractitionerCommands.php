<?php

namespace App\Contexts\Programmes\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainMail;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\PractitionerFinding;
use App\Models\PractitionerApplication;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\PractitionerProfile;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Shared admin workflows for practitioner verification, publication and responses. */
final class AdminPractitionerCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    public function verifyProfile(WorkflowInput $input, PractitionerProfile $profile): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($profile, $actor) {
            $record = PractitionerProfile::query()->lockForUpdate()->findOrFail($profile->id);
            if (!$record->is_verified) {
                $record->update(['is_verified' => true]);
                DomainOutbox::append('event', 'practitioners.profile_verified.v1', ['references' => [['resource' => 'PractitionerProfile', 'id' => $record->id]]], $actor->id);
            }
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'is_verified' => $record->is_verified])->with('success', 'Practitioner verified.');
    }

    public function publishFinding(WorkflowInput $input, PractitionerFinding $finding): WorkflowResult { return $this->setFindingPublished($input, $finding, true); }
    public function unpublishFinding(WorkflowInput $input, PractitionerFinding $finding): WorkflowResult { return $this->setFindingPublished($input, $finding, false); }

    private function setFindingPublished(WorkflowInput $input, PractitionerFinding $finding, bool $published): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($finding, $actor, $published) {
            $record = PractitionerFinding::query()->lockForUpdate()->findOrFail($finding->id);
            if ((bool) $record->is_published !== $published) {
                $record->update(['is_published' => $published]);
                $event = $published ? 'practitioners.finding_published.v1' : 'practitioners.finding_unpublished.v1';
                DomainOutbox::append('event', $event, ['references' => [['resource' => 'PractitionerFinding', 'id' => $record->id]]], $actor->id);
            }
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'is_published' => $record->is_published])->with('success', $published ? 'Finding published.' : 'Finding unpublished.');
    }

    public function respondToSuggestion(WorkflowInput $input, Suggestion $suggestion): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['status' => 'required|in:under_review,accepted,declined,implemented', 'admin_response' => 'required|string|max:10000']);
        $record = DB::transaction(function () use ($suggestion, $actor, $data) {
            $record = Suggestion::query()->with('user')->lockForUpdate()->findOrFail($suggestion->id);
            if (!in_array($record->status, ['pending', 'under_review'], true)) $this->invalid('status', 'Only pending or under-review suggestions can receive a response.');
            $record->update(['status' => $data['status'], 'admin_response' => $data['admin_response'], 'responded_by' => $actor->id, 'responded_at' => now()]);
            if (class_exists(\App\Mail\SuggestionResponded::class) && $record->user?->email) DomainMail::to($record->user->email)->queue(new \App\Mail\SuggestionResponded($record));
            $record->user?->notify(new \App\Notifications\FeedEntry('practitioner.suggestion_responded', 'Response to your suggestion', 'Your suggestion received a response.', 'light-bulb', route('practitioner.suggestions', ['locale' => 'en'])));
            DomainOutbox::append('event', 'practitioners.suggestion_responded.v1', ['references' => [['resource' => 'Suggestion', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status, 'responded_at' => $record->responded_at?->toISOString()])->with('success', 'Suggestion response saved.');
    }

    public function approveApplication(WorkflowInput $input, PractitionerApplication $application): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($application, $actor) {
            $record = PractitionerApplication::query()->with(['program', 'practitioner'])->lockForUpdate()->findOrFail($application->id);
            if ($record->status === 'approved') return $record;
            if ($record->status !== 'pending') $this->invalid('status', 'Only pending programme applications can be approved.');
            $record->markApproved($actor->id);
            if (class_exists(\App\Mail\PractitionerApplicationApproved::class) && $record->practitioner?->email) {
                DomainMail::to($record->practitioner->email)->queue(new \App\Mail\PractitionerApplicationApproved($record));
            }
            $record->practitioner?->notify(new \App\Notifications\FeedEntry(
                'practitioner.application_approved', 'Application approved', 'Your programme application was approved.',
                'check-circle', route('practitioner.applications', ['locale' => 'en']),
            ));
            DomainOutbox::append('event', 'practitioners.application_approved.v1', ['references' => [['resource' => 'PractitionerApplication', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status, 'payout_status' => $record->payout_status])->with('success', 'Application approved.');
    }

    public function rejectApplication(WorkflowInput $input, PractitionerApplication $application): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['admin_notes' => 'nullable|string|max:10000']);
        $record = DB::transaction(function () use ($application, $actor, $data) {
            $record = PractitionerApplication::query()->with('practitioner')->lockForUpdate()->findOrFail($application->id);
            if ($record->status !== 'pending') $this->invalid('status', 'Only pending programme applications can be rejected.');
            $record->update(['status' => 'rejected', 'reviewed_by' => $actor->id, 'reviewed_at' => now(), 'admin_notes' => $data['admin_notes'] ?? null]);
            if (class_exists(\App\Mail\PractitionerApplicationRejected::class) && $record->practitioner?->email) {
                DomainMail::to($record->practitioner->email)->queue(new \App\Mail\PractitionerApplicationRejected($record));
            }
            $record->practitioner?->notify(new \App\Notifications\FeedEntry(
                'practitioner.application_rejected', 'Application update', 'There is an update on your programme application.',
                'clipboard-document', route('practitioner.applications', ['locale' => 'en']),
            ));
            DomainOutbox::append('event', 'practitioners.application_rejected.v1', ['references' => [['resource' => 'PractitionerApplication', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Application rejected.');
    }

    public function placeApplicationInCohort(WorkflowInput $input, PractitionerApplication $application): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['cohort_id' => 'required|integer|exists:cohorts,id']);
        $member = DB::transaction(function () use ($application, $actor, $data) {
            $record = PractitionerApplication::query()->with(['program', 'practitioner'])->lockForUpdate()->findOrFail($application->id);
            if ($record->status !== 'approved' || $record->program?->program_type !== 'validation') {
                $this->invalid('status', 'Only approved validation programme applications can be placed in a cohort.');
            }
            $cohort = Cohort::query()->lockForUpdate()->findOrFail($data['cohort_id']);
            if ($cohort->status !== 'active' || (int) $cohort->practitioner_program_id !== (int) $record->program_id) {
                $this->invalid('cohort_id', 'Select an active cohort for this validation programme.');
            }
            $practitioner = \App\Models\User::query()->lockForUpdate()->findOrFail($record->practitioner_id);
            $existing = CohortMember::query()->where('user_id', $practitioner->id)->whereHas('cohort', fn ($query) => $query->where('practitioner_program_id', $record->program_id))->first();
            if ($existing) {
                if ((int) $existing->cohort_id !== (int) $cohort->id) $this->invalid('cohort_id', 'This practitioner already belongs to a cohort in the selected programme.');
                return $existing;
            }
            if ($cohort->max_members !== null && $cohort->members()->count() >= $cohort->max_members) {
                $this->invalid('cohort_id', 'The selected cohort has reached its member limit.');
            }
            $member = CohortMember::create(['cohort_id' => $cohort->id, 'user_id' => $practitioner->id, 'status' => 'active', 'placed_at' => now()]);
            $notification = new \App\Notifications\PlacedInCohort($cohort);
            DomainOutbox::append('notification', get_class($notification), [
                'recipient' => \App\Contexts\Shared\Infrastructure\MessageValueCodec::encode($practitioner),
                'message' => \App\Contexts\Shared\Infrastructure\MessageValueCodec::message($notification),
            ], $actor->id);
            DomainOutbox::append('event', 'practitioners.application_placed_in_cohort.v1', [
                'references' => [['resource' => 'PractitionerApplication', 'id' => $record->id], ['resource' => 'CohortMember', 'id' => $member->id]],
            ], $actor->id);
            return $member;
        }, 3);
        return WorkflowResult::payload(['id' => $member->id, 'cohort_id' => $member->cohort_id, 'status' => $member->status])->with('success', 'Practitioner placed in cohort.');
    }

    private function invalid(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
