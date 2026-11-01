<?php

$class = 'App\\Contexts\\Enquiries\\Application\\Commands\\AdminLeadCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$lead = ['name' => 'lead', 'kind' => 'model', 'class' => 'App\\Models\\Lead', 'key' => 'id'];
$queries = 'App\\Contexts\\Enquiries\\Application\\Queries\\AdminLeadQueries';

return [
    'admin.enquiries.leads.index' => ['handler' => $queries, 'method' => 'leads', 'parameters' => [$input], 'command' => false,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:read',
        'api_path' => 'admin/enquiries/leads', 'http_methods' => ['GET'], 'source_controller' => 'LeadResource', 'source_action' => 'index'],
    'admin.enquiries.leads.show' => ['handler' => $queries, 'method' => 'lead', 'parameters' => [$input, $lead], 'command' => false,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:read',
        'api_path' => 'admin/enquiries/leads/{lead}', 'http_methods' => ['GET'], 'source_controller' => 'LeadResource', 'source_action' => 'view'],
    'admin.enquiries.leads.create' => ['handler' => $class, 'method' => 'create', 'parameters' => [$input], 'command' => true,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:write',
        'api_path' => 'admin/enquiries/leads', 'http_methods' => ['POST'], 'source_controller' => 'LeadResource', 'source_action' => 'create'],
    'admin.enquiries.leads.update' => ['handler' => $class, 'method' => 'update', 'parameters' => [$input, $lead], 'command' => true,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:write',
        'api_path' => 'admin/enquiries/leads/{lead}', 'http_methods' => ['PUT'], 'source_controller' => 'LeadResource', 'source_action' => 'update'],
    'admin.enquiries.leads.delete' => ['handler' => $class, 'method' => 'delete', 'parameters' => [$input, $lead], 'command' => true,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:write',
        'api_path' => 'admin/enquiries/leads/{lead}', 'http_methods' => ['DELETE'], 'source_controller' => 'LeadResource', 'source_action' => 'delete'],
    'admin.enquiries.leads.sendFollowUp' => ['handler' => $class, 'method' => 'sendFollowUp', 'parameters' => [$input, $lead], 'command' => true,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:write',
        'api_path' => 'admin/enquiries/leads/{lead}/follow-up', 'http_methods' => ['POST'],
        'source_controller' => 'LeadResource', 'source_action' => 'send_email'],
];
