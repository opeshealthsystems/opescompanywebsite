<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$institution = ['name' => 'institution', 'kind' => 'model', 'class' => 'App\\Models\\PartnerInstitution', 'key' => 'id'];
$queries = 'App\\Contexts\\Publishing\\Application\\Queries\\AdminPartnerInstitutionQueries';
$commands = 'App\\Contexts\\Publishing\\Application\\Commands\\AdminPartnerInstitutionCommands';
$read = ['context' => 'Publishing', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'publishing:read'];
$write = ['context' => 'Publishing', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'publishing:write'];

return [
    'admin.publishing.partnerInstitutions.index' => ['handler' => $queries, 'method' => 'index', 'parameters' => [$input], 'command' => false] + $read + ['api_path' => 'partnerships/institutions', 'http_methods' => ['GET'], 'source_controller' => 'PartnerInstitutionResource', 'source_action' => 'index'],
    'admin.publishing.partnerInstitutions.show' => ['handler' => $queries, 'method' => 'show', 'parameters' => [$input, $institution], 'command' => false] + $read + ['api_path' => 'partnerships/institutions/{institution}', 'http_methods' => ['GET'], 'source_controller' => 'PartnerInstitutionResource', 'source_action' => 'show'],
    'admin.publishing.partnerInstitutions.create' => ['handler' => $commands, 'method' => 'create', 'parameters' => [$input], 'command' => true] + $write + ['api_path' => 'partnerships/institutions', 'http_methods' => ['POST'], 'source_controller' => 'PartnerInstitutionResource', 'source_action' => 'create'],
    'admin.publishing.partnerInstitutions.update' => ['handler' => $commands, 'method' => 'update', 'parameters' => [$input, $institution], 'command' => true] + $write + ['api_path' => 'partnerships/institutions/{institution}', 'http_methods' => ['PUT'], 'source_controller' => 'PartnerInstitutionResource', 'source_action' => 'update'],
];
