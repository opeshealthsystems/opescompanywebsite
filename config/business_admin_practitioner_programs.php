<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$program = ['name' => 'program', 'kind' => 'model', 'class' => 'App\\Models\\PractitionerProgram', 'key' => 'id'];
$queries = 'App\\Contexts\\Programmes\\Application\\Queries\\AdminPractitionerProgramQueries';
$commands = 'App\\Contexts\\Programmes\\Application\\Commands\\AdminPractitionerProgramCommands';
$read = ['context' => 'Programmes', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'practitioners:read'];
$write = ['context' => 'Programmes', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'practitioners:write'];

return [
    'admin.practitioners.programs.index' => ['handler' => $queries, 'method' => 'index', 'parameters' => [$input], 'command' => false] + $read + ['api_path' => 'practitioners/programs', 'http_methods' => ['GET'], 'source_controller' => 'PractitionerProgramResource', 'source_action' => 'index'],
    'admin.practitioners.programs.show' => ['handler' => $queries, 'method' => 'show', 'parameters' => [$input, $program], 'command' => false] + $read + ['api_path' => 'practitioners/programs/{program}', 'http_methods' => ['GET'], 'source_controller' => 'PractitionerProgramResource', 'source_action' => 'show'],
    'admin.practitioners.programs.create' => ['handler' => $commands, 'method' => 'create', 'parameters' => [$input], 'command' => true] + $write + ['api_path' => 'practitioners/programs', 'http_methods' => ['POST'], 'source_controller' => 'PractitionerProgramResource', 'source_action' => 'create'],
    'admin.practitioners.programs.update' => ['handler' => $commands, 'method' => 'update', 'parameters' => [$input, $program], 'command' => true] + $write + ['api_path' => 'practitioners/programs/{program}', 'http_methods' => ['PUT'], 'source_controller' => 'PractitionerProgramResource', 'source_action' => 'update'],
];
