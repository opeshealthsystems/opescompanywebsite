<?php

namespace App\Contexts\Validation\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\User;
use App\Models\ValidationProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AdminValidationProductCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); return $u; }
    private function values(WorkflowInput $input, ?ValidationProduct $product = null): array
    {
        $id = $product?->id;
        return $input->validate(['name' => ($id ? 'sometimes|' : '').'required|string|max:255',
            'code' => [$id ? 'sometimes' : 'required', 'string', 'max:50', Rule::unique('validation_products', 'code')->ignore($id)],
            'description' => 'sometimes|nullable|string|max:20000', 'is_active' => ($id ? 'sometimes|' : '').'required|boolean', 'id' => 'prohibited']);
    }
    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input);
        $r = DB::transaction(function () use ($data, $actor) { $r = ValidationProduct::create($data); DomainOutbox::append('event', 'validation.product_created.v1', ['references' => [['resource' => 'ValidationProduct', 'id' => $r->id]]], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $r, 'id' => $r->id], 201);
    }
    public function update(WorkflowInput $input, ValidationProduct $validationProduct): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input, $validationProduct); if (!$data) throw ValidationException::withMessages(['validation_product' => 'Provide at least one field to update.']);
        $r = DB::transaction(function () use ($validationProduct, $data, $actor) { $r = ValidationProduct::query()->lockForUpdate()->findOrFail($validationProduct->id); $r->update($data); DomainOutbox::append('event', 'validation.product_updated.v1', ['references' => [['resource' => 'ValidationProduct', 'id' => $r->id]], 'changed_fields' => array_keys($data)], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $r, 'id' => $r->id]);
    }
    public function delete(WorkflowInput $input, ValidationProduct $validationProduct): WorkflowResult
    {
        $actor = $this->admin($input); $id = DB::transaction(function () use ($validationProduct, $actor) { $r = ValidationProduct::query()->lockForUpdate()->findOrFail($validationProduct->id); if ($r->modules()->exists()) throw ValidationException::withMessages(['validation_product' => 'Products with validation modules cannot be deleted.']); $id = $r->id; $r->delete(); DomainOutbox::append('event', 'validation.product_deleted.v1', ['references' => [['resource' => 'ValidationProduct', 'id' => $id]]], $actor->id); return $id; }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
