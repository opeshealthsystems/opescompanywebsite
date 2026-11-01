<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Product;

/** Machine-client product catalogue query behind the client-credentials API adapter. */
final class MachineCatalogueQueries extends WorkflowHandler
{
    public function products(WorkflowInput $input): WorkflowResult
    {
        $filters = $input->validate(['page' => 'sometimes|integer|min:1|max:10000', 'per_page' => 'sometimes|integer|min:1|max:50', 'locale' => 'sometimes|in:en,fr']);
        $page = (int) ($filters['page'] ?? 1);
        $size = (int) ($filters['per_page'] ?? 20);
        $locale = $filters['locale'] ?? 'en';
        $configuredSlugs = array_keys(array_merge(
            config('products', []),
            config('products_specialist', []),
            config('products_drugtrace', []),
        ));

        if (Product::query()->exists()) {
            $catalogue = Product::query()
                ->where('is_active', true)
                ->whereIn('slug', $configuredSlugs)
                ->orderBy('slug');
            $total = (clone $catalogue)->count();
            $products = $catalogue->forPage($page, $size)->get(['slug', 'name', 'name_fr', 'category']);
        } else {
            $products = Product::publicCatalogue()->sortBy('slug')->values();
            $total = $products->count();
            $products = $products->forPage($page, $size)->values();
        }

        return WorkflowResult::payload([
            'items' => $products->map(fn (Product $product) => [
                'slug' => $product->slug,
                'name' => $locale === 'fr' ? ($product->name_fr ?: $product->name) : $product->name,
                'category' => $product->category,
                'url' => url('/'.$locale.'/products/'.$product->slug),
            ])->values()->all(),
            'meta' => ['page' => $page, 'per_page' => $size, 'total' => $total],
        ]);
    }
}
