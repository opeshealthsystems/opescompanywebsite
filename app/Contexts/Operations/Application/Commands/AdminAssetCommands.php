<?php

namespace App\Contexts\Operations\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Shared asset register commands used by Filament and the versioned API. */
final class AdminAssetCommands extends WorkflowHandler
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
            'name' => 'required|string|max:200', 'category' => 'required|in:'.implode(',', array_keys(Asset::categoryOptions())),
            'serial_number' => 'nullable|string|max:100', 'brand' => 'nullable|string|max:100', 'model' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date', 'purchase_price' => 'nullable|numeric|min:0', 'current_value' => 'nullable|numeric|min:0',
            'currency' => 'required|in:XAF,USD,EUR', 'location' => 'nullable|string|max:150',
            'assigned_to' => 'nullable|integer|exists:users,id', 'status' => 'sometimes|required|in:'.implode(',', array_keys(Asset::statusOptions())),
            'warranty_expires' => 'nullable|date', 'notes' => 'nullable|string|max:5000',
            'reference' => 'prohibited', 'id' => 'prohibited',
        ]);
        $record = DB::transaction(function () use ($data, $actor) {
            $year = now()->year; $name = 'asset:'.$year;
            DB::table('business_sequences')->insertOrIgnore(['name' => $name, 'last_value' => 0]);
            $sequence = DB::table('business_sequences')->where('name', $name)->lockForUpdate()->first();
            $previous = (int) $sequence->last_value;
            if ($previous === 0) {
                $prefix = 'AST-'.$year.'-';
                $previous = Asset::query()->where('reference', 'like', $prefix.'%')->pluck('reference')
                    ->map(fn ($reference) => ctype_digit(substr($reference, strlen($prefix))) ? (int) substr($reference, strlen($prefix)) : 0)->max() ?? 0;
            }
            $next = $previous + 1;
            DB::table('business_sequences')->where('name', $name)->update(['last_value' => $next]);
            $record = Asset::create($data + ['reference' => 'AST-'.$year.'-'.str_pad((string) $next, 3, '0', STR_PAD_LEFT), 'status' => 'active']);
            DomainOutbox::append('event', 'operations.asset_created.v1', ['references' => [['resource' => 'Asset', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'reference' => $record->reference, 'status' => $record->status], 201)->with('success', 'Asset recorded.');
    }

    public function update(WorkflowInput $input, Asset $asset): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'name' => 'sometimes|required|string|max:200', 'category' => 'sometimes|required|in:'.implode(',', array_keys(Asset::categoryOptions())),
            'serial_number' => 'sometimes|nullable|string|max:100', 'brand' => 'sometimes|nullable|string|max:100', 'model' => 'sometimes|nullable|string|max:100',
            'purchase_date' => 'sometimes|nullable|date', 'purchase_price' => 'sometimes|nullable|numeric|min:0', 'current_value' => 'sometimes|nullable|numeric|min:0',
            'currency' => 'sometimes|required|in:XAF,USD,EUR', 'location' => 'sometimes|nullable|string|max:150',
            'assigned_to' => 'sometimes|nullable|integer|exists:users,id', 'status' => 'sometimes|required|in:'.implode(',', array_keys(Asset::statusOptions())),
            'warranty_expires' => 'sometimes|nullable|date', 'notes' => 'sometimes|nullable|string|max:5000',
            'reference' => 'prohibited', 'id' => 'prohibited',
        ]);
        if ($data === []) throw ValidationException::withMessages(['asset' => 'Provide at least one asset field to update.']);
        $record = DB::transaction(function () use ($asset, $data, $actor) {
            $record = Asset::query()->lockForUpdate()->findOrFail($asset->id);
            $record->fill($data)->save();
            DomainOutbox::append('event', 'operations.asset_updated.v1', ['references' => [['resource' => 'Asset', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'reference' => $record->reference, 'status' => $record->status])->with('success', 'Asset updated.');
    }

    public function delete(WorkflowInput $input, Asset $asset): WorkflowResult
    {
        $actor = $this->admin($input);
        DB::transaction(function () use ($asset, $actor) {
            $record = Asset::query()->lockForUpdate()->findOrFail($asset->id);
            if ($record->status !== 'disposed' && $record->status !== 'retired') {
                throw ValidationException::withMessages(['status' => 'Only retired or disposed assets can be deleted.']);
            }
            DomainOutbox::append('event', 'operations.asset_deleted.v1', ['references' => [['resource' => 'Asset', 'id' => $record->id]]], $actor->id);
            $record->delete();
        }, 3);
        return WorkflowResult::payload(['id' => $asset->id, 'status' => 'deleted'])->with('success', 'Asset record deleted.');
    }
}
