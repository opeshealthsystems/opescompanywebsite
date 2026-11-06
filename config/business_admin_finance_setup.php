<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$budget = ['name' => 'budget', 'kind' => 'model', 'class' => 'App\\Models\\Budget', 'key' => 'id'];
$command = 'App\\Contexts\\Finance\\Application\\Commands\\AdminBudgetCommands';
$query = 'App\\Contexts\\Finance\\Application\\Queries\\AdminBudgetQueries';
$def = fn (string $handler, string $method, array $params, bool $write, string $verb, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $params, 'command' => $write, 'context' => 'Finance',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $write ? 'finance:write' : 'finance:read',
    'api_path' => 'finance/budgets'.(in_array($method, ['show', 'update', 'delete'], true) ? '/{budget}' : ''), 'http_methods' => [$verb],
    'source_controller' => 'BudgetResource', 'source_action' => $action,
];

return [
    'admin.finance.invoices.index' => ['handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AdminFinanceRecordQueries', 'method' => 'invoiceRegister', 'parameters' => [$input], 'command' => false,
        'context' => 'Finance', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'finance:read', 'api_path' => 'finance/invoices', 'http_methods' => ['GET'],
        'source_controller' => 'InvoiceResource', 'source_action' => 'index'],
    'admin.finance.creditNotes.index' => ['handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AdminFinanceRecordQueries', 'method' => 'creditNotes', 'parameters' => [$input], 'command' => false,
        'context' => 'Finance', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'finance:read', 'api_path' => 'finance/credit-notes', 'http_methods' => ['GET'],
        'source_controller' => 'CreditNoteResource', 'source_action' => 'index'],
    'admin.finance.records.summary' => ['handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AdminFinanceRecordQueries', 'method' => 'summary', 'parameters' => [$input], 'command' => false,
        'context' => 'Finance', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'finance:read', 'api_path' => 'finance/records/summary', 'http_methods' => ['GET'],
        'source_controller' => 'FinanceRecordsSummary', 'source_action' => 'summary'],
    'admin.finance.purchaseOrders.index' => ['handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AdminFinanceRecordQueries', 'method' => 'purchaseOrders', 'parameters' => [$input], 'command' => false,
        'context' => 'Finance', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'finance:read', 'api_path' => 'finance/purchase-orders', 'http_methods' => ['GET'],
        'source_controller' => 'PurchaseOrderResource', 'source_action' => 'index'],
    'admin.finance.quotes.index' => ['handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AdminFinanceRecordQueries', 'method' => 'quotes', 'parameters' => [$input], 'command' => false,
        'context' => 'Finance', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'finance:read', 'api_path' => 'finance/quotes', 'http_methods' => ['GET'],
        'source_controller' => 'QuoteResource', 'source_action' => 'index'],
    'admin.finance.supplierBills.index' => ['handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AdminFinanceRecordQueries', 'method' => 'supplierBills', 'parameters' => [$input], 'command' => false,
        'context' => 'Finance', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'finance:read', 'api_path' => 'finance/supplier-bills', 'http_methods' => ['GET'],
        'source_controller' => 'SupplierBillResource', 'source_action' => 'index'],
    'admin.finance.expenses.index' => ['handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AdminFinanceRecordQueries', 'method' => 'expenses', 'parameters' => [$input], 'command' => false,
        'context' => 'Finance', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'finance:read', 'api_path' => 'finance/expenses', 'http_methods' => ['GET'],
        'source_controller' => 'ExpenseResource', 'source_action' => 'index'],
    'admin.finance.payrollRuns.index' => ['handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AdminFinanceRecordQueries', 'method' => 'payrollRuns', 'parameters' => [$input], 'command' => false,
        'context' => 'Finance', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'finance:read', 'api_path' => 'finance/payroll-runs', 'http_methods' => ['GET'],
        'source_controller' => 'PayrollRunResource', 'source_action' => 'index'],
    'admin.finance.payrollRuns.show' => ['handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AdminFinanceRecordQueries', 'method' => 'payrollRun', 'parameters' => [$input, ['name' => 'run', 'kind' => 'model', 'class' => 'App\\Models\\PayrollRun', 'key' => 'id']], 'command' => false,
        'context' => 'Finance', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'finance:read', 'api_path' => 'finance/payroll-runs/{run}', 'http_methods' => ['GET'],
        'source_controller' => 'PayrollRunResource', 'source_action' => 'show'],
    'admin.finance.invoicePayments.index' => ['handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AdminFinanceRecordQueries', 'method' => 'invoicePayments', 'parameters' => [$input, ['name' => 'invoice', 'kind' => 'model', 'class' => 'App\\Models\\Invoice', 'key' => 'id']], 'command' => false,
        'context' => 'Finance', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'finance:read', 'api_path' => 'finance/invoices/{invoice}/payments', 'http_methods' => ['GET'],
        'source_controller' => 'InvoiceResource', 'source_action' => 'payments.index'],
    'admin.finance.payrollEntries.index' => ['handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AdminFinanceRecordQueries', 'method' => 'payrollEntries', 'parameters' => [$input, ['name' => 'run', 'kind' => 'model', 'class' => 'App\\Models\\PayrollRun', 'key' => 'id']], 'command' => false,
        'context' => 'Finance', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'finance:read', 'api_path' => 'finance/payroll-runs/{run}/entries', 'http_methods' => ['GET'],
        'source_controller' => 'PayrollRunResource', 'source_action' => 'entries.index'],
    'admin.finance.budgets.index' => $def($query, 'index', [$input], false, 'GET', 'index'),
    'admin.finance.budgets.show' => $def($query, 'show', [$input, $budget], false, 'GET', 'show'),
    'admin.finance.budgets.create' => $def($command, 'create', [$input], true, 'POST', 'create'),
    'admin.finance.budgets.update' => $def($command, 'update', [$input, $budget], true, 'PUT', 'update'),
    'admin.finance.budgets.delete' => $def($command, 'delete', [$input, $budget], true, 'DELETE', 'delete'),
];
