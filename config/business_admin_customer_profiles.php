<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$profile = ['name' => 'customerProfile', 'kind' => 'model', 'class' => 'App\\Models\\CustomerProfile', 'key' => 'id'];
$queries = 'App\\Contexts\\CustomerServices\\Application\\Queries\\AdminCustomerProfileQueries';
$commands = 'App\\Contexts\\CustomerServices\\Application\\Commands\\AdminCustomerProfileCommands';
$read = ['context' => 'Customers', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'administration:read'];
$write = ['context' => 'Customers', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'administration:write'];

return [
    'admin.customerProfiles.index' => ['handler' => $queries, 'method' => 'index', 'parameters' => [$input], 'command' => false] + $read + ['api_path' => 'admin/customer-profiles', 'http_methods' => ['GET'], 'source_controller' => 'CustomerProfileResource', 'source_action' => 'index'],
    'admin.customerProfiles.show' => ['handler' => $queries, 'method' => 'show', 'parameters' => [$input, $profile], 'command' => false] + $read + ['api_path' => 'admin/customer-profiles/{customerProfile}', 'http_methods' => ['GET'], 'source_controller' => 'CustomerProfileResource', 'source_action' => 'show'],
    'admin.customerProfiles.create' => ['handler' => $commands, 'method' => 'create', 'parameters' => [$input], 'command' => true] + $write + ['api_path' => 'admin/customer-profiles', 'http_methods' => ['POST'], 'source_controller' => 'CustomerProfileResource', 'source_action' => 'create'],
    'admin.customerProfiles.update' => ['handler' => $commands, 'method' => 'update', 'parameters' => [$input, $profile], 'command' => true] + $write + ['api_path' => 'admin/customer-profiles/{customerProfile}', 'http_methods' => ['PUT'], 'source_controller' => 'CustomerProfileResource', 'source_action' => 'update'],
];
