<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index(string $locale)
    {
        $all = Product::where('is_active', true)->orderBy('sort_order')->get()->toArray();

        $categoryMap = [
            'core'        => 'Core Platform',
            'diagnostics' => 'Diagnostics',
            'specialist'  => 'Specialist',
        ];

        $grouped = [];
        foreach ($all as $p) {
            $label           = $categoryMap[$p['category']] ?? 'Other';
            $grouped[$label][] = $p;
        }

        return view('pages.products-index', compact('all', 'grouped'));
    }

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
