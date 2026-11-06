<?php

namespace App\Contexts\ApiIdentity\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;

/** Public, non-sensitive API product metadata. */
final class ApiProductQueries extends WorkflowHandler
{
    public function discovery(WorkflowInput $input): WorkflowResult
    {
        return WorkflowResult::payload([
            'name' => 'OPES Health Systems API',
            'api_version' => (string) config('api_product.version'),
            'openapi_version' => '3.1.1',
            'authentication' => [
                'machine' => 'OAuth 2.0 client credentials with approved scopes',
                'delegated' => 'OAuth 2.0 authorization code with S256 PKCE and user consent',
            ],
            '_links' => [
                'self' => ['href' => '/api/v1/discovery'],
                'openapi' => ['href' => '/api/v1/openapi.json'],
                'status' => ['href' => '/api/v1/status'],
                'api_products' => ['href' => '/api/v1/api-products'],
                'service_offerings' => ['href' => '/api/v1/service-offerings'],
                'changelog' => ['href' => '/api/v1/changelog'],
                'sdk_metadata' => ['href' => '/downloads/opes-api-sdk.json'],
                'developer_portal' => ['href' => '/'.$input->locale().'/developers'],
            ],
        ]);
    }

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
