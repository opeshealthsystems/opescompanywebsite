<?php

$class = 'App\\Contexts\\Enquiries\\Application\\Commands\\AdminLeadCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$lead = ['name' => 'lead', 'kind' => 'model', 'class' => 'App\\Models\\Lead', 'key' => 'id'];
$queries = 'App\\Contexts\\Enquiries\\Application\\Queries\\AdminLeadQueries';
$deliveryQueries = 'App\\Contexts\\Enquiries\\Application\\Queries\\AdminWebsiteFormDeliveryQueries';
$deliveryCommands = 'App\\Contexts\\Enquiries\\Application\\Commands\\AdminWebsiteFormDeliveryCommands';
$delivery = ['name' => 'eventId', 'kind' => 'value', 'route_key' => 'event'];

return [
    'admin.enquiries.leads.index' => ['handler' => $queries, 'method' => 'leads', 'parameters' => [$input], 'command' => false,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:read',
        'api_path' => 'admin/enquiries/leads', 'http_methods' => ['GET'], 'source_controller' => 'LeadResource', 'source_action' => 'index'],
    'admin.enquiries.leads.show' => ['handler' => $queries, 'method' => 'lead', 'parameters' => [$input, $lead], 'command' => false,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:read',
        'api_path' => 'admin/enquiries/leads/{lead}', 'http_methods' => ['GET'], 'source_controller' => 'LeadResource', 'source_action' => 'view'],
    'admin.enquiries.integrationRequests.index' => ['handler' => $queries, 'method' => 'integrationRequests', 'parameters' => [$input], 'command' => false,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:read',
        'api_path' => 'admin/enquiries/integration-requests', 'http_methods' => ['GET'], 'source_controller' => 'LeadResource', 'source_action' => 'integrationRequests'],
    'admin.enquiries.integrationRequests.show' => ['handler' => $queries, 'method' => 'integrationRequest', 'parameters' => [$input, $lead], 'command' => false,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:read',
        'api_path' => 'admin/enquiries/integration-requests/{lead}', 'http_methods' => ['GET'], 'source_controller' => 'LeadResource', 'source_action' => 'integrationRequest'],
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
    'admin.enquiries.deliveries.index' => ['handler' => $deliveryQueries, 'method' => 'index', 'parameters' => [$input], 'command' => false,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:read',
        'api_path' => 'admin/enquiries/deliveries', 'http_methods' => ['GET'], 'source_controller' => 'WebsiteFormDeliveryOperations', 'source_action' => 'index'],
    'admin.enquiries.deliveries.show' => ['handler' => $deliveryQueries, 'method' => 'show', 'parameters' => [$input, $delivery], 'command' => false,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:read',
        'api_path' => 'admin/enquiries/deliveries/{event}', 'http_methods' => ['GET'], 'source_controller' => 'WebsiteFormDeliveryOperations', 'source_action' => 'show'],
    'admin.enquiries.deliveries.confirmReceived' => ['handler' => $deliveryCommands, 'method' => 'confirmReceived', 'parameters' => [$input, $delivery], 'command' => true,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:write',
        'api_path' => 'admin/enquiries/deliveries/{event}/resolve', 'http_methods' => ['POST'], 'source_controller' => 'WebsiteFormDeliveryOperations', 'source_action' => 'confirm_received'],
];
