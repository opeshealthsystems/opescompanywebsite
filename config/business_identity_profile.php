<?php

return [
    'identity.me' => [
        'handler' => 'App\\Contexts\\Identity\\Application\\Queries\\OwnProfile',
        'method' => 'execute',
        'parameters' => [['name' => 'input', 'kind' => 'input']],
        'command' => false,
        'context' => 'Identity',
        'roles' => [],
        'authenticated' => true,
        'scope' => 'identity:read',
        'api_path' => 'me',
        'http_methods' => ['GET'],
        'source_controller' => 'IdentityProfile',
        'source_action' => 'show',
    ],
];
