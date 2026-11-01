<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$member = ['name' => 'member', 'kind' => 'model', 'class' => 'App\\Models\\AdvisoryCouncilMember', 'key' => 'id'];
$commands = 'App\\Contexts\\Validation\\Application\\Commands\\AdminCouncilMemberCommands';
$queries = 'App\\Contexts\\Validation\\Application\\Queries\\AdminCouncilMemberQueries';
$def = fn (string $handler, string $method, array $parameters, bool $command, string $verb, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $parameters, 'command' => $command, 'context' => 'Validation',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $command ? 'validation:write' : 'validation:read',
    'api_path' => 'validation/council-members'.(in_array($method, ['show', 'update'], true) ? '/{member}' : ''),
    'http_methods' => [$verb], 'source_controller' => 'AdvisoryCouncilMemberResource', 'source_action' => $action,
];

return [
    'admin.validation.councilMembers.index' => $def($queries, 'index', [$input], false, 'GET', 'index'),
    'admin.validation.councilMembers.show' => $def($queries, 'show', [$input, $member], false, 'GET', 'show'),
    'admin.validation.councilMembers.update' => $def($commands, 'update', [$input, $member], true, 'PUT', 'update'),
];
