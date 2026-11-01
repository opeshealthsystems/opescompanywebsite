<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$grade = ['name' => 'salaryGrade', 'kind' => 'model', 'class' => 'App\\Models\\SalaryGrade', 'key' => 'id'];
$deduction = ['name' => 'deductionType', 'kind' => 'model', 'class' => 'App\\Models\\PayrollDeductionType', 'key' => 'id'];
$write = fn (string $handler, string $method, array $params, string $scope, string $path, string $verb, string $context, string $source, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $params, 'command' => true, 'context' => $context,
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $scope, 'api_path' => $path, 'http_methods' => [$verb],
    'source_controller' => $source, 'source_action' => $action,
];
$read = fn (string $handler, string $method, array $params, string $scope, string $path, string $context, string $source, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $params, 'command' => false, 'context' => $context,
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $scope, 'api_path' => $path, 'http_methods' => ['GET'],
    'source_controller' => $source, 'source_action' => $action,
];
$gradeCommands = 'App\\Contexts\\Workforce\\Application\\Commands\\AdminSalaryGradeCommands';
$gradeQueries = 'App\\Contexts\\Workforce\\Application\\Queries\\AdminSalaryGradeQueries';
$deductionCommands = 'App\\Contexts\\Finance\\Application\\Commands\\AdminPayrollConfigurationCommands';
$deductionQueries = 'App\\Contexts\\Finance\\Application\\Queries\\AdminPayrollConfigurationQueries';

return [
    'admin.workforce.salaryGrades.index' => $read($gradeQueries, 'index', [$input], 'workforce:read', 'salary-grades', 'Workforce', 'SalaryGradeResource', 'index'),
    'admin.workforce.salaryGrades.show' => $read($gradeQueries, 'show', [$input, $grade], 'workforce:read', 'salary-grades/{salaryGrade}', 'Workforce', 'SalaryGradeResource', 'show'),
    'admin.workforce.salaryGrades.create' => $write($gradeCommands, 'create', [$input], 'workforce:write', 'salary-grades', 'POST', 'Workforce', 'SalaryGradeResource', 'create'),
    'admin.workforce.salaryGrades.update' => $write($gradeCommands, 'update', [$input, $grade], 'workforce:write', 'salary-grades/{salaryGrade}', 'PUT', 'Workforce', 'SalaryGradeResource', 'update'),
    'admin.workforce.salaryGrades.delete' => $write($gradeCommands, 'delete', [$input, $grade], 'workforce:write', 'salary-grades/{salaryGrade}', 'DELETE', 'Workforce', 'SalaryGradeResource', 'delete'),
    'admin.finance.payrollDeductionTypes.index' => $read($deductionQueries, 'index', [$input], 'finance:read', 'finance/payroll-deduction-types', 'Finance', 'PayrollDeductionTypeResource', 'index'),
    'admin.finance.payrollDeductionTypes.show' => $read($deductionQueries, 'show', [$input, $deduction], 'finance:read', 'finance/payroll-deduction-types/{deductionType}', 'Finance', 'PayrollDeductionTypeResource', 'show'),
    'admin.finance.payrollDeductionTypes.create' => $write($deductionCommands, 'create', [$input], 'finance:write', 'finance/payroll-deduction-types', 'POST', 'Finance', 'PayrollDeductionTypeResource', 'create'),
    'admin.finance.payrollDeductionTypes.update' => $write($deductionCommands, 'update', [$input, $deduction], 'finance:write', 'finance/payroll-deduction-types/{deductionType}', 'PUT', 'Finance', 'PayrollDeductionTypeResource', 'update'),
    'admin.finance.payrollDeductionTypes.delete' => $write($deductionCommands, 'delete', [$input, $deduction], 'finance:write', 'finance/payroll-deduction-types/{deductionType}', 'DELETE', 'Finance', 'PayrollDeductionTypeResource', 'delete'),
];
