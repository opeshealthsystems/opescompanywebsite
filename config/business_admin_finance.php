<?php

// Finance actions previously executed mutations inside Filament closures.
// Each operation now runs through the shared dispatcher for both transports.
$class = 'App\\Contexts\\Finance\\Application\\Commands\\AdminFinanceCommands';
$roles = ['super_admin', 'admin'];
$scope = 'finance:write';
$definitions = [];
$add = function (string $name, string $method, string $path, array $parameters, string $source, string $action) use (&$definitions, $class, $roles, $scope): void {
    $definitions[$name] = [
        'handler' => $class,
        'method' => $method,
        'parameters' => $parameters,
        'command' => true,
        'context' => 'Finance',
        'roles' => $roles,
        'authenticated' => true,
        'scope' => $scope,
        'api_path' => $path,
        'http_methods' => ['POST'],
        'source_controller' => $source,
        'source_action' => $action,
    ];
};
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$model = fn (string $name, string $type) => ['name' => $name, 'kind' => 'model', 'class' => 'App\\Models\\'.$type, 'key' => 'id'];
$template = $model('invoiceTemplate', 'InvoiceTemplate');
$templateQueries = 'App\\Contexts\\Finance\\Application\\Queries\\AdminInvoiceTemplateQueries';
$definitions['admin.finance.invoiceTemplates.index'] = ['handler' => $templateQueries, 'method' => 'index', 'parameters' => [$input], 'command' => false,
    'context' => 'Finance', 'roles' => $roles, 'authenticated' => true, 'scope' => 'finance:read', 'api_path' => 'finance/invoice-templates', 'http_methods' => ['GET'],
    'source_controller' => 'InvoiceTemplateResource', 'source_action' => 'index'];
$definitions['admin.finance.invoiceTemplates.show'] = ['handler' => $templateQueries, 'method' => 'show', 'parameters' => [$input, $template], 'command' => false,
    'context' => 'Finance', 'roles' => $roles, 'authenticated' => true, 'scope' => 'finance:read', 'api_path' => 'finance/invoice-templates/{invoiceTemplate}', 'http_methods' => ['GET'],
    'source_controller' => 'InvoiceTemplateResource', 'source_action' => 'show'];
$add('admin.finance.invoiceTemplates.create', 'createInvoiceTemplate', 'finance/invoice-templates', [$input], 'InvoiceTemplateResource', 'create');
$add('admin.finance.invoiceTemplates.update', 'updateInvoiceTemplate', 'finance/invoice-templates/{invoiceTemplate}', [$input, $template], 'InvoiceTemplateResource', 'update');
$definitions['admin.finance.invoiceTemplates.update']['http_methods'] = ['PUT'];

