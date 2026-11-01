export const businessOperations = Object.freeze({
    "accountant_dashboardcontroller_index": {
        "method": "GET",
        "path": "/accountant/dashboard",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "accountant_expensecontroller_approve": {
        "method": "POST",
        "path": "/accountant/expenses/{id}/approve",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "accountant_expensecontroller_index": {
        "method": "GET",
        "path": "/accountant/expenses",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "accountant_expensecontroller_reject": {
        "method": "POST",
        "path": "/accountant/expenses/{id}/reject",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "accountant_invoicecontroller_index": {
        "method": "GET",
        "path": "/accountant/invoices",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "accountant_invoicecontroller_markPaid": {
        "method": "POST",
        "path": "/accountant/invoices/{invoice}/mark-paid",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "invoice"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "accountant_invoicecontroller_show": {
        "method": "GET",
        "path": "/accountant/invoices/{invoice}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "invoice"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "accountant_payrollcontroller_index": {
        "method": "GET",
        "path": "/accountant/payroll",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "accountant_payrollcontroller_show": {
        "method": "GET",
        "path": "/accountant/payroll/{run}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "run"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "accountant_profilecontroller_show": {
        "method": "GET",
        "path": "/accountant/profile",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "accountant_profilecontroller_update": {
        "method": "PATCH",
        "path": "/accountant/profile",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "accountant_reportcontroller_index": {
        "method": "GET",
        "path": "/accountant/reports",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_catalogue_products_archive": {
        "method": "POST",
        "path": "/admin/products/{product}/archive",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "product"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_catalogue_products_create": {
        "method": "POST",
        "path": "/admin/products",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_catalogue_products_index": {
        "method": "GET",
        "path": "/admin/products",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_catalogue_products_show": {
        "method": "GET",
        "path": "/admin/products/{product}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "product"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_catalogue_products_update": {
        "method": "PUT",
        "path": "/admin/products/{product}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "product"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_communications_announcements_create": {
        "method": "POST",
        "path": "/communications/announcements",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_communications_announcements_delete": {
        "method": "DELETE",
        "path": "/communications/announcements/{announcement}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "announcement"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_communications_announcements_index": {
        "method": "GET",
        "path": "/communications/announcements",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_communications_announcements_show": {
        "method": "GET",
        "path": "/communications/announcements/{announcement}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "announcement"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_communications_announcements_update": {
        "method": "PUT",
        "path": "/communications/announcements/{announcement}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "announcement"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_communications_emailTemplates_create": {
        "method": "POST",
        "path": "/communications/email-templates",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_communications_emailTemplates_delete": {
        "method": "DELETE",
        "path": "/communications/email-templates/{template}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "template"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_communications_emailTemplates_index": {
        "method": "GET",
        "path": "/communications/email-templates",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_communications_emailTemplates_show": {
        "method": "GET",
        "path": "/communications/email-templates/{template}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "template"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_communications_emailTemplates_update": {
        "method": "PUT",
        "path": "/communications/email-templates/{template}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "template"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_communications_testimonials_create": {
        "method": "POST",
        "path": "/communications/testimonials",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_communications_testimonials_delete": {
        "method": "DELETE",
        "path": "/communications/testimonials/{testimonial}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "testimonial"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_communications_testimonials_index": {
        "method": "GET",
        "path": "/communications/testimonials",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_communications_testimonials_show": {
        "method": "GET",
        "path": "/communications/testimonials/{testimonial}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "testimonial"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_communications_testimonials_update": {
        "method": "PUT",
        "path": "/communications/testimonials/{testimonial}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "testimonial"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_content_articles_publish": {
        "method": "POST",
        "path": "/content/articles/{article}/publish",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "article"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_contracts_activate": {
        "method": "POST",
        "path": "/contracts/{contract}/activate",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "contract"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_contracts_create": {
        "method": "POST",
        "path": "/contracts",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_contracts_delete": {
        "method": "DELETE",
        "path": "/contracts/{contract}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "contract"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_contracts_terminate": {
        "method": "POST",
        "path": "/contracts/{contract}/terminate",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "contract"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_contracts_update": {
        "method": "PUT",
        "path": "/contracts/{contract}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "contract"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_crm_deals_create": {
        "method": "POST",
        "path": "/crm/deals",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_crm_deals_delete": {
        "method": "DELETE",
        "path": "/crm/deals/{deal}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "deal"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_crm_deals_update": {
        "method": "PUT",
        "path": "/crm/deals/{deal}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "deal"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_crm_demoRequests_create": {
        "method": "POST",
        "path": "/crm/demo-requests",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_crm_demoRequests_delete": {
        "method": "DELETE",
        "path": "/crm/demo-requests/{demoRequest}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "demoRequest"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_crm_demoRequests_index": {
        "method": "GET",
        "path": "/crm/demo-requests",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_crm_demoRequests_show": {
        "method": "GET",
        "path": "/crm/demo-requests/{demoRequest}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "demoRequest"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_crm_demoRequests_update": {
        "method": "PUT",
        "path": "/crm/demo-requests/{demoRequest}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "demoRequest"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_documents_create": {
        "method": "POST",
        "path": "/documents",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_documents_delete": {
        "method": "DELETE",
        "path": "/documents/{document}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "document"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_documents_markSent": {
        "method": "POST",
        "path": "/documents/{document}/mark-sent",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "document"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_documents_templates_create": {
        "method": "POST",
        "path": "/document-templates",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_documents_templates_delete": {
        "method": "DELETE",
        "path": "/document-templates/{template}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "template"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_documents_templates_index": {
        "method": "GET",
        "path": "/document-templates",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_documents_templates_show": {
        "method": "GET",
        "path": "/document-templates/{template}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "template"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_documents_templates_update": {
        "method": "PUT",
        "path": "/document-templates/{template}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "template"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_documents_update": {
        "method": "PUT",
        "path": "/documents/{document}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "document"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_documents_void": {
        "method": "POST",
        "path": "/documents/{document}/void",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "document"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_enquiries_leads_sendFollowUp": {
        "method": "POST",
        "path": "/admin/enquiries/leads/{lead}/follow-up",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "lead"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_budgets_create": {
        "method": "POST",
        "path": "/finance/budgets",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_budgets_delete": {
        "method": "DELETE",
        "path": "/finance/budgets/{budget}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "budget"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_budgets_index": {
        "method": "GET",
        "path": "/finance/budgets",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_budgets_show": {
        "method": "GET",
        "path": "/finance/budgets/{budget}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "budget"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_budgets_update": {
        "method": "PUT",
        "path": "/finance/budgets/{budget}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "budget"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_creditNotes_create": {
        "method": "POST",
        "path": "/finance/credit-notes",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_creditNotes_delete": {
        "method": "DELETE",
        "path": "/finance/credit-notes/{creditNote}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "creditNote"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_creditNotes_issue": {
        "method": "POST",
        "path": "/finance/credit-notes/{creditNote}/issue",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "creditNote"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_creditNotes_update": {
        "method": "PUT",
        "path": "/finance/credit-notes/{creditNote}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "creditNote"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_creditNotes_void": {
        "method": "POST",
        "path": "/finance/credit-notes/{creditNote}/void",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "creditNote"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_expenses_approve": {
        "method": "POST",
        "path": "/finance/expenses/{expense}/approve",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "expense"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_expenses_create": {
        "method": "POST",
        "path": "/finance/expenses",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_expenses_markPaid": {
        "method": "POST",
        "path": "/finance/expenses/{expense}/mark-paid",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "expense"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_expenses_update": {
        "method": "PUT",
        "path": "/finance/expenses/{expense}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "expense"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_invoiceTemplates_generate": {
        "method": "POST",
        "path": "/finance/invoice-templates/{invoiceTemplate}/generate-invoice",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "invoiceTemplate"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_invoices_create": {
        "method": "POST",
        "path": "/finance/invoices",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_invoices_delete": {
        "method": "DELETE",
        "path": "/finance/invoices/{invoice}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "invoice"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_invoices_markManySent": {
        "method": "POST",
        "path": "/finance/invoices/mark-sent",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_invoices_markOverdue": {
        "method": "POST",
        "path": "/finance/invoices/{invoice}/mark-overdue",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "invoice"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_invoices_markPaid": {
        "method": "POST",
        "path": "/finance/invoices/{invoice}/mark-paid",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "invoice"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_invoices_markSent": {
        "method": "POST",
        "path": "/finance/invoices/{invoice}/mark-sent",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "invoice"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_invoices_update": {
        "method": "PUT",
        "path": "/finance/invoices/{invoice}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "invoice"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_payrollDeductionTypes_create": {
        "method": "POST",
        "path": "/finance/payroll-deduction-types",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_payrollDeductionTypes_delete": {
        "method": "DELETE",
        "path": "/finance/payroll-deduction-types/{deductionType}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "deductionType"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_payrollDeductionTypes_index": {
        "method": "GET",
        "path": "/finance/payroll-deduction-types",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_payrollDeductionTypes_show": {
        "method": "GET",
        "path": "/finance/payroll-deduction-types/{deductionType}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "deductionType"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_payrollDeductionTypes_update": {
        "method": "PUT",
        "path": "/finance/payroll-deduction-types/{deductionType}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "deductionType"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_payrollEntries_markManyPaid": {
        "method": "POST",
        "path": "/finance/payroll-runs/{run}/entries/mark-paid",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "run"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_payrollEntries_markPaid": {
        "method": "POST",
        "path": "/finance/payroll-runs/{run}/entries/{entry}/mark-paid",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "run",
            "entry"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_payrollRuns_complete": {
        "method": "POST",
        "path": "/finance/payroll-runs/{run}/complete",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "run"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_payrollRuns_create": {
        "method": "POST",
        "path": "/finance/payroll-runs",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_payrollRuns_delete": {
        "method": "DELETE",
        "path": "/finance/payroll-runs/{run}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "run"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_payrollRuns_generateEntries": {
        "method": "POST",
        "path": "/finance/payroll-runs/{run}/generate-entries",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "run"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_payrollRuns_update": {
        "method": "PUT",
        "path": "/finance/payroll-runs/{run}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "run"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_purchaseOrders_approve": {
        "method": "POST",
        "path": "/finance/purchase-orders/{purchaseOrder}/approve",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "purchaseOrder"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_purchaseOrders_create": {
        "method": "POST",
        "path": "/finance/purchase-orders",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_purchaseOrders_delete": {
        "method": "DELETE",
        "path": "/finance/purchase-orders/{purchaseOrder}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "purchaseOrder"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_purchaseOrders_receive": {
        "method": "POST",
        "path": "/finance/purchase-orders/{purchaseOrder}/receive",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "purchaseOrder"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_purchaseOrders_update": {
        "method": "PUT",
        "path": "/finance/purchase-orders/{purchaseOrder}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "purchaseOrder"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_quotes_accept": {
        "method": "POST",
        "path": "/finance/quotes/{quote}/accept",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "quote"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_quotes_create": {
        "method": "POST",
        "path": "/finance/quotes",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_quotes_delete": {
        "method": "DELETE",
        "path": "/finance/quotes/{quote}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "quote"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_quotes_markSent": {
        "method": "POST",
        "path": "/finance/quotes/{quote}/mark-sent",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "quote"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_quotes_update": {
        "method": "PUT",
        "path": "/finance/quotes/{quote}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "quote"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_supplierBills_approve": {
        "method": "POST",
        "path": "/finance/supplier-bills/{supplierBill}/approve",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "supplierBill"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_supplierBills_create": {
        "method": "POST",
        "path": "/finance/supplier-bills",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_supplierBills_delete": {
        "method": "DELETE",
        "path": "/finance/supplier-bills/{supplierBill}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "supplierBill"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_supplierBills_markPaid": {
        "method": "POST",
        "path": "/finance/supplier-bills/{supplierBill}/mark-paid",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "supplierBill"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_supplierBills_update": {
        "method": "PUT",
        "path": "/finance/supplier-bills/{supplierBill}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "supplierBill"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_vendors_create": {
        "method": "POST",
        "path": "/finance/vendors",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_finance_vendors_delete": {
        "method": "DELETE",
        "path": "/finance/vendors/{vendor}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "vendor"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_vendors_index": {
        "method": "GET",
        "path": "/finance/vendors",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_vendors_show": {
        "method": "GET",
        "path": "/finance/vendors/{vendor}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "vendor"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_finance_vendors_update": {
        "method": "PUT",
        "path": "/finance/vendors/{vendor}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "vendor"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_identity_users_create": {
        "method": "POST",
        "path": "/admin/users",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_identity_users_delete": {
        "method": "DELETE",
        "path": "/admin/users/{user}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "user"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_identity_users_sendWelcome": {
        "method": "POST",
        "path": "/admin/users/{user}/send-welcome",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "user"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_identity_users_update": {
        "method": "PUT",
        "path": "/admin/users/{user}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "user"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_learning_enrollments_complete": {
        "method": "POST",
        "path": "/learning/enrollments/{enrollment}/complete",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "enrollment"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_operations_assets_create": {
        "method": "POST",
        "path": "/operations/assets",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_operations_assets_delete": {
        "method": "DELETE",
        "path": "/operations/assets/{asset}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "asset"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_operations_assets_update": {
        "method": "PUT",
        "path": "/operations/assets/{asset}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "asset"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_practitioners_applications_approve": {
        "method": "POST",
        "path": "/practitioners/applications/{application}/approve",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_practitioners_applications_placeInCohort": {
        "method": "POST",
        "path": "/practitioners/applications/{application}/place-in-cohort",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_practitioners_applications_reject": {
        "method": "POST",
        "path": "/practitioners/applications/{application}/reject",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_practitioners_findings_publish": {
        "method": "POST",
        "path": "/practitioners/findings/{finding}/publish",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "finding"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_practitioners_findings_unpublish": {
        "method": "POST",
        "path": "/practitioners/findings/{finding}/unpublish",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "finding"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_practitioners_profiles_verify": {
        "method": "POST",
        "path": "/practitioners/profiles/{profile}/verify",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "profile"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_practitioners_suggestions_respond": {
        "method": "POST",
        "path": "/practitioners/suggestions/{suggestion}/respond",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "suggestion"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_publishing_blogPosts_create": {
        "method": "POST",
        "path": "/publishing/blog-posts",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_publishing_blogPosts_delete": {
        "method": "DELETE",
        "path": "/publishing/blog-posts/{blogPost}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "blogPost"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_publishing_blogPosts_index": {
        "method": "GET",
        "path": "/publishing/blog-posts",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_publishing_blogPosts_show": {
        "method": "GET",
        "path": "/publishing/blog-posts/{blogPost}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "blogPost"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_publishing_blogPosts_update": {
        "method": "PUT",
        "path": "/publishing/blog-posts/{blogPost}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "blogPost"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_recruitment_applications_create": {
        "method": "POST",
        "path": "/recruitment/applications",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data"
        ]
    },
    "admin_recruitment_applications_delete": {
        "method": "DELETE",
        "path": "/recruitment/applications/{application}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_recruitment_applications_hire": {
        "method": "POST",
        "path": "/recruitment/applications/{application}/hire",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_recruitment_applications_index": {
        "method": "GET",
        "path": "/recruitment/applications",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_recruitment_applications_makeOffer": {
        "method": "POST",
        "path": "/recruitment/applications/{application}/make-offer",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_recruitment_applications_reject": {
        "method": "POST",
        "path": "/recruitment/applications/{application}/reject",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_recruitment_applications_scheduleInterview": {
        "method": "POST",
        "path": "/recruitment/applications/{application}/schedule-interview",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_recruitment_applications_shortlist": {
        "method": "POST",
        "path": "/recruitment/applications/{application}/shortlist",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_recruitment_applications_show": {
        "method": "GET",
        "path": "/recruitment/applications/{application}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_recruitment_applications_update": {
        "method": "PUT",
        "path": "/recruitment/applications/{application}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data"
        ]
    },
    "admin_recruitment_openings_close": {
        "method": "POST",
        "path": "/recruitment/openings/{opening}/close",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "opening"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_recruitment_openings_create": {
        "method": "POST",
        "path": "/recruitment/openings",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_recruitment_openings_delete": {
        "method": "DELETE",
        "path": "/recruitment/openings/{opening}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "opening"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_recruitment_openings_index": {
        "method": "GET",
        "path": "/recruitment/openings",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_recruitment_openings_show": {
        "method": "GET",
        "path": "/recruitment/openings/{opening}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "opening"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_recruitment_openings_update": {
        "method": "PUT",
        "path": "/recruitment/openings/{opening}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "opening"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_reports_workforce_summary": {
        "method": "GET",
        "path": "/admin/reports/workforce-summary",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_risk_mitigate": {
        "method": "POST",
        "path": "/risk/{risk}/mitigate",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "risk"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_settings_show": {
        "method": "GET",
        "path": "/admin/company-settings",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_settings_update": {
        "method": "POST",
        "path": "/admin/company-settings",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_support_bugReports_respond": {
        "method": "POST",
        "path": "/practitioners/bug-reports/{bugReport}/respond",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "bugReport"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_support_knowledgeArticles_archive": {
        "method": "POST",
        "path": "/support/knowledge-base/articles/{article}/archive",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "article"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_support_knowledgeArticles_create": {
        "method": "POST",
        "path": "/support/knowledge-base/articles",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_support_knowledgeArticles_delete": {
        "method": "DELETE",
        "path": "/support/knowledge-base/articles/{article}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "article"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_support_knowledgeArticles_index": {
        "method": "GET",
        "path": "/support/knowledge-base/articles",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_support_knowledgeArticles_show": {
        "method": "GET",
        "path": "/support/knowledge-base/articles/{article}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "article"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_support_knowledgeArticles_update": {
        "method": "PUT",
        "path": "/support/knowledge-base/articles/{article}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "article"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_support_knowledgeCategories_create": {
        "method": "POST",
        "path": "/support/knowledge-base/categories",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_support_knowledgeCategories_delete": {
        "method": "DELETE",
        "path": "/support/knowledge-base/categories/{category}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "category"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_support_knowledgeCategories_index": {
        "method": "GET",
        "path": "/support/knowledge-base/categories",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_support_knowledgeCategories_show": {
        "method": "GET",
        "path": "/support/knowledge-base/categories/{category}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "category"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_support_knowledgeCategories_update": {
        "method": "PUT",
        "path": "/support/knowledge-base/categories/{category}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "category"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_support_serviceRequests_complete": {
        "method": "POST",
        "path": "/support/service-requests/{serviceRequest}/complete",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "serviceRequest"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_support_serviceRequests_confirm": {
        "method": "POST",
        "path": "/support/service-requests/{serviceRequest}/confirm-assign",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "serviceRequest"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_support_slaRules_create": {
        "method": "POST",
        "path": "/support/sla-rules",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_support_slaRules_delete": {
        "method": "DELETE",
        "path": "/support/sla-rules/{slaRule}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "slaRule"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_support_slaRules_index": {
        "method": "GET",
        "path": "/support/sla-rules",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_support_slaRules_show": {
        "method": "GET",
        "path": "/support/sla-rules/{slaRule}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "slaRule"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_support_slaRules_update": {
        "method": "PUT",
        "path": "/support/sla-rules/{slaRule}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "slaRule"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_support_tickets_reply": {
        "method": "POST",
        "path": "/admin/support/tickets/{ticket}/reply",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "ticket"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_support_tickets_status": {
        "method": "POST",
        "path": "/admin/support/tickets/{ticket}/status",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "ticket"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_testing_assignments_create": {
        "method": "POST",
        "path": "/admin/testing/assignments",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_testing_assignments_delete": {
        "method": "DELETE",
        "path": "/admin/testing/assignments/{testerAssignment}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "testerAssignment"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_testing_assignments_update": {
        "method": "PUT",
        "path": "/admin/testing/assignments/{testerAssignment}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "testerAssignment"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_certificates_inviteToCouncil": {
        "method": "POST",
        "path": "/validation/certificates/{certificate}/invite-to-council",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "certificate"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_cohortMembers_update": {
        "method": "PUT",
        "path": "/validation/cohorts/{cohort}/members/{member}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "cohort",
            "member"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_cohortTestCases_attach": {
        "method": "POST",
        "path": "/validation/cohorts/{cohort}/test-cases",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "cohort"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_cohortTestCases_detach": {
        "method": "DELETE",
        "path": "/validation/cohorts/{cohort}/test-cases/{testCase}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "cohort",
            "testCase"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_cohorts_create": {
        "method": "POST",
        "path": "/validation/cohorts",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_cohorts_delete": {
        "method": "DELETE",
        "path": "/validation/cohorts/{cohort}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "cohort"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_cohorts_index": {
        "method": "GET",
        "path": "/validation/cohorts",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_cohorts_show": {
        "method": "GET",
        "path": "/validation/cohorts/{cohort}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "cohort"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_cohorts_update": {
        "method": "PUT",
        "path": "/validation/cohorts/{cohort}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "cohort"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_councilMembers_index": {
        "method": "GET",
        "path": "/validation/council-members",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_councilMembers_show": {
        "method": "GET",
        "path": "/validation/council-members/{member}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "member"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_councilMembers_update": {
        "method": "PUT",
        "path": "/validation/council-members/{member}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "member"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_developerTasks_assign": {
        "method": "POST",
        "path": "/validation/developer-tasks/{task}/assign",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "task"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_developerTasks_markFixed": {
        "method": "POST",
        "path": "/validation/developer-tasks/{task}/mark-fixed",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "task"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_developerTasks_reopen": {
        "method": "POST",
        "path": "/validation/developer-tasks/{task}/reopen",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "task"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_developerTasks_start": {
        "method": "POST",
        "path": "/validation/developer-tasks/{task}/start",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "task"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_developerTasks_wontFix": {
        "method": "POST",
        "path": "/validation/developer-tasks/{task}/wont-fix",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "task"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_finalEvaluations_create": {
        "method": "POST",
        "path": "/validation/final-evaluations",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_finalEvaluations_issueCertificate": {
        "method": "POST",
        "path": "/validation/final-evaluations/{evaluation}/issue-certificate",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "evaluation"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_finalEvaluations_list": {
        "method": "GET",
        "path": "/validation/final-evaluations",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_finalEvaluations_update": {
        "method": "POST",
        "path": "/validation/final-evaluations/{evaluation}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "evaluation"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_issues_clinicalReview": {
        "method": "POST",
        "path": "/validation/issues/{issue}/clinical-review",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "issue"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_issues_close": {
        "method": "POST",
        "path": "/validation/issues/{issue}/close",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "issue"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_issues_productDecision": {
        "method": "POST",
        "path": "/validation/issues/{issue}/product-decision",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "issue"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_issues_sendToProductReview": {
        "method": "POST",
        "path": "/validation/issues/{issue}/send-to-product-review",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "issue"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_products_create": {
        "method": "POST",
        "path": "/validation/products",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_products_delete": {
        "method": "DELETE",
        "path": "/validation/products/{validationProduct}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "validationProduct"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_products_index": {
        "method": "GET",
        "path": "/validation/products",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_products_show": {
        "method": "GET",
        "path": "/validation/products/{validationProduct}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "validationProduct"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_validation_products_update": {
        "method": "PUT",
        "path": "/validation/products/{validationProduct}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "validationProduct"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_weeklyReviews_create": {
        "method": "POST",
        "path": "/validation/weekly-reviews",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_validation_weeklyReviews_update": {
        "method": "PUT",
        "path": "/validation/weekly-reviews/{weeklyReview}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "weeklyReview"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_attendance_create": {
        "method": "POST",
        "path": "/workforce/attendance-records",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_attendance_delete": {
        "method": "DELETE",
        "path": "/workforce/attendance-records/{attendanceRecord}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "attendanceRecord"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_attendance_index": {
        "method": "GET",
        "path": "/workforce/attendance-records",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_attendance_show": {
        "method": "GET",
        "path": "/workforce/attendance-records/{attendanceRecord}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "attendanceRecord"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_attendance_update": {
        "method": "PUT",
        "path": "/workforce/attendance-records/{attendanceRecord}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "attendanceRecord"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_departments_create": {
        "method": "POST",
        "path": "/departments",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_departments_delete": {
        "method": "DELETE",
        "path": "/departments/{department}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "department"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_departments_index": {
        "method": "GET",
        "path": "/departments",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_departments_show": {
        "method": "GET",
        "path": "/departments/{department}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "department"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_departments_update": {
        "method": "PUT",
        "path": "/departments/{department}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "department"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_leaveTypes_create": {
        "method": "POST",
        "path": "/leave-types",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_leaveTypes_delete": {
        "method": "DELETE",
        "path": "/leave-types/{leaveType}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "leaveType"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_leaveTypes_index": {
        "method": "GET",
        "path": "/leave-types",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_leaveTypes_show": {
        "method": "GET",
        "path": "/leave-types/{leaveType}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "leaveType"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_leaveTypes_update": {
        "method": "PUT",
        "path": "/leave-types/{leaveType}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "leaveType"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_leaves_approve": {
        "method": "POST",
        "path": "/admin/leaves/{leave}/approve",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "leave"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_leaves_bulkApprove": {
        "method": "POST",
        "path": "/admin/leaves/bulk-approve",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_leaves_bulkReject": {
        "method": "POST",
        "path": "/admin/leaves/bulk-reject",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_leaves_reject": {
        "method": "POST",
        "path": "/admin/leaves/{leave}/reject",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "leave"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_milestones_complete": {
        "method": "POST",
        "path": "/operations/milestones/{milestone}/complete",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "milestone"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_performanceReviews_acknowledge": {
        "method": "POST",
        "path": "/admin/performance-reviews/{review}/acknowledge",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "review"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_performanceReviews_submit": {
        "method": "POST",
        "path": "/admin/performance-reviews/{review}/submit",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "review"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_risks_create": {
        "method": "POST",
        "path": "/risk",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_risks_update": {
        "method": "PUT",
        "path": "/risk/{risk}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "risk"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_salaryGrades_create": {
        "method": "POST",
        "path": "/salary-grades",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_salaryGrades_delete": {
        "method": "DELETE",
        "path": "/salary-grades/{salaryGrade}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "salaryGrade"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_salaryGrades_index": {
        "method": "GET",
        "path": "/salary-grades",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_salaryGrades_show": {
        "method": "GET",
        "path": "/salary-grades/{salaryGrade}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "salaryGrade"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_salaryGrades_update": {
        "method": "PUT",
        "path": "/salary-grades/{salaryGrade}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "salaryGrade"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_timesheets_create": {
        "method": "POST",
        "path": "/workforce/timesheets",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_timesheets_delete": {
        "method": "DELETE",
        "path": "/workforce/timesheets/{timesheet}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "timesheet"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_timesheets_index": {
        "method": "GET",
        "path": "/workforce/timesheets",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_timesheets_show": {
        "method": "GET",
        "path": "/workforce/timesheets/{timesheet}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "timesheet"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_timesheets_update": {
        "method": "PUT",
        "path": "/workforce/timesheets/{timesheet}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "timesheet"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "admin_workforce_training_complete": {
        "method": "POST",
        "path": "/training-records/{trainingRecord}/complete",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "trainingRecord"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_training_create": {
        "method": "POST",
        "path": "/training-records",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data"
        ]
    },
    "admin_workforce_training_delete": {
        "method": "DELETE",
        "path": "/training-records/{trainingRecord}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "trainingRecord"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_training_index": {
        "method": "GET",
        "path": "/training-records",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_training_show": {
        "method": "GET",
        "path": "/training-records/{trainingRecord}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "trainingRecord"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "admin_workforce_training_update": {
        "method": "PUT",
        "path": "/training-records/{trainingRecord}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "trainingRecord"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data"
        ]
    },
    "administration_licenses_create": {
        "method": "POST",
        "path": "/administration/licenses",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "administration_licenses_delete": {
        "method": "DELETE",
        "path": "/administration/licenses/{license}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "license"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "administration_licenses_index": {
        "method": "GET",
        "path": "/administration/licenses",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "administration_licenses_reactivate": {
        "method": "POST",
        "path": "/administration/licenses/{license}/reactivate",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "license"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "administration_licenses_show": {
        "method": "GET",
        "path": "/administration/licenses/{license}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "license"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "administration_licenses_suspend": {
        "method": "POST",
        "path": "/administration/licenses/{license}/suspend",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "license"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "administration_licenses_update": {
        "method": "PUT",
        "path": "/administration/licenses/{license}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "license"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "administration_projects_create": {
        "method": "POST",
        "path": "/administration/projects",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "administration_projects_delete": {
        "method": "DELETE",
        "path": "/administration/projects/{project}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "project"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "administration_projects_index": {
        "method": "GET",
        "path": "/administration/projects",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "administration_projects_show": {
        "method": "GET",
        "path": "/administration/projects/{project}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "project"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "administration_projects_update": {
        "method": "PUT",
        "path": "/administration/projects/{project}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "project"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "blogcontroller_comment": {
        "method": "POST",
        "path": "/public/blog/{slug}/comment",
        "authenticated": false,
        "idempotencyRequired": true,
        "pathParameters": [
            "slug"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "blogcontroller_index": {
        "method": "GET",
        "path": "/public/blog",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "blogcontroller_like": {
        "method": "POST",
        "path": "/public/blog/{slug}/like",
        "authenticated": false,
        "idempotencyRequired": true,
        "pathParameters": [
            "slug"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "blogcontroller_share": {
        "method": "POST",
        "path": "/public/blog/{slug}/share",
        "authenticated": false,
        "idempotencyRequired": true,
        "pathParameters": [
            "slug"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "blogcontroller_show": {
        "method": "GET",
        "path": "/public/blog/{slug}",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [
            "slug"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "brochurecontroller_download": {
        "method": "GET",
        "path": "/public/products/{slug}/brochure",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [
            "slug"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "certificatepdfcontroller_download": {
        "method": "GET",
        "path": "/files/certificates/{certificate}/download",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "certificate"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "contractcontroller_pdf": {
        "method": "GET",
        "path": "/files/contracts/{contract}/pdf",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "contract"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_certificatecontroller_index": {
        "method": "GET",
        "path": "/customer/certificates",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_coursecontroller_enroll": {
        "method": "POST",
        "path": "/customer/courses/{course:slug}/enroll",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "course:slug"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_coursecontroller_index": {
        "method": "GET",
        "path": "/customer/courses",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_coursecontroller_show": {
        "method": "GET",
        "path": "/customer/courses/{course:slug}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "course:slug"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_dashboardcontroller_index": {
        "method": "GET",
        "path": "/customer/dashboard",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_documentcontroller_index": {
        "method": "GET",
        "path": "/customer/documents",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_documentcontroller_show": {
        "method": "GET",
        "path": "/customer/documents/{id}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_invoicecontroller_index": {
        "method": "GET",
        "path": "/customer/invoices",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_invoicecontroller_show": {
        "method": "GET",
        "path": "/customer/invoices/{id}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_knowledgebasecontroller_category": {
        "method": "GET",
        "path": "/customer/knowledge-base/category/{slug}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "slug"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_knowledgebasecontroller_index": {
        "method": "GET",
        "path": "/customer/knowledge-base",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_knowledgebasecontroller_show": {
        "method": "GET",
        "path": "/customer/knowledge-base/{slug}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "slug"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_lessoncontroller_markDone": {
        "method": "POST",
        "path": "/customer/courses/{course:slug}/lessons/{lesson}/done",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "course:slug",
            "lesson"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_lessoncontroller_show": {
        "method": "GET",
        "path": "/customer/courses/{course:slug}/lessons/{lesson}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "course:slug",
            "lesson"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_licensecontroller_index": {
        "method": "GET",
        "path": "/customer/licenses",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_licensecontroller_show": {
        "method": "GET",
        "path": "/customer/licenses/{id}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_profilecontroller_changePassword": {
        "method": "PUT",
        "path": "/customer/profile/password",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "customer_profilecontroller_show": {
        "method": "GET",
        "path": "/customer/profile",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_profilecontroller_update": {
        "method": "PUT",
        "path": "/customer/profile",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "customer_servicerequestcontroller_create": {
        "method": "GET",
        "path": "/customer/service-requests/options",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_servicerequestcontroller_index": {
        "method": "GET",
        "path": "/customer/service-requests",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_servicerequestcontroller_show": {
        "method": "GET",
        "path": "/customer/service-requests/{serviceRequest}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "serviceRequest"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_servicerequestcontroller_store": {
        "method": "POST",
        "path": "/customer/service-requests",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "customer_surveycontroller_index": {
        "method": "GET",
        "path": "/customer/surveys",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_surveycontroller_show": {
        "method": "GET",
        "path": "/customer/surveys/{survey}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "survey"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_surveycontroller_submit": {
        "method": "POST",
        "path": "/customer/surveys/{survey}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "survey"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_ticketcontroller_create": {
        "method": "GET",
        "path": "/customer/tickets/options",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_ticketcontroller_index": {
        "method": "GET",
        "path": "/customer/tickets",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_ticketcontroller_reply": {
        "method": "POST",
        "path": "/customer/tickets/{id}/reply",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "customer_ticketcontroller_show": {
        "method": "GET",
        "path": "/customer/tickets/{id}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "customer_ticketcontroller_store": {
        "method": "POST",
        "path": "/customer/tickets",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "demorequestcontroller_show": {
        "method": "GET",
        "path": "/public/book-demo",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "demorequestcontroller_submit": {
        "method": "POST",
        "path": "/public/book-demo",
        "authenticated": false,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "documentcontroller_pdf": {
        "method": "GET",
        "path": "/files/documents/{document}/pdf",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "document"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "documentcontroller_preview": {
        "method": "GET",
        "path": "/files/documents/{document}/view",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "document"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "documentsigningcontroller_show": {
        "method": "GET",
        "path": "/public/documents/{token}/sign",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [
            "token"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "documentsigningcontroller_sign": {
        "method": "POST",
        "path": "/public/documents/{token}/sign",
        "authenticated": false,
        "idempotencyRequired": true,
        "pathParameters": [
            "token"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "homecontroller_index": {
        "method": "GET",
        "path": "/public/home",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "hr_dashboardcontroller_index": {
        "method": "GET",
        "path": "/hr/dashboard",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "hr_departmentcontroller_index": {
        "method": "GET",
        "path": "/hr/departments",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "hr_departmentcontroller_updateHead": {
        "method": "POST",
        "path": "/hr/departments/{dept}/head",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "dept"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "hr_employeecontroller_index": {
        "method": "GET",
        "path": "/hr/employees",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "hr_employeecontroller_show": {
        "method": "GET",
        "path": "/hr/employees/{user}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "user"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "hr_leavecontroller_approve": {
        "method": "POST",
        "path": "/hr/leave/{id}/approve",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "hr_leavecontroller_index": {
        "method": "GET",
        "path": "/hr/leave",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "hr_leavecontroller_reject": {
        "method": "POST",
        "path": "/hr/leave/{id}/reject",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "hr_payrollcontroller_index": {
        "method": "GET",
        "path": "/hr/payroll",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "hr_payrollcontroller_show": {
        "method": "GET",
        "path": "/hr/payroll/{run}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "run"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "hr_performancecontroller_index": {
        "method": "GET",
        "path": "/hr/performance",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "hr_performancecontroller_store": {
        "method": "POST",
        "path": "/hr/performance",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "hr_profilecontroller_show": {
        "method": "GET",
        "path": "/hr/profile",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "hr_profilecontroller_update": {
        "method": "PATCH",
        "path": "/hr/profile",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "identity_password_request": {
        "method": "POST",
        "path": "/public/password/forgot",
        "authenticated": false,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "identity_password_reset": {
        "method": "POST",
        "path": "/public/password/reset",
        "authenticated": false,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "identity_register": {
        "method": "POST",
        "path": "/public/accounts",
        "authenticated": false,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "inspectIntegration": {
        "method": "GET",
        "path": "/integration",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": false,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "invoicecontroller_pdf": {
        "method": "GET",
        "path": "/files/invoices/{invoice}/pdf",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "invoice"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "issueDelegatedUserToken": {
        "method": "POST",
        "path": "/oauth/user-token",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": false,
        "requestBodyRequired": true,
        "contentTypes": [
            "application/x-www-form-urlencoded"
        ]
    },
    "issueMachineToken": {
        "method": "POST",
        "path": "/oauth/token",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": false,
        "requestBodyRequired": true,
        "contentTypes": [
            "application/x-www-form-urlencoded"
        ]
    },
    "listCatalogueProducts": {
        "method": "GET",
        "path": "/catalogue/products",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page",
            "locale"
        ],
        "acceptLanguage": false,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "manager_dashboardcontroller_index": {
        "method": "GET",
        "path": "/manager/dashboard",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "manager_leavecontroller_approve": {
        "method": "POST",
        "path": "/manager/leave/{id}/approve",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "manager_leavecontroller_index": {
        "method": "GET",
        "path": "/manager/leave",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "manager_leavecontroller_reject": {
        "method": "POST",
        "path": "/manager/leave/{id}/reject",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "manager_performancecontroller_index": {
        "method": "GET",
        "path": "/manager/performance",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "manager_performancecontroller_store": {
        "method": "POST",
        "path": "/manager/performance",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "manager_profilecontroller_show": {
        "method": "GET",
        "path": "/manager/profile",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "manager_profilecontroller_update": {
        "method": "PATCH",
        "path": "/manager/profile",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "manager_reportcontroller_index": {
        "method": "GET",
        "path": "/manager/reports",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "manager_teamcontroller_index": {
        "method": "GET",
        "path": "/manager/team",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "marketcontroller_index": {
        "method": "GET",
        "path": "/public/markets",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "marketcontroller_show": {
        "method": "GET",
        "path": "/public/markets/{country}",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [
            "country"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "notificationcontroller_index": {
        "method": "GET",
        "path": "/notifications",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "notificationcontroller_markAllRead": {
        "method": "POST",
        "path": "/notifications/read-all",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "notificationcontroller_markRead": {
        "method": "POST",
        "path": "/notifications/{id}/read",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "partnerapplicationcontroller_show": {
        "method": "GET",
        "path": "/public/become-a-partner",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "partnerapplicationcontroller_submit": {
        "method": "POST",
        "path": "/public/become-a-partner",
        "authenticated": false,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "partnershipscontroller_index": {
        "method": "GET",
        "path": "/public/partnerships",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "payrollcontroller_payslip": {
        "method": "GET",
        "path": "/files/payroll/entries/{entry}/payslip",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "entry"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_applicationcontroller_index": {
        "method": "GET",
        "path": "/practitioner/applications",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_applicationcontroller_show": {
        "method": "GET",
        "path": "/practitioner/applications/{application}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_bugreportcontroller_create": {
        "method": "GET",
        "path": "/practitioner/bug-reports/options",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_bugreportcontroller_index": {
        "method": "GET",
        "path": "/practitioner/bug-reports",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_bugreportcontroller_show": {
        "method": "GET",
        "path": "/practitioner/bug-reports/{bugReport}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "bugReport"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_bugreportcontroller_store": {
        "method": "POST",
        "path": "/practitioner/bug-reports",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data"
        ]
    },
    "practitioner_certificatecontroller_downloadValidation": {
        "method": "GET",
        "path": "/practitioner/certificates/validation/{certificate}/download",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "certificate"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_certificatecontroller_index": {
        "method": "GET",
        "path": "/practitioner/certificates",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_coursecontroller_enroll": {
        "method": "POST",
        "path": "/practitioner/courses/{course:slug}/enroll",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "course:slug"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_coursecontroller_index": {
        "method": "GET",
        "path": "/practitioner/courses",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_coursecontroller_show": {
        "method": "GET",
        "path": "/practitioner/courses/{course:slug}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "course:slug"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_dashboardcontroller_index": {
        "method": "GET",
        "path": "/practitioner/dashboard",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_findingcontroller_create": {
        "method": "GET",
        "path": "/practitioner/applications/{application}/findings/options",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_findingcontroller_store": {
        "method": "POST",
        "path": "/practitioner/applications/{application}/findings",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "application"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data"
        ]
    },
    "practitioner_lessoncontroller_markDone": {
        "method": "POST",
        "path": "/practitioner/courses/{course:slug}/lessons/{lesson}/done",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "course:slug",
            "lesson"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_lessoncontroller_show": {
        "method": "GET",
        "path": "/practitioner/courses/{course:slug}/lessons/{lesson}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "course:slug",
            "lesson"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_profilecontroller_changePassword": {
        "method": "PUT",
        "path": "/practitioner/profile/password",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "practitioner_profilecontroller_show": {
        "method": "GET",
        "path": "/practitioner/profile",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_profilecontroller_update": {
        "method": "PUT",
        "path": "/practitioner/profile",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "practitioner_programcontroller_apply": {
        "method": "POST",
        "path": "/practitioner/programs/{program}/apply",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "program"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "practitioner_programcontroller_index": {
        "method": "GET",
        "path": "/practitioner/programs",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_programcontroller_show": {
        "method": "GET",
        "path": "/practitioner/programs/{program}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "program"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_suggestioncontroller_create": {
        "method": "GET",
        "path": "/practitioner/suggestions/options",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_suggestioncontroller_index": {
        "method": "GET",
        "path": "/practitioner/suggestions",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_suggestioncontroller_store": {
        "method": "POST",
        "path": "/practitioner/suggestions",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "practitioner_surveycontroller_index": {
        "method": "GET",
        "path": "/practitioner/surveys",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_surveycontroller_show": {
        "method": "GET",
        "path": "/practitioner/surveys/{survey}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "survey"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_surveycontroller_submit": {
        "method": "POST",
        "path": "/practitioner/surveys/{survey}",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "survey"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_validation_dashboardcontroller_show": {
        "method": "GET",
        "path": "/practitioner/validation",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_validation_issuereportcontroller_create": {
        "method": "GET",
        "path": "/practitioner/validation/issues/options",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_validation_issuereportcontroller_index": {
        "method": "GET",
        "path": "/practitioner/validation/issues",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_validation_issuereportcontroller_show": {
        "method": "GET",
        "path": "/practitioner/validation/issues/{issue}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "issue"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_validation_issuereportcontroller_store": {
        "method": "POST",
        "path": "/practitioner/validation/issues",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "practitioner_validation_retestcontroller_store": {
        "method": "POST",
        "path": "/practitioner/validation/issues/{issue}/retests",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "issue"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "practitioner_validation_sessioncontroller_create": {
        "method": "GET",
        "path": "/practitioner/validation/sessions/options",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_validation_sessioncontroller_index": {
        "method": "GET",
        "path": "/practitioner/validation/sessions",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "practitioner_validation_sessioncontroller_store": {
        "method": "POST",
        "path": "/practitioner/validation/sessions",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "productcontroller_index": {
        "method": "GET",
        "path": "/public/products",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "productcontroller_show": {
        "method": "GET",
        "path": "/public/products/{slug}",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [
            "slug"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "public_practitionerdirectorycontroller_index": {
        "method": "GET",
        "path": "/public/practitioners",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "public_practitionerdirectorycontroller_show": {
        "method": "GET",
        "path": "/public/practitioners/{id}",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "publiccoursecontroller_index": {
        "method": "GET",
        "path": "/public/courses",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "publiccoursecontroller_show": {
        "method": "GET",
        "path": "/public/courses/{course:slug}",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [
            "course:slug"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "purchaseordercontroller_pdf": {
        "method": "GET",
        "path": "/files/purchase-orders/{purchaseOrder}/pdf",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "purchaseOrder"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "quotecontroller_pdf": {
        "method": "GET",
        "path": "/files/quotes/{quote}/pdf",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "quote"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "readOwnBusinessActivity": {
        "method": "GET",
        "path": "/me/activity",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": false,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "submitEnquiry": {
        "method": "POST",
        "path": "/enquiries",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": false,
        "requestBodyRequired": true,
        "contentTypes": [
            "application/json"
        ]
    },
    "supplierbillcontroller_pdf": {
        "method": "GET",
        "path": "/files/supplier-bills/{supplierBill}/pdf",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "supplierBill"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "support_dashboardcontroller_index": {
        "method": "GET",
        "path": "/support/dashboard",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "support_profilecontroller_show": {
        "method": "GET",
        "path": "/support/profile",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "support_profilecontroller_update": {
        "method": "PATCH",
        "path": "/support/profile",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "support_ticketcontroller_assignToMe": {
        "method": "PATCH",
        "path": "/support/tickets/{ticket}/assign",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "ticket"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "support_ticketcontroller_index": {
        "method": "GET",
        "path": "/support/tickets",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "support_ticketcontroller_reply": {
        "method": "POST",
        "path": "/support/tickets/{ticket}/reply",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "ticket"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "support_ticketcontroller_show": {
        "method": "GET",
        "path": "/support/tickets/{ticket}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "ticket"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "support_ticketcontroller_updateStatus": {
        "method": "PATCH",
        "path": "/support/tickets/{ticket}/status",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "ticket"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "tester_assignmentcontroller_index": {
        "method": "GET",
        "path": "/testing/assignments",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "tester_assignmentcontroller_show": {
        "method": "GET",
        "path": "/testing/assignments/{id}",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "tester_assignmentcontroller_storeBugReport": {
        "method": "POST",
        "path": "/testing/assignments/{id}/bug-reports",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "tester_assignmentcontroller_updateStatus": {
        "method": "PATCH",
        "path": "/testing/assignments/{id}/status",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [
            "id"
        ],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "tester_bugreportcontroller_index": {
        "method": "GET",
        "path": "/testing/bug-reports",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "tester_dashboardcontroller_index": {
        "method": "GET",
        "path": "/testing/dashboard",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "tester_profilecontroller_show": {
        "method": "GET",
        "path": "/testing/profile",
        "authenticated": true,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "tester_profilecontroller_update": {
        "method": "PATCH",
        "path": "/testing/profile",
        "authenticated": true,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    },
    "testerapplicationcontroller_show": {
        "method": "GET",
        "path": "/public/join-testers",
        "authenticated": false,
        "idempotencyRequired": false,
        "pathParameters": [],
        "queryParameters": [
            "page",
            "per_page"
        ],
        "acceptLanguage": true,
        "requestBodyRequired": false,
        "contentTypes": []
    },
    "testerapplicationcontroller_submit": {
        "method": "POST",
        "path": "/public/join-testers",
        "authenticated": false,
        "idempotencyRequired": true,
        "pathParameters": [],
        "queryParameters": [],
        "acceptLanguage": true,
        "requestBodyRequired": true,
        "contentTypes": [
            "multipart/form-data",
            "application/json"
        ]
    }
});
