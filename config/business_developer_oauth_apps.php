<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$client = ['name' => 'clientId', 'kind' => 'value', 'route_key' => 'client'];
$base = ['context' => 'ApiIdentity', 'roles' => [], 'authenticated' => true];

return [
    'developer.oauth_applications.index' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Queries\\DeveloperOAuthApplicationQueries',
        'method' => 'index', 'parameters' => [$input], 'command' => false, 'scope' => 'identity:read',
        'api_path' => 'developer/applications', 'http_methods' => ['GET'], 'rate_limiter' => 'api-consumer',
        'web_route' => 'developers.console.index',
        'source_controller' => 'DeveloperOAuthApplications', 'source_action' => 'index',
    ],
    'developer.oauth_applications.create' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Commands\\DeveloperOAuthApplicationCommands',
        'method' => 'create', 'parameters' => [$input], 'command' => true, 'scope' => 'identity:write',
        'api_path' => 'developer/applications', 'http_methods' => ['POST'], 'rate_limiter' => 'api-consumer',
        'web_route' => 'developers.console.applications.create',
        'source_controller' => 'DeveloperOAuthApplications', 'source_action' => 'create',
    ],
    'developer.oauth_applications.update' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Commands\\DeveloperOAuthApplicationCommands',
        'method' => 'update', 'parameters' => [$input, $client], 'command' => true, 'scope' => 'identity:write',
        'api_path' => 'developer/applications/{client}', 'http_methods' => ['PATCH'], 'rate_limiter' => 'api-consumer',
        'web_route' => 'developers.console.applications.update',
        'source_controller' => 'DeveloperOAuthApplications', 'source_action' => 'update',
    ],
    'developer.oauth_applications.revoke' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Commands\\DeveloperOAuthApplicationCommands',
        'method' => 'revoke', 'parameters' => [$input, $client], 'command' => true, 'scope' => 'identity:write',
        'api_path' => 'developer/applications/{client}', 'http_methods' => ['DELETE'], 'rate_limiter' => 'api-consumer',
        'web_route' => 'developers.console.applications.revoke',
        'source_controller' => 'DeveloperOAuthApplications', 'source_action' => 'revoke',
    ],
];