$add('admin.finance.purchaseOrders.create', 'createPurchaseOrder', 'finance/purchase-orders', [$input], 'PurchaseOrderResource', 'create');
$add('admin.finance.purchaseOrders.update', 'updatePurchaseOrder', 'finance/purchase-orders/{purchaseOrder}', [$input, $model('purchaseOrder', 'PurchaseOrder')], 'PurchaseOrderResource', 'update');
$definitions['admin.finance.purchaseOrders.update']['http_methods'] = ['PUT'];
$add('admin.finance.purchaseOrders.delete', 'deletePurchaseOrder', 'finance/purchase-orders/{purchaseOrder}', [$input, $model('purchaseOrder', 'PurchaseOrder')], 'PurchaseOrderResource', 'delete');
$definitions['admin.finance.purchaseOrders.delete']['http_methods'] = ['DELETE'];
$add('admin.finance.supplierBills.create', 'createSupplierBill', 'finance/supplier-bills', [$input], 'SupplierBillResource', 'create');
$add('admin.finance.supplierBills.update', 'updateSupplierBill', 'finance/supplier-bills/{supplierBill}', [$input, $model('supplierBill', 'SupplierBill')], 'SupplierBillResource', 'update');
$definitions['admin.finance.supplierBills.update']['http_methods'] = ['PUT'];
$add('admin.finance.supplierBills.delete', 'deleteSupplierBill', 'finance/supplier-bills/{supplierBill}', [$input, $model('supplierBill', 'SupplierBill')], 'SupplierBillResource', 'delete');
$definitions['admin.finance.supplierBills.delete']['http_methods'] = ['DELETE'];
$add('admin.finance.invoices.create', 'createInvoice', 'finance/invoices', [$input], 'InvoiceResource', 'create');
$add('admin.finance.invoices.update', 'updateInvoice', 'finance/invoices/{invoice}', [$input, $model('invoice', 'Invoice')], 'InvoiceResource', 'update');
$definitions['admin.finance.invoices.update']['http_methods'] = ['PUT'];
$add('admin.finance.invoices.delete', 'deleteInvoice', 'finance/invoices/{invoice}', [$input, $model('invoice', 'Invoice')], 'InvoiceResource', 'delete');
$definitions['admin.finance.invoices.delete']['http_methods'] = ['DELETE'];
$add('admin.finance.quotes.create', 'createQuote', 'finance/quotes', [$input], 'QuoteResource', 'create');
$add('admin.finance.quotes.update', 'updateQuote', 'finance/quotes/{quote}', [$input, $model('quote', 'Quote')], 'QuoteResource', 'update');
$definitions['admin.finance.quotes.update']['http_methods'] = ['PUT'];
$add('admin.finance.quotes.delete', 'deleteQuote', 'finance/quotes/{quote}', [$input, $model('quote', 'Quote')], 'QuoteResource', 'delete');
$definitions['admin.finance.quotes.delete']['http_methods'] = ['DELETE'];
$add('admin.finance.creditNotes.create', 'createCreditNote', 'finance/credit-notes', [$input], 'CreditNoteResource', 'create');
$add('admin.finance.creditNotes.update', 'updateCreditNote', 'finance/credit-notes/{creditNote}', [$input, $model('creditNote', 'CreditNote')], 'CreditNoteResource', 'update');
$definitions['admin.finance.creditNotes.update']['http_methods'] = ['PUT'];
$add('admin.finance.creditNotes.delete', 'deleteCreditNote', 'finance/credit-notes/{creditNote}', [$input, $model('creditNote', 'CreditNote')], 'CreditNoteResource', 'delete');
$definitions['admin.finance.creditNotes.delete']['http_methods'] = ['DELETE'];
$add('admin.finance.expenses.create', 'createExpense', 'finance/expenses', [$input], 'ExpenseResource', 'create');
$add('admin.finance.expenses.update', 'updateExpense', 'finance/expenses/{expense}', [$input, $model('expense', 'Expense')], 'ExpenseResource', 'update');
$definitions['admin.finance.expenses.update']['http_methods'] = ['PUT'];
$add('admin.finance.expenses.approve', 'approveExpense', 'finance/expenses/{expense}/approve', [$input, $model('expense', 'Expense')], 'ExpenseResource', 'approve');
$add('admin.finance.expenses.markPaid', 'markExpensePaid', 'finance/expenses/{expense}/mark-paid', [$input, $model('expense', 'Expense')], 'ExpenseResource', 'mark_paid');
$add('admin.finance.creditNotes.issue', 'issueCreditNote', 'finance/credit-notes/{creditNote}/issue', [$input, $model('creditNote', 'CreditNote')], 'CreditNoteResource', 'issue');
$add('admin.finance.creditNotes.void', 'voidCreditNote', 'finance/credit-notes/{creditNote}/void', [$input, $model('creditNote', 'CreditNote')], 'CreditNoteResource', 'void');
$add('admin.finance.purchaseOrders.approve', 'approvePurchaseOrder', 'finance/purchase-orders/{purchaseOrder}/approve', [$input, $model('purchaseOrder', 'PurchaseOrder')], 'PurchaseOrderResource', 'approve');
$add('admin.finance.purchaseOrders.receive', 'markPurchaseOrderReceived', 'finance/purchase-orders/{purchaseOrder}/receive', [$input, $model('purchaseOrder', 'PurchaseOrder')], 'PurchaseOrderResource', 'mark_received');
$add('admin.finance.supplierBills.approve', 'approveSupplierBill', 'finance/supplier-bills/{supplierBill}/approve', [$input, $model('supplierBill', 'SupplierBill')], 'SupplierBillResource', 'approve');
$add('admin.finance.supplierBills.markPaid', 'markSupplierBillPaid', 'finance/supplier-bills/{supplierBill}/mark-paid', [$input, $model('supplierBill', 'SupplierBill')], 'SupplierBillResource', 'mark_paid');
$add('admin.finance.quotes.markSent', 'markQuoteSent', 'finance/quotes/{quote}/mark-sent', [$input, $model('quote', 'Quote')], 'QuoteResource', 'mark_sent');
$add('admin.finance.quotes.accept', 'acceptQuote', 'finance/quotes/{quote}/accept', [$input, $model('quote', 'Quote')], 'QuoteResource', 'accept');
$add('admin.finance.invoices.markSent', 'markInvoiceSent', 'finance/invoices/{invoice}/mark-sent', [$input, $model('invoice', 'Invoice')], 'InvoiceResource', 'mark_sent');
$add('admin.finance.invoices.markPaid', 'markInvoicePaid', 'finance/invoices/{invoice}/mark-paid', [$input, $model('invoice', 'Invoice')], 'InvoiceResource', 'mark_paid');
$add('admin.finance.invoices.recordPayment', 'recordInvoicePayment', 'finance/invoices/{invoice}/payments', [$input, $model('invoice', 'Invoice')], 'InvoiceResource', 'record_payment');
$add('admin.finance.invoices.markOverdue', 'markInvoiceOverdue', 'finance/invoices/{invoice}/mark-overdue', [$input, $model('invoice', 'Invoice')], 'InvoiceResource', 'mark_overdue');
$add('admin.finance.payrollRuns.create', 'createPayrollRun', 'finance/payroll-runs', [$input], 'PayrollRunResource', 'create');
$add('admin.finance.payrollRuns.update', 'updatePayrollRun', 'finance/payroll-runs/{run}', [$input, $model('run', 'PayrollRun')], 'PayrollRunResource', 'update');
$definitions['admin.finance.payrollRuns.update']['http_methods'] = ['PUT'];
$add('admin.finance.payrollRuns.delete', 'deletePayrollRun', 'finance/payroll-runs/{run}', [$input, $model('run', 'PayrollRun')], 'PayrollRunResource', 'delete');
$definitions['admin.finance.payrollRuns.delete']['http_methods'] = ['DELETE'];
$add('admin.finance.invoices.markManySent', 'markInvoicesSent', 'finance/invoices/mark-sent', [$input], 'InvoiceResource', 'mark_sent_bulk');
$add('admin.finance.invoiceTemplates.generate', 'generateInvoiceFromTemplate', 'finance/invoice-templates/{invoiceTemplate}/generate-invoice', [$input, $model('invoiceTemplate', 'InvoiceTemplate')], 'InvoiceTemplateResource', 'generate_invoice');
$add('admin.finance.payrollRuns.generateEntries', 'generatePayrollEntries', 'finance/payroll-runs/{run}/generate-entries', [$input, $model('run', 'PayrollRun')], 'PayrollRunResource', 'generate_entries');
$add('admin.finance.payrollRuns.complete', 'completePayroll', 'finance/payroll-runs/{run}/complete', [$input, $model('run', 'PayrollRun')], 'PayrollRunResource', 'complete');
$add('admin.finance.payrollEntries.markPaid', 'markPayrollEntryPaid', 'finance/payroll-runs/{run}/entries/{entry}/mark-paid', [$input, $model('run', 'PayrollRun'), $model('entry', 'PayrollEntry')], 'PayrollEntryResource', 'mark_paid');
$add('admin.finance.payrollEntries.markManyPaid', 'markPayrollEntriesPaid', 'finance/payroll-runs/{run}/entries/mark-paid', [$input, $model('run', 'PayrollRun')], 'PayrollEntryResource', 'mark_all_paid');

return $definitions;
