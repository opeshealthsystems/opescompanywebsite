<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$client = ['name' => 'clientId', 'kind' => 'value', 'route_key' => 'client'];
$base = ['context' => 'ApiIdentity', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'api-clients:manage'];
$read = [...$base, 'scope' => 'api-clients:read'];

return [
    'admin.apiClients.index' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Queries\\AdminApiClientQueries',
        'method' => 'index', 'parameters' => [$input], 'command' => false,
        'api_path' => 'admin/api-clients', 'http_methods' => ['GET'],
        'source_controller' => 'ApiClientAdministration', 'source_action' => 'index',
    ],
    'admin.apiClients.usage' => $read + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Queries\\AdminApiClientQueries',
        'method' => 'usage', 'parameters' => [$input, $client], 'command' => false,
        'api_path' => 'admin/api-clients/{client}/usage', 'http_methods' => ['GET'],
        'source_controller' => 'ApiClientAdministration', 'source_action' => 'usage',
    ],
    'admin.apiClients.quota' => $read + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Queries\\MachineClientQuotaQueries',
        'method' => 'admin', 'parameters' => [$input, $client], 'command' => false,
        'api_path' => 'admin/api-clients/{client}/quota', 'http_methods' => ['GET'],
        'source_controller' => 'ApiClientAdministration', 'source_action' => 'quota',
    ],
    'admin.apiClients.setQuota' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Commands\\AdminApiClientCommands',
        'method' => 'setQuota', 'parameters' => [$input, $client], 'command' => true,
        'api_path' => 'admin/api-clients/{client}/quota', 'http_methods' => ['PATCH'],
        'source_controller' => 'ApiClientAdministration', 'source_action' => 'setQuota',
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
