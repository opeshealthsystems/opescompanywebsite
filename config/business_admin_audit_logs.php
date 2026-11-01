<?php

$handler = 'App\\Contexts\\Shared\\Application\\Queries\\AdminAuditLogQueries';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$auditLog = ['name' => 'auditLog', 'kind' => 'model', 'class' => 'App\\Models\\AuditLog', 'key' => 'id'];
$base = ['context' => 'Administration', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'audit:read'];

return [
    'admin.auditLogs.index' => $base + ['handler' => $handler, 'method' => 'index', 'parameters' => [$input], 'command' => false,
        'api_path' => 'admin/audit-logs', 'http_methods' => ['GET'], 'source_controller' => 'AuditLogResource', 'source_action' => 'index'],
    'admin.auditLogs.show' => $base + ['handler' => $handler, 'method' => 'show', 'parameters' => [$input, $auditLog], 'command' => false,
        'api_path' => 'admin/audit-logs/{auditLog}', 'http_methods' => ['GET'], 'source_controller' => 'AuditLogResource', 'source_action' => 'show'],
];
