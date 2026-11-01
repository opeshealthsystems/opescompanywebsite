<?php

$class = 'App\\Contexts\\Operations\\Application\\Commands\\AdminMilestoneCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$milestone = ['name' => 'milestone', 'kind' => 'model', 'class' => 'App\\Models\\Milestone', 'key' => 'id'];

return [
    'admin.workforce.milestones.complete' => ['handler' => $class, 'method' => 'complete', 'parameters' => [$input, $milestone], 'command' => true,
        'context' => 'Operations', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'operations:write',
        'api_path' => 'operations/milestones/{milestone}/complete', 'http_methods' => ['POST'],
        'source_controller' => 'ProjectResource', 'source_action' => 'complete_milestone'],
];
