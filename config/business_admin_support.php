<?php

$class = 'App\\Contexts\\Support\\Application\\Commands\\AdminSupportCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$bugReport = ['name' => 'bugReport', 'kind' => 'model', 'class' => 'App\\Models\\PractitionerBugReport', 'key' => 'id'];
$serviceRequest = ['name' => 'serviceRequest', 'kind' => 'model', 'class' => 'App\\Models\\ServiceRequest', 'key' => 'id'];
$ticket = ['name' => 'ticket', 'kind' => 'model', 'class' => 'App\\Models\\Ticket', 'key' => 'id'];
$definitions = [];
$add = function (string $name, string $method, string $path, array $parameters, string $source, string $action) use (&$definitions, $class): void {
    $definitions[$name] = ['handler' => $class, 'method' => $method, 'parameters' => $parameters, 'command' => true,
        'context' => 'Support', 'roles' => ['super_admin', 'admin', 'support'], 'authenticated' => true,
        'scope' => 'support:write', 'api_path' => $path, 'http_methods' => ['POST'],
        'source_controller' => $source, 'source_action' => $action];
};
$add('admin.support.bugReports.respond', 'respondToBugReport', 'practitioners/bug-reports/{bugReport}/respond', [$input, $bugReport], 'PractitionerBugReportResource', 'respond');
$add('admin.support.serviceRequests.confirm', 'confirmServiceRequest', 'support/service-requests/{serviceRequest}/confirm-assign', [$input, $serviceRequest], 'ServiceRequestResource', 'confirm_assign');
$add('admin.support.serviceRequests.complete', 'completeServiceRequest', 'support/service-requests/{serviceRequest}/complete', [$input, $serviceRequest], 'ServiceRequestResource', 'complete');
$add('admin.support.tickets.reply', 'replyToTicket', 'admin/support/tickets/{ticket}/reply', [$input, $ticket], 'TicketResource', 'reply');
$add('admin.support.tickets.status', 'changeTicketStatus', 'admin/support/tickets/{ticket}/status', [$input, $ticket], 'TicketResource', 'change_status');
return $definitions;
