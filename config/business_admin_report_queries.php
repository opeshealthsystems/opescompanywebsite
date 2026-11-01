<?php

return [
    'admin.reports.workforce.summary' => ['handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\AdminWorkforceReportQueries',
        'method' => 'summary', 'parameters' => [['name' => 'workflowInput', 'kind' => 'input']], 'command' => false,
        'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:read',
        'api_path' => 'admin/reports/workforce-summary', 'http_methods' => ['GET'],
        'source_controller' => 'HrSummary', 'source_action' => 'summary'],
];
