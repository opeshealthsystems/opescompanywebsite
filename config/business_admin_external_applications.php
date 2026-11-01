<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$partner = ['name' => 'partnerApplication', 'kind' => 'model', 'class' => 'App\\Models\\PartnerApplication', 'key' => 'id'];
$tester = ['name' => 'testerApplication', 'kind' => 'model', 'class' => 'App\\Models\\TesterApplication', 'key' => 'id'];
$queries = 'App\\Contexts\\Enquiries\\Application\\Queries\\AdminExternalApplicationQueries';
$commands = 'App\\Contexts\\Enquiries\\Application\\Commands\\AdminExternalApplicationCommands';
$read = ['context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'crm:read'];
$write = ['context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'crm:write'];

return [
    'admin.enquiries.partnerApplications.index' => ['handler' => $queries, 'method' => 'partners', 'parameters' => [$input], 'command' => false] + $read + ['api_path' => 'crm/partner-applications', 'http_methods' => ['GET'], 'source_controller' => 'PartnerApplicationResource', 'source_action' => 'index'],
    'admin.enquiries.partnerApplications.show' => ['handler' => $queries, 'method' => 'partner', 'parameters' => [$input, $partner], 'command' => false] + $read + ['api_path' => 'crm/partner-applications/{partnerApplication}', 'http_methods' => ['GET'], 'source_controller' => 'PartnerApplicationResource', 'source_action' => 'show'],
    'admin.enquiries.partnerApplications.update' => ['handler' => $commands, 'method' => 'updatePartner', 'parameters' => [$input, $partner], 'command' => true] + $write + ['api_path' => 'crm/partner-applications/{partnerApplication}', 'http_methods' => ['PATCH'], 'source_controller' => 'PartnerApplicationResource', 'source_action' => 'update'],
    'admin.enquiries.testerApplications.index' => ['handler' => $queries, 'method' => 'testers', 'parameters' => [$input], 'command' => false] + $read + ['api_path' => 'crm/tester-applications', 'http_methods' => ['GET'], 'source_controller' => 'TesterApplicationResource', 'source_action' => 'index'],
    'admin.enquiries.testerApplications.show' => ['handler' => $queries, 'method' => 'tester', 'parameters' => [$input, $tester], 'command' => false] + $read + ['api_path' => 'crm/tester-applications/{testerApplication}', 'http_methods' => ['GET'], 'source_controller' => 'TesterApplicationResource', 'source_action' => 'show'],
    'admin.enquiries.testerApplications.update' => ['handler' => $commands, 'method' => 'updateTester', 'parameters' => [$input, $tester], 'command' => true] + $write + ['api_path' => 'crm/tester-applications/{testerApplication}', 'http_methods' => ['PATCH'], 'source_controller' => 'TesterApplicationResource', 'source_action' => 'update'],
];
