<?php

namespace App\Contexts\Publishing\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminCatalogueProductCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
        return $user;
    }

    private function values(WorkflowInput $input, bool $update = false): array
    {
        $prefix = $update ? 'sometimes|' : '';
        $configuredSlugs = array_keys(array_merge(config('products', []), config('products_specialist', []), config('products_drugtrace', [])));
        return $input->validate([
            'slug' => [$prefix.'required', 'string', 'max:80', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'in:'.implode(',', $configuredSlugs)],
            'category' => $prefix.'required|in:core,diagnostics,specialist',
            'name' => $prefix.'required|string|max:100', 'name_fr' => 'sometimes|nullable|string|max:100',
            'subtitle' => 'sometimes|nullable|string|max:120', 'subtitle_fr' => 'sometimes|nullable|string|max:120',
            'tagline' => 'sometimes|nullable|string|max:300', 'icon' => 'sometimes|nullable|string|max:80',
            'color' => 'sometimes|nullable|regex:/^#[0-9a-fA-F]{6}$/',
            'sort_order' => 'sometimes|integer|min:0|max:100000', 'is_active' => 'sometimes|boolean', 'is_featured' => 'sometimes|boolean',
            'id' => 'prohibited',
        ]);
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->values($input);
        abort_if(Product::where('slug', $data['slug'])->exists(), 409, 'A catalogue record already exists for this product.');
        $product = DB::transaction(function () use ($data, $actor) {
            $product = Product::create($data);
            DomainOutbox::append('event', 'publishing.catalogue_product_created.v1', ['references' => [['resource' => 'Product', 'id' => $product->id]]], $actor->id);
            return $product;
        }, 3);
        return WorkflowResult::payload(['record' => $product, 'id' => $product->id], 201);
    }

    public function update(WorkflowInput $input, Product $product): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->values($input, true);
        if (array_key_exists('slug', $data) && $data['slug'] !== $product->slug) {
            throw ValidationException::withMessages(['slug' => 'Product slugs are immutable because licences and public links reference them.']);
        }
        unset($data['slug']);
        if (!$data) throw ValidationException::withMessages(['product' => 'Provide at least one product field to update.']);
        $updated = DB::transaction(function () use ($product, $data, $actor) {
            $record = Product::query()->lockForUpdate()->findOrFail($product->id);
            $record->update($data);
            DomainOutbox::append('event', 'publishing.catalogue_product_updated.v1', ['references' => [['resource' => 'Product', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $updated, 'id' => $updated->id]);
    }

    public function archive(WorkflowInput $input, Product $product): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($product, $actor) {
            $record = Product::query()->lockForUpdate()->findOrFail($product->id);
            $record->update(['is_active' => false, 'is_featured' => false]);
            DomainOutbox::append('event', 'publishing.catalogue_product_archived.v1', ['references' => [['resource' => 'Product', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'slug' => $record->slug, 'is_active' => false]);
    }
}
