<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$application = ['name' => 'application', 'kind' => 'model', 'class' => 'App\\Models\\PractitionerApplication', 'key' => 'id'];

return [
    'admin.practitioners.applications.pay' => [
        'handler' => 'App\\Contexts\\Programmes\\Application\\Commands\\AdminPractitionerPayoutCommands',
        'method' => 'request',
        'parameters' => [$input, $application],
        'command' => true,
        'context' => 'Programmes',
        'roles' => ['super_admin', 'admin'],
        'authenticated' => true,
        'scope' => 'practitioners:write',
        'api_path' => 'practitioners/applications/{application}/payout',
        'http_methods' => ['POST'],
        'source_controller' => 'PractitionerApplicationResource',
        'source_action' => 'pay',
    ],
];
