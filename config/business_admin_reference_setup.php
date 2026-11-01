<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$vendor = ['name' => 'vendor', 'kind' => 'model', 'class' => 'App\\Models\\Vendor', 'key' => 'id'];
$sla = ['name' => 'slaRule', 'kind' => 'model', 'class' => 'App\\Models\\SlaRule', 'key' => 'id'];
$product = ['name' => 'validationProduct', 'kind' => 'model', 'class' => 'App\\Models\\ValidationProduct', 'key' => 'id'];
$def = fn (string $handler, string $method, array $params, bool $command, string $scope, string $path, string $verb, string $context, string $source, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $params, 'command' => $command, 'context' => $context,
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $scope, 'api_path' => $path, 'http_methods' => [$verb],
    'source_controller' => $source, 'source_action' => $action,
];
$vendorCommand = 'App\\Contexts\\Finance\\Application\\Commands\\AdminVendorCommands';
$vendorQuery = 'App\\Contexts\\Finance\\Application\\Queries\\AdminVendorQueries';
$slaCommand = 'App\\Contexts\\Support\\Application\\Commands\\AdminSlaRuleCommands';
$slaQuery = 'App\\Contexts\\Support\\Application\\Queries\\AdminSlaRuleQueries';
$productCommand = 'App\\Contexts\\Validation\\Application\\Commands\\AdminValidationProductCommands';
$productQuery = 'App\\Contexts\\Validation\\Application\\Queries\\AdminValidationProductQueries';

return [
    'admin.finance.vendors.index' => $def($vendorQuery, 'index', [$input], false, 'finance:read', 'finance/vendors', 'GET', 'Finance', 'VendorResource', 'index'),
    'admin.finance.vendors.show' => $def($vendorQuery, 'show', [$input, $vendor], false, 'finance:read', 'finance/vendors/{vendor}', 'GET', 'Finance', 'VendorResource', 'show'),
    'admin.finance.vendors.create' => $def($vendorCommand, 'create', [$input], true, 'finance:write', 'finance/vendors', 'POST', 'Finance', 'VendorResource', 'create'),
    'admin.finance.vendors.update' => $def($vendorCommand, 'update', [$input, $vendor], true, 'finance:write', 'finance/vendors/{vendor}', 'PUT', 'Finance', 'VendorResource', 'update'),
    'admin.finance.vendors.delete' => $def($vendorCommand, 'delete', [$input, $vendor], true, 'finance:write', 'finance/vendors/{vendor}', 'DELETE', 'Finance', 'VendorResource', 'delete'),
    'admin.support.slaRules.index' => $def($slaQuery, 'index', [$input], false, 'support:read', 'support/sla-rules', 'GET', 'Support', 'SlaRuleResource', 'index'),
    'admin.support.slaRules.show' => $def($slaQuery, 'show', [$input, $sla], false, 'support:read', 'support/sla-rules/{slaRule}', 'GET', 'Support', 'SlaRuleResource', 'show'),
    'admin.support.slaRules.create' => $def($slaCommand, 'create', [$input], true, 'support:write', 'support/sla-rules', 'POST', 'Support', 'SlaRuleResource', 'create'),
    'admin.support.slaRules.update' => $def($slaCommand, 'update', [$input, $sla], true, 'support:write', 'support/sla-rules/{slaRule}', 'PUT', 'Support', 'SlaRuleResource', 'update'),
    'admin.support.slaRules.delete' => $def($slaCommand, 'delete', [$input, $sla], true, 'support:write', 'support/sla-rules/{slaRule}', 'DELETE', 'Support', 'SlaRuleResource', 'delete'),
    'admin.validation.products.index' => $def($productQuery, 'index', [$input], false, 'validation:read', 'validation/products', 'GET', 'Validation', 'ValidationProductResource', 'index'),
    'admin.validation.products.show' => $def($productQuery, 'show', [$input, $product], false, 'validation:read', 'validation/products/{validationProduct}', 'GET', 'Validation', 'ValidationProductResource', 'show'),
    'admin.validation.products.create' => $def($productCommand, 'create', [$input], true, 'validation:write', 'validation/products', 'POST', 'Validation', 'ValidationProductResource', 'create'),
    'admin.validation.products.update' => $def($productCommand, 'update', [$input, $product], true, 'validation:write', 'validation/products/{validationProduct}', 'PUT', 'Validation', 'ValidationProductResource', 'update'),
    'admin.validation.products.delete' => $def($productCommand, 'delete', [$input, $product], true, 'validation:write', 'validation/products/{validationProduct}', 'DELETE', 'Validation', 'ValidationProductResource', 'delete'),
];
