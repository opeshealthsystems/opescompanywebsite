<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$client = ['name' => 'clientId', 'kind' => 'value', 'route_key' => 'client'];
$base = ['context' => 'ApiIdentity', 'roles' => [], 'authenticated' => true];

return [
    'developer.machine_clients.index' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Queries\\DeveloperMachineClientQueries',
        'method' => 'index', 'parameters' => [$input], 'command' => false, 'scope' => 'identity:read',
        'api_path' => 'developer/machine-clients', 'http_methods' => ['GET'], 'rate_limiter' => 'api-consumer',
        'web_route' => 'developers.console.machine-clients.index',
        'source_controller' => 'DeveloperMachineClients', 'source_action' => 'index',
    ],
    'developer.machine_clients.create' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Commands\\DeveloperMachineClientCommands',
        'method' => 'create', 'parameters' => [$input], 'command' => true, 'scope' => 'identity:write',
        'api_path' => 'developer/machine-clients', 'http_methods' => ['POST'], 'rate_limiter' => 'api-consumer',
        'web_route' => 'developers.console.machine-clients.create',
        'source_controller' => 'DeveloperMachineClients', 'source_action' => 'create',
    ],
    'developer.machine_clients.rotate' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Commands\\DeveloperMachineClientCommands',
        'method' => 'rotate', 'parameters' => [$input, $client], 'command' => true, 'scope' => 'identity:write',
        'api_path' => 'developer/machine-clients/{client}/rotate', 'http_methods' => ['POST'], 'rate_limiter' => 'api-consumer',
        'web_route' => 'developers.console.machine-clients.rotate',
        'source_controller' => 'DeveloperMachineClients', 'source_action' => 'rotate',
    ],
    'developer.machine_clients.revoke' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Commands\\DeveloperMachineClientCommands',
        'method' => 'revoke', 'parameters' => [$input, $client], 'command' => true, 'scope' => 'identity:write',
        'api_path' => 'developer/machine-clients/{client}', 'http_methods' => ['DELETE'], 'rate_limiter' => 'api-consumer',
        'web_route' => 'developers.console.machine-clients.revoke',
        'source_controller' => 'DeveloperMachineClients', 'source_action' => 'revoke',
    ],
];
