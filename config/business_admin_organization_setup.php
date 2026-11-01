<?php

$commands = 'App\\Contexts\\Workforce\\Application\\Commands\\AdminOrganizationSetupCommands';
$queries = 'App\\Contexts\\Workforce\\Application\\Queries\\AdminOrganizationSetupQueries';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$department = ['name' => 'department', 'kind' => 'model', 'class' => 'App\\Models\\Department', 'key' => 'id'];
$leaveType = ['name' => 'leaveType', 'kind' => 'model', 'class' => 'App\\Models\\LeaveType', 'key' => 'id'];
$base = ['context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true];
$write = fn (string $method, array $parameters, string $path, string $verb, string $source, string $action) => [
    'handler' => $commands, 'method' => $method, 'parameters' => $parameters, 'command' => true, ...$base,
    'scope' => 'workforce:write', 'api_path' => $path, 'http_methods' => [$verb], 'source_controller' => $source, 'source_action' => $action,
];
$read = fn (string $method, array $parameters, string $path, string $source, string $action) => [
    'handler' => $queries, 'method' => $method, 'parameters' => $parameters, 'command' => false, ...$base,
    'scope' => 'workforce:read', 'api_path' => $path, 'http_methods' => ['GET'], 'source_controller' => $source, 'source_action' => $action,
];

return [
    'admin.workforce.departments.index' => $read('departments', [$input], 'departments', 'DepartmentResource', 'index'),
    'admin.workforce.departments.show' => $read('department', [$input, $department], 'departments/{department}', 'DepartmentResource', 'show'),
    'admin.workforce.departments.create' => $write('createDepartment', [$input], 'departments', 'POST', 'DepartmentResource', 'create'),
    'admin.workforce.departments.update' => $write('updateDepartment', [$input, $department], 'departments/{department}', 'PUT', 'DepartmentResource', 'update'),
    'admin.workforce.departments.delete' => $write('deleteDepartment', [$input, $department], 'departments/{department}', 'DELETE', 'DepartmentResource', 'delete'),
    'admin.workforce.leaveTypes.index' => $read('leaveTypes', [$input], 'leave-types', 'LeaveTypeResource', 'index'),
    'admin.workforce.leaveTypes.show' => $read('leaveType', [$input, $leaveType], 'leave-types/{leaveType}', 'LeaveTypeResource', 'show'),
    'admin.workforce.leaveTypes.create' => $write('createLeaveType', [$input], 'leave-types', 'POST', 'LeaveTypeResource', 'create'),
    'admin.workforce.leaveTypes.update' => $write('updateLeaveType', [$input, $leaveType], 'leave-types/{leaveType}', 'PUT', 'LeaveTypeResource', 'update'),
    'admin.workforce.leaveTypes.delete' => $write('deleteLeaveType', [$input, $leaveType], 'leave-types/{leaveType}', 'DELETE', 'LeaveTypeResource', 'delete'),
];
