<?php

namespace App\Contexts\Publishing\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Contract state transitions shared by the API and Filament. */
final class AdminContractCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'title' => 'required|string|max:250',
            'lead_id' => 'nullable|integer|exists:leads,id',
            'type' => 'required|in:'.implode(',', array_keys(Contract::typeOptions())),
            'value' => 'nullable|numeric|min:0',
            'currency' => 'required|in:XAF,USD,EUR',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'auto_renew' => 'required|boolean',
            'notes' => 'nullable|string|max:20000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'signed_at' => 'prohibited', 'created_by' => 'prohibited',
        ]);
        $record = DB::transaction(function () use ($data, $actor) {
            $year = now()->year;
            $sequenceName = 'contract:'.$year;
            DB::table('business_sequences')->insertOrIgnore(['name' => $sequenceName, 'last_value' => 0]);
            $sequence = DB::table('business_sequences')->where('name', $sequenceName)->lockForUpdate()->first();
            $previous = (int) $sequence->last_value;
            if ($previous === 0) {
                $prefix = 'CONT-'.$year.'-';
                $previous = Contract::query()->where('reference', 'like', $prefix.'%')->pluck('reference')
                    ->map(fn ($ref) => ctype_digit(substr($ref, strlen($prefix))) ? (int) substr($ref, strlen($prefix)) : 0)->max() ?? 0;
            }
            $next = $previous + 1;
            DB::table('business_sequences')->where('name', $sequenceName)->update(['last_value' => $next]);
            $record = Contract::create($data + ['reference' => 'CONT-'.$year.'-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT), 'status' => 'draft', 'created_by' => $actor->id]);
            DomainOutbox::append('event', 'publishing.contract_created.v1', ['references' => [['resource' => 'Contract', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'reference' => $record->reference], 201);
    }

    public function update(WorkflowInput $input, Contract $contract): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'title' => 'sometimes|required|string|max:250', 'lead_id' => 'sometimes|nullable|integer|exists:leads,id',
            'type' => 'sometimes|required|in:'.implode(',', array_keys(Contract::typeOptions())), 'value' => 'sometimes|nullable|numeric|min:0',
            'currency' => 'sometimes|required|in:XAF,USD,EUR', 'start_date' => 'sometimes|nullable|date',
            'end_date' => 'sometimes|nullable|date', 'auto_renew' => 'sometimes|required|boolean', 'notes' => 'sometimes|nullable|string|max:20000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'signed_at' => 'prohibited', 'created_by' => 'prohibited',
        ]);
        if ($data === []) throw ValidationException::withMessages(['contract' => 'Provide at least one contract field to update.']);
        $record = DB::transaction(function () use ($contract, $data, $actor) {
            $record = Contract::query()->lockForUpdate()->findOrFail($contract->id);
            if (!in_array($record->status, ['draft', 'sent'], true)) throw ValidationException::withMessages(['status' => 'Only draft or sent contracts can be edited.']);
            $start = isset($data['start_date']) ? \Carbon\Carbon::parse($data['start_date']) : $record->start_date;
            $end = isset($data['end_date']) ? \Carbon\Carbon::parse($data['end_date']) : $record->end_date;
            if ($start && $end && $end->lt($start)) throw ValidationException::withMessages(['end_date' => 'The end date must not precede the start date.']);
            $record->update($data);
            DomainOutbox::append('event', 'publishing.contract_updated.v1', ['references' => [['resource' => 'Contract', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'reference' => $record->reference]);
    }

    public function delete(WorkflowInput $input, Contract $contract): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($contract, $actor) {
            $record = Contract::query()->lockForUpdate()->findOrFail($contract->id);
            if (!in_array($record->status, ['draft', 'sent', 'terminated', 'expired'], true)) throw ValidationException::withMessages(['status' => 'Active or renewed contracts cannot be deleted.']);
            $id = $record->id;
            $record->delete();
            DomainOutbox::append('event', 'publishing.contract_deleted.v1', ['references' => [['resource' => 'Contract', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $record]);
    }

    public function activate(WorkflowInput $input, Contract $contract): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($contract, $actor) {
            $record = Contract::query()->lockForUpdate()->findOrFail($contract->id);
            if (in_array($record->status, ['draft', 'sent'], true)) {
                $record->update(['status' => 'active', 'signed_at' => $record->signed_at ?? now()]);
                DomainOutbox::append('event', 'publishing.contract_activated.v1', ['references' => [['resource' => 'Contract', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'active') {
                throw ValidationException::withMessages(['status' => 'Only draft or sent contracts can be activated.']);
            }
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Contract activated.');
    }

    public function terminate(WorkflowInput $input, Contract $contract): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($contract, $actor) {
            $record = Contract::query()->lockForUpdate()->findOrFail($contract->id);
            if ($record->status === 'active') {
                $record->update(['status' => 'terminated']);
                DomainOutbox::append('event', 'publishing.contract_terminated.v1', ['references' => [['resource' => 'Contract', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'terminated') {
                throw ValidationException::withMessages(['status' => 'Only active contracts can be terminated.']);
            }
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Contract terminated.');
    }
}
