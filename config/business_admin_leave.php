<?php

$class = 'App\\Contexts\\Workforce\\Application\\Commands\\AdminLeaveCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$leave = ['name' => 'leave', 'kind' => 'model', 'class' => 'App\\Models\\LeaveRequest', 'key' => 'id'];
$employee = ['name' => 'employee', 'kind' => 'model', 'class' => 'App\\Models\\User', 'key' => 'id'];
$leaveBalance = ['name' => 'leaveBalance', 'kind' => 'model', 'class' => 'App\\Models\\LeaveBalance', 'key' => 'id'];
$queries = 'App\\Contexts\\Workforce\\Application\\Queries\\AdminLeaveQueries';
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
$definitions['admin.workforce.leaves.index'] = ['handler' => $queries, 'method' => 'index', 'parameters' => [$input], 'command' => false,
    'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:read', 'api_path' => 'admin/leaves',
    'http_methods' => ['GET'], 'source_controller' => 'LeaveRequestResource', 'source_action' => 'index'];
$definitions['admin.workforce.leaves.show'] = ['handler' => $queries, 'method' => 'show', 'parameters' => [$input, $leave], 'command' => false,
    'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:read', 'api_path' => 'admin/leaves/{leave}',
    'http_methods' => ['GET'], 'source_controller' => 'LeaveRequestResource', 'source_action' => 'show'];
$definitions['admin.workforce.leaves.create'] = ['handler' => $class, 'method' => 'create', 'parameters' => [$input], 'command' => true,
    'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:write', 'api_path' => 'admin/leaves',
    'http_methods' => ['POST'], 'source_controller' => 'LeaveRequestResource', 'source_action' => 'create'];
$definitions['admin.workforce.leaves.update'] = ['handler' => $class, 'method' => 'update', 'parameters' => [$input, $leave], 'command' => true,
    'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:write', 'api_path' => 'admin/leaves/{leave}',
    'http_methods' => ['PUT'], 'source_controller' => 'LeaveRequestResource', 'source_action' => 'update'];
$definitions['admin.workforce.leaveBalances.index'] = ['handler' => $queries, 'method' => 'leaveBalances', 'parameters' => [$input, $employee], 'command' => false,
    'context' => 'Workforce HR', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:hr:read', 'api_path' => 'admin/workforce/employees/{employee}/leave-balances',
    'http_methods' => ['GET'], 'source_controller' => 'EmployeeResource', 'source_action' => 'leave_balances.index'];
$definitions['admin.workforce.leaveBalances.show'] = ['handler' => $queries, 'method' => 'leaveBalance', 'parameters' => [$input, $leaveBalance], 'command' => false,
    'context' => 'Workforce HR', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:hr:read', 'api_path' => 'admin/workforce/leave-balances/{leaveBalance}',
    'http_methods' => ['GET'], 'source_controller' => 'EmployeeResource', 'source_action' => 'leave_balances.show'];
$definitions['admin.workforce.leaveBalances.create'] = ['handler' => $class, 'method' => 'createLeaveBalance', 'parameters' => [$input, $employee], 'command' => true,
    'context' => 'Workforce HR', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:hr:write', 'api_path' => 'admin/workforce/employees/{employee}/leave-balances',
    'http_methods' => ['POST'], 'source_controller' => 'EmployeeResource', 'source_action' => 'leave_balances.create'];
$definitions['admin.workforce.leaveBalances.update'] = ['handler' => $class, 'method' => 'updateLeaveBalance', 'parameters' => [$input, $leaveBalance], 'command' => true,
    'context' => 'Workforce HR', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:hr:write', 'api_path' => 'admin/workforce/leave-balances/{leaveBalance}',
    'http_methods' => ['PUT'], 'source_controller' => 'EmployeeResource', 'source_action' => 'leave_balances.update'];
$definitions['admin.workforce.leaveBalances.delete'] = ['handler' => $class, 'method' => 'deleteLeaveBalance', 'parameters' => [$input, $leaveBalance], 'command' => true,
    'context' => 'Workforce HR', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:hr:write', 'api_path' => 'admin/workforce/leave-balances/{leaveBalance}',
    'http_methods' => ['DELETE'], 'source_controller' => 'EmployeeResource', 'source_action' => 'leave_balances.delete'];
return $definitions;
