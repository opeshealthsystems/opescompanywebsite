<?php

$class = 'App\\Contexts\\Publishing\\Application\\Commands\\AdminContractCommands';
$model = ['name' => 'contract', 'kind' => 'model', 'class' => 'App\\Models\\Contract', 'key' => 'id'];
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$definitions = [];
$definitions['admin.contracts.index'] = [
    'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\AdminContractQueries', 'method' => 'index', 'parameters' => [$input], 'command' => false,
    'context' => 'Publishing', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
    'scope' => 'contracts:read', 'api_path' => 'contracts', 'http_methods' => ['GET'],
    'source_controller' => 'ContractResource', 'source_action' => 'index',
];
$definitions['admin.contracts.expiringSoon'] = [
    'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\AdminContractQueries', 'method' => 'expiringSoon', 'parameters' => [$input], 'command' => false,
    'context' => 'Publishing', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
    'scope' => 'contracts:read', 'api_path' => 'contracts/expiring-soon', 'http_methods' => ['GET'],
    'source_controller' => 'ContractResource', 'source_action' => 'expiring_soon',
];
$definitions['admin.contracts.create'] = [
    'handler' => $class, 'method' => 'create', 'parameters' => [$input], 'command' => true,
    'context' => 'Publishing', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
    'scope' => 'contracts:write', 'api_path' => 'contracts', 'http_methods' => ['POST'],
    'source_controller' => 'ContractResource', 'source_action' => 'create',
];
$definitions['admin.contracts.update'] = [
    'handler' => $class, 'method' => 'update', 'parameters' => [$input, $model], 'command' => true,
    'context' => 'Publishing', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
    'scope' => 'contracts:write', 'api_path' => 'contracts/{contract}', 'http_methods' => ['PUT'],
    'source_controller' => 'ContractResource', 'source_action' => 'update',
];
$definitions['admin.contracts.delete'] = [
    'handler' => $class, 'method' => 'delete', 'parameters' => [$input, $model], 'command' => true,
    'context' => 'Publishing', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
    'scope' => 'contracts:write', 'api_path' => 'contracts/{contract}', 'http_methods' => ['DELETE'],
    'source_controller' => 'ContractResource', 'source_action' => 'delete',
];
foreach ([
    ['admin.contracts.activate', 'activate', 'contracts/{contract}/activate', 'activate'],
    ['admin.contracts.terminate', 'terminate', 'contracts/{contract}/terminate', 'terminate'],
] as [$name, $method, $path, $action]) {
    $definitions[$name] = [
        'handler' => $class, 'method' => $method, 'parameters' => [$input, $model], 'command' => true,
        'context' => 'Publishing', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
        'scope' => 'contracts:write', 'api_path' => $path, 'http_methods' => ['POST'],
        'source_controller' => 'ContractResource', 'source_action' => $action,
    ];
}
return $definitions;
