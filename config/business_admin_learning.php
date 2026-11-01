<?php

$class = 'App\\Contexts\\Learning\\Application\\Commands\\AdminLearningCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$enrollment = ['name' => 'enrollment', 'kind' => 'model', 'class' => 'App\\Models\\CourseEnrollment', 'key' => 'id'];
$queries = 'App\\Contexts\\Learning\\Application\\Queries\\AdminLearningRecordQueries';

return [
    'admin.learning.enrollments.index' => ['handler' => $queries, 'method' => 'enrollments', 'parameters' => [$input], 'command' => false,
        'context' => 'Learning', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'learning:read',
        'api_path' => 'learning/enrollments', 'http_methods' => ['GET'], 'source_controller' => 'CourseEnrollmentResource', 'source_action' => 'index'],
    'admin.learning.certificates.index' => ['handler' => $queries, 'method' => 'certificates', 'parameters' => [$input], 'command' => false,
        'context' => 'Learning', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'learning:read',
        'api_path' => 'learning/certificates', 'http_methods' => ['GET'], 'source_controller' => 'CourseCertificateResource', 'source_action' => 'index'],
    'admin.learning.enrollments.complete' => ['handler' => $class, 'method' => 'completeEnrollment', 'parameters' => [$input, $enrollment], 'command' => true,
        'context' => 'Learning', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'learning:write',
        'api_path' => 'learning/enrollments/{enrollment}/complete', 'http_methods' => ['POST'],
        'source_controller' => 'CourseResource', 'source_action' => 'mark_completed'],
];
