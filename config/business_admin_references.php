<?php

$handler = 'App\\Contexts\\Shared\\Application\\Queries\\AdminFormReferenceQueries';
$input = [['name' => 'workflowInput', 'kind' => 'input']];
$definition = fn (string $method, string $path, string $source, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $input, 'command' => false, 'context' => 'ReferenceData',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'reference-data:read',
    'api_path' => 'admin/references/'.$path, 'http_methods' => ['GET'],
    'source_controller' => $source, 'source_action' => $action,
];

return [
    'admin.resourceSummaries.metric' => [
        'handler' => 'App\\Contexts\\Shared\\Application\\Queries\\AdminResourceSummaryQueries', 'method' => 'metric',
        'parameters' => $input, 'command' => false, 'context' => 'ReferenceData', 'roles' => ['super_admin', 'admin'],
        'authenticated' => true, 'scope' => 'reporting:read', 'api_path' => 'admin/resource-summaries', 'http_methods' => ['GET'],
        'source_controller' => 'AdminResourceSummaries', 'source_action' => 'metric',
    ],
    'admin.references.users' => $definition('users', 'users', 'AdminReferenceData', 'users'),
    'admin.references.leads' => $definition('leads', 'leads', 'AdminReferenceData', 'leads'),
    'admin.references.departments' => $definition('departments', 'departments', 'AdminReferenceData', 'departments'),
    'admin.references.salaryGrades' => $definition('salaryGrades', 'salary-grades', 'AdminReferenceData', 'salary_grades'),
    'admin.references.nextEmployeeId' => $definition('nextEmployeeId', 'next-employee-id', 'AdminReferenceData', 'next_employee_id'),
    'admin.references.customerCountries' => $definition('customerCountries', 'customer-countries', 'AdminReferenceData', 'customer_countries'),
    'admin.references.userRoles' => $definition('userRoles', 'user-roles', 'AdminReferenceData', 'user_roles'),
    'admin.references.rolePermissions' => $definition('rolePermissions', 'role-permissions', 'AdminReferenceData', 'role_permissions'),
    'admin.references.auditLogFilters' => $definition('auditLogFilters', 'audit-log-filters', 'AdminReferenceData', 'audit_log_filters'),
    'admin.references.projects' => $definition('projects', 'projects', 'AdminReferenceData', 'projects'),
    'admin.references.jobOpenings' => $definition('jobOpenings', 'open-jobs', 'AdminReferenceData', 'open_jobs'),
    'admin.references.knowledgeCategories' => $definition('knowledgeCategories', 'knowledge-categories', 'AdminReferenceData', 'knowledge_categories'),
    'admin.references.validationPrograms' => $definition('validationPrograms', 'validation-programs', 'AdminReferenceData', 'validation_programs'),
    'admin.references.testCases' => $definition('testCases', 'test-cases', 'AdminReferenceData', 'test_cases'),
    'admin.references.vendors' => $definition('vendors', 'vendors', 'AdminReferenceData', 'vendors'),
    'admin.references.invoices' => $definition('invoices', 'invoices', 'AdminReferenceData', 'invoices'),
    'admin.references.licenses' => $definition('licenses', 'licenses', 'AdminReferenceData', 'licenses'),
    'admin.references.purchaseOrders' => $definition('purchaseOrders', 'purchase-orders', 'AdminReferenceData', 'purchase_orders'),
    'admin.references.documentTemplates' => $definition('documentTemplates', 'document-templates', 'AdminReferenceData', 'document_templates'),
    'admin.references.cohortMembers' => $definition('cohortMembers', 'cohort-members', 'AdminReferenceData', 'cohort_members'),
    'admin.references.cohorts' => $definition('cohorts', 'cohorts', 'AdminReferenceData', 'cohorts'),
];
