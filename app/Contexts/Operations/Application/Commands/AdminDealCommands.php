<?php

namespace App\Contexts\Operations\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Shared CRM deal commands used by Filament and the versioned API. */
final class AdminDealCommands extends WorkflowHandler
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
            'title' => 'required|string|max:250', 'lead_id' => 'nullable|integer|exists:leads,id',
            'stage' => 'required|in:'.implode(',', array_keys(Deal::stageOptions())),
            'value' => 'required|numeric|min:0', 'currency' => 'required|in:XAF,USD,EUR',
            'probability' => 'required|integer|min:0|max:100', 'expected_close_date' => 'nullable|date',
            'actual_close_date' => 'nullable|date', 'owner_id' => 'required|integer|exists:users,id',
            'notes' => 'nullable|string|max:5000', 'lost_reason' => 'nullable|string|max:300',
            'reference' => 'prohibited', 'id' => 'prohibited',
        ]);
        $record = DB::transaction(function () use ($data, $actor) {
            $year = now()->year; $name = 'deal:'.$year;
            DB::table('business_sequences')->insertOrIgnore(['name' => $name, 'last_value' => 0]);
            $sequence = DB::table('business_sequences')->where('name', $name)->lockForUpdate()->first();
            $previous = (int) $sequence->last_value;
            if ($previous === 0) {
                $prefix = 'DEAL-'.$year.'-';
                $previous = Deal::query()->where('reference', 'like', $prefix.'%')->pluck('reference')
                    ->map(fn ($reference) => ctype_digit(substr($reference, strlen($prefix))) ? (int) substr($reference, strlen($prefix)) : 0)->max() ?? 0;
            }
            $next = $previous + 1;
            DB::table('business_sequences')->where('name', $name)->update(['last_value' => $next]);
            $record = Deal::create($data + ['reference' => 'DEAL-'.$year.'-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT)]);
            DomainOutbox::append('event', 'crm.deal_created.v1', ['references' => [['resource' => 'Deal', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'reference' => $record->reference, 'stage' => $record->stage], 201)->with('success', 'Deal recorded.');
    }

    public function update(WorkflowInput $input, Deal $deal): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'title' => 'sometimes|required|string|max:250', 'lead_id' => 'sometimes|nullable|integer|exists:leads,id',
            'stage' => 'sometimes|required|in:'.implode(',', array_keys(Deal::stageOptions())),
            'value' => 'sometimes|required|numeric|min:0', 'currency' => 'sometimes|required|in:XAF,USD,EUR',
            'probability' => 'sometimes|required|integer|min:0|max:100', 'expected_close_date' => 'sometimes|nullable|date',
            'actual_close_date' => 'sometimes|nullable|date', 'owner_id' => 'sometimes|required|integer|exists:users,id',
            'notes' => 'sometimes|nullable|string|max:5000', 'lost_reason' => 'sometimes|nullable|string|max:300',
            'reference' => 'prohibited', 'id' => 'prohibited',
        ]);
        if ($data === []) throw ValidationException::withMessages(['deal' => 'Provide at least one deal field to update.']);
        $record = DB::transaction(function () use ($deal, $data, $actor) {
            $record = Deal::query()->lockForUpdate()->findOrFail($deal->id);
            if (in_array($record->stage, ['closed_won', 'closed_lost'], true)) throw ValidationException::withMessages(['stage' => 'Closed deals cannot be edited.']);
            $record->fill($data)->save();
            DomainOutbox::append('event', 'crm.deal_updated.v1', ['references' => [['resource' => 'Deal', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'reference' => $record->reference, 'stage' => $record->stage])->with('success', 'Deal updated.');
    }

    public function delete(WorkflowInput $input, Deal $deal): WorkflowResult
    {
        $actor = $this->admin($input);
        DB::transaction(function () use ($deal, $actor) {
            $record = Deal::query()->lockForUpdate()->findOrFail($deal->id);
            if (in_array($record->stage, ['closed_won', 'closed_lost'], true)) throw ValidationException::withMessages(['stage' => 'Closed deals cannot be deleted.']);
            DomainOutbox::append('event', 'crm.deal_deleted.v1', ['references' => [['resource' => 'Deal', 'id' => $record->id]]], $actor->id);
            $record->delete();
        }, 3);
        return WorkflowResult::payload(['id' => $deal->id, 'status' => 'deleted'])->with('success', 'Deal deleted.');
    }
}
