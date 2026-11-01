<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$budget = ['name' => 'budget', 'kind' => 'model', 'class' => 'App\\Models\\Budget', 'key' => 'id'];
$command = 'App\\Contexts\\Finance\\Application\\Commands\\AdminBudgetCommands';
$query = 'App\\Contexts\\Finance\\Application\\Queries\\AdminBudgetQueries';
$def = fn (string $handler, string $method, array $params, bool $write, string $verb, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $params, 'command' => $write, 'context' => 'Finance',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $write ? 'finance:write' : 'finance:read',
    'api_path' => 'finance/budgets'.(in_array($method, ['show', 'update', 'delete'], true) ? '/{budget}' : ''), 'http_methods' => [$verb],
    'source_controller' => 'BudgetResource', 'source_action' => $action,
];

return [
    'admin.finance.budgets.index' => $def($query, 'index', [$input], false, 'GET', 'index'),
    'admin.finance.budgets.show' => $def($query, 'show', [$input, $budget], false, 'GET', 'show'),
    'admin.finance.budgets.create' => $def($command, 'create', [$input], true, 'POST', 'create'),
    'admin.finance.budgets.update' => $def($command, 'update', [$input, $budget], true, 'PUT', 'update'),
    'admin.finance.budgets.delete' => $def($command, 'delete', [$input, $budget], true, 'DELETE', 'delete'),
];
