<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$command = 'App\\Contexts\\Operations\\Application\\Commands\\AdminCompanySettingsCommands';
$query = 'App\\Contexts\\Operations\\Application\\Queries\\AdminCompanySettingsQueries';
$roles = ['super_admin', 'admin'];

return [
    'admin.settings.update' => ['handler' => $command, 'method' => 'update', 'parameters' => [$input], 'command' => true,
        'context' => 'Operations', 'roles' => $roles, 'authenticated' => true, 'scope' => 'administration:write',
        'api_path' => 'admin/company-settings', 'http_methods' => ['POST'],
        'source_controller' => 'SystemSettings', 'source_action' => 'save'],
    'admin.settings.show' => ['handler' => $query, 'method' => 'show', 'parameters' => [$input], 'command' => false,
        'context' => 'Operations', 'roles' => $roles, 'authenticated' => true, 'scope' => 'administration:read',
        'api_path' => 'admin/company-settings', 'http_methods' => ['GET'],
        'source_controller' => 'SystemSettings', 'source_action' => 'view'],
];
