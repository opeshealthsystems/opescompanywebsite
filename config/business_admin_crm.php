<?php

$class = 'App\\Contexts\\Operations\\Application\\Commands\\AdminDealCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$deal = ['name' => 'deal', 'kind' => 'model', 'class' => 'App\\Models\\Deal', 'key' => 'id'];
$queries = 'App\\Contexts\\Operations\\Application\\Queries\\AdminDealQueries';
return [
    'admin.crm.deals.index' => ['handler' => $queries, 'method' => 'index', 'parameters' => [$input], 'command' => false,
        'context' => 'CRM', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'crm:read',
        'api_path' => 'crm/deals', 'http_methods' => ['GET'], 'source_controller' => 'DealResource', 'source_action' => 'index'],
    'admin.crm.deals.show' => ['handler' => $queries, 'method' => 'show', 'parameters' => [$input, $deal], 'command' => false,
        'context' => 'CRM', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'crm:read',
        'api_path' => 'crm/deals/{deal}', 'http_methods' => ['GET'], 'source_controller' => 'DealResource', 'source_action' => 'show'],
    'admin.crm.deals.pipeline' => ['handler' => $queries, 'method' => 'pipeline', 'parameters' => [$input], 'command' => false,
        'context' => 'CRM', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'crm:read',
        'api_path' => 'crm/deals/pipeline', 'http_methods' => ['GET'], 'source_controller' => 'DealPipeline', 'source_action' => 'pipeline'],
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
