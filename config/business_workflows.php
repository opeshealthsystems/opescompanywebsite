<?php
// Explicit operation registry; caller input never selects handler classes.
$registry = array (
  'definitions' => 
  array (
    'certificatepdfcontroller.download' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Queries\\CertificatePdfQueries',
      'method' => 'download',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'certificate',
          'kind' => 'model',
          'class' => 'App\\Models\\CourseCertificate',
          'key' => 'id',
          'route_key' => 'certificate',
        ),
      ),
      'command' => false,
      'context' => 'Learning',
      'roles' => 
      array (
      ),
      'authenticated' => true,
      'scope' => 'documents:read',
      'api_path' => 'files/certificates/{certificate}/download',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\CertificatePdfController',
      'source_action' => 'download',
      'web_route' => 'certificates.pdf',
    ),
    'contractcontroller.pdf' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\ContractQueries',
      'method' => 'pdf',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'contract',
          'kind' => 'model',
          'class' => 'App\\Models\\Contract',
          'key' => 'id',
          'route_key' => 'contract',
        ),
      ),
      'command' => false,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => true,
      'scope' => 'documents:read',
      'api_path' => 'files/contracts/{contract}/pdf',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\ContractController',
      'source_action' => 'pdf',
      'web_route' => 'contracts.pdf',
    ),
    'documentcontroller.pdf' => 
    array (
      'handler' => 'App\\Contexts\\Documents\\Application\\Queries\\DocumentQueries',
      'method' => 'pdf',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'document',
          'kind' => 'model',
          'class' => 'App\\Models\\Document',
          'key' => 'id',
          'route_key' => 'document',
        ),
      ),
      'command' => false,
      'context' => 'Documents',
      'roles' => 
      array (
      ),
      'authenticated' => true,
      'scope' => 'documents:read',
      'api_path' => 'files/documents/{document}/pdf',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\DocumentController',
      'source_action' => 'pdf',
      'web_route' => 'documents.pdf',
    ),
    'documentcontroller.preview' => 
    array (
      'handler' => 'App\\Contexts\\Documents\\Application\\Queries\\DocumentQueries',
      'method' => 'preview',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'document',
          'kind' => 'model',
          'class' => 'App\\Models\\Document',
          'key' => 'id',
          'route_key' => 'document',
        ),
      ),
      'command' => false,
      'context' => 'Documents',
      'roles' => 
      array (
      ),
      'authenticated' => true,
      'scope' => 'documents:read',
      'api_path' => 'files/documents/{document}/view',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\DocumentController',
      'source_action' => 'preview',
      'web_route' => 'documents.preview',
    ),
    'documentsigningcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Documents\\Application\\Queries\\DocumentSigningQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'token',
          'kind' => 'value',
          'route_key' => 'token',
        ),
      ),
      'command' => false,
      'context' => 'Documents',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/documents/{token}/sign',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\DocumentSigningController',
      'source_action' => 'show',
      'web_route' => 'documents.sign',
    ),
    'documentsigningcontroller.sign' => 
    array (
      'handler' => 'App\\Contexts\\Documents\\Application\\Commands\\DocumentSigningCommands',
      'method' => 'sign',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'token',
          'kind' => 'value',
          'route_key' => 'token',
        ),
      ),
      'command' => true,
      'context' => 'Documents',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:write',
      'api_path' => 'public/documents/{token}/sign',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\DocumentSigningController',
      'source_action' => 'sign',
      'web_route' => 'documents.sign.submit',
    ),
    'invoicecontroller.pdf' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\InvoiceQueries',
      'method' => 'pdf',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
      ),
      'authenticated' => true,
      'scope' => 'documents:read',
      'api_path' => 'files/invoices/{invoice}/pdf',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\InvoiceController',
      'source_action' => 'pdf',
      'web_route' => 'invoices.pdf',
    ),
    'payrollcontroller.payslip' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\PayrollQueries',
      'method' => 'payslip',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'entry',
          'kind' => 'model',
          'class' => 'App\\Models\\PayrollEntry',
          'key' => 'id',
          'route_key' => 'entry',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
      ),
      'authenticated' => true,
      'scope' => 'documents:read',
      'api_path' => 'files/payroll/entries/{entry}/payslip',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\PayrollController',
      'source_action' => 'payslip',
      'web_route' => 'payroll.payslip',
    ),
    'purchaseordercontroller.pdf' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\PurchaseOrderQueries',
      'method' => 'pdf',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'purchaseOrder',
          'kind' => 'model',
          'class' => 'App\\Models\\PurchaseOrder',
          'key' => 'id',
          'route_key' => 'purchaseOrder',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
      ),
      'authenticated' => true,
      'scope' => 'documents:read',
      'api_path' => 'files/purchase-orders/{purchaseOrder}/pdf',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\PurchaseOrderController',
      'source_action' => 'pdf',
      'web_route' => 'purchase-orders.pdf',
    ),
    'quotecontroller.pdf' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\QuoteQueries',
      'method' => 'pdf',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'quote',
          'kind' => 'model',
          'class' => 'App\\Models\\Quote',
          'key' => 'id',
          'route_key' => 'quote',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
      ),
      'authenticated' => true,
      'scope' => 'documents:read',
      'api_path' => 'files/quotes/{quote}/pdf',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\QuoteController',
      'source_action' => 'pdf',
      'web_route' => 'quotes.pdf',
    ),
    'supplierbillcontroller.pdf' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\SupplierBillQueries',
      'method' => 'pdf',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'supplierBill',
          'kind' => 'model',
          'class' => 'App\\Models\\SupplierBill',
          'key' => 'id',
          'route_key' => 'supplierBill',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
      ),
      'authenticated' => true,
      'scope' => 'documents:read',
      'api_path' => 'files/supplier-bills/{supplierBill}/pdf',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\SupplierBillController',
      'source_action' => 'pdf',
      'web_route' => 'supplier-bills.pdf',
    ),
    'homecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\HomeQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/home',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\HomeController',
      'source_action' => 'index',
      'web_route' => 'home',
    ),
    'accountant.dashboardcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AccountantDashboardQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'accountant',
      ),
      'authenticated' => true,
      'scope' => 'finance:read',
      'api_path' => 'accountant/dashboard',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Accountant\\DashboardController',
      'source_action' => 'index',
      'web_route' => 'accountant.dashboard',
    ),
    'accountant.expensecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AccountantExpenseQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'accountant',
      ),
      'authenticated' => true,
      'scope' => 'finance:read',
      'api_path' => 'accountant/expenses',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Accountant\\ExpenseController',
      'source_action' => 'index',
      'web_route' => 'accountant.expenses.index',
    ),
    'accountant.expensecontroller.approve' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Commands\\AccountantExpenseCommands',
      'method' => 'approve',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'id',
          'kind' => 'value',
          'route_key' => 'id',
        ),
      ),
      'command' => true,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'accountant',
      ),
      'authenticated' => true,
      'scope' => 'finance:write',
      'api_path' => 'accountant/expenses/{id}/approve',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Accountant\\ExpenseController',
      'source_action' => 'approve',
      'web_route' => 'accountant.expenses.approve',
    ),
    'accountant.expensecontroller.reject' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Commands\\AccountantExpenseCommands',
      'method' => 'reject',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'id',
          'kind' => 'value',
          'route_key' => 'id',
        ),
      ),
      'command' => true,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'accountant',
      ),
      'authenticated' => true,
      'scope' => 'finance:write',
      'api_path' => 'accountant/expenses/{id}/reject',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Accountant\\ExpenseController',
      'source_action' => 'reject',
      'web_route' => 'accountant.expenses.reject',
    ),
    'accountant.invoicecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AccountantInvoiceQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'accountant',
      ),
      'authenticated' => true,
      'scope' => 'finance:read',
      'api_path' => 'accountant/invoices',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Accountant\\InvoiceController',
      'source_action' => 'index',
      'web_route' => 'accountant.invoices.index',
    ),
    'accountant.invoicecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AccountantInvoiceQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'invoice',
          'kind' => 'model',
          'class' => 'App\\Models\\Invoice',
          'key' => 'id',
          'route_key' => 'invoice',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'accountant',
      ),
      'authenticated' => true,
      'scope' => 'finance:read',
      'api_path' => 'accountant/invoices/{invoice}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Accountant\\InvoiceController',
      'source_action' => 'show',
      'web_route' => 'accountant.invoices.show',
    ),
    'accountant.invoicecontroller.markPaid' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Commands\\AccountantInvoiceCommands',
      'method' => 'markPaid',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'invoice',
          'kind' => 'model',
          'class' => 'App\\Models\\Invoice',
          'key' => 'id',
          'route_key' => 'invoice',
        ),
      ),
      'command' => true,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'accountant',
      ),
      'authenticated' => true,
      'scope' => 'finance:write',
      'api_path' => 'accountant/invoices/{invoice}/mark-paid',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Accountant\\InvoiceController',
      'source_action' => 'markPaid',
      'web_route' => 'accountant.invoices.mark-paid',
    ),
    'accountant.payrollcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AccountantPayrollQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'accountant',
      ),
      'authenticated' => true,
      'scope' => 'finance:read',
      'api_path' => 'accountant/payroll',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Accountant\\PayrollController',
      'source_action' => 'index',
      'web_route' => 'accountant.payroll.index',
    ),
    'accountant.payrollcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AccountantPayrollQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'run',
          'kind' => 'model',
          'class' => 'App\\Models\\PayrollRun',
          'key' => 'id',
          'route_key' => 'run',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'accountant',
      ),
      'authenticated' => true,
      'scope' => 'finance:read',
      'api_path' => 'accountant/payroll/{run}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Accountant\\PayrollController',
      'source_action' => 'show',
      'web_route' => 'accountant.payroll.show',
    ),
    'accountant.profilecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AccountantProfileQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'accountant',
      ),
      'authenticated' => true,
      'scope' => 'finance:read',
      'api_path' => 'accountant/profile',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Accountant\\ProfileController',
      'source_action' => 'show',
      'web_route' => 'accountant.profile',
    ),
    'accountant.profilecontroller.update' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Commands\\AccountantProfileCommands',
      'method' => 'update',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'accountant',
      ),
      'authenticated' => true,
      'scope' => 'finance:write',
      'api_path' => 'accountant/profile',
      'http_methods' => 
      array (
        0 => 'PATCH',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Accountant\\ProfileController',
      'source_action' => 'update',
      'web_route' => 'accountant.profile.update',
    ),
    'accountant.reportcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\AccountantReportQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'accountant',
      ),
      'authenticated' => true,
      'scope' => 'finance:read',
      'api_path' => 'accountant/reports',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Accountant\\ReportController',
      'source_action' => 'index',
      'web_route' => 'accountant.reports',
    ),
    'partnerapplicationcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Enquiries\\Application\\Queries\\PartnerApplicationQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Enquiries',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/become-a-partner',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\PartnerApplicationController',
      'source_action' => 'show',
      'web_route' => 'become-a-partner',
    ),
    'partnerapplicationcontroller.submit' => 
    array (
      'handler' => 'App\\Contexts\\Enquiries\\Application\\Commands\\PartnerApplicationCommands',
      'method' => 'submit',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Enquiries',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:write',
      'api_path' => 'public/become-a-partner',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\PartnerApplicationController',
      'source_action' => 'submit',
      'web_route' => 'become-a-partner.submit',
    ),
    'blogcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\BlogQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/blog',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\BlogController',
      'source_action' => 'index',
      'web_route' => 'blog',
    ),
    'blogcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\BlogQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'slug',
          'kind' => 'value',
          'route_key' => 'slug',
        ),
      ),
      'command' => false,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/blog/{slug}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\BlogController',
      'source_action' => 'show',
      'web_route' => 'blog.show',
    ),
    'blogcontroller.comment' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Commands\\BlogCommands',
      'method' => 'comment',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'slug',
          'kind' => 'value',
          'route_key' => 'slug',
        ),
      ),
      'command' => true,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:write',
      'api_path' => 'public/blog/{slug}/comment',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\BlogController',
      'source_action' => 'comment',
      'web_route' => 'blog.comment',
    ),
    'blogcontroller.like' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Commands\\BlogCommands',
      'method' => 'like',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'slug',
          'kind' => 'value',
          'route_key' => 'slug',
        ),
      ),
      'command' => true,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:write',
      'api_path' => 'public/blog/{slug}/like',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\BlogController',
      'source_action' => 'like',
      'web_route' => 'blog.like',
    ),
    'blogcontroller.share' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Commands\\BlogCommands',
      'method' => 'share',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'slug',
          'kind' => 'value',
          'route_key' => 'slug',
        ),
      ),
      'command' => true,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:write',
      'api_path' => 'public/blog/{slug}/share',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\BlogController',
      'source_action' => 'share',
      'web_route' => 'blog.share',
    ),
    'demorequestcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Enquiries\\Application\\Queries\\DemoRequestQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Enquiries',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/book-demo',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\DemoRequestController',
      'source_action' => 'show',
      'web_route' => 'book-demo',
    ),
    'demorequestcontroller.submit' => 
    array (
      'handler' => 'App\\Contexts\\Enquiries\\Application\\Commands\\DemoRequestCommands',
      'method' => 'submit',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Enquiries',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:write',
      'api_path' => 'public/book-demo',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\DemoRequestController',
      'source_action' => 'submit',
      'web_route' => 'book-demo.submit',
    ),
    'publiccoursecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Queries\\PublicCourseQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Learning',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/courses',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\PublicCourseController',
      'source_action' => 'index',
      'web_route' => 'courses.index',
    ),
    'publiccoursecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Queries\\PublicCourseQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'course',
          'kind' => 'model',
          'class' => 'App\\Models\\Course',
          'key' => 'slug',
          'route_key' => 'course',
        ),
      ),
      'command' => false,
      'context' => 'Learning',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/courses/{course:slug}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\PublicCourseController',
      'source_action' => 'show',
      'web_route' => 'courses.show',
    ),
    'customer.certificatecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Queries\\CustomerCertificateQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Learning',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/certificates',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\CertificateController',
      'source_action' => 'index',
      'web_route' => 'customer.certificates',
    ),
    'customer.coursecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Queries\\CustomerCourseQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Learning',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/courses',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\CourseController',
      'source_action' => 'index',
      'web_route' => 'customer.courses',
    ),
    'customer.coursecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Queries\\CustomerCourseQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'course',
          'kind' => 'model',
          'class' => 'App\\Models\\Course',
          'key' => 'slug',
          'route_key' => 'course',
        ),
      ),
      'command' => false,
      'context' => 'Learning',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/courses/{course:slug}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\CourseController',
      'source_action' => 'show',
      'web_route' => 'customer.courses.show',
    ),
    'customer.coursecontroller.enroll' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Commands\\CustomerCourseCommands',
      'method' => 'enroll',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'course',
          'kind' => 'model',
          'class' => 'App\\Models\\Course',
          'key' => 'slug',
          'route_key' => 'course',
        ),
      ),
      'command' => true,
      'context' => 'Learning',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:write',
      'api_path' => 'customer/courses/{course:slug}/enroll',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\CourseController',
      'source_action' => 'enroll',
      'web_route' => 'customer.courses.enroll',
    ),
    'customer.lessoncontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Queries\\CustomerLessonQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'course',
          'kind' => 'model',
          'class' => 'App\\Models\\Course',
          'key' => 'slug',
          'route_key' => 'course',
        ),
        3 => 
        array (
          'name' => 'lesson',
          'kind' => 'model',
          'class' => 'App\\Models\\CourseLesson',
          'key' => 'id',
          'route_key' => 'lesson',
        ),
      ),
      'command' => false,
      'context' => 'Learning',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/courses/{course:slug}/lessons/{lesson}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\LessonController',
      'source_action' => 'show',
      'web_route' => 'customer.lessons.show',
    ),
    'customer.lessoncontroller.markDone' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Commands\\CustomerLessonCommands',
      'method' => 'markDone',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'course',
          'kind' => 'model',
          'class' => 'App\\Models\\Course',
          'key' => 'slug',
          'route_key' => 'course',
        ),
        3 => 
        array (
          'name' => 'lesson',
          'kind' => 'model',
          'class' => 'App\\Models\\CourseLesson',
          'key' => 'id',
          'route_key' => 'lesson',
        ),
      ),
      'command' => true,
      'context' => 'Learning',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:write',
      'api_path' => 'customer/courses/{course:slug}/lessons/{lesson}/done',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\LessonController',
      'source_action' => 'markDone',
      'web_route' => 'customer.lessons.done',
    ),
    'customer.dashboardcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\CustomerServices\\Application\\Queries\\CustomerDashboardQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'CustomerServices',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/dashboard',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\DashboardController',
      'source_action' => 'index',
      'web_route' => 'customer.dashboard',
    ),
    'customer.documentcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Documents\\Application\\Queries\\CustomerDocumentQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Documents',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/documents',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\DocumentController',
      'source_action' => 'index',
      'web_route' => 'customer.documents',
    ),
    'customer.documentcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Documents\\Application\\Queries\\CustomerDocumentQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Documents',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/documents/{id}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\DocumentController',
      'source_action' => 'show',
      'web_route' => 'customer.documents.show',
    ),
    'customer.invoicecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\CustomerInvoiceQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/invoices',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\InvoiceController',
      'source_action' => 'index',
      'web_route' => 'customer.invoices',
    ),
    'customer.invoicecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Finance\\Application\\Queries\\CustomerInvoiceQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Finance',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/invoices/{id}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\InvoiceController',
      'source_action' => 'show',
      'web_route' => 'customer.invoices.show',
    ),
    'customer.knowledgebasecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Queries\\CustomerKnowledgeBaseQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/knowledge-base',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\KnowledgeBaseController',
      'source_action' => 'index',
      'web_route' => 'customer.knowledge-base.index',
    ),
    'customer.knowledgebasecontroller.category' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Queries\\CustomerKnowledgeBaseQueries',
      'method' => 'category',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'slug',
          'kind' => 'value',
          'route_key' => 'slug',
        ),
      ),
      'command' => false,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/knowledge-base/category/{slug}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\KnowledgeBaseController',
      'source_action' => 'category',
      'web_route' => 'customer.knowledge-base.category',
    ),
    'customer.knowledgebasecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Queries\\CustomerKnowledgeBaseQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'slug',
          'kind' => 'value',
          'route_key' => 'slug',
        ),
      ),
      'command' => false,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/knowledge-base/{slug}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\KnowledgeBaseController',
      'source_action' => 'show',
      'web_route' => 'customer.knowledge-base.show',
    ),
    'customer.licensecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\CustomerServices\\Application\\Queries\\CustomerLicenseQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'CustomerServices',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/licenses',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\LicenseController',
      'source_action' => 'index',
      'web_route' => 'customer.licenses',
    ),
    'customer.licensecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\CustomerServices\\Application\\Queries\\CustomerLicenseQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'CustomerServices',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/licenses/{id}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\LicenseController',
      'source_action' => 'show',
      'web_route' => 'customer.licenses.show',
    ),
    'customer.profilecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\CustomerServices\\Application\\Queries\\CustomerProfileQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'CustomerServices',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/profile',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\ProfileController',
      'source_action' => 'show',
      'web_route' => 'customer.profile',
    ),
    'customer.profilecontroller.update' => 
    array (
      'handler' => 'App\\Contexts\\CustomerServices\\Application\\Commands\\CustomerProfileCommands',
      'method' => 'update',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'CustomerServices',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:write',
      'api_path' => 'customer/profile',
      'http_methods' => 
      array (
        0 => 'PUT',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\ProfileController',
      'source_action' => 'update',
      'web_route' => 'customer.profile.update',
    ),
    'customer.profilecontroller.changePassword' => 
    array (
      'handler' => 'App\\Contexts\\CustomerServices\\Application\\Commands\\CustomerProfileCommands',
      'method' => 'changePassword',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'CustomerServices',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:write',
      'api_path' => 'customer/profile/password',
      'http_methods' => 
      array (
        0 => 'PUT',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\ProfileController',
      'source_action' => 'changePassword',
      'web_route' => 'customer.profile.password',
    ),
    'customer.servicerequestcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\CustomerServices\\Application\\Queries\\CustomerServiceRequestQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'CustomerServices',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/service-requests',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\ServiceRequestController',
      'source_action' => 'index',
      'web_route' => 'customer.service-requests',
    ),
    'customer.servicerequestcontroller.store' => 
    array (
      'handler' => 'App\\Contexts\\CustomerServices\\Application\\Commands\\CustomerServiceRequestCommands',
      'method' => 'store',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'CustomerServices',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:write',
      'api_path' => 'customer/service-requests',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\ServiceRequestController',
      'source_action' => 'store',
      'web_route' => 'customer.service-requests.store',
    ),
    'customer.servicerequestcontroller.create' => 
    array (
      'handler' => 'App\\Contexts\\CustomerServices\\Application\\Queries\\CustomerServiceRequestQueries',
      'method' => 'create',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'CustomerServices',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/service-requests/options',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\ServiceRequestController',
      'source_action' => 'create',
      'web_route' => 'customer.service-requests.create',
    ),
    'customer.servicerequestcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\CustomerServices\\Application\\Queries\\CustomerServiceRequestQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'serviceRequest',
          'kind' => 'model',
          'class' => 'App\\Models\\ServiceRequest',
          'key' => 'id',
          'route_key' => 'serviceRequest',
        ),
      ),
      'command' => false,
      'context' => 'CustomerServices',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/service-requests/{serviceRequest}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\ServiceRequestController',
      'source_action' => 'show',
      'web_route' => 'customer.service-requests.show',
    ),
    'customer.surveycontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\CustomerServices\\Application\\Queries\\CustomerSurveyQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'CustomerServices',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/surveys',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\SurveyController',
      'source_action' => 'index',
      'web_route' => 'customer.surveys',
    ),
    'customer.surveycontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\CustomerServices\\Application\\Queries\\CustomerSurveyQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'survey',
          'kind' => 'model',
          'class' => 'App\\Models\\Survey',
          'key' => 'id',
          'route_key' => 'survey',
        ),
      ),
      'command' => false,
      'context' => 'CustomerServices',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/surveys/{survey}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\SurveyController',
      'source_action' => 'show',
      'web_route' => 'customer.surveys.show',
    ),
    'customer.surveycontroller.submit' => 
    array (
      'handler' => 'App\\Contexts\\CustomerServices\\Application\\Commands\\CustomerSurveyCommands',
      'method' => 'submit',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'survey',
          'kind' => 'model',
          'class' => 'App\\Models\\Survey',
          'key' => 'id',
          'route_key' => 'survey',
        ),
      ),
      'command' => true,
      'context' => 'CustomerServices',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:write',
      'api_path' => 'customer/surveys/{survey}',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\SurveyController',
      'source_action' => 'submit',
      'web_route' => 'customer.surveys.submit',
    ),
    'customer.ticketcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Queries\\CustomerTicketQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/tickets',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\TicketController',
      'source_action' => 'index',
      'web_route' => 'customer.tickets',
    ),
    'customer.ticketcontroller.store' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Commands\\CustomerTicketCommands',
      'method' => 'store',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:write',
      'api_path' => 'customer/tickets',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\TicketController',
      'source_action' => 'store',
      'web_route' => 'customer.tickets.store',
    ),
    'customer.ticketcontroller.create' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Queries\\CustomerTicketQueries',
      'method' => 'create',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/tickets/options',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\TicketController',
      'source_action' => 'create',
      'web_route' => 'customer.tickets.create',
    ),
    'customer.ticketcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Queries\\CustomerTicketQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:read',
      'api_path' => 'customer/tickets/{id}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\TicketController',
      'source_action' => 'show',
      'web_route' => 'customer.tickets.show',
    ),
    'customer.ticketcontroller.reply' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Commands\\CustomerTicketCommands',
      'method' => 'reply',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'customer',
      ),
      'authenticated' => true,
      'scope' => 'customer:write',
      'api_path' => 'customer/tickets/{id}/reply',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Customer\\TicketController',
      'source_action' => 'reply',
      'web_route' => 'customer.tickets.reply',
    ),
    'hr.dashboardcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\HRDashboardQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'hr/dashboard',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\HR\\DashboardController',
      'source_action' => 'index',
      'web_route' => 'hr.dashboard',
    ),
    'hr.departmentcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\HRDepartmentQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'hr/departments',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\HR\\DepartmentController',
      'source_action' => 'index',
      'web_route' => 'hr.departments.index',
    ),
    'hr.departmentcontroller.updateHead' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Commands\\HRDepartmentCommands',
      'method' => 'updateHead',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'dept',
          'kind' => 'model',
          'class' => 'App\\Models\\Department',
          'key' => 'id',
          'route_key' => 'dept',
        ),
      ),
      'command' => true,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:write',
      'api_path' => 'hr/departments/{dept}/head',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\HR\\DepartmentController',
      'source_action' => 'updateHead',
      'web_route' => 'hr.departments.head',
    ),
    'hr.employeecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\HREmployeeQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'hr/employees',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\HR\\EmployeeController',
      'source_action' => 'index',
      'web_route' => 'hr.employees.index',
    ),
    'hr.employeecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\HREmployeeQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'user',
          'kind' => 'model',
          'class' => 'App\\Models\\User',
          'key' => 'id',
          'route_key' => 'user',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'hr/employees/{user}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\HR\\EmployeeController',
      'source_action' => 'show',
      'web_route' => 'hr.employees.show',
    ),
    'hr.leavecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\HRLeaveQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'hr/leave',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\HR\\LeaveController',
      'source_action' => 'index',
      'web_route' => 'hr.leave.index',
    ),
    'hr.leavecontroller.approve' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Commands\\HRLeaveCommands',
      'method' => 'approve',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'id',
          'kind' => 'value',
          'route_key' => 'id',
        ),
      ),
      'command' => true,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:write',
      'api_path' => 'hr/leave/{id}/approve',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\HR\\LeaveController',
      'source_action' => 'approve',
      'web_route' => 'hr.leave.approve',
    ),
    'hr.leavecontroller.reject' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Commands\\HRLeaveCommands',
      'method' => 'reject',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'id',
          'kind' => 'value',
          'route_key' => 'id',
        ),
      ),
      'command' => true,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:write',
      'api_path' => 'hr/leave/{id}/reject',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\HR\\LeaveController',
      'source_action' => 'reject',
      'web_route' => 'hr.leave.reject',
    ),
    'hr.payrollcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\HRPayrollQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'hr/payroll',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\HR\\PayrollController',
      'source_action' => 'index',
      'web_route' => 'hr.payroll.index',
    ),
    'hr.payrollcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\HRPayrollQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'run',
          'kind' => 'model',
          'class' => 'App\\Models\\PayrollRun',
          'key' => 'id',
          'route_key' => 'run',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'hr/payroll/{run}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\HR\\PayrollController',
      'source_action' => 'show',
      'web_route' => 'hr.payroll.show',
    ),
    'hr.performancecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\HRPerformanceQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'hr/performance',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\HR\\PerformanceController',
      'source_action' => 'index',
      'web_route' => 'hr.performance.index',
    ),
    'hr.performancecontroller.store' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Commands\\HRPerformanceCommands',
      'method' => 'store',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:write',
      'api_path' => 'hr/performance',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\HR\\PerformanceController',
      'source_action' => 'store',
      'web_route' => 'hr.performance.store',
    ),
    'hr.profilecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\HrProfileQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'hr/profile',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Hr\\ProfileController',
      'source_action' => 'show',
      'web_route' => 'hr.profile',
    ),
    'hr.profilecontroller.update' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Commands\\HrProfileCommands',
      'method' => 'update',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'hr',
      ),
      'authenticated' => true,
      'scope' => 'workforce:write',
      'api_path' => 'hr/profile',
      'http_methods' => 
      array (
        0 => 'PATCH',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Hr\\ProfileController',
      'source_action' => 'update',
      'web_route' => 'hr.profile.update',
    ),
    'testerapplicationcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Enquiries\\Application\\Queries\\TesterApplicationQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Enquiries',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/join-testers',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\TesterApplicationController',
      'source_action' => 'show',
      'web_route' => 'join-testers',
    ),
    'testerapplicationcontroller.submit' => 
    array (
      'handler' => 'App\\Contexts\\Enquiries\\Application\\Commands\\TesterApplicationCommands',
      'method' => 'submit',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Enquiries',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:write',
      'api_path' => 'public/join-testers',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\TesterApplicationController',
      'source_action' => 'submit',
      'web_route' => 'join-testers.submit',
    ),
    'manager.dashboardcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\ManagerDashboardQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'manager',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'manager/dashboard',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Manager\\DashboardController',
      'source_action' => 'index',
      'web_route' => 'manager.dashboard',
    ),
    'manager.leavecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\ManagerLeaveQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'manager',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'manager/leave',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Manager\\LeaveController',
      'source_action' => 'index',
      'web_route' => 'manager.leave.index',
    ),
    'manager.leavecontroller.approve' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Commands\\ManagerLeaveCommands',
      'method' => 'approve',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'id',
          'kind' => 'value',
          'route_key' => 'id',
        ),
      ),
      'command' => true,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'manager',
      ),
      'authenticated' => true,
      'scope' => 'workforce:write',
      'api_path' => 'manager/leave/{id}/approve',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Manager\\LeaveController',
      'source_action' => 'approve',
      'web_route' => 'manager.leave.approve',
    ),
    'manager.leavecontroller.reject' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Commands\\ManagerLeaveCommands',
      'method' => 'reject',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'id',
          'kind' => 'value',
          'route_key' => 'id',
        ),
      ),
      'command' => true,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'manager',
      ),
      'authenticated' => true,
      'scope' => 'workforce:write',
      'api_path' => 'manager/leave/{id}/reject',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Manager\\LeaveController',
      'source_action' => 'reject',
      'web_route' => 'manager.leave.reject',
    ),
    'manager.performancecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\ManagerPerformanceQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'manager',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'manager/performance',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Manager\\PerformanceController',
      'source_action' => 'index',
      'web_route' => 'manager.performance.index',
    ),
    'manager.performancecontroller.store' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Commands\\ManagerPerformanceCommands',
      'method' => 'store',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'manager',
      ),
      'authenticated' => true,
      'scope' => 'workforce:write',
      'api_path' => 'manager/performance',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Manager\\PerformanceController',
      'source_action' => 'store',
      'web_route' => 'manager.performance.store',
    ),
    'manager.profilecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\ManagerProfileQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'manager',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'manager/profile',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Manager\\ProfileController',
      'source_action' => 'show',
      'web_route' => 'manager.profile',
    ),
    'manager.profilecontroller.update' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Commands\\ManagerProfileCommands',
      'method' => 'update',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'manager',
      ),
      'authenticated' => true,
      'scope' => 'workforce:write',
      'api_path' => 'manager/profile',
      'http_methods' => 
      array (
        0 => 'PATCH',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Manager\\ProfileController',
      'source_action' => 'update',
      'web_route' => 'manager.profile.update',
    ),
    'manager.reportcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\ManagerReportQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'manager',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'manager/reports',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Manager\\ReportController',
      'source_action' => 'index',
      'web_route' => 'manager.reports',
    ),
    'manager.teamcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Workforce\\Application\\Queries\\ManagerTeamQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Workforce',
      'roles' => 
      array (
        0 => 'manager',
      ),
      'authenticated' => true,
      'scope' => 'workforce:read',
      'api_path' => 'manager/team',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Manager\\TeamController',
      'source_action' => 'index',
      'web_route' => 'manager.team',
    ),
    'marketcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\MarketQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
      ),
      'command' => false,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/markets',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\MarketController',
      'source_action' => 'index',
      'web_route' => 'markets.index',
    ),
    'marketcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\MarketQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'slug',
          'kind' => 'value',
          'route_key' => 'country',
        ),
      ),
      'command' => false,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/markets/{country}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\MarketController',
      'source_action' => 'show',
      'web_route' => 'markets.show',
    ),
    'notificationcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Identity\\Application\\Queries\\NotificationQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Identity',
      'roles' => 
      array (
      ),
      'authenticated' => true,
      'scope' => 'identity:read',
      'api_path' => 'notifications',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\NotificationController',
      'source_action' => 'index',
      'web_route' => 'notifications.index',
    ),
    'notificationcontroller.markAllRead' => 
    array (
      'handler' => 'App\\Contexts\\Identity\\Application\\Commands\\NotificationCommands',
      'method' => 'markAllRead',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Identity',
      'roles' => 
      array (
      ),
      'authenticated' => true,
      'scope' => 'identity:write',
      'api_path' => 'notifications/read-all',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\NotificationController',
      'source_action' => 'markAllRead',
      'web_route' => 'notifications.read-all',
    ),
    'notificationcontroller.markRead' => 
    array (
      'handler' => 'App\\Contexts\\Identity\\Application\\Commands\\NotificationCommands',
      'method' => 'markRead',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        1 => 
        array (
          'name' => 'id',
          'kind' => 'value',
          'route_key' => 'id',
        ),
        2 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Identity',
      'roles' => 
      array (
      ),
      'authenticated' => true,
      'scope' => 'identity:write',
      'api_path' => 'notifications/{id}/read',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\NotificationController',
      'source_action' => 'markRead',
      'web_route' => 'notifications.read',
    ),
    'partnershipscontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\PartnershipsQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/partnerships',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\PartnershipsController',
      'source_action' => 'index',
      'web_route' => 'partnerships',
    ),
    'practitioner.applicationcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerApplicationQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/applications',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\ApplicationController',
      'source_action' => 'index',
      'web_route' => 'practitioner.applications',
    ),
    'practitioner.applicationcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerApplicationQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'application',
          'kind' => 'model',
          'class' => 'App\\Models\\PractitionerApplication',
          'key' => 'id',
          'route_key' => 'application',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/applications/{application}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\ApplicationController',
      'source_action' => 'show',
      'web_route' => 'practitioner.applications.show',
    ),
    'practitioner.findingcontroller.store' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Commands\\PractitionerFindingCommands',
      'method' => 'store',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'application',
          'kind' => 'model',
          'class' => 'App\\Models\\PractitionerApplication',
          'key' => 'id',
          'route_key' => 'application',
        ),
      ),
      'command' => true,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:write',
      'api_path' => 'practitioner/applications/{application}/findings',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\FindingController',
      'source_action' => 'store',
      'web_route' => 'practitioner.findings.store',
    ),
    'practitioner.findingcontroller.create' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerFindingQueries',
      'method' => 'create',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'application',
          'kind' => 'model',
          'class' => 'App\\Models\\PractitionerApplication',
          'key' => 'id',
          'route_key' => 'application',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/applications/{application}/findings/options',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\FindingController',
      'source_action' => 'create',
      'web_route' => 'practitioner.findings.create',
    ),
    'practitioner.bugreportcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerBugReportQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/bug-reports',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\BugReportController',
      'source_action' => 'index',
      'web_route' => 'practitioner.bug-reports',
    ),
    'practitioner.bugreportcontroller.store' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Commands\\PractitionerBugReportCommands',
      'method' => 'store',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:write',
      'api_path' => 'practitioner/bug-reports',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\BugReportController',
      'source_action' => 'store',
      'web_route' => 'practitioner.bug-reports.store',
    ),
    'practitioner.bugreportcontroller.create' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerBugReportQueries',
      'method' => 'create',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/bug-reports/options',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\BugReportController',
      'source_action' => 'create',
      'web_route' => 'practitioner.bug-reports.create',
    ),
    'practitioner.bugreportcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerBugReportQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'bugReport',
          'kind' => 'model',
          'class' => 'App\\Models\\PractitionerBugReport',
          'key' => 'id',
          'route_key' => 'bugReport',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/bug-reports/{bugReport}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\BugReportController',
      'source_action' => 'show',
      'web_route' => 'practitioner.bug-reports.show',
    ),
    'practitioner.certificatecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Queries\\PractitionerCertificateQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Learning',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/certificates',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\CertificateController',
      'source_action' => 'index',
      'web_route' => 'practitioner.certificates',
    ),
    'practitioner.certificatecontroller.downloadValidation' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Queries\\PractitionerCertificateQueries',
      'method' => 'downloadValidation',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'certificate',
          'kind' => 'model',
          'class' => 'App\\Models\\ValidationCertificate',
          'key' => 'id',
          'route_key' => 'certificate',
        ),
      ),
      'command' => false,
      'context' => 'Learning',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/certificates/validation/{certificate}/download',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\CertificateController',
      'source_action' => 'downloadValidation',
      'web_route' => 'practitioner.certificates.validation-download',
    ),
    'practitioner.coursecontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Queries\\PractitionerCourseQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Learning',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/courses',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\CourseController',
      'source_action' => 'index',
      'web_route' => 'practitioner.courses',
    ),
    'practitioner.coursecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Queries\\PractitionerCourseQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'course',
          'kind' => 'model',
          'class' => 'App\\Models\\Course',
          'key' => 'slug',
          'route_key' => 'course',
        ),
      ),
      'command' => false,
      'context' => 'Learning',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/courses/{course:slug}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\CourseController',
      'source_action' => 'show',
      'web_route' => 'practitioner.courses.show',
    ),
    'practitioner.coursecontroller.enroll' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Commands\\PractitionerCourseCommands',
      'method' => 'enroll',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'course',
          'kind' => 'model',
          'class' => 'App\\Models\\Course',
          'key' => 'slug',
          'route_key' => 'course',
        ),
      ),
      'command' => true,
      'context' => 'Learning',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:write',
      'api_path' => 'practitioner/courses/{course:slug}/enroll',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\CourseController',
      'source_action' => 'enroll',
      'web_route' => 'practitioner.courses.enroll',
    ),
    'practitioner.lessoncontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Queries\\PractitionerLessonQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'course',
          'kind' => 'model',
          'class' => 'App\\Models\\Course',
          'key' => 'slug',
          'route_key' => 'course',
        ),
        3 => 
        array (
          'name' => 'lesson',
          'kind' => 'model',
          'class' => 'App\\Models\\CourseLesson',
          'key' => 'id',
          'route_key' => 'lesson',
        ),
      ),
      'command' => false,
      'context' => 'Learning',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/courses/{course:slug}/lessons/{lesson}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\LessonController',
      'source_action' => 'show',
      'web_route' => 'practitioner.lessons.show',
    ),
    'practitioner.lessoncontroller.markDone' => 
    array (
      'handler' => 'App\\Contexts\\Learning\\Application\\Commands\\PractitionerLessonCommands',
      'method' => 'markDone',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'course',
          'kind' => 'model',
          'class' => 'App\\Models\\Course',
          'key' => 'slug',
          'route_key' => 'course',
        ),
        3 => 
        array (
          'name' => 'lesson',
          'kind' => 'model',
          'class' => 'App\\Models\\CourseLesson',
          'key' => 'id',
          'route_key' => 'lesson',
        ),
      ),
      'command' => true,
      'context' => 'Learning',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:write',
      'api_path' => 'practitioner/courses/{course:slug}/lessons/{lesson}/done',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\LessonController',
      'source_action' => 'markDone',
      'web_route' => 'practitioner.lessons.done',
    ),
    'practitioner.dashboardcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerDashboardQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/dashboard',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\DashboardController',
      'source_action' => 'index',
      'web_route' => 'practitioner.dashboard',
    ),
    'practitioner.profilecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerProfileQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/profile',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\ProfileController',
      'source_action' => 'show',
      'web_route' => 'practitioner.profile',
    ),
    'practitioner.profilecontroller.update' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Commands\\PractitionerProfileCommands',
      'method' => 'update',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:write',
      'api_path' => 'practitioner/profile',
      'http_methods' => 
      array (
        0 => 'PUT',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\ProfileController',
      'source_action' => 'update',
      'web_route' => 'practitioner.profile.update',
    ),
    'practitioner.profilecontroller.changePassword' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Commands\\PractitionerProfileCommands',
      'method' => 'changePassword',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:write',
      'api_path' => 'practitioner/profile/password',
      'http_methods' => 
      array (
        0 => 'PUT',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\ProfileController',
      'source_action' => 'changePassword',
      'web_route' => 'practitioner.profile.password',
    ),
    'practitioner.programcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerProgramQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/programs',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\ProgramController',
      'source_action' => 'index',
      'web_route' => 'practitioner.programs',
    ),
    'practitioner.programcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerProgramQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'program',
          'kind' => 'model',
          'class' => 'App\\Models\\PractitionerProgram',
          'key' => 'id',
          'route_key' => 'program',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/programs/{program}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\ProgramController',
      'source_action' => 'show',
      'web_route' => 'practitioner.programs.show',
    ),
    'practitioner.programcontroller.apply' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Commands\\PractitionerProgramCommands',
      'method' => 'apply',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'program',
          'kind' => 'model',
          'class' => 'App\\Models\\PractitionerProgram',
          'key' => 'id',
          'route_key' => 'program',
        ),
      ),
      'command' => true,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:write',
      'api_path' => 'practitioner/programs/{program}/apply',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\ProgramController',
      'source_action' => 'apply',
      'web_route' => 'practitioner.programs.apply',
    ),
    'practitioner.suggestioncontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerSuggestionQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/suggestions',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\SuggestionController',
      'source_action' => 'index',
      'web_route' => 'practitioner.suggestions',
    ),
    'practitioner.suggestioncontroller.store' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Commands\\PractitionerSuggestionCommands',
      'method' => 'store',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:write',
      'api_path' => 'practitioner/suggestions',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\SuggestionController',
      'source_action' => 'store',
      'web_route' => 'practitioner.suggestions.store',
    ),
    'practitioner.suggestioncontroller.create' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerSuggestionQueries',
      'method' => 'create',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/suggestions/options',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\SuggestionController',
      'source_action' => 'create',
      'web_route' => 'practitioner.suggestions.create',
    ),
    'practitioner.surveycontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerSurveyQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/surveys',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\SurveyController',
      'source_action' => 'index',
      'web_route' => 'practitioner.surveys',
    ),
    'practitioner.surveycontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\PractitionerSurveyQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'survey',
          'kind' => 'model',
          'class' => 'App\\Models\\Survey',
          'key' => 'id',
          'route_key' => 'survey',
        ),
      ),
      'command' => false,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/surveys/{survey}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\SurveyController',
      'source_action' => 'show',
      'web_route' => 'practitioner.surveys.show',
    ),
    'practitioner.surveycontroller.submit' => 
    array (
      'handler' => 'App\\Contexts\\Programmes\\Application\\Commands\\PractitionerSurveyCommands',
      'method' => 'submit',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'survey',
          'kind' => 'model',
          'class' => 'App\\Models\\Survey',
          'key' => 'id',
          'route_key' => 'survey',
        ),
      ),
      'command' => true,
      'context' => 'Programmes',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:write',
      'api_path' => 'practitioner/surveys/{survey}',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\SurveyController',
      'source_action' => 'submit',
      'web_route' => 'practitioner.surveys.submit',
    ),
    'practitioner.validation.dashboardcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Validation\\Application\\Queries\\PractitionerValidationDashboardQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Validation',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/validation',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\Validation\\DashboardController',
      'source_action' => 'show',
      'web_route' => 'practitioner.validation.dashboard',
    ),
    'practitioner.validation.issuereportcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Validation\\Application\\Queries\\PractitionerValidationIssueReportQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Validation',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/validation/issues',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\Validation\\IssueReportController',
      'source_action' => 'index',
      'web_route' => 'practitioner.validation.issues.index',
    ),
    'practitioner.validation.issuereportcontroller.store' => 
    array (
      'handler' => 'App\\Contexts\\Validation\\Application\\Commands\\PractitionerValidationIssueReportCommands',
      'method' => 'store',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Validation',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:write',
      'api_path' => 'practitioner/validation/issues',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\Validation\\IssueReportController',
      'source_action' => 'store',
      'web_route' => 'practitioner.validation.issues.store',
    ),
    'practitioner.validation.issuereportcontroller.create' => 
    array (
      'handler' => 'App\\Contexts\\Validation\\Application\\Queries\\PractitionerValidationIssueReportQueries',
      'method' => 'create',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Validation',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/validation/issues/options',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\Validation\\IssueReportController',
      'source_action' => 'create',
      'web_route' => 'practitioner.validation.issues.create',
    ),
    'practitioner.validation.issuereportcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Validation\\Application\\Queries\\PractitionerValidationIssueReportQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'issue',
          'kind' => 'model',
          'class' => 'App\\Models\\IssueReport',
          'key' => 'id',
          'route_key' => 'issue',
        ),
      ),
      'command' => false,
      'context' => 'Validation',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/validation/issues/{issue}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\Validation\\IssueReportController',
      'source_action' => 'show',
      'web_route' => 'practitioner.validation.issues.show',
    ),
    'practitioner.validation.retestcontroller.store' => 
    array (
      'handler' => 'App\\Contexts\\Validation\\Application\\Commands\\PractitionerValidationRetestCommands',
      'method' => 'store',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'issue',
          'kind' => 'model',
          'class' => 'App\\Models\\IssueReport',
          'key' => 'id',
          'route_key' => 'issue',
        ),
      ),
      'command' => true,
      'context' => 'Validation',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:write',
      'api_path' => 'practitioner/validation/issues/{issue}/retests',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\Validation\\RetestController',
      'source_action' => 'store',
      'web_route' => 'practitioner.validation.issues.retests.store',
    ),
    'practitioner.validation.sessioncontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Validation\\Application\\Queries\\PractitionerValidationSessionQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Validation',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/validation/sessions',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\Validation\\SessionController',
      'source_action' => 'index',
      'web_route' => 'practitioner.validation.sessions.index',
    ),
    'practitioner.validation.sessioncontroller.store' => 
    array (
      'handler' => 'App\\Contexts\\Validation\\Application\\Commands\\PractitionerValidationSessionCommands',
      'method' => 'store',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Validation',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:write',
      'api_path' => 'practitioner/validation/sessions',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\Validation\\SessionController',
      'source_action' => 'store',
      'web_route' => 'practitioner.validation.sessions.store',
    ),
    'practitioner.validation.sessioncontroller.create' => 
    array (
      'handler' => 'App\\Contexts\\Validation\\Application\\Queries\\PractitionerValidationSessionQueries',
      'method' => 'create',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Validation',
      'roles' => 
      array (
        0 => 'practitioner',
      ),
      'authenticated' => true,
      'scope' => 'practitioner:read',
      'api_path' => 'practitioner/validation/sessions/options',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Practitioner\\Validation\\SessionController',
      'source_action' => 'create',
      'web_route' => 'practitioner.validation.sessions.create',
    ),
    'public.practitionerdirectorycontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\PublicPractitionerDirectoryQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/practitioners',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Public\\PractitionerDirectoryController',
      'source_action' => 'index',
      'web_route' => 'practitioners.index',
    ),
    'public.practitionerdirectorycontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\PublicPractitionerDirectoryQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'id',
          'kind' => 'value',
          'route_key' => 'id',
        ),
      ),
      'command' => false,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/practitioners/{id}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Public\\PractitionerDirectoryController',
      'source_action' => 'show',
      'web_route' => 'practitioners.show',
    ),
    'productcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\ProductQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
      ),
      'command' => false,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/products',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\ProductController',
      'source_action' => 'index',
      'web_route' => 'products.index',
    ),
    'productcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\ProductQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'slug',
          'kind' => 'value',
          'route_key' => 'slug',
        ),
      ),
      'command' => false,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/products/{slug}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\ProductController',
      'source_action' => 'show',
      'web_route' => 'product.show',
    ),
    'brochurecontroller.download' => 
    array (
      'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\BrochureQueries',
      'method' => 'download',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'slug',
          'kind' => 'value',
          'route_key' => 'slug',
        ),
      ),
      'command' => false,
      'context' => 'Publishing',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'documents:read',
      'api_path' => 'public/products/{slug}/brochure',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\BrochureController',
      'source_action' => 'download',
      'web_route' => 'product.brochure',
    ),
    'support.dashboardcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Queries\\SupportDashboardQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'support:read',
      'api_path' => 'support/dashboard',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Support\\DashboardController',
      'source_action' => 'index',
      'web_route' => 'support.dashboard',
    ),
    'support.profilecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Queries\\SupportProfileQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'support:read',
      'api_path' => 'support/profile',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Support\\ProfileController',
      'source_action' => 'show',
      'web_route' => 'support.profile',
    ),
    'support.profilecontroller.update' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Commands\\SupportProfileCommands',
      'method' => 'update',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'support:write',
      'api_path' => 'support/profile',
      'http_methods' => 
      array (
        0 => 'PATCH',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Support\\ProfileController',
      'source_action' => 'update',
      'web_route' => 'support.profile.update',
    ),
    'support.ticketcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Queries\\SupportTicketQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'support:read',
      'api_path' => 'support/tickets',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Support\\TicketController',
      'source_action' => 'index',
      'web_route' => 'support.tickets',
    ),
    'support.ticketcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Queries\\SupportTicketQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'ticket',
          'kind' => 'model',
          'class' => 'App\\Models\\Ticket',
          'key' => 'id',
          'route_key' => 'ticket',
        ),
      ),
      'command' => false,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'support:read',
      'api_path' => 'support/tickets/{ticket}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Support\\TicketController',
      'source_action' => 'show',
      'web_route' => 'support.tickets.show',
    ),
    'support.ticketcontroller.assignToMe' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Commands\\SupportTicketCommands',
      'method' => 'assignToMe',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'ticket',
          'kind' => 'model',
          'class' => 'App\\Models\\Ticket',
          'key' => 'id',
          'route_key' => 'ticket',
        ),
      ),
      'command' => true,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'support:write',
      'api_path' => 'support/tickets/{ticket}/assign',
      'http_methods' => 
      array (
        0 => 'PATCH',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Support\\TicketController',
      'source_action' => 'assignToMe',
      'web_route' => 'support.tickets.assign',
    ),
    'support.ticketcontroller.reply' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Commands\\SupportTicketCommands',
      'method' => 'reply',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'ticket',
          'kind' => 'model',
          'class' => 'App\\Models\\Ticket',
          'key' => 'id',
          'route_key' => 'ticket',
        ),
      ),
      'command' => true,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'support:write',
      'api_path' => 'support/tickets/{ticket}/reply',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Support\\TicketController',
      'source_action' => 'reply',
      'web_route' => 'support.tickets.reply',
    ),
    'support.ticketcontroller.updateStatus' => 
    array (
      'handler' => 'App\\Contexts\\Support\\Application\\Commands\\SupportTicketCommands',
      'method' => 'updateStatus',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
        1 => 
        array (
          'name' => 'locale',
          'kind' => 'value',
        ),
        2 => 
        array (
          'name' => 'ticket',
          'kind' => 'model',
          'class' => 'App\\Models\\Ticket',
          'key' => 'id',
          'route_key' => 'ticket',
        ),
      ),
      'command' => true,
      'context' => 'Support',
      'roles' => 
      array (
        0 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'support:write',
      'api_path' => 'support/tickets/{ticket}/status',
      'http_methods' => 
      array (
        0 => 'PATCH',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Support\\TicketController',
      'source_action' => 'updateStatus',
      'web_route' => 'support.tickets.status',
    ),
    'tester.assignmentcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Testing\\Application\\Queries\\TesterAssignmentQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Testing',
      'roles' => 
      array (
        0 => 'tester',
      ),
      'authenticated' => true,
      'scope' => 'testing:read',
      'api_path' => 'testing/assignments',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Tester\\AssignmentController',
      'source_action' => 'index',
      'web_route' => 'tester.assignments',
    ),
    'tester.assignmentcontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Testing\\Application\\Queries\\TesterAssignmentQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Testing',
      'roles' => 
      array (
        0 => 'tester',
      ),
      'authenticated' => true,
      'scope' => 'testing:read',
      'api_path' => 'testing/assignments/{id}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Tester\\AssignmentController',
      'source_action' => 'show',
      'web_route' => 'tester.assignments.show',
    ),
    'tester.assignmentcontroller.storeBugReport' => 
    array (
      'handler' => 'App\\Contexts\\Testing\\Application\\Commands\\TesterAssignmentCommands',
      'method' => 'storeBugReport',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Testing',
      'roles' => 
      array (
        0 => 'tester',
      ),
      'authenticated' => true,
      'scope' => 'testing:write',
      'api_path' => 'testing/assignments/{id}/bug-reports',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Tester\\AssignmentController',
      'source_action' => 'storeBugReport',
      'web_route' => 'tester.assignments.bug-reports',
    ),
    'tester.assignmentcontroller.updateStatus' => 
    array (
      'handler' => 'App\\Contexts\\Testing\\Application\\Commands\\TesterAssignmentCommands',
      'method' => 'updateStatus',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Testing',
      'roles' => 
      array (
        0 => 'tester',
      ),
      'authenticated' => true,
      'scope' => 'testing:write',
      'api_path' => 'testing/assignments/{id}/status',
      'http_methods' => 
      array (
        0 => 'PATCH',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Tester\\AssignmentController',
      'source_action' => 'updateStatus',
      'web_route' => 'tester.assignments.status',
    ),
    'tester.bugreportcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Testing\\Application\\Queries\\TesterBugReportQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Testing',
      'roles' => 
      array (
        0 => 'tester',
      ),
      'authenticated' => true,
      'scope' => 'testing:read',
      'api_path' => 'testing/bug-reports',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Tester\\BugReportController',
      'source_action' => 'index',
      'web_route' => 'tester.bug-reports',
    ),
    'tester.dashboardcontroller.index' => 
    array (
      'handler' => 'App\\Contexts\\Testing\\Application\\Queries\\TesterDashboardQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'workflowInput',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Testing',
      'roles' => 
      array (
        0 => 'tester',
      ),
      'authenticated' => true,
      'scope' => 'testing:read',
      'api_path' => 'testing/dashboard',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Tester\\DashboardController',
      'source_action' => 'index',
      'web_route' => 'tester.dashboard',
    ),
    'tester.profilecontroller.show' => 
    array (
      'handler' => 'App\\Contexts\\Testing\\Application\\Queries\\TesterProfileQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Testing',
      'roles' => 
      array (
        0 => 'tester',
      ),
      'authenticated' => true,
      'scope' => 'testing:read',
      'api_path' => 'testing/profile',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Tester\\ProfileController',
      'source_action' => 'show',
      'web_route' => 'tester.profile',
    ),
    'tester.profilecontroller.update' => 
    array (
      'handler' => 'App\\Contexts\\Testing\\Application\\Commands\\TesterProfileCommands',
      'method' => 'update',
      'parameters' => 
      array (
        0 => 
        array (
          'name' => 'request',
          'kind' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Testing',
      'roles' => 
      array (
        0 => 'tester',
      ),
      'authenticated' => true,
      'scope' => 'testing:write',
      'api_path' => 'testing/profile',
      'http_methods' => 
      array (
        0 => 'PATCH',
      ),
      'source_controller' => 'App\\Http\\Controllers\\Tester\\ProfileController',
      'source_action' => 'update',
      'web_route' => 'tester.profile.update',
    ),
    'identity.register' => 
    array (
      'handler' => 'App\\Contexts\\Identity\\Application\\Commands\\RegisterAccount',
      'method' => 'register',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Identity',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'identity:write',
      'api_path' => 'public/accounts',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'shared-identity-adapter',
      'source_action' => 'register',
      'web_route' => 'register.post',
    ),
    'identity.password_request' => 
    array (
      'handler' => 'App\\Contexts\\Identity\\Application\\Commands\\PasswordRecovery',
      'method' => 'request',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Identity',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'identity:write',
      'api_path' => 'public/password/forgot',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'shared-identity-adapter',
      'source_action' => 'request',
      'web_route' => 'password.email',
    ),
    'identity.password_reset' => 
    array (
      'handler' => 'App\\Contexts\\Identity\\Application\\Commands\\PasswordRecovery',
      'method' => 'reset',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Identity',
      'roles' => 
      array (
      ),
      'authenticated' => false,
      'scope' => 'identity:write',
      'api_path' => 'public/password/reset',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'shared-identity-adapter',
      'source_action' => 'reset',
      'web_route' => 'password.reset',
    ),
    'administration.projects.index' => 
    array (
      'handler' => 'App\\Contexts\\Operations\\Application\\Queries\\ProjectQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Operations',
      'roles' => 
      array (
        0 => 'super_admin',
        1 => 'admin',
      ),
      'authenticated' => true,
      'scope' => 'administration:read',
      'api_path' => 'administration/projects',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'Filament shared adapter',
      'source_action' => 'index',
      'web_route' => NULL,
    ),
    'administration.projects.show' => 
    array (
      'handler' => 'App\\Contexts\\Operations\\Application\\Queries\\ProjectQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
        1 => 
        array (
          'kind' => 'model',
          'name' => 'project',
          'route_key' => 'project',
          'class' => 'App\\Models\\Project',
          'key' => 'id',
        ),
      ),
      'command' => false,
      'context' => 'Operations',
      'roles' => 
      array (
        0 => 'super_admin',
        1 => 'admin',
      ),
      'authenticated' => true,
      'scope' => 'administration:read',
      'api_path' => 'administration/projects/{project}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'Filament shared adapter',
      'source_action' => 'show',
      'web_route' => NULL,
    ),
    'administration.projects.create' => 
    array (
      'handler' => 'App\\Contexts\\Operations\\Application\\Commands\\ProjectCommands',
      'method' => 'create',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Operations',
      'roles' => 
      array (
        0 => 'super_admin',
        1 => 'admin',
      ),
      'authenticated' => true,
      'scope' => 'administration:write',
      'api_path' => 'administration/projects',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'Filament shared adapter',
      'source_action' => 'create',
      'web_route' => NULL,
    ),
    'administration.projects.update' => 
    array (
      'handler' => 'App\\Contexts\\Operations\\Application\\Commands\\ProjectCommands',
      'method' => 'update',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
        1 => 
        array (
          'kind' => 'model',
          'name' => 'project',
          'route_key' => 'project',
          'class' => 'App\\Models\\Project',
          'key' => 'id',
        ),
      ),
      'command' => true,
      'context' => 'Operations',
      'roles' => 
      array (
        0 => 'super_admin',
        1 => 'admin',
      ),
      'authenticated' => true,
      'scope' => 'administration:write',
      'api_path' => 'administration/projects/{project}',
      'http_methods' => 
      array (
        0 => 'PUT',
      ),
      'source_controller' => 'Filament shared adapter',
      'source_action' => 'update',
      'web_route' => NULL,
    ),
    'administration.projects.delete' => 
    array (
      'handler' => 'App\\Contexts\\Operations\\Application\\Commands\\ProjectCommands',
      'method' => 'delete',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
        1 => 
        array (
          'kind' => 'model',
          'name' => 'project',
          'route_key' => 'project',
          'class' => 'App\\Models\\Project',
          'key' => 'id',
        ),
      ),
      'command' => true,
      'context' => 'Operations',
      'roles' => 
      array (
        0 => 'super_admin',
        1 => 'admin',
      ),
      'authenticated' => true,
      'scope' => 'administration:write',
      'api_path' => 'administration/projects/{project}',
      'http_methods' => 
      array (
        0 => 'DELETE',
      ),
      'source_controller' => 'Filament shared adapter',
      'source_action' => 'delete',
      'web_route' => NULL,
    ),
    'administration.licenses.index' => 
    array (
      'handler' => 'App\\Contexts\\Licensing\\Application\\Queries\\LicenseQueries',
      'method' => 'index',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
      ),
      'command' => false,
      'context' => 'Licensing',
      'roles' => 
      array (
        0 => 'super_admin',
        1 => 'admin',
        2 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'licensing:read',
      'api_path' => 'administration/licenses',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'LicenseResource',
      'source_action' => 'index',
      'web_route' => NULL,
    ),
    'administration.licenses.show' => 
    array (
      'handler' => 'App\\Contexts\\Licensing\\Application\\Queries\\LicenseQueries',
      'method' => 'show',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
        1 => 
        array (
          'kind' => 'model',
          'name' => 'license',
          'route_key' => 'license',
          'class' => 'App\\Models\\License',
          'key' => 'id',
        ),
      ),
      'command' => false,
      'context' => 'Licensing',
      'roles' => 
      array (
        0 => 'super_admin',
        1 => 'admin',
        2 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'licensing:read',
      'api_path' => 'administration/licenses/{license}',
      'http_methods' => 
      array (
        0 => 'GET',
      ),
      'source_controller' => 'Filament shared adapter',
      'source_action' => 'show',
      'web_route' => NULL,
    ),
    'administration.licenses.create' => 
    array (
      'handler' => 'App\\Contexts\\Licensing\\Application\\Commands\\LicenseCommands',
      'method' => 'create',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
      ),
      'command' => true,
      'context' => 'Licensing',
      'roles' => 
      array (
        0 => 'super_admin',
        1 => 'admin',
        2 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'licensing:write',
      'api_path' => 'administration/licenses',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'Filament shared adapter',
      'source_action' => 'create',
      'web_route' => NULL,
    ),
    'administration.licenses.update' => 
    array (
      'handler' => 'App\\Contexts\\Licensing\\Application\\Commands\\LicenseCommands',
      'method' => 'update',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
        1 => 
        array (
          'kind' => 'model',
          'name' => 'license',
          'route_key' => 'license',
          'class' => 'App\\Models\\License',
          'key' => 'id',
        ),
      ),
      'command' => true,
      'context' => 'Licensing',
      'roles' => 
      array (
        0 => 'super_admin',
        1 => 'admin',
        2 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'licensing:write',
      'api_path' => 'administration/licenses/{license}',
      'http_methods' => 
      array (
        0 => 'PUT',
      ),
      'source_controller' => 'Filament shared adapter',
      'source_action' => 'update',
      'web_route' => NULL,
    ),
    'administration.licenses.delete' => 
    array (
      'handler' => 'App\\Contexts\\Licensing\\Application\\Commands\\LicenseCommands',
      'method' => 'delete',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
        1 => 
        array (
          'kind' => 'model',
          'name' => 'license',
          'route_key' => 'license',
          'class' => 'App\\Models\\License',
          'key' => 'id',
        ),
      ),
      'command' => true,
      'context' => 'Licensing',
      'roles' => 
      array (
        0 => 'super_admin',
        1 => 'admin',
        2 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'licensing:write',
      'api_path' => 'administration/licenses/{license}',
      'http_methods' => 
      array (
        0 => 'DELETE',
      ),
      'source_controller' => 'Filament shared adapter',
      'source_action' => 'delete',
      'web_route' => NULL,
    ),
    'administration.licenses.suspend' => 
    array (
      'handler' => 'App\\Contexts\\Licensing\\Application\\Commands\\LicenseCommands',
      'method' => 'suspend',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
        1 => 
        array (
          'kind' => 'model',
          'name' => 'license',
          'route_key' => 'license',
          'class' => 'App\\Models\\License',
          'key' => 'id',
        ),
      ),
      'command' => true,
      'context' => 'Licensing',
      'roles' => 
      array (
        0 => 'super_admin',
        1 => 'admin',
        2 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'licensing:write',
      'api_path' => 'administration/licenses/{license}/suspend',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'Filament shared adapter',
      'source_action' => 'suspend',
      'web_route' => NULL,
    ),
    'administration.licenses.reactivate' => 
    array (
      'handler' => 'App\\Contexts\\Licensing\\Application\\Commands\\LicenseCommands',
      'method' => 'reactivate',
      'parameters' => 
      array (
        0 => 
        array (
          'kind' => 'input',
          'name' => 'input',
        ),
        1 => 
        array (
          'kind' => 'model',
          'name' => 'license',
          'route_key' => 'license',
          'class' => 'App\\Models\\License',
          'key' => 'id',
        ),
      ),
      'command' => true,
      'context' => 'Licensing',
      'roles' => 
      array (
        0 => 'super_admin',
        1 => 'admin',
        2 => 'support',
      ),
      'authenticated' => true,
      'scope' => 'licensing:write',
      'api_path' => 'administration/licenses/{license}/reactivate',
      'http_methods' => 
      array (
        0 => 'POST',
      ),
      'source_controller' => 'Filament shared adapter',
      'source_action' => 'reactivate',
      'web_route' => NULL,
    ),
  ),
  'presentations' => 
  array (
    'documentcontroller_preview_1' => 
    array (
      'kind' => 'view',
      'target' => 'documents.pdf',
    ),
    'documentsigningcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'documents.sign',
    ),
    'documentsigningcontroller_show_2' => 
    array (
      'kind' => 'view',
      'target' => 'documents.sign',
    ),
    'documentsigningcontroller_sign_1' => 
    array (
      'kind' => 'route',
      'target' => 'documents.sign.success',
    ),
    'homecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.home',
    ),
    'accountant_dashboardcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'accountant.dashboard',
    ),
    'accountant_expensecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'accountant.expenses.index',
    ),
    'accountant_expensecontroller_approve_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'accountant_expensecontroller_reject_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'accountant_invoicecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'accountant.invoices.index',
    ),
    'accountant_invoicecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'accountant.invoices.show',
    ),
    'accountant_invoicecontroller_markPaid_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'accountant_payrollcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'accountant.payroll.index',
    ),
    'accountant_payrollcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'accountant.payroll.show',
    ),
    'accountant_profilecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'accountant.profile',
    ),
    'accountant_profilecontroller_update_1' => 
    array (
      'kind' => 'route',
      'target' => 'accountant.profile',
    ),
    'accountant_reportcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'accountant.reports.index',
    ),
    'partnerapplicationcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.become-a-partner',
    ),
    'partnerapplicationcontroller_submit_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'blogcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.blog-index',
    ),
    'blogcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.blog-show',
    ),
    'blogcontroller_comment_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'demorequestcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.book-demo',
    ),
    'demorequestcontroller_submit_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'publiccoursecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.courses.index',
    ),
    'publiccoursecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.courses.show',
    ),
    'customer_certificatecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.certificates.index',
    ),
    'customer_coursecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.courses.index',
    ),
    'customer_coursecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.courses.show',
    ),
    'customer_coursecontroller_enroll_1' => 
    array (
      'kind' => 'route',
      'target' => 'customer.courses.show',
    ),
    'customer_coursecontroller_enroll_2' => 
    array (
      'kind' => 'route',
      'target' => 'customer.courses.show',
    ),
    'customer_lessoncontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.lessons.show',
    ),
    'customer_lessoncontroller_markDone_1' => 
    array (
      'kind' => 'route',
      'target' => 'customer.courses.show',
    ),
    'customer_lessoncontroller_markDone_2' => 
    array (
      'kind' => 'route',
      'target' => 'customer.lessons.show',
    ),
    'customer_lessoncontroller_markDone_3' => 
    array (
      'kind' => 'route',
      'target' => 'customer.courses.show',
    ),
    'customer_dashboardcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.dashboard',
    ),
    'customer_documentcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.documents.index',
    ),
    'customer_documentcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.documents.show',
    ),
    'customer_invoicecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.invoices.index',
    ),
    'customer_invoicecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.invoices.show',
    ),
    'customer_knowledgebasecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.knowledge-base.index',
    ),
    'customer_knowledgebasecontroller_category_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.knowledge-base.category',
    ),
    'customer_knowledgebasecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.knowledge-base.show',
    ),
    'customer_licensecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.licenses.index',
    ),
    'customer_licensecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.licenses.show',
    ),
    'customer_profilecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.profile',
    ),
    'customer_profilecontroller_update_1' => 
    array (
      'kind' => 'route',
      'target' => 'customer.profile',
    ),
    'customer_profilecontroller_changePassword_1' => 
    array (
      'kind' => 'route',
      'target' => 'customer.profile',
    ),
    'customer_servicerequestcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.service-requests.index',
    ),
    'customer_servicerequestcontroller_store_1' => 
    array (
      'kind' => 'route',
      'target' => 'customer.service-requests',
    ),
    'customer_servicerequestcontroller_create_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.service-requests.create',
    ),
    'customer_servicerequestcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.service-requests.show',
    ),
    'customer_surveycontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.surveys.index',
    ),
    'customer_surveycontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.surveys.show',
    ),
    'customer_surveycontroller_submit_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'customer_surveycontroller_submit_2' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'customer_surveycontroller_submit_3' => 
    array (
      'kind' => 'route',
      'target' => 'customer.surveys',
    ),
    'customer_ticketcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.tickets.index',
    ),
    'customer_ticketcontroller_store_1' => 
    array (
      'kind' => 'route',
      'target' => 'customer.tickets',
    ),
    'customer_ticketcontroller_create_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.tickets.create',
    ),
    'customer_ticketcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'customer.tickets.show',
    ),
    'customer_ticketcontroller_reply_1' => 
    array (
      'kind' => 'route',
      'target' => 'customer.tickets.show',
    ),
    'hr_dashboardcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'hr.dashboard',
    ),
    'hr_departmentcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'hr.departments.index',
    ),
    'hr_departmentcontroller_updateHead_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'hr_employeecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'hr.employees.index',
    ),
    'hr_employeecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'hr.employees.show',
    ),
    'hr_leavecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'hr.leave.index',
    ),
    'hr_leavecontroller_approve_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'hr_leavecontroller_reject_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'hr_payrollcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'hr.payroll.index',
    ),
    'hr_payrollcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'hr.payroll.show',
    ),
    'hr_performancecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'hr.performance.index',
    ),
    'hr_performancecontroller_store_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'hr_profilecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'hr.profile',
    ),
    'hr_profilecontroller_update_1' => 
    array (
      'kind' => 'route',
      'target' => 'hr.profile',
    ),
    'testerapplicationcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.join-testers',
    ),
    'testerapplicationcontroller_submit_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'manager_dashboardcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'manager.dashboard',
    ),
    'manager_leavecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'manager.leave.index',
    ),
    'manager_leavecontroller_approve_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'manager_leavecontroller_reject_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'manager_performancecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'manager.performance.index',
    ),
    'manager_performancecontroller_store_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'manager_profilecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'manager.profile',
    ),
    'manager_profilecontroller_update_1' => 
    array (
      'kind' => 'route',
      'target' => 'manager.profile',
    ),
    'manager_reportcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'manager.reports.index',
    ),
    'manager_teamcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'manager.team.index',
    ),
    'marketcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.markets.index',
    ),
    'marketcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.markets.show',
    ),
    'notificationcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'notifications.index',
    ),
    'notificationcontroller_markAllRead_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'notificationcontroller_markRead_1' => 
    array (
      'kind' => 'url',
      'target' => NULL,
    ),
    'notificationcontroller_markRead_2' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'partnershipscontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.partnerships',
    ),
    'practitioner_applicationcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.applications.index',
    ),
    'practitioner_applicationcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.applications.show',
    ),
    'practitioner_findingcontroller_store_1' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.applications.show',
    ),
    'practitioner_findingcontroller_create_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.findings.create',
    ),
    'practitioner_bugreportcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.bug-reports.index',
    ),
    'practitioner_bugreportcontroller_store_1' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.bug-reports',
    ),
    'practitioner_bugreportcontroller_create_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.bug-reports.create',
    ),
    'practitioner_bugreportcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.bug-reports.show',
    ),
    'practitioner_certificatecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.certificates.index',
    ),
    'practitioner_coursecontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.courses.index',
    ),
    'practitioner_coursecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.courses.show',
    ),
    'practitioner_coursecontroller_enroll_1' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.courses.show',
    ),
    'practitioner_coursecontroller_enroll_2' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.courses.show',
    ),
    'practitioner_lessoncontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.lessons.show',
    ),
    'practitioner_lessoncontroller_markDone_1' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.courses.show',
    ),
    'practitioner_lessoncontroller_markDone_2' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.lessons.show',
    ),
    'practitioner_lessoncontroller_markDone_3' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.courses.show',
    ),
    'practitioner_dashboardcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.dashboard',
    ),
    'practitioner_profilecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.profile',
    ),
    'practitioner_profilecontroller_update_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'practitioner_profilecontroller_changePassword_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'practitioner_programcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.programs.index',
    ),
    'practitioner_programcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.programs.show',
    ),
    'practitioner_programcontroller_apply_1' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.applications',
    ),
    'practitioner_suggestioncontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.suggestions.index',
    ),
    'practitioner_suggestioncontroller_store_1' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.suggestions',
    ),
    'practitioner_suggestioncontroller_create_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.suggestions.create',
    ),
    'practitioner_surveycontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.surveys.index',
    ),
    'practitioner_surveycontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.surveys.show',
    ),
    'practitioner_surveycontroller_submit_1' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'practitioner_surveycontroller_submit_2' => 
    array (
      'kind' => 'back',
      'target' => NULL,
    ),
    'practitioner_surveycontroller_submit_3' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.surveys',
    ),
    'practitioner_validation_dashboardcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.validation.dashboard',
    ),
    'practitioner_validation_issuereportcontroller_index_1' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.validation.dashboard',
    ),
    'practitioner_validation_issuereportcontroller_index_2' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.validation.issues.index',
    ),
    'practitioner_validation_issuereportcontroller_store_1' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.validation.dashboard',
    ),
    'practitioner_validation_issuereportcontroller_store_2' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.validation.issues.index',
    ),
    'practitioner_validation_issuereportcontroller_create_1' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.validation.dashboard',
    ),
    'practitioner_validation_issuereportcontroller_create_2' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.validation.issues.create',
    ),
    'practitioner_validation_issuereportcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.validation.issues.show',
    ),
    'practitioner_validation_retestcontroller_store_1' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.validation.issues.show',
    ),
    'practitioner_validation_sessioncontroller_index_1' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.validation.dashboard',
    ),
    'practitioner_validation_sessioncontroller_index_2' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.validation.sessions.index',
    ),
    'practitioner_validation_sessioncontroller_store_1' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.validation.dashboard',
    ),
    'practitioner_validation_sessioncontroller_store_2' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.validation.sessions.index',
    ),
    'practitioner_validation_sessioncontroller_create_1' => 
    array (
      'kind' => 'route',
      'target' => 'practitioner.validation.dashboard',
    ),
    'practitioner_validation_sessioncontroller_create_2' => 
    array (
      'kind' => 'view',
      'target' => 'practitioner.validation.sessions.create',
    ),
    'public_practitionerdirectorycontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.practitioners.index',
    ),
    'public_practitionerdirectorycontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.practitioners.show',
    ),
    'productcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.products-index',
    ),
    'productcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'pages.drugtrace',
    ),
    'productcontroller_show_2' => 
    array (
      'kind' => 'view',
      'target' => 'pages.product-show',
    ),
    'support_dashboardcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'support.dashboard',
    ),
    'support_profilecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'support.profile',
    ),
    'support_profilecontroller_update_1' => 
    array (
      'kind' => 'route',
      'target' => 'support.profile',
    ),
    'support_ticketcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'support.tickets.index',
    ),
    'support_ticketcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'support.tickets.show',
    ),
    'support_ticketcontroller_assignToMe_1' => 
    array (
      'kind' => 'route',
      'target' => 'support.tickets.show',
    ),
    'support_ticketcontroller_reply_1' => 
    array (
      'kind' => 'route',
      'target' => 'support.tickets.show',
    ),
    'support_ticketcontroller_updateStatus_1' => 
    array (
      'kind' => 'route',
      'target' => 'support.tickets.show',
    ),
    'tester_assignmentcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'tester.assignments.index',
    ),
    'tester_assignmentcontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'tester.assignments.show',
    ),
    'tester_assignmentcontroller_storeBugReport_1' => 
    array (
      'kind' => 'route',
      'target' => 'tester.assignments.show',
    ),
    'tester_assignmentcontroller_updateStatus_1' => 
    array (
      'kind' => 'route',
      'target' => 'tester.assignments.show',
    ),
    'tester_bugreportcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'tester.bug-reports.index',
    ),
    'tester_dashboardcontroller_index_1' => 
    array (
      'kind' => 'view',
      'target' => 'tester.dashboard',
    ),
    'tester_profilecontroller_show_1' => 
    array (
      'kind' => 'view',
      'target' => 'tester.profile',
    ),
    'tester_profilecontroller_update_1' => 
    array (
      'kind' => 'route',
      'target' => 'tester.profile',
    ),
  ),
);
$registry['definitions'] += require __DIR__.'/business_admin_finance.php';
$registry['definitions'] += require __DIR__.'/business_admin_contracts.php';
$registry['definitions'] += require __DIR__.'/business_admin_validation.php';
$registry['definitions'] += require __DIR__.'/business_admin_recruitment.php';
$registry['definitions'] += require __DIR__.'/business_admin_courses.php';
$registry['definitions'] += require __DIR__.'/business_learning_assessments.php';
$registry['definitions'] += require __DIR__.'/business_admin_surveys.php';
$registry['definitions'] += require __DIR__.'/business_admin_publishing.php';
$registry['definitions'] += require __DIR__.'/business_admin_leave.php';
$registry['definitions'] += require __DIR__.'/business_admin_performance.php';
$registry['definitions'] += require __DIR__.'/business_admin_people.php';
$registry['definitions'] += require __DIR__.'/business_admin_practitioners.php';
$registry['definitions'] += require __DIR__.'/business_admin_support.php';
$registry['definitions'] += require __DIR__.'/business_admin_validation_queries.php';
$registry['definitions'] += require __DIR__.'/business_admin_learning.php';
$registry['definitions'] += require __DIR__.'/business_admin_operations.php';
$registry['definitions'] += require __DIR__.'/business_admin_enquiries.php';
$registry['definitions'] += require __DIR__.'/business_admin_settings.php';
$registry['definitions'] += require __DIR__.'/business_admin_report_queries.php';
$registry['definitions'] += require __DIR__.'/business_admin_crm.php';
$registry['definitions'] += require __DIR__.'/business_admin_testing.php';
$registry['definitions'] += require __DIR__.'/business_admin_organization_setup.php';
$registry['definitions'] += require __DIR__.'/business_admin_compensation_setup.php';
$registry['definitions'] += require __DIR__.'/business_admin_reference_setup.php';
$registry['definitions'] += require __DIR__.'/business_admin_content_setup.php';
$registry['definitions'] += require __DIR__.'/business_admin_finance_setup.php';
$registry['definitions'] += require __DIR__.'/business_admin_workforce_records.php';
$registry['definitions'] += require __DIR__.'/business_admin_catalogue.php';
$registry['definitions'] += require __DIR__.'/business_admin_council.php';
$registry['definitions'] += require __DIR__.'/business_admin_email_templates.php';
$registry['definitions'] += require __DIR__.'/business_admin_blog_posts.php';
$registry['definitions'] += require __DIR__.'/business_admin_demo_requests.php';
$registry['definitions'] += require __DIR__.'/business_admin_cohorts.php';
$registry['definitions'] += require __DIR__.'/business_admin_employees.php';
$registry['definitions'] += require __DIR__.'/business_admin_customer_profiles.php';
$registry['definitions'] += require __DIR__.'/business_admin_validation_catalog.php';
$registry['definitions'] += require __DIR__.'/business_admin_practitioner_records.php';
$registry['definitions'] += require __DIR__.'/business_admin_external_applications.php';
$registry['definitions'] += require __DIR__.'/business_partner_accounts.php';
$registry['definitions'] += require __DIR__.'/business_admin_service_requests.php';
$registry['definitions'] += require __DIR__.'/business_admin_partner_institutions.php';
$registry['definitions'] += require __DIR__.'/business_admin_practitioner_programs.php';
$registry['definitions'] += require __DIR__.'/business_admin_references.php';
$registry['definitions'] += require __DIR__.'/business_public_content.php';
$registry['definitions'] += require __DIR__.'/business_admin_finance_details.php';
$registry['definitions'] += require __DIR__.'/business_admin_audit_logs.php';
$registry['definitions'] += require __DIR__.'/business_admin_payouts.php';
$registry['definitions'] += require __DIR__.'/business_admin_api_clients.php';
$registry['definitions'] += require __DIR__.'/business_admin_oauth_clients.php';
$registry['definitions'] += require __DIR__.'/business_developer_oauth_apps.php';
$registry['definitions'] += require __DIR__.'/business_developer_machine_clients.php';
$registry['definitions'] += require __DIR__.'/business_api_product.php';
$registry['definitions'] += require __DIR__.'/business_admin_oauth_clients.php';
$registry['definitions'] += require __DIR__.'/business_identity_profile.php';
$registry['web_adapters'] = require __DIR__.'/business_web_adapters.php';
return $registry;
