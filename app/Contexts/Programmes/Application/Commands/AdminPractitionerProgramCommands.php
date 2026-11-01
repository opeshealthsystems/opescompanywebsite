<?php

namespace App\Contexts\Programmes\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\PractitionerProgram;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminPractitionerProgramCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function values(WorkflowInput $input, bool $update = false): array
    {
        $optional = $update ? 'sometimes|' : '';
        return $input->validate([
            'title' => $optional.'required|string|max:200', 'title_fr' => $optional.'nullable|string|max:200',
            'description' => $optional.'nullable|string|max:20000', 'description_fr' => $optional.'nullable|string|max:20000',
            'type' => $optional.'required|in:volunteer,paid', 'program_type' => $optional.'required|in:general,validation',
            'compensation' => $optional.'nullable|string|max:100', 'max_participants' => $optional.'nullable|integer|min:1|max:65535',
            'product_slug' => $optional.'nullable|string|max:100', 'product_name' => $optional.'nullable|string|max:150',
            'starts_at' => $optional.'nullable|date', 'ends_at' => $optional.'nullable|date',
            'status' => $optional.'required|in:draft,open,closed,archived', 'id' => 'prohibited',
        ]);
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input);
        $this->assertDateRange($data);
        $record = DB::transaction(function () use ($data, $actor) {
            $record = PractitionerProgram::create($data);
            DomainOutbox::append('event', 'practitioners.program_created.v1', ['references' => [['resource' => 'PractitionerProgram', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function update(WorkflowInput $input, PractitionerProgram $program): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input, true);
        if ($data === []) throw ValidationException::withMessages(['program' => 'Provide at least one field to update.']);
        $record = DB::transaction(function () use ($program, $data, $actor) {
            $record = PractitionerProgram::query()->lockForUpdate()->findOrFail($program->id);
            $this->assertDateRange($data, $record);
            if (isset($data['status']) && $data['status'] !== $record->status) {
                $allowed = ['draft' => ['open', 'closed'], 'open' => ['closed'], 'closed' => ['open', 'archived'], 'archived' => []];
                if (!in_array($data['status'], $allowed[$record->status] ?? [], true)) throw ValidationException::withMessages(['status' => 'This programme status transition is not allowed.']);
            }
            if (isset($data['type']) && $data['type'] !== $record->type && $record->applications()->exists()) throw ValidationException::withMessages(['type' => 'Programme type cannot change after applications are received.']);
            if (isset($data['program_type']) && $data['program_type'] !== $record->program_type && $record->cohorts()->exists()) throw ValidationException::withMessages(['program_type' => 'Programme category cannot change while cohorts reference it.']);
            if (isset($data['max_participants']) && $data['max_participants'] !== null && $record->applications()->where('status', 'approved')->count() > (int) $data['max_participants']) throw ValidationException::withMessages(['max_participants' => 'Participant limit cannot be lower than existing approved applications.']);
            $record->update($data);
            DomainOutbox::append('event', 'practitioners.program_updated.v1', ['references' => [['resource' => 'PractitionerProgram', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'status' => $record->status]);
    }

    private function assertDateRange(array $data, ?PractitionerProgram $program = null): void
    {
        $start = $data['starts_at'] ?? $program?->starts_at?->format('Y-m-d');
        $end = $data['ends_at'] ?? $program?->ends_at?->format('Y-m-d');
        if ($start !== null && $end !== null && \Illuminate\Support\Carbon::parse($end)->lt(\Illuminate\Support\Carbon::parse($start))) {
            throw ValidationException::withMessages(['ends_at' => 'The programme end date must be on or after its start date.']);
        }
    }
}
