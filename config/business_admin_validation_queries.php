<?php

return [
    'admin.validation.finalEvaluations.list' => [
        'handler' => 'App\\Contexts\\Validation\\Application\\Queries\\AdminFinalEvaluationQueries',
        'method' => 'index',
        'parameters' => [['name' => 'workflowInput', 'kind' => 'input']],
        'command' => false,
        'context' => 'Validation',
        'roles' => ['super_admin', 'admin'],
        'authenticated' => true,
        'scope' => 'validation:read',
        'api_path' => 'validation/final-evaluations',
        'http_methods' => ['GET'],
        'source_controller' => 'FinalEvaluationResource',
        'source_action' => 'list',
    ],
];
