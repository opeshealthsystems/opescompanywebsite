<?php

$class = 'App\\Contexts\\Learning\\Application\\Commands\\AdminLearningCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$enrollment = ['name' => 'enrollment', 'kind' => 'model', 'class' => 'App\\Models\\CourseEnrollment', 'key' => 'id'];

return [
    'admin.learning.enrollments.complete' => ['handler' => $class, 'method' => 'completeEnrollment', 'parameters' => [$input, $enrollment], 'command' => true,
        'context' => 'Learning', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'learning:write',
        'api_path' => 'learning/enrollments/{enrollment}/complete', 'http_methods' => ['POST'],
        'source_controller' => 'CourseResource', 'source_action' => 'mark_completed'],
];
