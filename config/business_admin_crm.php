<?php

$class = 'App\\Contexts\\Operations\\Application\\Commands\\AdminDealCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$deal = ['name' => 'deal', 'kind' => 'model', 'class' => 'App\\Models\\Deal', 'key' => 'id'];
return [
    'admin.crm.deals.create' => ['handler' => $class, 'method' => 'create', 'parameters' => [$input], 'command' => true,
        'context' => 'CRM', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'crm:write',
        'api_path' => 'crm/deals', 'http_methods' => ['POST'], 'source_controller' => 'DealResource', 'source_action' => 'create'],
    'admin.crm.deals.update' => ['handler' => $class, 'method' => 'update', 'parameters' => [$input, $deal], 'command' => true,
        'context' => 'CRM', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'crm:write',
        'api_path' => 'crm/deals/{deal}', 'http_methods' => ['PUT'], 'source_controller' => 'DealResource', 'source_action' => 'update'],
    'admin.crm.deals.delete' => ['handler' => $class, 'method' => 'delete', 'parameters' => [$input, $deal], 'command' => true,
        'context' => 'CRM', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'crm:write',
        'api_path' => 'crm/deals/{deal}', 'http_methods' => ['DELETE'], 'source_controller' => 'DealResource', 'source_action' => 'delete'],
];
