<?php

$class = 'App\\Contexts\\Workforce\\Application\\Commands\\AdminPeopleCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$risk = ['name' => 'risk', 'kind' => 'model', 'class' => 'App\\Models\\Risk', 'key' => 'id'];
$trainingRecord = ['name' => 'trainingRecord', 'kind' => 'model', 'class' => 'App\\Models\\TrainingRecord', 'key' => 'id'];
$user = ['name' => 'user', 'kind' => 'model', 'class' => 'App\\Models\\User', 'key' => 'id'];
return [
    'admin.identity.users.create' => ['handler' => $class, 'method' => 'createUser', 'parameters' => [$input], 'command' => true,
        'context' => 'Identity', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'administration:write',
        'api_path' => 'admin/users', 'http_methods' => ['POST'], 'source_controller' => 'UserResource', 'source_action' => 'create'],
    'admin.identity.users.update' => ['handler' => $class, 'method' => 'updateUser', 'parameters' => [$input, $user], 'command' => true,
        'context' => 'Identity', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'administration:write',
        'api_path' => 'admin/users/{user}', 'http_methods' => ['PUT'], 'source_controller' => 'UserResource', 'source_action' => 'update'],
    'admin.identity.users.delete' => ['handler' => $class, 'method' => 'deleteUser', 'parameters' => [$input, $user], 'command' => true,
        'context' => 'Identity', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'administration:write',
        'api_path' => 'admin/users/{user}', 'http_methods' => ['DELETE'], 'source_controller' => 'UserResource', 'source_action' => 'delete'],
    'admin.workforce.risks.create' => ['handler' => $class, 'method' => 'createRisk', 'parameters' => [$input], 'command' => true,
        'context' => 'Risk', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'risk:write',
        'api_path' => 'risk', 'http_methods' => ['POST'], 'source_controller' => 'RiskResource', 'source_action' => 'create'],
    'admin.workforce.risks.update' => ['handler' => $class, 'method' => 'updateRisk', 'parameters' => [$input, $risk], 'command' => true,
        'context' => 'Risk', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'risk:write',
        'api_path' => 'risk/{risk}', 'http_methods' => ['PUT'], 'source_controller' => 'RiskResource', 'source_action' => 'update'],
    'admin.risk.mitigate' => ['handler' => $class, 'method' => 'mitigateRisk', 'parameters' => [$input, $risk], 'command' => true,
        'context' => 'Risk', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'risk:write',
        'api_path' => 'risk/{risk}/mitigate', 'http_methods' => ['POST'], 'source_controller' => 'RiskResource', 'source_action' => 'mitigate'],
    'admin.workforce.training.complete' => ['handler' => $class, 'method' => 'completeTraining', 'parameters' => [$input, $trainingRecord], 'command' => true,
        'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:write',
        'api_path' => 'training-records/{trainingRecord}/complete', 'http_methods' => ['POST'], 'source_controller' => 'TrainingRecordResource', 'source_action' => 'complete'],
    'admin.workforce.training.index' => ['handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\AdminTrainingRecordQueries', 'method' => 'index', 'parameters' => [$input], 'command' => false,
        'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:read', 'api_path' => 'training-records', 'http_methods' => ['GET'], 'source_controller' => 'TrainingRecordResource', 'source_action' => 'index'],
    'admin.workforce.training.show' => ['handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\AdminTrainingRecordQueries', 'method' => 'show', 'parameters' => [$input, $trainingRecord], 'command' => false,
        'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:read', 'api_path' => 'training-records/{trainingRecord}', 'http_methods' => ['GET'], 'source_controller' => 'TrainingRecordResource', 'source_action' => 'show'],
    'admin.workforce.training.create' => ['handler' => $class, 'method' => 'createTraining', 'parameters' => [$input], 'command' => true,
        'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:write', 'api_path' => 'training-records', 'http_methods' => ['POST'], 'source_controller' => 'TrainingRecordResource', 'source_action' => 'create'],
    'admin.workforce.training.update' => ['handler' => $class, 'method' => 'updateTraining', 'parameters' => [$input, $trainingRecord], 'command' => true,
        'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:write', 'api_path' => 'training-records/{trainingRecord}', 'http_methods' => ['PUT'], 'source_controller' => 'TrainingRecordResource', 'source_action' => 'update'],
    'admin.workforce.training.delete' => ['handler' => $class, 'method' => 'deleteTraining', 'parameters' => [$input, $trainingRecord], 'command' => true,
        'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:write', 'api_path' => 'training-records/{trainingRecord}', 'http_methods' => ['DELETE'], 'source_controller' => 'TrainingRecordResource', 'source_action' => 'delete'],
    'admin.identity.users.sendWelcome' => ['handler' => $class, 'method' => 'sendWelcome', 'parameters' => [$input, $user], 'command' => true,
        'context' => 'Identity', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'administration:write',
        'api_path' => 'admin/users/{user}/send-welcome', 'http_methods' => ['POST'], 'source_controller' => 'UserResource', 'source_action' => 'send_welcome'],
];
