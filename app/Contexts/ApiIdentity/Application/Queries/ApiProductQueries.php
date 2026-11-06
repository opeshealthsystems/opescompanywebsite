<?php

namespace App\Contexts\ApiIdentity\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;

/** Public, non-sensitive API product metadata. */
final class ApiProductQueries extends WorkflowHandler
{
    public function status(WorkflowInput $input): WorkflowResult
    {
        return WorkflowResult::payload([
            'status' => 'responding',
            'api_version' => (string) config('api_product.version'),
            'checked_at' => now('UTC')->toIso8601String(),
        ]);
    }

    public function changelog(WorkflowInput $input): WorkflowResult
    {
        return WorkflowResult::payload(['items' => config('api_product.changelog', [])]);
    }

    public function catalogue(WorkflowInput $input): WorkflowResult
    {
        $products = array_map(static function (array $product) use ($input): array {
            if ($input->locale() === 'fr') {
                foreach (['name', 'summary', 'data_boundary'] as $field) {
                    $localized = $field.'_fr';
                    if (isset($product[$localized])) $product[$field] = $product[$localized];
                }
            }
            unset($product['name_fr'], $product['summary_fr'], $product['data_boundary_fr']);

            return $product;
        }, config('api_product.products', []));

        return WorkflowResult::payload([
            'api_version' => (string) config('api_product.version'),
            'locale' => $input->locale(),
            'items' => $products,
        ]);
    }
}
