<?php

namespace App\Http\Controllers;

class ProductController extends Controller
{
    public function show(string $locale, string $slug)
    {
        $products = array_merge(
            config('products'),
            config('products_specialist')
        );

        if (! array_key_exists($slug, $products)) {
            abort(404);
        }

        $product = $products[$slug];

        // Resolve related products to their full data
        $related = [];
        foreach ($product['related'] ?? [] as $relSlug) {
            if (isset($products[$relSlug])) {
                $related[] = $products[$relSlug];
            }
        }

        return view('pages.product-show', compact('product', 'related'));
    }
}
