<?php

$base = ['context' => 'ApiIdentity', 'roles' => [], 'authenticated' => false, 'scope' => null];
$input = ['name' => 'input', 'kind' => 'input'];

return [
    'api.product.status' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Queries\\ApiProductQueries',
        'method' => 'status', 'parameters' => [$input], 'command' => false,
        'api_path' => 'status', 'http_methods' => ['GET'], 'rate_limiter' => 'public-api-read',
        'source_controller' => 'ApiProduct', 'source_action' => 'status',
    ],
    'api.product.changelog' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Queries\\ApiProductQueries',
        'method' => 'changelog', 'parameters' => [$input], 'command' => false,
        'api_path' => 'changelog', 'http_methods' => ['GET'], 'rate_limiter' => 'public-api-read',
        'source_controller' => 'ApiProduct', 'source_action' => 'changelog',
    ],
    'api.product.catalogue' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Queries\\ApiProductQueries',
        'method' => 'catalogue', 'parameters' => [$input], 'command' => false,
        'api_path' => 'api-products', 'http_methods' => ['GET'], 'rate_limiter' => 'public-api-read',
        'source_controller' => 'ApiProduct', 'source_action' => 'catalogue',
    ],
    'publishing.service-offerings.catalogue' => ['context' => 'Publishing'] + $base + [
        'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\PublicServiceOfferingQueries',
        'method' => 'catalogue', 'parameters' => [$input], 'command' => false,
        'api_path' => 'service-offerings', 'http_methods' => ['GET'], 'rate_limiter' => 'public-api-read',
        'source_controller' => 'ServiceOffering', 'source_action' => 'catalogue',
    ],
];
