<?php

namespace App\Http\Controllers;

class ProductController extends Controller
{
    public function index(string $locale)
    {
        $core       = config('products');
        $specialist = config('products_specialist');

        $grouped = [
            'Core Platform' => array_filter($core,       fn ($p) => $p['category'] === 'Core Platform'),
            'Diagnostics'   => array_filter($core,       fn ($p) => $p['category'] !== 'Core Platform'),
            'Specialist'    => $specialist,
        ];

        $all = array_merge($core, $specialist);

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
