<?php

$class = 'App\\Contexts\\Workforce\\Application\\Commands\\AdminPeopleCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$risk = ['name' => 'risk', 'kind' => 'model', 'class' => 'App\\Models\\Risk', 'key' => 'id'];
$trainingRecord = ['name' => 'trainingRecord', 'kind' => 'model', 'class' => 'App\\Models\\TrainingRecord', 'key' => 'id'];
$user = ['name' => 'user', 'kind' => 'model', 'class' => 'App\\Models\\User', 'key' => 'id'];
return [
    'admin.risk.mitigate' => ['handler' => $class, 'method' => 'mitigateRisk', 'parameters' => [$input, $risk], 'command' => true,
        'context' => 'Risk', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'risk:write',
        'api_path' => 'risk/{risk}/mitigate', 'http_methods' => ['POST'], 'source_controller' => 'RiskResource', 'source_action' => 'mitigate'],
    'admin.workforce.training.complete' => ['handler' => $class, 'method' => 'completeTraining', 'parameters' => [$input, $trainingRecord], 'command' => true,
        'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:write',
        'api_path' => 'training-records/{trainingRecord}/complete', 'http_methods' => ['POST'], 'source_controller' => 'TrainingRecordResource', 'source_action' => 'complete'],
    'admin.identity.users.sendWelcome' => ['handler' => $class, 'method' => 'sendWelcome', 'parameters' => [$input, $user], 'command' => true,
        'context' => 'Identity', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'administration:write',
        'api_path' => 'admin/users/{user}/send-welcome', 'http_methods' => ['POST'], 'source_controller' => 'UserResource', 'source_action' => 'send_welcome'],
];
