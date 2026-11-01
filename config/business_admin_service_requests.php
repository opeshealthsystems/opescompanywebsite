<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$serviceRequest = ['name' => 'serviceRequest', 'kind' => 'model', 'class' => 'App\\Models\\ServiceRequest', 'key' => 'id'];
$queries = 'App\\Contexts\\Support\\Application\\Queries\\AdminServiceRequestQueries';
$commands = 'App\\Contexts\\Support\\Application\\Commands\\AdminSupportCommands';
$read = ['context' => 'Support', 'roles' => ['super_admin', 'admin', 'support'], 'authenticated' => true, 'scope' => 'support:read'];
$write = ['context' => 'Support', 'roles' => ['super_admin', 'admin', 'support'], 'authenticated' => true, 'scope' => 'support:write'];

return [
    'admin.support.serviceRequests.index' => ['handler' => $queries, 'method' => 'index', 'parameters' => [$input], 'command' => false] + $read + ['api_path' => 'support/service-requests', 'http_methods' => ['GET'], 'source_controller' => 'ServiceRequestResource', 'source_action' => 'index'],
    'admin.support.serviceRequests.show' => ['handler' => $queries, 'method' => 'show', 'parameters' => [$input, $serviceRequest], 'command' => false] + $read + ['api_path' => 'support/service-requests/{serviceRequest}', 'http_methods' => ['GET'], 'source_controller' => 'ServiceRequestResource', 'source_action' => 'show'],
    'admin.support.serviceRequests.update' => ['handler' => $commands, 'method' => 'updateServiceRequest', 'parameters' => [$input, $serviceRequest], 'command' => true] + $write + ['api_path' => 'support/service-requests/{serviceRequest}', 'http_methods' => ['PATCH'], 'source_controller' => 'ServiceRequestResource', 'source_action' => 'update'],
    'admin.support.serviceRequests.cancel' => ['handler' => $commands, 'method' => 'cancelServiceRequest', 'parameters' => [$input, $serviceRequest], 'command' => true] + $write + ['api_path' => 'support/service-requests/{serviceRequest}/cancel', 'http_methods' => ['POST'], 'source_controller' => 'ServiceRequestResource', 'source_action' => 'cancel'],
];
