<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$profile = ['name' => 'profile', 'kind' => 'model', 'class' => 'App\\Models\\PractitionerProfile', 'key' => 'id'];
$finding = ['name' => 'finding', 'kind' => 'model', 'class' => 'App\\Models\\PractitionerFinding', 'key' => 'id'];
$application = ['name' => 'application', 'kind' => 'model', 'class' => 'App\\Models\\PractitionerApplication', 'key' => 'id'];
$queries = 'App\\Contexts\\Programmes\\Application\\Queries\\AdminPractitionerQueries';
$commands = 'App\\Contexts\\Programmes\\Application\\Commands\\AdminPractitionerRecordCommands';
$read = ['context' => 'Programmes', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'practitioners:read'];
$write = ['context' => 'Programmes', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'practitioners:write'];

return [
    'admin.practitioners.profiles.index' => ['handler' => $queries, 'method' => 'profiles', 'parameters' => [$input], 'command' => false] + $read + ['api_path' => 'practitioners/profiles', 'http_methods' => ['GET'], 'source_controller' => 'PractitionerProfileResource', 'source_action' => 'index'],
    'admin.practitioners.profiles.show' => ['handler' => $queries, 'method' => 'profile', 'parameters' => [$input, $profile], 'command' => false] + $read + ['api_path' => 'practitioners/profiles/{profile}', 'http_methods' => ['GET'], 'source_controller' => 'PractitionerProfileResource', 'source_action' => 'show'],
    'admin.practitioners.profiles.update' => ['handler' => $commands, 'method' => 'updateProfile', 'parameters' => [$input, $profile], 'command' => true] + $write + ['api_path' => 'practitioners/profiles/{profile}', 'http_methods' => ['PUT'], 'source_controller' => 'PractitionerProfileResource', 'source_action' => 'update'],
    'admin.practitioners.findings.index' => ['handler' => $queries, 'method' => 'findings', 'parameters' => [$input], 'command' => false] + $read + ['api_path' => 'practitioners/findings', 'http_methods' => ['GET'], 'source_controller' => 'PractitionerFindingResource', 'source_action' => 'index'],
    'admin.practitioners.findings.show' => ['handler' => $queries, 'method' => 'finding', 'parameters' => [$input, $finding], 'command' => false] + $read + ['api_path' => 'practitioners/findings/{finding}', 'http_methods' => ['GET'], 'source_controller' => 'PractitionerFindingResource', 'source_action' => 'show'],
    'admin.practitioners.findings.update' => ['handler' => $commands, 'method' => 'updateFinding', 'parameters' => [$input, $finding], 'command' => true] + $write + ['api_path' => 'practitioners/findings/{finding}', 'http_methods' => ['PUT'], 'source_controller' => 'PractitionerFindingResource', 'source_action' => 'update'],
    'admin.practitioners.applications.index' => ['handler' => $queries, 'method' => 'applications', 'parameters' => [$input], 'command' => false] + $read + ['api_path' => 'practitioners/applications', 'http_methods' => ['GET'], 'source_controller' => 'PractitionerApplicationResource', 'source_action' => 'index'],
    'admin.practitioners.applications.show' => ['handler' => $queries, 'method' => 'application', 'parameters' => [$input, $application], 'command' => false] + $read + ['api_path' => 'practitioners/applications/{application}', 'http_methods' => ['GET'], 'source_controller' => 'PractitionerApplicationResource', 'source_action' => 'show'],
    'admin.practitioners.applications.updateNotes' => ['handler' => $commands, 'method' => 'updateApplicationNotes', 'parameters' => [$input, $application], 'command' => true] + $write + ['api_path' => 'practitioners/applications/{application}', 'http_methods' => ['PUT'], 'source_controller' => 'PractitionerApplicationResource', 'source_action' => 'update_notes'],
];
