<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$cohort = ['name' => 'cohort', 'kind' => 'model', 'class' => 'App\\Models\\Cohort', 'key' => 'id'];
$member = ['name' => 'member', 'kind' => 'model', 'class' => 'App\\Models\\CohortMember', 'key' => 'id'];
$testCase = ['name' => 'testCase', 'kind' => 'model', 'class' => 'App\\Models\\ValidationTestCase', 'key' => 'id'];
$commands = 'App\\Contexts\\Validation\\Application\\Commands\\AdminCohortCommands';
$queries = 'App\\Contexts\\Validation\\Application\\Queries\\AdminCohortQueries';
$def = fn (string $handler, string $method, array $parameters, bool $command, string $path, string $verb, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $parameters, 'command' => $command, 'context' => 'Validation',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $command ? 'validation:write' : 'validation:read',
    'api_path' => $path, 'http_methods' => [$verb], 'source_controller' => 'CohortResource', 'source_action' => $action,
];

return [
    'admin.validation.cohorts.index' => $def($queries, 'index', [$input], false, 'validation/cohorts', 'GET', 'index'),
    'admin.validation.cohorts.show' => $def($queries, 'show', [$input, $cohort], false, 'validation/cohorts/{cohort}', 'GET', 'show'),
    'admin.validation.cohorts.create' => $def($commands, 'create', [$input], true, 'validation/cohorts', 'POST', 'create'),
    'admin.validation.cohorts.update' => $def($commands, 'update', [$input, $cohort], true, 'validation/cohorts/{cohort}', 'PUT', 'update'),
    'admin.validation.cohorts.delete' => $def($commands, 'delete', [$input, $cohort], true, 'validation/cohorts/{cohort}', 'DELETE', 'delete'),
    'admin.validation.cohortMembers.update' => $def($commands, 'updateMember', [$input, $cohort, $member], true, 'validation/cohorts/{cohort}/members/{member}', 'PUT', 'members.update'),
    'admin.validation.cohortTestCases.attach' => $def($commands, 'attachTestCase', [$input, $cohort], true, 'validation/cohorts/{cohort}/test-cases', 'POST', 'testCases.attach'),
    'admin.validation.cohortTestCases.detach' => $def($commands, 'detachTestCase', [$input, $cohort, $testCase], true, 'validation/cohorts/{cohort}/test-cases/{testCase}', 'DELETE', 'testCases.detach'),
];
