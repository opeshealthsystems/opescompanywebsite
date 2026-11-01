<?php

namespace App\Contexts\Finance\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminVendorCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User { $actor = $input->user(); abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403); return $actor; }
    private function values(WorkflowInput $input, bool $update = false): array
    {
        $prefix = $update ? 'sometimes|' : '';
        return $input->validate(['name' => $prefix.'required|string|max:200', 'contact_name' => 'sometimes|nullable|string|max:150',
            'email' => 'sometimes|nullable|email|max:255', 'phone' => 'sometimes|nullable|string|max:50', 'tax_id' => 'sometimes|nullable|string|max:80',
            'is_active' => $prefix.'required|boolean', 'address' => 'sometimes|nullable|string|max:2000', 'notes' => 'sometimes|nullable|string|max:5000',
            'id' => 'prohibited', 'purchase_orders_count' => 'prohibited']);
    }
    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input);
        $record = DB::transaction(function () use ($data, $actor) { $r = Vendor::create($data); DomainOutbox::append('event', 'finance.vendor_created.v1', ['references' => [['resource' => 'Vendor', 'id' => $r->id]]], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }
    public function update(WorkflowInput $input, Vendor $vendor): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input, true); if (!$data) throw ValidationException::withMessages(['vendor' => 'Provide at least one vendor field to update.']);
        $r = DB::transaction(function () use ($vendor, $data, $actor) { $r = Vendor::query()->lockForUpdate()->findOrFail($vendor->id); $r->update($data); DomainOutbox::append('event', 'finance.vendor_updated.v1', ['references' => [['resource' => 'Vendor', 'id' => $r->id]], 'changed_fields' => array_keys($data)], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $r, 'id' => $r->id]);
    }
    public function delete(WorkflowInput $input, Vendor $vendor): WorkflowResult
    {
        $actor = $this->admin($input); $id = DB::transaction(function () use ($vendor, $actor) { $r = Vendor::query()->lockForUpdate()->findOrFail($vendor->id); if ($r->purchaseOrders()->exists()) throw ValidationException::withMessages(['vendor' => 'Vendors with purchase-order history cannot be deleted.']); $id = $r->id; $r->delete(); DomainOutbox::append('event', 'finance.vendor_deleted.v1', ['references' => [['resource' => 'Vendor', 'id' => $id]]], $actor->id); return $id; }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
