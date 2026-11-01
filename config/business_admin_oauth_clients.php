<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$client = ['name' => 'clientId', 'kind' => 'value'];
$base = ['context' => 'ApiIdentity', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'oauth-clients:manage'];

return [
    'admin.oauthClients.index' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Queries\\AdminOAuthClientQueries',
        'method' => 'index', 'parameters' => [$input], 'command' => false,
        'api_path' => 'admin/oauth-clients', 'http_methods' => ['GET'],
        'source_controller' => 'OAuthClientAdministration', 'source_action' => 'index',
    ],
    'admin.oauthClients.create' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Commands\\AdminOAuthClientCommands',
        'method' => 'create', 'parameters' => [$input], 'command' => true,
        'api_path' => 'admin/oauth-clients', 'http_methods' => ['POST'],
        'source_controller' => 'OAuthClientAdministration', 'source_action' => 'create',
    ],
    'admin.oauthClients.revoke' => $base + [
        'handler' => 'App\\Contexts\\ApiIdentity\\Application\\Commands\\AdminOAuthClientCommands',
        'method' => 'revoke', 'parameters' => [$input, $client], 'command' => true,
        'api_path' => 'admin/oauth-clients/{client}', 'http_methods' => ['DELETE'],
        'source_controller' => 'OAuthClientAdministration', 'source_action' => 'revoke',
    ],
];
