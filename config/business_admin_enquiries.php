<?php

$class = 'App\\Contexts\\Enquiries\\Application\\Commands\\AdminLeadCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$lead = ['name' => 'lead', 'kind' => 'model', 'class' => 'App\\Models\\Lead', 'key' => 'id'];

return [
    'admin.enquiries.leads.sendFollowUp' => ['handler' => $class, 'method' => 'sendFollowUp', 'parameters' => [$input, $lead], 'command' => true,
        'context' => 'Enquiries', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'enquiries:write',
        'api_path' => 'admin/enquiries/leads/{lead}/follow-up', 'http_methods' => ['POST'],
        'source_controller' => 'LeadResource', 'source_action' => 'send_email'],
];
