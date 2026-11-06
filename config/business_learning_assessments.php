<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$course = ['name' => 'course', 'kind' => 'model', 'class' => 'App\\Models\\Course', 'key' => 'id'];
$assessment = ['name' => 'assessment', 'kind' => 'model', 'class' => 'App\\Models\\CourseAssessment', 'key' => 'id'];
$attempt = ['name' => 'attempt', 'kind' => 'model', 'class' => 'App\\Models\\CourseAssessmentAttempt', 'key' => 'id'];
$adminQueries = 'App\\Contexts\\Learning\\Application\\Queries\\CourseAssessmentQueries';
$adminCommands = 'App\\Contexts\\Learning\\Application\\Commands\\CourseAssessmentCommands';
$definition = fn (string $handler, string $method, array $parameters, bool $command, array $roles, string $scope, string $path, string $verb, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $parameters, 'command' => $command, 'context' => 'Learning',
    'roles' => $roles, 'authenticated' => true, 'scope' => $scope, 'api_path' => $path, 'http_methods' => [$verb],
    'source_controller' => 'CourseAssessmentResource', 'source_action' => $action,
];

return [
    'admin.learning.assessments.index' => $definition($adminQueries, 'adminList', [$input, $course], false, ['super_admin', 'admin'], 'learning:read', 'learning/courses/{course}/assessments', 'GET', 'index'),
    'admin.learning.assessments.show' => $definition($adminQueries, 'adminShow', [$input, $course, $assessment], false, ['super_admin', 'admin'], 'learning:read', 'learning/courses/{course}/assessments/{assessment}', 'GET', 'show'),
    'admin.learning.assessments.create' => $definition($adminCommands, 'create', [$input, $course], true, ['super_admin', 'admin'], 'learning:write', 'learning/courses/{course}/assessments', 'POST', 'create'),
    'admin.learning.assessments.update' => $definition($adminCommands, 'update', [$input, $course, $assessment], true, ['super_admin', 'admin'], 'learning:write', 'learning/courses/{course}/assessments/{assessment}', 'PUT', 'update'),
    'admin.learning.assessments.delete' => $definition($adminCommands, 'delete', [$input, $course, $assessment], true, ['super_admin', 'admin'], 'learning:write', 'learning/courses/{course}/assessments/{assessment}', 'DELETE', 'delete'),
    'practitioner.assessments.index' => $definition($adminQueries, 'practitionerList', [$input, $course], false, ['practitioner'], 'practitioner:read', 'practitioner/courses/{course:slug}/assessments', 'GET', 'practitioner.index'),
    'practitioner.assessments.show' => $definition($adminQueries, 'practitionerShow', [$input, $assessment], false, ['practitioner'], 'practitioner:read', 'practitioner/assessments/{assessment}', 'GET', 'practitioner.show'),
    'practitioner.assessments.attempt' => $definition($adminQueries, 'attempt', [$input, $assessment, $attempt], false, ['practitioner'], 'practitioner:read', 'practitioner/assessments/{assessment}/attempts/{attempt}', 'GET', 'practitioner.attempt'),
    'practitioner.assessments.answers.save' => $definition($adminCommands, 'saveAnswers', [$input, $assessment, $attempt], true, ['practitioner'], 'practitioner:write', 'practitioner/assessments/{assessment}/attempts/{attempt}', 'PATCH', 'practitioner.saveAnswers'),
    'practitioner.assessments.attempts.start' => $definition($adminCommands, 'startAttempt', [$input, $assessment], true, ['practitioner'], 'practitioner:write', 'practitioner/assessments/{assessment}/attempts', 'POST', 'practitioner.start'),
    'practitioner.assessments.attempts.submit' => $definition($adminCommands, 'submitAttempt', [$input, $assessment, $attempt], true, ['practitioner'], 'practitioner:write', 'practitioner/assessments/{assessment}/attempts/{attempt}/submit', 'POST', 'practitioner.submit'),
];
