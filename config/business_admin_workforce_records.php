<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$attendance = ['name' => 'attendanceRecord', 'kind' => 'model', 'class' => 'App\\Models\\AttendanceRecord', 'key' => 'id'];
$timesheet = ['name' => 'timesheet', 'kind' => 'model', 'class' => 'App\\Models\\Timesheet', 'key' => 'id'];
$commands = 'App\\Contexts\\Workforce\\Application\\Commands\\AdminWorkforceRecordCommands';
$queries = 'App\\Contexts\\Workforce\\Application\\Queries\\AdminWorkforceRecordQueries';
$def = fn (string $handler, string $method, array $params, bool $write, string $path, string $verb, string $resource, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $params, 'command' => $write, 'context' => 'Workforce',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $write ? 'workforce:write' : 'workforce:read',
    'api_path' => $path, 'http_methods' => [$verb], 'source_controller' => $resource, 'source_action' => $action,
];

return [
    'admin.workforce.attendance.index' => $def($queries, 'attendanceIndex', [$input], false, 'workforce/attendance-records', 'GET', 'AttendanceRecordResource', 'index'),
    'admin.workforce.attendance.show' => $def($queries, 'attendanceShow', [$input, $attendance], false, 'workforce/attendance-records/{attendanceRecord}', 'GET', 'AttendanceRecordResource', 'show'),
    'admin.workforce.attendance.create' => $def($commands, 'createAttendance', [$input], true, 'workforce/attendance-records', 'POST', 'AttendanceRecordResource', 'create'),
    'admin.workforce.attendance.update' => $def($commands, 'updateAttendance', [$input, $attendance], true, 'workforce/attendance-records/{attendanceRecord}', 'PUT', 'AttendanceRecordResource', 'update'),
    'admin.workforce.attendance.delete' => $def($commands, 'deleteAttendance', [$input, $attendance], true, 'workforce/attendance-records/{attendanceRecord}', 'DELETE', 'AttendanceRecordResource', 'delete'),
    'admin.workforce.timesheets.index' => $def($queries, 'timesheetIndex', [$input], false, 'workforce/timesheets', 'GET', 'TimesheetResource', 'index'),
    'admin.workforce.timesheets.show' => $def($queries, 'timesheetShow', [$input, $timesheet], false, 'workforce/timesheets/{timesheet}', 'GET', 'TimesheetResource', 'show'),
    'admin.workforce.timesheets.create' => $def($commands, 'createTimesheet', [$input], true, 'workforce/timesheets', 'POST', 'TimesheetResource', 'create'),
    'admin.workforce.timesheets.update' => $def($commands, 'updateTimesheet', [$input, $timesheet], true, 'workforce/timesheets/{timesheet}', 'PUT', 'TimesheetResource', 'update'),
    'admin.workforce.timesheets.delete' => $def($commands, 'deleteTimesheet', [$input, $timesheet], true, 'workforce/timesheets/{timesheet}', 'DELETE', 'TimesheetResource', 'delete'),
];
