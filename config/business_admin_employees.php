<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$employee = ['name' => 'employee', 'kind' => 'model', 'class' => 'App\\Models\\User', 'key' => 'id'];
$commands = 'App\\Contexts\\Workforce\\Application\\Commands\\AdminEmployeeCommands';
$queries = 'App\\Contexts\\Workforce\\Application\\Queries\\AdminEmployeeQueries';
$def = fn (string $method, array $parameters, bool $command, string $path, string $verb, string $action) => [
    'handler' => $commands, 'method' => $method, 'parameters' => $parameters, 'command' => $command, 'context' => 'Workforce HR',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $command ? 'workforce:hr:write' : 'workforce:hr:read',
    'api_path' => $path, 'http_methods' => [$verb], 'source_controller' => 'EmployeeResource', 'source_action' => $action,
];

return [
    'admin.workforce.employees.index' => array_replace($def('index', [$input], false, 'admin/workforce/employees', 'GET', 'index'), ['handler' => $queries]),
    'admin.workforce.employees.show' => array_replace($def('show', [$input, $employee], false, 'admin/workforce/employees/{employee}', 'GET', 'show'), ['handler' => $queries]),
    'admin.workforce.employees.create' => $def('create', [$input], true, 'admin/workforce/employees', 'POST', 'create'),
    'admin.workforce.employees.update' => $def('update', [$input, $employee], true, 'admin/workforce/employees/{employee}', 'PUT', 'update'),
];
