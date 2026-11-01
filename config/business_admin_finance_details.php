<?php

$handler = 'App\\Contexts\\Finance\\Application\\Queries\\AdminFinanceLineItemQueries';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$model = fn (string $name, string $type) => ['name' => $name, 'kind' => 'model', 'class' => 'App\\Models\\'.$type, 'key' => 'id'];
$definition = fn (string $method, string $path, string $parameter, string $type, string $source) => [
    'handler' => $handler, 'method' => $method, 'parameters' => [$input, $model($parameter, $type)], 'command' => false,
    'context' => 'Finance', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'finance:read',
    'api_path' => 'admin/finance/'.$path, 'http_methods' => ['GET'], 'source_controller' => $source,
    'source_action' => 'items',
];

return [
    'admin.finance.invoiceItems' => $definition('invoiceItems', 'invoices/{invoice}/items', 'invoice', 'Invoice', 'InvoiceResource'),
    'admin.finance.quoteItems' => $definition('quoteItems', 'quotes/{quote}/items', 'quote', 'Quote', 'QuoteResource'),
    'admin.finance.supplierBillItems' => $definition('supplierBillItems', 'supplier-bills/{supplierBill}/items', 'supplierBill', 'SupplierBill', 'SupplierBillResource'),
    'admin.finance.purchaseOrderItems' => $definition('purchaseOrderItems', 'purchase-orders/{purchaseOrder}/items', 'purchaseOrder', 'PurchaseOrder', 'PurchaseOrderResource'),
    'admin.finance.creditNoteItems' => $definition('creditNoteItems', 'credit-notes/{creditNote}/items', 'creditNote', 'CreditNote', 'CreditNoteResource'),
];
