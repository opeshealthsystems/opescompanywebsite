<?php

namespace App\Contexts\Validation\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\CohortTestCase;
use App\Models\PractitionerProgram;
use App\Models\User;
use App\Models\ValidationTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AdminCohortCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
        return $user;
    }

    private function cohortValues(WorkflowInput $input, bool $update = false, ?Cohort $cohort = null): array
    {
        $required = $update ? 'sometimes|required|' : 'required|';
        $data = $input->validate([
            'practitioner_program_id' => $required.'integer|exists:practitioner_programs,id,program_type,validation',
            'name' => $required.'string|max:255', 'specialty' => $required.'string|max:255',
            'description' => 'sometimes|nullable|string|max:10000', 'start_date' => $required.'date', 'end_date' => $required.'date',
            'max_members' => 'sometimes|nullable|integer|min:1|max:50000',
            'status' => ['sometimes', Rule::in(['draft', 'active', 'completed'])],
            'id' => 'prohibited', 'members_count' => 'prohibited',
        ]);
        if (!$update) $data['status'] = $data['status'] ?? 'draft';
        $start = $data['start_date'] ?? $cohort?->start_date?->toDateString();
        $end = $data['end_date'] ?? $cohort?->end_date?->toDateString();
        if ($start && $end && $end < $start) throw ValidationException::withMessages(['end_date' => 'The end date must be on or after the start date.']);
        if ($cohort && isset($data['status']) && $cohort->status === 'completed' && $data['status'] !== 'completed') {
            throw ValidationException::withMessages(['status' => 'Completed cohorts cannot be reopened.']);
        }
        if ($cohort && isset($data['practitioner_program_id']) && (int) $data['practitioner_program_id'] !== (int) $cohort->practitioner_program_id && $cohort->members()->exists()) {
            throw ValidationException::withMessages(['practitioner_program_id' => 'A cohort with members cannot be moved to another programme.']);
        }
        $limit = $data['max_members'] ?? $cohort?->max_members;
        if ($limit !== null && $cohort && (int) $limit < $cohort->members()->count()) {
            throw ValidationException::withMessages(['max_members' => 'The member limit cannot be lower than the current cohort size.']);
        }
        $programId = $data['practitioner_program_id'] ?? $cohort?->practitioner_program_id;
        if ($programId && !PractitionerProgram::validation()->whereKey($programId)->exists()) {
            throw ValidationException::withMessages(['practitioner_program_id' => 'Select a validation programme.']);
        }
        return $data;
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->cohortValues($input);
        $cohort = DB::transaction(function () use ($data, $actor) {
            $cohort = Cohort::create($data);
            DomainOutbox::append('event', 'validation.cohort_created.v1', ['references' => [['resource' => 'Cohort', 'id' => $cohort->id]]], $actor->id);
            return $cohort;
        }, 3);
        return WorkflowResult::payload(['record' => $cohort, 'id' => $cohort->id], 201);
    }

    public function update(WorkflowInput $input, Cohort $cohort): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->cohortValues($input, true, $cohort);
        if (!$data) throw ValidationException::withMessages(['cohort' => 'Provide at least one cohort field to update.']);
        $updated = DB::transaction(function () use ($cohort, $data, $actor) {
            $record = Cohort::query()->lockForUpdate()->findOrFail($cohort->id);
            $record->update($data);
            DomainOutbox::append('event', 'validation.cohort_updated.v1', ['references' => [['resource' => 'Cohort', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $updated, 'id' => $updated->id]);
    }

    public function delete(WorkflowInput $input, Cohort $cohort): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($cohort, $actor) {
            $record = Cohort::query()->lockForUpdate()->findOrFail($cohort->id);
            if ($record->status !== 'draft' || $record->members()->exists() || $record->testCases()->exists() || $record->weeklyReviews()->exists()) {
                throw ValidationException::withMessages(['cohort' => 'Only empty draft cohorts can be deleted.']);
            }
            $id = $record->id; $record->delete();
            DomainOutbox::append('event', 'validation.cohort_deleted.v1', ['references' => [['resource' => 'Cohort', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }

    public function updateMember(WorkflowInput $input, Cohort $cohort, CohortMember $member): WorkflowResult
    {
        $actor = $this->admin($input);
        abort_unless((int) $member->cohort_id === (int) $cohort->id, 404);
        $data = $input->validate(['status' => ['required', Rule::in(['active', 'suspended', 'completed', 'removed'])], 'id' => 'prohibited', 'user_id' => 'prohibited', 'cohort_id' => 'prohibited']);
        $updated = DB::transaction(function () use ($cohort, $member, $data, $actor) {
            $record = CohortMember::query()->lockForUpdate()->where('cohort_id', $cohort->id)->findOrFail($member->id);
            $record->update($data);
            DomainOutbox::append('event', 'validation.cohort_member_updated.v1', ['references' => [['resource' => 'CohortMember', 'id' => $record->id], ['resource' => 'Cohort', 'id' => $cohort->id]], 'changed_fields' => ['status']], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['member' => ['id' => $updated->id, 'cohort_id' => $updated->cohort_id, 'user_id' => $updated->user_id, 'status' => $updated->status, 'placed_at' => $updated->placed_at]]);
    }

    public function attachTestCase(WorkflowInput $input, Cohort $cohort): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['validation_test_case_id' => 'required|integer|exists:validation_test_cases,id', 'due_date' => 'sometimes|nullable|date', 'id' => 'prohibited']);
        $pivot = DB::transaction(function () use ($cohort, $data, $actor) {
            $record = Cohort::query()->lockForUpdate()->findOrFail($cohort->id);
            $testCase = ValidationTestCase::query()->findOrFail($data['validation_test_case_id']);
            $existing = CohortTestCase::query()->where('cohort_id', $record->id)->where('validation_test_case_id', $testCase->id)->lockForUpdate()->first();
            if ($existing) {
                if (array_key_exists('due_date', $data)) $existing->update(['due_date' => $data['due_date']]);
                $pivot = $existing->refresh();
            } else {
                $pivot = CohortTestCase::create(['cohort_id' => $record->id, 'validation_test_case_id' => $testCase->id, 'due_date' => $data['due_date'] ?? null]);
            }
            DomainOutbox::append('event', 'validation.cohort_test_case_attached.v1', ['references' => [['resource' => 'Cohort', 'id' => $record->id], ['resource' => 'ValidationTestCase', 'id' => $testCase->id]], 'due_date' => $pivot->due_date?->toDateString()], $actor->id);
            return $pivot;
        }, 3);
        return WorkflowResult::payload(['id' => $pivot->id, 'cohort_id' => $pivot->cohort_id, 'validation_test_case_id' => $pivot->validation_test_case_id, 'due_date' => $pivot->due_date]);
    }

    public function detachTestCase(WorkflowInput $input, Cohort $cohort, ValidationTestCase $testCase): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($cohort, $testCase, $actor) {
            $record = Cohort::query()->lockForUpdate()->findOrFail($cohort->id);
            $pivot = CohortTestCase::query()->where('cohort_id', $record->id)->where('validation_test_case_id', $testCase->id)->lockForUpdate()->firstOrFail();
            $workflowId = $testCase->validation_workflow_id;
            $hasSessions = $record->members()->whereHas('dailyTestSessions', fn ($query) => $query->where('validation_workflow_id', $workflowId))->exists();
            if ($hasSessions) throw ValidationException::withMessages(['test_case' => 'A workflow with recorded sessions cannot be detached from this cohort.']);
            $id = $pivot->id; $pivot->delete();
            DomainOutbox::append('event', 'validation.cohort_test_case_detached.v1', ['references' => [['resource' => 'Cohort', 'id' => $record->id], ['resource' => 'ValidationTestCase', 'id' => $testCase->id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['detached_id' => $id]);
    }
}
