<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Models\Product;
class ProductQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, string $locale): \App\Contexts\Shared\Application\WorkflowResult
    {
        $all = \App\Models\Product::publicCatalogue()->map->toArray()->all();
        foreach ($all as &$item) {
            if ($item['slug'] === 'opescare') {
                $item['tagline'] = 'Health ID and interoperability for participating OPES systems.';
            }
            if ($locale === 'fr' && $copy = config('product_copy_fr.' . $item['slug'])) {
                $item['subtitle'] = $copy[0];
                $item['tagline'] = $copy[1];
            }
        }
        unset($item);
        $categoryMap = ['core' => 'Core Platform', 'diagnostics' => 'Diagnostics', 'specialist' => 'Specialist', 'medicine-safety' => 'Medicine Safety'];
        $grouped = [];
        foreach ($all as $p) {
            $label = $p['slug'] === 'opes-drugtrace' ? 'Medicine Safety' : $categoryMap[$p['category']] ?? 'Other';
            $grouped[$label][] = $p;
        }
        return \App\Contexts\Shared\Application\WorkflowResult::read('productcontroller_index_1', compact('all', 'grouped'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, string $locale, string $slug): \App\Contexts\Shared\Application\WorkflowResult
    {
        $products = array_merge(config('products'), config('products_specialist'), config('products_drugtrace'));
        if (!array_key_exists($slug, $products)) {
            abort(404);
        }
        $product = $products[$slug];
        foreach ($product['stats'] ?? [] as $index => $stat) {
            if ((string) ($stat['value'] ?? '') === '22' && preg_match('/systems|systèmes/i', $stat['label'] ?? '')) {
                $product['stats'][$index] = ['value' => (string) \App\Models\Product::publicCatalogue()->count(), 'label' => $locale === 'fr' ? 'produits au catalogue' : 'products in catalogue'];
            }
        }
        if ($slug === 'opes-drugtrace') {
            return \App\Contexts\Shared\Application\WorkflowResult::read('productcontroller_show_1', compact('product'));
        }
        // Resolve related products to their full data
        $related = [];
        foreach ($product['related'] ?? [] as $relSlug) {
            if (isset($products[$relSlug])) {
                $related[] = $products[$relSlug];
            }
        }
        return \App\Contexts\Shared\Application\WorkflowResult::read('productcontroller_show_2', compact('product', 'related'));
    }
}
