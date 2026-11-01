<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

final class AdminCatalogueProductQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function item(Product $product): array
    {
        return $product->only(['id', 'slug', 'name', 'name_fr', 'subtitle', 'subtitle_fr', 'category', 'tagline', 'icon', 'color', 'is_active', 'is_featured', 'sort_order']);
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return Product::query()->orderBy('sort_order')->orderBy('name');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $page = $input->paginate($this->query($input));
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (Product $product) => $this->item($product))->all(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, Product $product): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['product' => $this->item($product)]);
    }
}
