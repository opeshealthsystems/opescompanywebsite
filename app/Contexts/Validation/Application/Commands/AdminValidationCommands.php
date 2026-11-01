<?php

namespace App\Contexts\Validation\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\FinalEvaluation;
use App\Models\CohortMember;
use App\Models\ValidationCertificate;
use App\Models\AdvisoryCouncilMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Shared reviewer and developer state transitions for the API and Filament. */
final class AdminValidationCommands extends WorkflowHandler
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

    private function result(Model $record, string $message): WorkflowResult
    {
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', $message);
    }

    public function clinicalReview(WorkflowInput $input, IssueReport $issue): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['decision' => 'required|in:approved_for_product_review,rejected,needs_more_information', 'notes' => 'nullable|string|max:5000']);
        $record = DB::transaction(function () use ($issue, $actor, $data) {
            $record = IssueReport::query()->lockForUpdate()->findOrFail($issue->id);
            if ($record->status !== 'submitted' || $record->clinicalReview()->exists()) $this->invalid('status', 'Clinical review is only available once for a submitted issue.');
            $record->recordClinicalReview($actor->id, $data['decision'], $data['notes'] ?? null);
            DomainOutbox::append('event', 'validation.issue_clinical_reviewed.v1', ['references' => [['resource' => 'IssueReport', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return $this->result($record, 'Clinical review recorded.');
    }

    public function sendToProductReview(WorkflowInput $input, IssueReport $issue): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($issue, $actor) {
            $record = IssueReport::query()->lockForUpdate()->findOrFail($issue->id);
            if ($record->status === 'product_review') return $record;
            if ($record->status !== 'clinical_review' || !$record->clinicalApproved()) $this->invalid('status', 'Only clinically approved issues can enter product review.');
            $record->sendToProductReview();
            DomainOutbox::append('event', 'validation.issue_sent_to_product_review.v1', ['references' => [['resource' => 'IssueReport', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return $this->result($record, 'Issue sent to product review.');
    }

    public function productDecision(WorkflowInput $input, IssueReport $issue): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['decision' => 'required|in:accepted,rejected,duplicate,sent_to_development', 'notes' => 'nullable|string|max:5000']);
        $record = DB::transaction(function () use ($issue, $actor, $data) {
            $record = IssueReport::query()->lockForUpdate()->findOrFail($issue->id);
            if ($record->status !== 'product_review' || $record->productReview()->exists()) $this->invalid('status', 'Product decisions are only available once while an issue is in product review.');
            $record->recordProductReview($actor->id, $data['decision'], $data['notes'] ?? null);
            DomainOutbox::append('event', 'validation.issue_product_decided.v1', ['references' => [['resource' => 'IssueReport', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return $this->result($record, 'Product decision recorded.');
    }

    public function closeIssue(WorkflowInput $input, IssueReport $issue): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($issue, $actor) {
            $record = IssueReport::query()->lockForUpdate()->findOrFail($issue->id);
            if ($record->status === 'closed') return $record;
            if (!in_array($record->status, ['accepted', 'rejected', 'duplicate', 'retest_passed'], true)) $this->invalid('status', 'Only resolved issues can be closed.');
            $record->closeIssue();
            DomainOutbox::append('event', 'validation.issue_closed.v1', ['references' => [['resource' => 'IssueReport', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return $this->result($record, 'Issue closed.');
    }

    public function assignDeveloperTask(WorkflowInput $input, DeveloperTask $task): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['assigned_to' => 'required|integer|exists:users,id']);
        $record = DB::transaction(function () use ($task, $actor, $data) {
            $record = DeveloperTask::query()->lockForUpdate()->findOrFail($task->id);
            $assignee = User::query()->where('is_active', true)->find($data['assigned_to']);
            if (!$assignee) $this->invalid('assigned_to', 'The assignee must have an active account.');
            if ($record->assigned_to !== $assignee->id) {
                $record->update(['assigned_to' => $assignee->id]);
                DomainOutbox::append('event', 'validation.developer_task_assigned.v1', ['references' => [['resource' => 'DeveloperTask', 'id' => $record->id]]], $actor->id);
            }
            return $record;
        }, 3);
        return $this->result($record, 'Task assigned.');
    }

    public function startDeveloperTask(WorkflowInput $input, DeveloperTask $task): WorkflowResult
    {
        return $this->changeTask($input, $task, 'start', null);
    }

    public function fixDeveloperTask(WorkflowInput $input, DeveloperTask $task): WorkflowResult
    {
        $data = $input->validate(['resolution_notes' => 'nullable|string|max:5000']);
        return $this->changeTask($input, $task, 'fixed', $data['resolution_notes'] ?? null);
    }

    public function reopenDeveloperTask(WorkflowInput $input, DeveloperTask $task): WorkflowResult
    {
        return $this->changeTask($input, $task, 'reopen', null);
    }

    public function wontFixDeveloperTask(WorkflowInput $input, DeveloperTask $task): WorkflowResult
    {
        $data = $input->validate(['notes' => 'nullable|string|max:5000']);
        return $this->changeTask($input, $task, 'wont_fix', $data['notes'] ?? null);
    }

    public function issueCertificate(WorkflowInput $input, FinalEvaluation $evaluation): WorkflowResult
    {
        $actor = $this->admin($input);
        $certificate = DB::transaction(function () use ($evaluation, $actor) {
            $record = FinalEvaluation::query()->lockForUpdate()->findOrFail($evaluation->id);
            $member = CohortMember::query()->lockForUpdate()->findOrFail($record->cohort_member_id);
            $existing = ValidationCertificate::query()->where('cohort_member_id', $member->id)->first();
            if ($existing) return $existing;

            $issued = ValidationCertificate::issueFor($record, $actor->id);
            DomainOutbox::append('event', 'validation.certificate_issued.v1', [
                'references' => [
                    ['resource' => 'ValidationCertificate', 'id' => $issued->id],
                    ['resource' => 'FinalEvaluation', 'id' => $record->id],
                ],
            ], $actor->id);
            return $issued;
        }, 3);

        return WorkflowResult::payload([
            'id' => $certificate->id,
            'certificate_number' => $certificate->certificate_number,
            'tier' => $certificate->tier,
            'score' => $certificate->score,
            'issued_at' => $certificate->issued_at?->toISOString(),
        ])->with('success', 'Certificate issued.');
    }

    public function inviteToCouncil(WorkflowInput $input, ValidationCertificate $certificate): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'title' => 'required|string|max:255',
            'term_start' => 'required|date',
            'term_end' => 'nullable|date|after_or_equal:term_start',
        ]);
        $member = DB::transaction(function () use ($certificate, $actor, $data) {
            $record = ValidationCertificate::query()->with('cohortMember.user')->lockForUpdate()->findOrFail($certificate->id);
            if ($record->tier !== 'distinction') $this->invalid('certificate', 'Only distinction certificate holders are eligible for council invitation.');
            $user = User::query()->lockForUpdate()->findOrFail($record->cohortMember->user_id);
            if (AdvisoryCouncilMember::query()->where('user_id', $user->id)->exists()) $this->invalid('user_id', 'This practitioner is already a council member.');
            $member = AdvisoryCouncilMember::create([
                'user_id' => $user->id,
                'validation_certificate_id' => $record->id,
                'title' => $data['title'],
                'term_start' => $data['term_start'],
                'term_end' => $data['term_end'] ?? null,
                'status' => 'active',
                'invited_by' => $actor->id,
                'invited_at' => now(),
            ]);
            $notification = new \App\Notifications\CouncilInvitation($member);
            DomainOutbox::append('notification', get_class($notification), [
                'recipient' => \App\Contexts\Shared\Infrastructure\MessageValueCodec::encode($user),
                'message' => \App\Contexts\Shared\Infrastructure\MessageValueCodec::message($notification),
            ], $actor->id);
            DomainOutbox::append('event', 'validation.council_member_invited.v1', [
                'references' => [['resource' => 'AdvisoryCouncilMember', 'id' => $member->id], ['resource' => 'ValidationCertificate', 'id' => $record->id]],
            ], $actor->id);
            return $member;
        }, 3);
        return WorkflowResult::payload(['id' => $member->id, 'status' => $member->status, 'title' => $member->title])->with('success', 'Practitioner invited to the Advisory Council.');
    }

    public function createEvaluation(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'cohort_member_id' => 'required|integer|exists:cohort_members,id',
            'assessment' => 'required|string|max:20000',
            'rating' => 'required|in:'.implode(',', array_keys(FinalEvaluation::ratingOptions())),
            'recommendation' => 'nullable|string|max:10000',
        ]);
        $evaluation = DB::transaction(function () use ($data, $actor) {
            $member = CohortMember::query()->lockForUpdate()->findOrFail($data['cohort_member_id']);
            if (FinalEvaluation::query()->where('cohort_member_id', $member->id)->exists()) $this->invalid('cohort_member_id', 'This cohort member already has a final evaluation.');
            $record = FinalEvaluation::create(array_merge([
                'cohort_member_id' => $member->id,
                'assessment' => $data['assessment'],
                'rating' => $data['rating'],
                'recommendation' => $data['recommendation'] ?? null,
            ], FinalEvaluation::snapshotData($member, $actor->id)));
            DomainOutbox::append('event', 'validation.final_evaluation_recorded.v1', [
                'references' => [['resource' => 'FinalEvaluation', 'id' => $record->id], ['resource' => 'CohortMember', 'id' => $member->id]],
            ], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $evaluation->id, 'cohort_member_id' => $evaluation->cohort_member_id, 'rating' => $evaluation->rating], 201)->with('success', 'Final evaluation recorded.');
    }

    public function updateEvaluation(WorkflowInput $input, FinalEvaluation $evaluation): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'assessment' => 'required|string|max:20000',
            'rating' => 'required|in:'.implode(',', array_keys(FinalEvaluation::ratingOptions())),
            'recommendation' => 'nullable|string|max:10000',
        ]);
        $record = DB::transaction(function () use ($evaluation, $actor, $data) {
            $record = FinalEvaluation::query()->lockForUpdate()->findOrFail($evaluation->id);
            if (ValidationCertificate::query()->where('cohort_member_id', $record->cohort_member_id)->exists()) {
                $this->invalid('evaluation', 'A certified evaluation is frozen and cannot be edited.');
            }
            $record->update($data);
            DomainOutbox::append('event', 'validation.final_evaluation_updated.v1', [
                'references' => [['resource' => 'FinalEvaluation', 'id' => $record->id]],
            ], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'cohort_member_id' => $record->cohort_member_id, 'rating' => $record->rating])->with('success', 'Final evaluation updated.');
    }

    private function changeTask(WorkflowInput $input, DeveloperTask $task, string $transition, ?string $notes): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($task, $actor, $transition, $notes) {
            $record = DeveloperTask::query()->with('issueReport')->lockForUpdate()->findOrFail($task->id);
            $event = null;
            if ($transition === 'start' && in_array($record->status, ['open', 'reopened'], true)) {
                $record->markInProgress(); $event = 'validation.developer_task_started.v1';
            } elseif ($transition === 'fixed' && in_array($record->status, ['in_progress', 'reopened'], true)) {
                $record->markFixed($notes); $event = 'validation.developer_task_fixed.v1';
            } elseif ($transition === 'reopen' && $record->status === 'fixed' && !in_array($record->issueReport->status, ['closed', 'retest_passed'], true)) {
                $record->reopen(); $event = 'validation.developer_task_reopened.v1';
            } elseif ($transition === 'wont_fix' && in_array($record->status, ['open', 'in_progress', 'reopened', 'fixed'], true)) {
                $record->markWontFix($notes); $event = 'validation.developer_task_wont_fix.v1';
            } elseif (($transition === 'start' && $record->status === 'in_progress') || ($transition === 'fixed' && $record->status === 'fixed') || ($transition === 'reopen' && $record->status === 'reopened') || ($transition === 'wont_fix' && $record->status === 'wont_fix')) {
                return $record;
            } else {
                $this->invalid('status', 'This developer task cannot make the requested transition from its current state.');
            }
            DomainOutbox::append('event', $event, ['references' => [['resource' => 'DeveloperTask', 'id' => $record->id], ['resource' => 'IssueReport', 'id' => $record->issue_report_id]]], $actor->id);
            return $record;
        }, 3);
        return $this->result($record, 'Developer task updated.');
    }
}
