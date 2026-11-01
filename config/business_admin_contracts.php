<?php

$class = 'App\\Contexts\\Publishing\\Application\\Commands\\AdminContractCommands';
$model = ['name' => 'contract', 'kind' => 'model', 'class' => 'App\\Models\\Contract', 'key' => 'id'];
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$definitions = [];
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
