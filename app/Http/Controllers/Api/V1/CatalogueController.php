<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use Illuminate\Http\Request;

class CatalogueController
{
    public function __invoke(Request $request)
    {
        $input = $request->validate(['page' => 'sometimes|integer|min:1|max:10000', 'per_page' => 'sometimes|integer|min:1|max:50', 'locale' => 'sometimes|in:en,fr']);
        $page = (int) ($input['page'] ?? 1); $size = (int) ($input['per_page'] ?? 20); $locale = $input['locale'] ?? 'en';
        $products = Product::publicCatalogue()->sortBy('slug')->values();
        return response()->json(['data' => $products->forPage($page, $size)->map(fn ($product) => [
            'slug' => $product->slug, 'name' => $locale === 'fr' ? ($product->name_fr ?: $product->name) : $product->name,
            'category' => $product->category, 'url' => url('/'.$locale.'/products/'.$product->slug),
        ])->values(), 'meta' => ['page' => $page, 'per_page' => $size, 'total' => $products->count()]]);
    }
}
