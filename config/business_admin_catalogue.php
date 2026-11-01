<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$product = ['name' => 'product', 'kind' => 'model', 'class' => 'App\\Models\\Product', 'key' => 'id'];
$commands = 'App\\Contexts\\Publishing\\Application\\Commands\\AdminCatalogueProductCommands';
$queries = 'App\\Contexts\\Publishing\\Application\\Queries\\AdminCatalogueProductQueries';
$def = fn (string $handler, string $method, array $parameters, bool $command, string $verb, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $parameters, 'command' => $command, 'context' => 'Publishing',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $command ? 'administration:write' : 'administration:read',
    'api_path' => 'admin/products'.(in_array($method, ['show', 'update', 'archive'], true) ? '/{product}' : '').($method === 'archive' ? '/archive' : ''),
    'http_methods' => [$verb], 'source_controller' => 'ProductResource', 'source_action' => $action,
];

return [
    'admin.catalogue.products.index' => $def($queries, 'index', [$input], false, 'GET', 'index'),
    'admin.catalogue.products.show' => $def($queries, 'show', [$input, $product], false, 'GET', 'show'),
    'admin.catalogue.products.create' => $def($commands, 'create', [$input], true, 'POST', 'create'),
    'admin.catalogue.products.update' => $def($commands, 'update', [$input, $product], true, 'PUT', 'update'),
    'admin.catalogue.products.archive' => $def($commands, 'archive', [$input, $product], true, 'POST', 'archive'),
];
