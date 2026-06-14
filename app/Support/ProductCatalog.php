<?php

namespace App\Support;

class ProductCatalog
{
    public static function options(): array
    {
        $options = [];
        foreach (array_merge(config('products', []), config('products_specialist', [])) as $product) {
            $slug = $product['slug'] ?? null;
            $name = $product['name'] ?? null;
            if ($slug && $name) {
                $options[$slug] = $name;
            }
        }
        return $options;
    }
}
