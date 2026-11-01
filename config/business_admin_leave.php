<?php

$class = 'App\\Contexts\\Workforce\\Application\\Commands\\AdminLeaveCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$leave = ['name' => 'leave', 'kind' => 'model', 'class' => 'App\\Models\\LeaveRequest', 'key' => 'id'];
$definitions = [];
$add = function (string $name, string $method, string $path, array $params, string $action) use (&$definitions, $class): void {
    $definitions[$name] = ['handler' => $class, 'method' => $method, 'parameters' => $params, 'command' => true,
        'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
        'scope' => 'workforce:write', 'api_path' => $path, 'http_methods' => ['POST'],
        'source_controller' => 'LeaveRequestResource', 'source_action' => $action];
};
$add('admin.workforce.leaves.approve', 'approve', 'admin/leaves/{leave}/approve', [$input, $leave], 'approve');
$add('admin.workforce.leaves.reject', 'reject', 'admin/leaves/{leave}/reject', [$input, $leave], 'reject');
$add('admin.workforce.leaves.bulkApprove', 'bulkApprove', 'admin/leaves/bulk-approve', [$input], 'bulk_approve');
$add('admin.workforce.leaves.bulkReject', 'bulkReject', 'admin/leaves/bulk-reject', [$input], 'bulk_reject');
return $definitions;
