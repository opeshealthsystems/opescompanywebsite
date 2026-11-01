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
