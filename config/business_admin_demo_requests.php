<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$demoRequest = ['name' => 'demoRequest', 'kind' => 'model', 'class' => 'App\\Models\\DemoRequest', 'key' => 'id'];
$commands = 'App\\Contexts\\Enquiries\\Application\\Commands\\AdminDemoRequestCommands';
$queries = 'App\\Contexts\\Enquiries\\Application\\Queries\\AdminDemoRequestQueries';
$def = fn (string $handler, string $method, array $parameters, bool $command, string $verb, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $parameters, 'command' => $command, 'context' => 'Enquiries',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $command ? 'crm:write' : 'crm:read',
    'api_path' => 'crm/demo-requests'.(in_array($method, ['show', 'update', 'delete'], true) ? '/{demoRequest}' : ''),
    'http_methods' => [$verb], 'source_controller' => 'DemoRequestResource', 'source_action' => $action,
];

return [
    'admin.crm.demoRequests.index' => $def($queries, 'index', [$input], false, 'GET', 'index'),
    'admin.crm.demoRequests.show' => $def($queries, 'show', [$input, $demoRequest], false, 'GET', 'show'),
    'admin.crm.demoRequests.create' => $def($commands, 'create', [$input], true, 'POST', 'create'),
    'admin.crm.demoRequests.update' => $def($commands, 'update', [$input, $demoRequest], true, 'PUT', 'update'),
    'admin.crm.demoRequests.delete' => $def($commands, 'delete', [$input, $demoRequest], true, 'DELETE', 'delete'),
];
