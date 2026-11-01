<?php

$class = 'App\\Contexts\\Testing\\Application\\Commands\\AdminTesterAssignmentCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$assignment = ['name' => 'testerAssignment', 'kind' => 'model', 'class' => 'App\\Models\\TesterAssignment', 'key' => 'id'];
return [
    'admin.testing.assignments.index' => ['handler' => 'App\\Contexts\\Testing\\Application\\Queries\\AdminTesterAssignmentQueries', 'method' => 'index',
        'parameters' => [$input], 'command' => false, 'context' => 'Testing', 'roles' => ['super_admin', 'admin', 'support'], 'authenticated' => true,
        'scope' => 'testing:read', 'api_path' => 'admin/testing/assignments', 'http_methods' => ['GET'],
        'source_controller' => 'TesterAssignmentResource', 'source_action' => 'index'],
    'admin.testing.assignments.create' => ['handler' => $class, 'method' => 'create', 'parameters' => [$input], 'command' => true,
        'context' => 'Testing', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'testing:write',
        'api_path' => 'admin/testing/assignments', 'http_methods' => ['POST'], 'source_controller' => 'TesterAssignmentResource', 'source_action' => 'create'],
    'admin.testing.assignments.update' => ['handler' => $class, 'method' => 'update', 'parameters' => [$input, $assignment], 'command' => true,
        'context' => 'Testing', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'testing:write',
        'api_path' => 'admin/testing/assignments/{testerAssignment}', 'http_methods' => ['PUT'], 'source_controller' => 'TesterAssignmentResource', 'source_action' => 'update'],
    'admin.testing.assignments.delete' => ['handler' => $class, 'method' => 'delete', 'parameters' => [$input, $assignment], 'command' => true,
        'context' => 'Testing', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'testing:write',
        'api_path' => 'admin/testing/assignments/{testerAssignment}', 'http_methods' => ['DELETE'], 'source_controller' => 'TesterAssignmentResource', 'source_action' => 'delete'],
];
