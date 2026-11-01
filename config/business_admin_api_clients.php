<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$client = ['name' => 'clientId', 'kind' => 'value'];
$base = ['context' => 'ApiIdentity', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'api-clients:manage'];

return [
    'admin.apiClients.index' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Queries\\AdminApiClientQueries',
        'method' => 'index', 'parameters' => [$input], 'command' => false,
        'api_path' => 'admin/api-clients', 'http_methods' => ['GET'],
        'source_controller' => 'ApiClientAdministration', 'source_action' => 'index',
    ],
    'admin.apiClients.create' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Commands\\AdminApiClientCommands',
        'method' => 'create', 'parameters' => [$input], 'command' => true,
        'api_path' => 'admin/api-clients', 'http_methods' => ['POST'],
        'source_controller' => 'ApiClientAdministration', 'source_action' => 'create',
    ],
    'admin.apiClients.rotate' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Commands\\AdminApiClientCommands',
        'method' => 'rotate', 'parameters' => [$input, $client], 'command' => true,
        'api_path' => 'admin/api-clients/{client}/rotate', 'http_methods' => ['POST'],
        'source_controller' => 'ApiClientAdministration', 'source_action' => 'rotate',
    ],
    'admin.apiClients.revoke' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Commands\\AdminApiClientCommands',
        'method' => 'revoke', 'parameters' => [$input, $client], 'command' => true,
        'api_path' => 'admin/api-clients/{client}', 'http_methods' => ['DELETE'],
        'source_controller' => 'ApiClientAdministration', 'source_action' => 'revoke',
    ],
];
