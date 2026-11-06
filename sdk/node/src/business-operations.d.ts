export type BusinessOperationId =
  | 'accountant_dashboardcontroller_index'
  | 'accountant_expensecontroller_approve'
  | 'accountant_expensecontroller_index'
  | 'accountant_expensecontroller_reject'
  | 'accountant_invoicecontroller_index'
  | 'accountant_invoicecontroller_markPaid'
  | 'accountant_invoicecontroller_show'
  | 'accountant_payrollcontroller_index'
  | 'accountant_payrollcontroller_show'
  | 'accountant_profilecontroller_show'
  | 'accountant_profilecontroller_update'
  | 'accountant_reportcontroller_index'
  | 'admin_apiClients_create'
  | 'admin_apiClients_index'
  | 'admin_apiClients_quota'
  | 'admin_apiClients_revoke'
  | 'admin_apiClients_rotate'
  | 'admin_apiClients_setQuota'
  | 'admin_apiClients_usage'
  | 'admin_auditLogs_index'
  | 'admin_auditLogs_show'
  | 'admin_catalogue_products_archive'
  | 'admin_catalogue_products_create'
  | 'admin_catalogue_products_index'
  | 'admin_catalogue_products_show'
  | 'admin_catalogue_products_update'
  | 'admin_communications_announcements_create'
  | 'admin_communications_announcements_delete'
  | 'admin_communications_announcements_index'
  | 'admin_communications_announcements_show'
  | 'admin_communications_announcements_update'
  | 'admin_communications_emailTemplates_create'
  | 'admin_communications_emailTemplates_delete'
  | 'admin_communications_emailTemplates_index'
  | 'admin_communications_emailTemplates_show'
  | 'admin_communications_emailTemplates_update'
  | 'admin_communications_testimonials_create'
  | 'admin_communications_testimonials_delete'
  | 'admin_communications_testimonials_index'
  | 'admin_communications_testimonials_show'
  | 'admin_communications_testimonials_update'
  | 'admin_content_articles_publish'
  | 'admin_contracts_activate'
  | 'admin_contracts_create'
  | 'admin_contracts_delete'
  | 'admin_contracts_expiringSoon'
  | 'admin_contracts_index'
  | 'admin_contracts_terminate'
  | 'admin_contracts_update'
  | 'admin_crm_deals_create'
  | 'admin_crm_deals_delete'
  | 'admin_crm_deals_index'
  | 'admin_crm_deals_pipeline'
  | 'admin_crm_deals_show'
  | 'admin_crm_deals_update'
  | 'admin_crm_demoRequests_create'
  | 'admin_crm_demoRequests_delete'
  | 'admin_crm_demoRequests_index'
  | 'admin_crm_demoRequests_show'
  | 'admin_crm_demoRequests_update'
  | 'admin_customerProfiles_create'
  | 'admin_customerProfiles_index'
  | 'admin_customerProfiles_show'
  | 'admin_customerProfiles_update'
  | 'admin_dashboard_announcements'
  | 'admin_dashboard_outstandingInvoices'
  | 'admin_dashboard_recentLeads'
  | 'admin_dashboard_recentTickets'
  | 'admin_dashboard_summary'
  | 'admin_documents_create'
  | 'admin_documents_delete'
  | 'admin_documents_index'
  | 'admin_documents_markSent'
  | 'admin_documents_templates_create'
  | 'admin_documents_templates_delete'
  | 'admin_documents_templates_index'
  | 'admin_documents_templates_show'
  | 'admin_documents_templates_update'
  | 'admin_documents_update'
  | 'admin_documents_void'
  | 'admin_enquiries_deliveries_confirmReceived'
  | 'admin_enquiries_deliveries_index'
  | 'admin_enquiries_deliveries_show'
  | 'admin_enquiries_integrationRequests_index'
  | 'admin_enquiries_integrationRequests_show'
  | 'admin_enquiries_leads_create'
  | 'admin_enquiries_leads_delete'
  | 'admin_enquiries_leads_index'
  | 'admin_enquiries_leads_sendFollowUp'
  | 'admin_enquiries_leads_show'
  | 'admin_enquiries_leads_update'
  | 'admin_enquiries_partnerApplications_index'
  | 'admin_enquiries_partnerApplications_members_add'
  | 'admin_enquiries_partnerApplications_members_eligible'
  | 'admin_enquiries_partnerApplications_members_index'
  | 'admin_enquiries_partnerApplications_members_remove'
  | 'admin_enquiries_partnerApplications_show'
  | 'admin_enquiries_partnerApplications_update'
  | 'admin_enquiries_testerApplications_index'
  | 'admin_enquiries_testerApplications_show'
  | 'admin_enquiries_testerApplications_update'
  | 'admin_finance_budgets_create'
  | 'admin_finance_budgets_delete'
  | 'admin_finance_budgets_index'
  | 'admin_finance_budgets_show'
  | 'admin_finance_budgets_update'
  | 'admin_finance_creditNoteItems'
  | 'admin_finance_creditNotes_create'
  | 'admin_finance_creditNotes_delete'
  | 'admin_finance_creditNotes_index'
  | 'admin_finance_creditNotes_issue'
  | 'admin_finance_creditNotes_update'
  | 'admin_finance_creditNotes_void'
  | 'admin_finance_expenses_approve'
  | 'admin_finance_expenses_create'
  | 'admin_finance_expenses_index'
  | 'admin_finance_expenses_markPaid'
  | 'admin_finance_expenses_update'
  | 'admin_finance_invoiceItems'
  | 'admin_finance_invoicePayments_index'
  | 'admin_finance_invoiceTemplates_create'
  | 'admin_finance_invoiceTemplates_generate'
  | 'admin_finance_invoiceTemplates_index'
  | 'admin_finance_invoiceTemplates_show'
  | 'admin_finance_invoiceTemplates_update'
  | 'admin_finance_invoices_create'
  | 'admin_finance_invoices_delete'
  | 'admin_finance_invoices_index'
  | 'admin_finance_invoices_markManySent'
  | 'admin_finance_invoices_markOverdue'
  | 'admin_finance_invoices_markPaid'
  | 'admin_finance_invoices_markSent'
  | 'admin_finance_invoices_recordPayment'
  | 'admin_finance_invoices_update'
  | 'admin_finance_payrollDeductionTypes_create'
  | 'admin_finance_payrollDeductionTypes_delete'
  | 'admin_finance_payrollDeductionTypes_index'
  | 'admin_finance_payrollDeductionTypes_show'
  | 'admin_finance_payrollDeductionTypes_update'
  | 'admin_finance_payrollEntries_index'
  | 'admin_finance_payrollEntries_markManyPaid'
  | 'admin_finance_payrollEntries_markPaid'
  | 'admin_finance_payrollRuns_complete'
  | 'admin_finance_payrollRuns_create'
  | 'admin_finance_payrollRuns_delete'
  | 'admin_finance_payrollRuns_generateEntries'
  | 'admin_finance_payrollRuns_index'
  | 'admin_finance_payrollRuns_show'
  | 'admin_finance_payrollRuns_update'
  | 'admin_finance_purchaseOrderItems'
  | 'admin_finance_purchaseOrders_approve'
  | 'admin_finance_purchaseOrders_create'
  | 'admin_finance_purchaseOrders_delete'
  | 'admin_finance_purchaseOrders_index'
  | 'admin_finance_purchaseOrders_receive'
  | 'admin_finance_purchaseOrders_update'
  | 'admin_finance_quoteItems'
  | 'admin_finance_quotes_accept'
  | 'admin_finance_quotes_create'
  | 'admin_finance_quotes_delete'
  | 'admin_finance_quotes_index'
  | 'admin_finance_quotes_markSent'
  | 'admin_finance_quotes_update'
  | 'admin_finance_records_summary'
  | 'admin_finance_reports_accountsPayableAging'
  | 'admin_finance_reports_accountsReceivableAging'
  | 'admin_finance_reports_budgetVsActual'
  | 'admin_finance_reports_cashFlow'
  | 'admin_finance_reports_overview'
  | 'admin_finance_reports_payrollHeadcount'
  | 'admin_finance_reports_profitAndLoss'
  | 'admin_finance_supplierBillItems'
  | 'admin_finance_supplierBills_approve'
  | 'admin_finance_supplierBills_create'
  | 'admin_finance_supplierBills_delete'
  | 'admin_finance_supplierBills_index'
  | 'admin_finance_supplierBills_markPaid'
  | 'admin_finance_supplierBills_update'
  | 'admin_finance_vendors_create'
  | 'admin_finance_vendors_delete'
  | 'admin_finance_vendors_index'
  | 'admin_finance_vendors_show'
  | 'admin_finance_vendors_update'
  | 'admin_identity_roles_index'
  | 'admin_identity_users_create'
  | 'admin_identity_users_delete'
  | 'admin_identity_users_employeeProfile'
  | 'admin_identity_users_index'
  | 'admin_identity_users_sendWelcome'
  | 'admin_identity_users_update'
  | 'admin_learning_assessments_create'
  | 'admin_learning_assessments_delete'
  | 'admin_learning_assessments_index'
  | 'admin_learning_assessments_show'
  | 'admin_learning_assessments_update'
  | 'admin_learning_certificates_index'
  | 'admin_learning_courseLessons_create'
  | 'admin_learning_courseLessons_delete'
  | 'admin_learning_courseLessons_index'
  | 'admin_learning_courseLessons_show'
  | 'admin_learning_courseLessons_update'
  | 'admin_learning_courses_create'
  | 'admin_learning_courses_delete'
  | 'admin_learning_courses_index'
  | 'admin_learning_courses_show'
  | 'admin_learning_courses_update'
  | 'admin_learning_enrollments_complete'
  | 'admin_learning_enrollments_index'
  | 'admin_oauthClients_create'
  | 'admin_oauthClients_index'
  | 'admin_oauthClients_revoke'
  | 'admin_operations_assets_create'
  | 'admin_operations_assets_delete'
  | 'admin_operations_assets_index'
  | 'admin_operations_assets_update'
  | 'admin_operations_milestones_create'
  | 'admin_operations_milestones_delete'
  | 'admin_operations_milestones_index'
  | 'admin_operations_milestones_show'
  | 'admin_operations_milestones_update'
  | 'admin_practitioners_applications_approve'
  | 'admin_practitioners_applications_index'
  | 'admin_practitioners_applications_pay'
  | 'admin_practitioners_applications_placeInCohort'
  | 'admin_practitioners_applications_reject'
  | 'admin_practitioners_applications_show'
  | 'admin_practitioners_applications_updateNotes'
  | 'admin_practitioners_findings_index'
  | 'admin_practitioners_findings_publish'
  | 'admin_practitioners_findings_show'
  | 'admin_practitioners_findings_unpublish'
  | 'admin_practitioners_findings_update'
  | 'admin_practitioners_profiles_index'
  | 'admin_practitioners_profiles_show'
  | 'admin_practitioners_profiles_update'
  | 'admin_practitioners_profiles_verify'
  | 'admin_practitioners_programs_create'
  | 'admin_practitioners_programs_index'
  | 'admin_practitioners_programs_show'
  | 'admin_practitioners_programs_update'
  | 'admin_practitioners_suggestions_index'
  | 'admin_practitioners_suggestions_respond'
  | 'admin_programmes_surveyQuestions_create'
  | 'admin_programmes_surveyQuestions_delete'
  | 'admin_programmes_surveyQuestions_index'
  | 'admin_programmes_surveyQuestions_show'
  | 'admin_programmes_surveyQuestions_update'
  | 'admin_programmes_surveys_activate'
  | 'admin_programmes_surveys_close'
  | 'admin_programmes_surveys_create'
  | 'admin_programmes_surveys_delete'
  | 'admin_programmes_surveys_index'
  | 'admin_programmes_surveys_show'
  | 'admin_programmes_surveys_update'
  | 'admin_publishing_blogPosts_create'
  | 'admin_publishing_blogPosts_delete'
  | 'admin_publishing_blogPosts_index'
  | 'admin_publishing_blogPosts_show'
  | 'admin_publishing_blogPosts_update'
  | 'admin_publishing_partnerInstitutions_create'
  | 'admin_publishing_partnerInstitutions_index'
  | 'admin_publishing_partnerInstitutions_show'
  | 'admin_publishing_partnerInstitutions_update'
  | 'admin_recruitment_applications_create'
  | 'admin_recruitment_applications_delete'
  | 'admin_recruitment_applications_hire'
  | 'admin_recruitment_applications_index'
  | 'admin_recruitment_applications_makeOffer'
  | 'admin_recruitment_applications_reject'
  | 'admin_recruitment_applications_scheduleInterview'
  | 'admin_recruitment_applications_shortlist'
  | 'admin_recruitment_applications_show'
  | 'admin_recruitment_applications_update'
  | 'admin_recruitment_openings_close'
  | 'admin_recruitment_openings_create'
  | 'admin_recruitment_openings_delete'
  | 'admin_recruitment_openings_index'
  | 'admin_recruitment_openings_show'
  | 'admin_recruitment_openings_update'
  | 'admin_references_auditLogFilters'
  | 'admin_references_cohortMembers'
  | 'admin_references_cohorts'
  | 'admin_references_customerCountries'
  | 'admin_references_departments'
  | 'admin_references_documentTemplates'
  | 'admin_references_invoices'
  | 'admin_references_jobOpenings'
  | 'admin_references_knowledgeCategories'
  | 'admin_references_leads'
  | 'admin_references_licenses'
  | 'admin_references_nextEmployeeId'
  | 'admin_references_projects'
  | 'admin_references_purchaseOrders'
  | 'admin_references_rolePermissions'
  | 'admin_references_salaryGrades'
  | 'admin_references_testCases'
  | 'admin_references_userRoles'
  | 'admin_references_users'
  | 'admin_references_validationPrograms'
  | 'admin_references_vendors'
  | 'admin_reports_contractsExpiry'
  | 'admin_reports_crmSummary'
  | 'admin_reports_dashboard'
  | 'admin_reports_operational_overview'
  | 'admin_reports_peopleSummary'
  | 'admin_reports_workforce_summary'
  | 'admin_resourceSummaries_metric'
  | 'admin_risk_mitigate'
  | 'admin_settings_show'
  | 'admin_settings_update'
  | 'admin_support_bugReports_index'
  | 'admin_support_bugReports_respond'
  | 'admin_support_knowledgeArticles_archive'
  | 'admin_support_knowledgeArticles_create'
  | 'admin_support_knowledgeArticles_delete'
  | 'admin_support_knowledgeArticles_index'
  | 'admin_support_knowledgeArticles_show'
  | 'admin_support_knowledgeArticles_update'
  | 'admin_support_knowledgeCategories_create'
  | 'admin_support_knowledgeCategories_delete'
  | 'admin_support_knowledgeCategories_index'
  | 'admin_support_knowledgeCategories_show'
  | 'admin_support_knowledgeCategories_update'
  | 'admin_support_serviceRequests_cancel'
  | 'admin_support_serviceRequests_complete'
  | 'admin_support_serviceRequests_confirm'
  | 'admin_support_serviceRequests_index'
  | 'admin_support_serviceRequests_show'
  | 'admin_support_serviceRequests_update'
  | 'admin_support_slaRules_create'
  | 'admin_support_slaRules_delete'
  | 'admin_support_slaRules_index'
  | 'admin_support_slaRules_show'
  | 'admin_support_slaRules_update'
  | 'admin_support_tickets_create'
  | 'admin_support_tickets_reply'
  | 'admin_support_tickets_status'
  | 'admin_support_tickets_update'
  | 'admin_testing_assignments_create'
  | 'admin_testing_assignments_delete'
  | 'admin_testing_assignments_index'
  | 'admin_testing_assignments_update'
  | 'admin_validation_certificates_index'
  | 'admin_validation_certificates_inviteToCouncil'
  | 'admin_validation_cohortMembers_index'
  | 'admin_validation_cohortMembers_update'
  | 'admin_validation_cohortTestCases_attach'
  | 'admin_validation_cohortTestCases_detach'
  | 'admin_validation_cohortTestCases_index'
  | 'admin_validation_cohorts_create'
  | 'admin_validation_cohorts_delete'
  | 'admin_validation_cohorts_index'
  | 'admin_validation_cohorts_show'
  | 'admin_validation_cohorts_update'
  | 'admin_validation_councilMembers_index'
  | 'admin_validation_councilMembers_show'
  | 'admin_validation_councilMembers_update'
  | 'admin_validation_dailyTestSessions_index'
  | 'admin_validation_developerTasks_assign'
  | 'admin_validation_developerTasks_index'
  | 'admin_validation_developerTasks_markFixed'
  | 'admin_validation_developerTasks_reopen'
  | 'admin_validation_developerTasks_retests'
  | 'admin_validation_developerTasks_start'
  | 'admin_validation_developerTasks_wontFix'
  | 'admin_validation_finalEvaluations_create'
  | 'admin_validation_finalEvaluations_issueCertificate'
  | 'admin_validation_finalEvaluations_list'
  | 'admin_validation_finalEvaluations_update'
  | 'admin_validation_issues_clinicalReview'
  | 'admin_validation_issues_close'
  | 'admin_validation_issues_index'
  | 'admin_validation_issues_productDecision'
  | 'admin_validation_issues_sendToProductReview'
  | 'admin_validation_metrics_cohortProgress'
  | 'admin_validation_metrics_developerThroughput'
  | 'admin_validation_metrics_issueAnalytics'
  | 'admin_validation_metrics_practitionerLeaderboard'
  | 'admin_validation_modules_create'
  | 'admin_validation_modules_delete'
  | 'admin_validation_modules_index'
  | 'admin_validation_modules_show'
  | 'admin_validation_modules_update'
  | 'admin_validation_products_create'
  | 'admin_validation_products_delete'
  | 'admin_validation_products_index'
  | 'admin_validation_products_show'
  | 'admin_validation_products_update'
  | 'admin_validation_testCases_create'
  | 'admin_validation_testCases_delete'
  | 'admin_validation_testCases_index'
  | 'admin_validation_testCases_show'
  | 'admin_validation_testCases_update'
  | 'admin_validation_weeklyReviews_create'
  | 'admin_validation_weeklyReviews_index'
  | 'admin_validation_weeklyReviews_update'
  | 'admin_validation_workflows_create'
  | 'admin_validation_workflows_delete'
  | 'admin_validation_workflows_index'
  | 'admin_validation_workflows_show'
  | 'admin_validation_workflows_update'
  | 'admin_workforce_attendance_create'
  | 'admin_workforce_attendance_delete'
  | 'admin_workforce_attendance_index'
  | 'admin_workforce_attendance_show'
  | 'admin_workforce_attendance_update'
  | 'admin_workforce_departments_create'
  | 'admin_workforce_departments_delete'
  | 'admin_workforce_departments_index'
  | 'admin_workforce_departments_show'
  | 'admin_workforce_departments_update'
  | 'admin_workforce_employees_create'
  | 'admin_workforce_employees_index'
  | 'admin_workforce_employees_show'
  | 'admin_workforce_employees_update'
  | 'admin_workforce_leaveBalances_create'
  | 'admin_workforce_leaveBalances_delete'
  | 'admin_workforce_leaveBalances_index'
  | 'admin_workforce_leaveBalances_show'
  | 'admin_workforce_leaveBalances_update'
  | 'admin_workforce_leaveTypes_create'
  | 'admin_workforce_leaveTypes_delete'
  | 'admin_workforce_leaveTypes_index'
  | 'admin_workforce_leaveTypes_show'
  | 'admin_workforce_leaveTypes_update'
  | 'admin_workforce_leaves_approve'
  | 'admin_workforce_leaves_bulkApprove'
  | 'admin_workforce_leaves_bulkReject'
  | 'admin_workforce_leaves_create'
  | 'admin_workforce_leaves_index'
  | 'admin_workforce_leaves_reject'
  | 'admin_workforce_leaves_show'
  | 'admin_workforce_leaves_update'
  | 'admin_workforce_milestones_complete'
  | 'admin_workforce_performanceReviews_acknowledge'
  | 'admin_workforce_performanceReviews_create'
  | 'admin_workforce_performanceReviews_index'
  | 'admin_workforce_performanceReviews_show'
  | 'admin_workforce_performanceReviews_submit'
  | 'admin_workforce_performanceReviews_update'
  | 'admin_workforce_risks_create'
  | 'admin_workforce_risks_index'
  | 'admin_workforce_risks_show'
  | 'admin_workforce_risks_update'
  | 'admin_workforce_salaryGrades_create'
  | 'admin_workforce_salaryGrades_delete'
  | 'admin_workforce_salaryGrades_index'
  | 'admin_workforce_salaryGrades_show'
  | 'admin_workforce_salaryGrades_update'
  | 'admin_workforce_timesheets_create'
  | 'admin_workforce_timesheets_delete'
  | 'admin_workforce_timesheets_index'
  | 'admin_workforce_timesheets_show'
  | 'admin_workforce_timesheets_update'
  | 'admin_workforce_training_complete'
  | 'admin_workforce_training_create'
  | 'admin_workforce_training_delete'
  | 'admin_workforce_training_index'
  | 'admin_workforce_training_show'
  | 'admin_workforce_training_update'
  | 'administration_licenses_create'
  | 'administration_licenses_delete'
  | 'administration_licenses_index'
  | 'administration_licenses_reactivate'
  | 'administration_licenses_show'
  | 'administration_licenses_suspend'
  | 'administration_licenses_update'
  | 'administration_projects_create'
  | 'administration_projects_delete'
  | 'administration_projects_index'
  | 'administration_projects_show'
  | 'administration_projects_update'
  | 'api_product_catalogue'
  | 'api_product_changelog'
  | 'api_product_discovery'
  | 'api_product_status'
  | 'blogcontroller_comment'
  | 'blogcontroller_index'
  | 'blogcontroller_like'
  | 'blogcontroller_share'
  | 'blogcontroller_show'
  | 'brochurecontroller_download'
  | 'certificatepdfcontroller_download'
  | 'contractcontroller_pdf'
  | 'customer_certificatecontroller_index'
  | 'customer_coursecontroller_enroll'
  | 'customer_coursecontroller_index'
  | 'customer_coursecontroller_show'
  | 'customer_dashboardcontroller_index'
  | 'customer_documentcontroller_index'
  | 'customer_documentcontroller_show'
  | 'customer_invoicecontroller_index'
  | 'customer_invoicecontroller_show'
  | 'customer_knowledgebasecontroller_category'
  | 'customer_knowledgebasecontroller_index'
  | 'customer_knowledgebasecontroller_show'
  | 'customer_lessoncontroller_markDone'
  | 'customer_lessoncontroller_show'
  | 'customer_licensecontroller_index'
  | 'customer_licensecontroller_show'
  | 'customer_profilecontroller_changePassword'
  | 'customer_profilecontroller_show'
  | 'customer_profilecontroller_update'
  | 'customer_servicerequestcontroller_create'
  | 'customer_servicerequestcontroller_index'
  | 'customer_servicerequestcontroller_show'
  | 'customer_servicerequestcontroller_store'
  | 'customer_surveycontroller_index'
  | 'customer_surveycontroller_show'
  | 'customer_surveycontroller_submit'
  | 'customer_ticketcontroller_create'
  | 'customer_ticketcontroller_index'
  | 'customer_ticketcontroller_reply'
  | 'customer_ticketcontroller_show'
  | 'customer_ticketcontroller_store'
  | 'demorequestcontroller_show'
  | 'demorequestcontroller_submit'
  | 'developer_machine_clients_create'
  | 'developer_machine_clients_index'
  | 'developer_machine_clients_revoke'
  | 'developer_machine_clients_rotate'
  | 'developer_oauth_applications_create'
  | 'developer_oauth_applications_index'
  | 'developer_oauth_applications_revoke'
  | 'developer_oauth_applications_update'
  | 'documentcontroller_pdf'
  | 'documentcontroller_preview'
  | 'documentsigningcontroller_show'
  | 'documentsigningcontroller_sign'
  | 'homecontroller_index'
  | 'hr_dashboardcontroller_index'
  | 'hr_departmentcontroller_index'
  | 'hr_departmentcontroller_updateHead'
  | 'hr_employeecontroller_index'
  | 'hr_employeecontroller_show'
  | 'hr_leavecontroller_approve'
  | 'hr_leavecontroller_index'
  | 'hr_leavecontroller_reject'
  | 'hr_payrollcontroller_index'
  | 'hr_payrollcontroller_show'
  | 'hr_performancecontroller_index'
  | 'hr_performancecontroller_store'
  | 'hr_profilecontroller_show'
  | 'hr_profilecontroller_update'
  | 'identity_password_request'
  | 'identity_password_reset'
  | 'identity_register'
  | 'inspectIntegration'
  | 'invoicecontroller_pdf'
  | 'issueDelegatedUserToken'
  | 'issueMachineToken'
  | 'listCatalogueProducts'
  | 'manager_dashboardcontroller_index'
  | 'manager_leavecontroller_approve'
  | 'manager_leavecontroller_index'
  | 'manager_leavecontroller_reject'
  | 'manager_performancecontroller_index'
  | 'manager_performancecontroller_store'
  | 'manager_profilecontroller_show'
  | 'manager_profilecontroller_update'
  | 'manager_reportcontroller_index'
  | 'manager_teamcontroller_index'
  | 'marketcontroller_index'
  | 'marketcontroller_show'
  | 'notificationcontroller_index'
  | 'notificationcontroller_markAllRead'
  | 'notificationcontroller_markRead'
  | 'partner_accounts_index'
  | 'partner_accounts_members_add'
  | 'partner_accounts_members_index'
  | 'partner_accounts_members_remove'
  | 'partner_accounts_update'
  | 'partnerapplicationcontroller_show'
  | 'partnerapplicationcontroller_submit'
  | 'partnershipscontroller_index'
  | 'payrollcontroller_payslip'
  | 'practitioner_applicationcontroller_index'
  | 'practitioner_applicationcontroller_show'
  | 'practitioner_assessments_answers_save'
  | 'practitioner_assessments_attempt'
  | 'practitioner_assessments_attempts_start'
  | 'practitioner_assessments_attempts_submit'
  | 'practitioner_assessments_index'
  | 'practitioner_assessments_show'
  | 'practitioner_bugreportcontroller_create'
  | 'practitioner_bugreportcontroller_index'
  | 'practitioner_bugreportcontroller_show'
  | 'practitioner_bugreportcontroller_store'
  | 'practitioner_certificatecontroller_downloadValidation'
  | 'practitioner_certificatecontroller_index'
  | 'practitioner_coursecontroller_enroll'
  | 'practitioner_coursecontroller_index'
  | 'practitioner_coursecontroller_show'
  | 'practitioner_dashboardcontroller_index'
  | 'practitioner_findingcontroller_create'
  | 'practitioner_findingcontroller_store'
  | 'practitioner_lessoncontroller_markDone'
  | 'practitioner_lessoncontroller_show'
  | 'practitioner_profilecontroller_changePassword'
  | 'practitioner_profilecontroller_show'
  | 'practitioner_profilecontroller_update'
  | 'practitioner_programcontroller_apply'
  | 'practitioner_programcontroller_index'
  | 'practitioner_programcontroller_show'
  | 'practitioner_suggestioncontroller_create'
  | 'practitioner_suggestioncontroller_index'
  | 'practitioner_suggestioncontroller_store'
  | 'practitioner_surveycontroller_index'
  | 'practitioner_surveycontroller_show'
  | 'practitioner_surveycontroller_submit'
  | 'practitioner_validation_dashboardcontroller_show'
  | 'practitioner_validation_issuereportcontroller_create'
  | 'practitioner_validation_issuereportcontroller_index'
  | 'practitioner_validation_issuereportcontroller_show'
  | 'practitioner_validation_issuereportcontroller_store'
  | 'practitioner_validation_retestcontroller_store'
  | 'practitioner_validation_sessioncontroller_create'
  | 'practitioner_validation_sessioncontroller_index'
  | 'practitioner_validation_sessioncontroller_store'
  | 'productcontroller_index'
  | 'productcontroller_show'
  | 'public_practitionerdirectorycontroller_index'
  | 'public_practitionerdirectorycontroller_show'
  | 'public_sitemap_data'
  | 'publiccoursecontroller_index'
  | 'publiccoursecontroller_show'
  | 'publishing_service_offerings_catalogue'
  | 'purchaseordercontroller_pdf'
  | 'quotecontroller_pdf'
  | 'readIntegrationQuota'
  | 'readIntegrationUsage'
  | 'readOwnBusinessActivity'
  | 'readOwnBusinessRequestMetrics'
  | 'readOwnBusinessUsage'
  | 'readOwnMachineRequestMetrics'
  | 'readOwnProfile'
  | 'submitEnquiry'
  | 'supplierbillcontroller_pdf'
  | 'support_dashboardcontroller_index'
  | 'support_profilecontroller_show'
  | 'support_profilecontroller_update'
  | 'support_ticketcontroller_assignToMe'
  | 'support_ticketcontroller_index'
  | 'support_ticketcontroller_reply'
  | 'support_ticketcontroller_show'
  | 'support_ticketcontroller_updateStatus'
  | 'tester_assignmentcontroller_index'
  | 'tester_assignmentcontroller_show'
  | 'tester_assignmentcontroller_storeBugReport'
  | 'tester_assignmentcontroller_updateStatus'
  | 'tester_bugreportcontroller_index'
  | 'tester_dashboardcontroller_index'
  | 'tester_profilecontroller_show'
  | 'tester_profilecontroller_update'
  | 'testerapplicationcontroller_show'
  | 'testerapplicationcontroller_submit';

export type ApiSchema_EnquiryInput = { "name": string; "email": string; "phone"?: string | null; "facility_type"?: string | null; "products"?: string | null; "message"?: string | null; "product_slug"?: string | null; "locale"?: "en" | "fr"; "quote_option"?: "clinic-saas" | "clinic-perpetual" | "hospital-small" | "hospital-medium-large" | "specialty-lab" | "specialty-pharmacy" | "specialty-radiology" | "specialty-cardiology" | "specialty-dental" | "specialty-dermatology" | "specialty-pediatrics" | "specialty-obgyn" | "care-single-hospital" | "care-multi-hospital" | "care-national" | "health-id-facility" | "health-id-multi-facility" | "health-id-national" | "clinical-cdms-saas" | "clinical-cdms-perpetual" | "clinical-triage-saas" | "clinical-triage-perpetual" | "clinical-cdss-hospital" | "clinical-cdss-network" | "government-insurance" | "government-public-health" | "government-national-platform" | "service-implementation" | "service-data-migration" | "service-training" | "service-custom-integration" | "maintenance-standard" | "maintenance-premium" | "maintenance-enterprise" | "managed-hosting" | "custom-proposal"; "integration_application"?: { "application_name": string; "access_type": "machine" | "delegated" | "both"; "redirect_uri"?: string | null; "requested_scopes"?: Array<string>; "use_case": string; } & Record<string, unknown>; } & Record<string, unknown>;
export type ApiSchema_Problem = { "type": string; "title": string; "status": number; "request_id": string; "errors"?: Record<string, Array<string>>; };
export type ApiSchema_OAuthError = { "error": string; "error_description"?: string; "hint"?: string; "message"?: string; };
export type ApiSchema_OAuthToken = { "access_token": string; "token_type": string; "expires_in": number; };
export type ApiSchema_CatalogueProduct = { "slug": string; "name": string; "category": string; "url": string; } & Record<string, unknown>;
export type ApiSchema_MachineApiClientSummary = { "client_id": string; "name": string; "owner_id"?: number | null; "owner_name"?: string | null; "scopes": Array<"catalogue:read" | "integration:read">; "revoked": boolean; "created_at"?: string | null; };
export type ApiSchema_MachineClientQuota = { "period": string; "requests_today": number; "requests_this_month": number; "daily_limit": number | null; "daily_remaining": number | null; "daily_resets_at": string; "monthly_limit": number | null; "monthly_remaining": number | null; "monthly_resets_at": string; } & Record<string, unknown>;
export type ApiSchema_PublicOAuthClientSummary = { "client_id": string; "name": string; "owner_id"?: number | null; "owner_name"?: string | null; "redirect_uris": Array<string>; "scopes": Array<string>; "revoked": boolean; "created_at"?: string | null; };
export type ApiSchema_ApiRequestMetrics = { "period_start": string; "period_end": string; "requests": number; "responses_2xx": number; "responses_3xx": number; "responses_4xx": number; "responses_5xx": number; "average_response_time_ms": number; "max_response_time_ms": number; "daily": Array<{ "date": string; "requests": number; "responses_4xx": number; "responses_5xx": number; }>; "operations": Array<{ "operation": string; "method": string; "requests": number; "responses_4xx": number; "responses_5xx": number; "average_response_time_ms": number; "max_response_time_ms": number; }>; };

export interface BusinessOperationRequestMap {
  "submitEnquiry": { path: {  }; query: {  }; queryRequired: false; body: ApiSchema_EnquiryInput; bodyRequired: true; idempotencyRequired: false; };
  "issueMachineToken": { path: {  }; query: {  }; queryRequired: false; body: URLSearchParams; bodyRequired: true; idempotencyRequired: false; };
  "listCatalogueProducts": { path: {  }; query: { "page"?: number; "per_page"?: number; "locale"?: "en" | "fr"; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "inspectIntegration": { path: {  }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "certificatepdfcontroller_download": { path: { "certificate": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "contractcontroller_pdf": { path: { "contract": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "documentcontroller_pdf": { path: { "document": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "documentcontroller_preview": { path: { "document": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "documentsigningcontroller_show": { path: { "token": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "documentsigningcontroller_sign": { path: { "token": string | number; }; query: {  }; queryRequired: false; body: { "typed_name": string; "canvas_data"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "invoicecontroller_pdf": { path: { "invoice": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "payrollcontroller_payslip": { path: { "entry": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "purchaseordercontroller_pdf": { path: { "purchaseOrder": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "quotecontroller_pdf": { path: { "quote": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "supplierbillcontroller_pdf": { path: { "supplierBill": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "homecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "accountant_dashboardcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "accountant_expensecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "accountant_expensecontroller_approve": { path: { "id": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "accountant_expensecontroller_reject": { path: { "id": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "accountant_invoicecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "accountant_invoicecontroller_show": { path: { "invoice": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "accountant_invoicecontroller_markPaid": { path: { "invoice": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "accountant_payrollcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "accountant_payrollcontroller_show": { path: { "run": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "accountant_profilecontroller_show": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "accountant_profilecontroller_update": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "phone"?: string | null; "accounting_specialization"?: "general" | "tax" | "payroll" | "audit"; "certifications"?: string | null; "bio"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "accountant_reportcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "partnerapplicationcontroller_show": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "partnerapplicationcontroller_submit": { path: {  }; query: {  }; queryRequired: false; body: { "organization_name": string; "contact_name": string; "email": string; "phone"?: string | null; "country": string; "city"?: string | null; "partner_type": "reseller" | "technology" | "implementation" | "referral" | "hospital" | "academic" | "ngo" | "health_ai" | "investor"; "organization_type"?: string | null; "annual_revenue_range"?: string | null; "target_market"?: string | null; "description": string; "website"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "blogcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "blogcontroller_show": { path: { "slug": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "blogcontroller_comment": { path: { "slug": string | number; }; query: {  }; queryRequired: false; body: { "name": string; "email": string; "content": string; }; bodyRequired: true; idempotencyRequired: true; };
  "blogcontroller_like": { path: { "slug": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "blogcontroller_share": { path: { "slug": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "demorequestcontroller_show": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "demorequestcontroller_submit": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "email": string; "phone"?: string | null; "organization_name": string; "country"?: string | null; "institution_type"?: string | null; "institution_size"?: string | null; "products"?: Array<string> | null; "message"?: string | null; "preferred_date"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "publiccoursecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "publiccoursecontroller_show": { path: { "course": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_certificatecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_coursecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_coursecontroller_show": { path: { "course": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_coursecontroller_enroll": { path: { "course": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "customer_lessoncontroller_show": { path: { "course": string | number; "lesson": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_lessoncontroller_markDone": { path: { "course": string | number; "lesson": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "customer_dashboardcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_documentcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_documentcontroller_show": { path: { "id": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_invoicecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_invoicecontroller_show": { path: { "id": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_knowledgebasecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_knowledgebasecontroller_category": { path: { "slug": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_knowledgebasecontroller_show": { path: { "slug": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_licensecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_licensecontroller_show": { path: { "id": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_profilecontroller_show": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_profilecontroller_update": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "phone"?: string | null; "facility_name"?: string | null; "facility_type"?: string | null; "country": string; "city"?: string | null; "address"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "customer_profilecontroller_changePassword": { path: {  }; query: {  }; queryRequired: false; body: { "current_password": string; "password": string; "password_confirmation": string; }; bodyRequired: true; idempotencyRequired: true; };
  "customer_servicerequestcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_servicerequestcontroller_store": { path: {  }; query: {  }; queryRequired: false; body: { "product_slug"?: string | null; "description"?: string | null; "preferred_date": string; "preferred_time"?: string | null; "location"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "customer_servicerequestcontroller_create": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_servicerequestcontroller_show": { path: { "serviceRequest": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_surveycontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_surveycontroller_show": { path: { "survey": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_surveycontroller_submit": { path: { "survey": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "customer_ticketcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_ticketcontroller_store": { path: {  }; query: {  }; queryRequired: false; body: { "subject": string; "description": string; "type": "support" | "billing" | "technical" | "bug_report" | "other"; "priority": "low" | "medium" | "high" | "urgent"; }; bodyRequired: true; idempotencyRequired: true; };
  "customer_ticketcontroller_create": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_ticketcontroller_show": { path: { "id": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "customer_ticketcontroller_reply": { path: { "id": string | number; }; query: {  }; queryRequired: false; body: { "body": string; }; bodyRequired: true; idempotencyRequired: true; };
  "hr_dashboardcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "hr_departmentcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "hr_departmentcontroller_updateHead": { path: { "dept": string | number; }; query: {  }; queryRequired: false; body: { "head_id": string; }; bodyRequired: true; idempotencyRequired: true; };
  "hr_employeecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "hr_employeecontroller_show": { path: { "user": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "hr_leavecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "hr_leavecontroller_approve": { path: { "id": string | number; }; query: {  }; queryRequired: false; body: { "note"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "hr_leavecontroller_reject": { path: { "id": string | number; }; query: {  }; queryRequired: false; body: { "note"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "hr_payrollcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "hr_payrollcontroller_show": { path: { "run": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "hr_performancecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "hr_performancecontroller_store": { path: {  }; query: {  }; queryRequired: false; body: { "user_id": string; "review_period": string; "review_date": string; "strengths"?: string | null; "areas_for_improvement"?: string | null; "goals_for_next_period"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "hr_profilecontroller_show": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "hr_profilecontroller_update": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "phone"?: string | null; "emergency_contact_name"?: string | null; "emergency_contact_phone"?: string | null; "emergency_contact_relation"?: string | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "testerapplicationcontroller_show": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "testerapplicationcontroller_submit": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "email": string; "phone"?: string | null; "profession": string; "specialty"?: string | null; "institution_name"?: string | null; "country": string; "city"?: string | null; "years_experience": number; "devices"?: Array<string> | null; "platforms"?: Array<string> | null; "motivation": string; "tech_experience"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "manager_dashboardcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "manager_leavecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "manager_leavecontroller_approve": { path: { "id": string | number; }; query: {  }; queryRequired: false; body: { "note"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "manager_leavecontroller_reject": { path: { "id": string | number; }; query: {  }; queryRequired: false; body: { "note"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "manager_performancecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "manager_performancecontroller_store": { path: {  }; query: {  }; queryRequired: false; body: { "user_id": string; "review_period": string; "review_date": string; "strengths"?: string | null; "areas_for_improvement"?: string | null; "goals_for_next_period"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "manager_profilecontroller_show": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "manager_profilecontroller_update": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "phone"?: string | null; "management_level"?: "team_lead" | "senior_manager" | "director"; "bio"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "manager_reportcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "manager_teamcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "marketcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "marketcontroller_show": { path: { "country": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "notificationcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "notificationcontroller_markAllRead": { path: {  }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "notificationcontroller_markRead": { path: { "id": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "partnershipscontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_applicationcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_applicationcontroller_show": { path: { "application": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_findingcontroller_store": { path: { "application": string | number; }; query: {  }; queryRequired: false; body: FormData; bodyRequired: true; idempotencyRequired: true; };
  "practitioner_findingcontroller_create": { path: { "application": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_bugreportcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_bugreportcontroller_store": { path: {  }; query: {  }; queryRequired: false; body: FormData; bodyRequired: true; idempotencyRequired: true; };
  "practitioner_bugreportcontroller_create": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_bugreportcontroller_show": { path: { "bugReport": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_certificatecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_certificatecontroller_downloadValidation": { path: { "certificate": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_coursecontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_coursecontroller_show": { path: { "course": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_coursecontroller_enroll": { path: { "course": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "practitioner_lessoncontroller_show": { path: { "course": string | number; "lesson": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_lessoncontroller_markDone": { path: { "course": string | number; "lesson": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "practitioner_dashboardcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_profilecontroller_show": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_profilecontroller_update": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "phone"?: string | null; "specialty"?: string | null; "workplace_name"?: string | null; "workplace_city"?: string | null; "workplace_country"?: string | null; "registration_number"?: string | null; "years_of_experience"?: number | null; "bio"?: string | null; "opes_testimonial"?: string | null; "payout_number"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "practitioner_profilecontroller_changePassword": { path: {  }; query: {  }; queryRequired: false; body: { "current_password": string; "password": string; "password_confirmation": string; }; bodyRequired: true; idempotencyRequired: true; };
  "practitioner_programcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_programcontroller_show": { path: { "program": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_programcontroller_apply": { path: { "program": string | number; }; query: {  }; queryRequired: false; body: { "motivation"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "practitioner_suggestioncontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_suggestioncontroller_store": { path: {  }; query: {  }; queryRequired: false; body: { "title": string; "body": string; }; bodyRequired: true; idempotencyRequired: true; };
  "practitioner_suggestioncontroller_create": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_surveycontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_surveycontroller_show": { path: { "survey": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_surveycontroller_submit": { path: { "survey": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "practitioner_validation_dashboardcontroller_show": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_validation_issuereportcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_validation_issuereportcontroller_store": { path: {  }; query: {  }; queryRequired: false; body: { "validation_product_id": string; "validation_module_id": string; "validation_workflow_id": string; "validation_test_case_id"?: string | null; "daily_test_session_id"?: string | null; "title": string; "issue_type": "bug" | "missing_feature" | "workflow_problem" | "clinical_risk" | "ui_ux_problem" | "performance_issue" | "security_concern" | "interoperability_issue" | "data_quality_issue" | "recommendation"; "severity": "critical" | "high" | "medium" | "low"; "description": string; "steps_to_reproduce": string; "expected_result": string; "actual_result": string; "clinical_impact": string; "recommendation"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "practitioner_validation_issuereportcontroller_create": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_validation_issuereportcontroller_show": { path: { "issue": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_validation_retestcontroller_store": { path: { "issue": string | number; }; query: {  }; queryRequired: false; body: { "result": "passed" | "failed"; "notes": string; }; bodyRequired: true; idempotencyRequired: true; };
  "practitioner_validation_sessioncontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_validation_sessioncontroller_store": { path: {  }; query: {  }; queryRequired: false; body: { "validation_product_id": string; "validation_module_id": string; "validation_workflow_id": string; "facility_context"?: string | null; "start_time"?: string | null; "end_time"?: string | null; "tasks_completed": number; "comments"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "practitioner_validation_sessioncontroller_create": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "public_practitionerdirectorycontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "public_practitionerdirectorycontroller_show": { path: { "id": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "productcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "productcontroller_show": { path: { "slug": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "brochurecontroller_download": { path: { "slug": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "support_dashboardcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "support_profilecontroller_show": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "support_profilecontroller_update": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "phone"?: string | null; "ticket_specialization"?: "all" | "technical" | "billing" | "general"; "shift"?: "morning" | "afternoon" | "evening"; "languages"?: string | null; "bio"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "support_ticketcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "support_ticketcontroller_show": { path: { "ticket": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "support_ticketcontroller_assignToMe": { path: { "ticket": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "support_ticketcontroller_reply": { path: { "ticket": string | number; }; query: {  }; queryRequired: false; body: { "body": string; "is_internal"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "support_ticketcontroller_updateStatus": { path: { "ticket": string | number; }; query: {  }; queryRequired: false; body: { "status": "open" | "in_progress" | "pending_customer" | "resolved" | "closed"; }; bodyRequired: true; idempotencyRequired: true; };
  "tester_assignmentcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "tester_assignmentcontroller_show": { path: { "id": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "tester_assignmentcontroller_storeBugReport": { path: { "id": string | number; }; query: {  }; queryRequired: false; body: { "subject": string; "description": string; "priority": "low" | "medium" | "high" | "urgent"; }; bodyRequired: true; idempotencyRequired: true; };
  "tester_assignmentcontroller_updateStatus": { path: { "id": string | number; }; query: {  }; queryRequired: false; body: { "status": "pending" | "in_progress" | "completed" | "cancelled"; }; bodyRequired: true; idempotencyRequired: true; };
  "tester_bugreportcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "tester_dashboardcontroller_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "tester_profilecontroller_show": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "tester_profilecontroller_update": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "phone"?: string | null; "testing_specialty"?: "web" | "mobile" | "api" | "desktop"; "device_types"?: string | null; "portfolio_url"?: string | null; "certifications"?: string | null; "availability_notes"?: string | null; "bio"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "identity_register": { path: {  }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "identity_password_request": { path: {  }; query: {  }; queryRequired: false; body: { "email": string; }; bodyRequired: true; idempotencyRequired: true; };
  "identity_password_reset": { path: {  }; query: {  }; queryRequired: false; body: { "token": string; "email": string; "password": string; "password_confirmation": string; }; bodyRequired: true; idempotencyRequired: true; };
  "administration_projects_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "planning" | "active" | "on_hold" | "completed" | "cancelled"; "priority"?: "low" | "medium" | "high" | "critical"; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "administration_projects_create": { path: {  }; query: {  }; queryRequired: false; body: { "title": string; "description"?: string | null; "status": "planning" | "active" | "on_hold" | "completed" | "cancelled"; "priority": "low" | "medium" | "high" | "critical"; "owner_id": string; "start_date"?: string | null; "end_date"?: string | null; "budget"?: number | null; "currency": "XAF" | "USD" | "EUR"; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "administration_projects_show": { path: { "project": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "administration_projects_update": { path: { "project": string | number; }; query: {  }; queryRequired: false; body: { "title": string; "description"?: string | null; "status": "planning" | "active" | "on_hold" | "completed" | "cancelled"; "priority": "low" | "medium" | "high" | "critical"; "owner_id": string; "start_date"?: string | null; "end_date"?: string | null; "budget"?: number | null; "currency": "XAF" | "USD" | "EUR"; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "administration_projects_delete": { path: { "project": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "administration_licenses_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "active" | "suspended" | "expired" | "cancelled"; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "administration_licenses_create": { path: {  }; query: {  }; queryRequired: false; body: { "user_id": string; "product_slug": string; "license_key"?: string | null; "plan": "starter" | "standard" | "professional" | "enterprise"; "seats": number; "status": "active" | "suspended" | "expired" | "cancelled"; "start_date": string; "end_date": string; "price"?: number | null; "currency": "XAF" | "USD" | "EUR"; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "administration_licenses_show": { path: { "license": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "administration_licenses_update": { path: { "license": string | number; }; query: {  }; queryRequired: false; body: { "user_id": string; "product_slug": string; "license_key"?: string | null; "plan": "starter" | "standard" | "professional" | "enterprise"; "seats": number; "status": "active" | "suspended" | "expired" | "cancelled"; "start_date": string; "end_date": string; "price"?: number | null; "currency": "XAF" | "USD" | "EUR"; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "administration_licenses_delete": { path: { "license": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "administration_licenses_suspend": { path: { "license": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "administration_licenses_reactivate": { path: { "license": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_invoiceTemplates_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "active"?: boolean; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_invoiceTemplates_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "customer_id"?: number | null; "client_name"?: string | null; "client_email"?: string | null; "next_due_date": string; "end_date"?: string | null; "payment_terms_days": number; "currency": "XAF" | "USD" | "EUR"; "tax_rate": number; "is_active"?: boolean; "issued_by": number; "line_items": Array<unknown>; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_invoiceTemplates_show": { path: { "invoiceTemplate": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_invoiceTemplates_update": { path: { "invoiceTemplate": string | number; }; query: {  }; queryRequired: false; body: { "name"?: string; "customer_id"?: number | null; "client_name"?: string | null; "client_email"?: string | null; "next_due_date"?: string; "end_date"?: string | null; "payment_terms_days"?: number; "currency"?: "XAF" | "USD" | "EUR"; "tax_rate"?: number; "is_active"?: boolean; "issued_by"?: number; "line_items"?: Array<unknown>; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_purchaseOrders_create": { path: {  }; query: {  }; queryRequired: false; body: { "vendor_id"?: number | null; "vendor_name"?: string | null; "title": string; "description"?: string | null; "expected_date"?: string | null; "currency": "XAF" | "USD" | "EUR"; "tax_amount"?: number | null; "notes"?: string | null; "items": Array<unknown>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_purchaseOrders_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "vendor_id"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_purchaseOrders_update": { path: { "purchaseOrder": string | number; }; query: {  }; queryRequired: false; body: { "vendor_id"?: number | null; "vendor_name"?: string | null; "title"?: string; "description"?: string | null; "expected_date"?: string | null; "currency"?: "XAF" | "USD" | "EUR"; "tax_amount"?: number | null; "notes"?: string | null; "items"?: Array<unknown>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_purchaseOrders_delete": { path: { "purchaseOrder": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_supplierBills_create": { path: {  }; query: {  }; queryRequired: false; body: { "vendor_id"?: number | null; "vendor_name"?: string | null; "purchase_order_id"?: number | null; "bill_number"?: string | null; "issue_date": string; "due_date"?: string | null; "currency": "XAF" | "USD" | "EUR"; "tax_amount"?: number | null; "notes"?: string | null; "items": Array<unknown>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_supplierBills_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "vendor_id"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_supplierBills_update": { path: { "supplierBill": string | number; }; query: {  }; queryRequired: false; body: { "vendor_id"?: number | null; "vendor_name"?: string | null; "purchase_order_id"?: number | null; "bill_number"?: string | null; "issue_date"?: string; "due_date"?: string | null; "currency"?: "XAF" | "USD" | "EUR"; "tax_amount"?: number | null; "notes"?: string | null; "items"?: Array<unknown>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_supplierBills_delete": { path: { "supplierBill": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_invoices_create": { path: {  }; query: {  }; queryRequired: false; body: { "customer_id": number; "license_id"?: number | null; "currency": "XAF" | "USD" | "EUR"; "tax_rate"?: number | null; "due_date"?: string | null; "notes"?: string | null; "items": Array<unknown>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_invoices_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "draft" | "sent" | "paid" | "overdue" | "cancelled"; "customer_id"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_invoices_update": { path: { "invoice": string | number; }; query: {  }; queryRequired: false; body: { "customer_id"?: number; "license_id"?: number | null; "currency"?: "XAF" | "USD" | "EUR"; "tax_rate"?: number | null; "due_date"?: string | null; "notes"?: string | null; "items"?: Array<unknown>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_invoices_delete": { path: { "invoice": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_quotes_create": { path: {  }; query: {  }; queryRequired: false; body: { "lead_id"?: number | null; "title": string; "valid_until"?: string | null; "tax_rate"?: number | null; "currency": "XAF" | "USD" | "EUR"; "notes"?: string | null; "items": Array<unknown>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_quotes_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "lead_id"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_quotes_update": { path: { "quote": string | number; }; query: {  }; queryRequired: false; body: { "lead_id"?: number | null; "title"?: string; "valid_until"?: string | null; "tax_rate"?: number | null; "currency"?: "XAF" | "USD" | "EUR"; "notes"?: string | null; "items"?: Array<unknown>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_quotes_delete": { path: { "quote": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_creditNotes_create": { path: {  }; query: {  }; queryRequired: false; body: { "invoice_id"?: number | null; "reason": string; "currency": "XAF" | "USD" | "EUR"; "tax_amount"?: number | null; "notes"?: string | null; "items": Array<unknown>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_creditNotes_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "draft" | "issued" | "applied" | "void"; "invoice_id"?: number; "search"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_creditNotes_update": { path: { "creditNote": string | number; }; query: {  }; queryRequired: false; body: { "invoice_id"?: number | null; "reason"?: string; "currency"?: "XAF" | "USD" | "EUR"; "tax_amount"?: number | null; "notes"?: string | null; "items"?: Array<unknown>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_creditNotes_delete": { path: { "creditNote": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_expenses_create": { path: {  }; query: {  }; queryRequired: false; body: { "title": string; "description"?: string | null; "amount": number; "currency": "XAF" | "USD" | "EUR"; "vendor"?: string | null; "expense_date": string; "receipt_path"?: string | null; "submitted_by": number; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_expenses_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_expenses_update": { path: { "expense": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "description"?: string | null; "amount"?: number; "currency"?: "XAF" | "USD" | "EUR"; "vendor"?: string | null; "expense_date"?: string; "receipt_path"?: string | null; "submitted_by"?: number; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_expenses_approve": { path: { "expense": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_expenses_markPaid": { path: { "expense": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_creditNotes_issue": { path: { "creditNote": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_creditNotes_void": { path: { "creditNote": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_purchaseOrders_approve": { path: { "purchaseOrder": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_purchaseOrders_receive": { path: { "purchaseOrder": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_supplierBills_approve": { path: { "supplierBill": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_supplierBills_markPaid": { path: { "supplierBill": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_quotes_markSent": { path: { "quote": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_quotes_accept": { path: { "quote": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_invoices_markSent": { path: { "invoice": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_invoices_markPaid": { path: { "invoice": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_invoices_recordPayment": { path: { "invoice": string | number; }; query: {  }; queryRequired: false; body: { "amount": number; "payment_date": string; "reference_number"?: string | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_invoicePayments_index": { path: { "invoice": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_invoices_markOverdue": { path: { "invoice": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_payrollRuns_create": { path: {  }; query: {  }; queryRequired: false; body: { "period_start": string; "period_end": string; "currency": "XAF" | "USD" | "EUR"; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_payrollRuns_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "year"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_payrollRuns_update": { path: { "run": string | number; }; query: {  }; queryRequired: false; body: { "period_start"?: string; "period_end"?: string; "currency"?: "XAF" | "USD" | "EUR"; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_payrollRuns_delete": { path: { "run": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_payrollRuns_show": { path: { "run": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_invoices_markManySent": { path: {  }; query: {  }; queryRequired: false; body: { "ids": Array<number>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_invoiceTemplates_generate": { path: { "invoiceTemplate": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_payrollRuns_generateEntries": { path: { "run": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_payrollRuns_complete": { path: { "run": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_payrollEntries_markPaid": { path: { "run": string | number; "entry": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_payrollEntries_markManyPaid": { path: { "run": string | number; }; query: {  }; queryRequired: false; body: { "ids": Array<number>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_contracts_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "auto_renew"?: boolean; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_contracts_create": { path: {  }; query: {  }; queryRequired: false; body: { "title": string; "lead_id"?: number | null; "value"?: number | null; "currency": "XAF" | "USD" | "EUR"; "start_date"?: string | null; "end_date"?: string | null; "auto_renew": boolean; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_contracts_expiringSoon": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_contracts_update": { path: { "contract": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "lead_id"?: number | null; "value"?: number | null; "currency"?: "XAF" | "USD" | "EUR"; "start_date"?: string | null; "end_date"?: string | null; "auto_renew"?: boolean; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_contracts_delete": { path: { "contract": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_contracts_activate": { path: { "contract": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_contracts_terminate": { path: { "contract": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_validation_issues_clinicalReview": { path: { "issue": string | number; }; query: {  }; queryRequired: false; body: { "decision": "approved_for_product_review" | "rejected" | "needs_more_information"; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_issues_sendToProductReview": { path: { "issue": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_validation_issues_productDecision": { path: { "issue": string | number; }; query: {  }; queryRequired: false; body: { "decision": "accepted" | "rejected" | "duplicate" | "sent_to_development"; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_issues_close": { path: { "issue": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_validation_developerTasks_assign": { path: { "task": string | number; }; query: {  }; queryRequired: false; body: { "assigned_to": number; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_developerTasks_start": { path: { "task": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_validation_developerTasks_markFixed": { path: { "task": string | number; }; query: {  }; queryRequired: false; body: { "resolution_notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_developerTasks_reopen": { path: { "task": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_validation_developerTasks_wontFix": { path: { "task": string | number; }; query: {  }; queryRequired: false; body: { "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_finalEvaluations_issueCertificate": { path: { "evaluation": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_validation_finalEvaluations_create": { path: {  }; query: {  }; queryRequired: false; body: { "cohort_member_id": number; "assessment": string; "recommendation"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_finalEvaluations_list": { path: {  }; query: { "page"?: number; "per_page"?: number; "cohort_member_id"?: number; "rating"?: "outstanding" | "strong" | "satisfactory" | "needs_improvement"; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_finalEvaluations_update": { path: { "evaluation": string | number; }; query: {  }; queryRequired: false; body: { "assessment": string; "recommendation"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_certificates_inviteToCouncil": { path: { "certificate": string | number; }; query: {  }; queryRequired: false; body: { "title": string; "term_start": string; "term_end"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_weeklyReviews_create": { path: {  }; query: {  }; queryRequired: false; body: { "cohort_id": number; "week_start": string; "summary"?: string | null; "action_items"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_weeklyReviews_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "cohort_id"?: number; "week_from"?: string; "week_to"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_weeklyReviews_update": { path: { "weeklyReview": string | number; }; query: {  }; queryRequired: false; body: { "summary"?: string | null; "action_items"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_recruitment_applications_shortlist": { path: { "application": string | number; }; query: {  }; queryRequired: false; body: { "interview_date": string; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_recruitment_applications_scheduleInterview": { path: { "application": string | number; }; query: {  }; queryRequired: false; body: { "interview_date": string; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_recruitment_applications_makeOffer": { path: { "application": string | number; }; query: {  }; queryRequired: false; body: { "interview_date": string; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_recruitment_applications_hire": { path: { "application": string | number; }; query: {  }; queryRequired: false; body: { "interview_date": string; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_recruitment_applications_reject": { path: { "application": string | number; }; query: {  }; queryRequired: false; body: { "interview_date": string; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_recruitment_openings_close": { path: { "opening": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_recruitment_openings_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_recruitment_openings_create": { path: {  }; query: {  }; queryRequired: false; body: { "title": string; "department_id"?: number | null; "type"?: string; "location"?: string | null; "description"?: string | null; "requirements"?: string | null; "openings_count"?: number; "posted_at"?: string | null; "closes_at"?: string | null; "salary_range"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_recruitment_openings_show": { path: { "opening": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_recruitment_openings_update": { path: { "opening": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "department_id"?: number | null; "type"?: string; "location"?: string | null; "description"?: string | null; "requirements"?: string | null; "openings_count"?: number; "posted_at"?: string | null; "closes_at"?: string | null; "salary_range"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_recruitment_openings_delete": { path: { "opening": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_recruitment_applications_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "job_opening_id"?: number; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_recruitment_applications_create": { path: {  }; query: {  }; queryRequired: false; body: FormData; bodyRequired: true; idempotencyRequired: true; };
  "admin_recruitment_applications_show": { path: { "application": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_recruitment_applications_update": { path: { "application": string | number; }; query: {  }; queryRequired: false; body: FormData; bodyRequired: true; idempotencyRequired: true; };
  "admin_recruitment_applications_delete": { path: { "application": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_learning_courses_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "is_active"?: boolean; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_learning_courses_create": { path: {  }; query: {  }; queryRequired: false; body: FormData; bodyRequired: true; idempotencyRequired: true; };
  "admin_learning_courses_show": { path: { "course": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_learning_courses_update": { path: { "course": string | number; }; query: {  }; queryRequired: false; body: FormData; bodyRequired: true; idempotencyRequired: true; };
  "admin_learning_courses_delete": { path: { "course": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_learning_courseLessons_index": { path: { "course": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_learning_courseLessons_create": { path: { "course": string | number; }; query: {  }; queryRequired: false; body: { "title": string; "title_fr"?: string | null; "content"?: string | null; "content_fr"?: string | null; "video_url"?: string | null; "duration_minutes"?: number | null; "sort_order"?: number; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_learning_courseLessons_show": { path: { "course": string | number; "lesson": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_learning_courseLessons_update": { path: { "course": string | number; "lesson": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "title_fr"?: string | null; "content"?: string | null; "content_fr"?: string | null; "video_url"?: string | null; "duration_minutes"?: number | null; "sort_order"?: number; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_learning_courseLessons_delete": { path: { "course": string | number; "lesson": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_learning_assessments_index": { path: { "course": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_learning_assessments_create": { path: { "course": string | number; }; query: {  }; queryRequired: false; body: { "title": string; "instructions"?: string | null; "pass_mark"?: number; "attempt_limit"?: number | null; "duration_minutes"?: number | null; "is_published"?: boolean; "questions": Array<{ "id": string; "prompt": string; "choices": Array<{ "id": string; "label": string; }>; "correct_choice_id": string; "points"?: number; "explanation"?: string | null; }>; } & Record<string, unknown>; bodyRequired: true; idempotencyRequired: true; };
  "admin_learning_assessments_show": { path: { "course": string | number; "assessment": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_learning_assessments_update": { path: { "course": string | number; "assessment": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "instructions"?: string | null; "pass_mark"?: number; "attempt_limit"?: number | null; "duration_minutes"?: number | null; "is_published"?: boolean; "questions"?: Array<{ "id": string; "prompt": string; "choices": Array<{ "id": string; "label": string; }>; "correct_choice_id": string; "points"?: number; "explanation"?: string | null; }>; } & Record<string, unknown>; bodyRequired: true; idempotencyRequired: true; };
  "admin_learning_assessments_delete": { path: { "course": string | number; "assessment": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "practitioner_assessments_index": { path: { "course": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_assessments_show": { path: { "assessment": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_assessments_attempt": { path: { "assessment": string | number; "attempt": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "practitioner_assessments_answers_save": { path: { "assessment": string | number; "attempt": string | number; }; query: {  }; queryRequired: false; body: { "answers": Record<string, string>; } & Record<string, unknown>; bodyRequired: true; idempotencyRequired: true; };
  "practitioner_assessments_attempts_start": { path: { "assessment": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "practitioner_assessments_attempts_submit": { path: { "assessment": string | number; "attempt": string | number; }; query: {  }; queryRequired: false; body: { "answers": Record<string, string>; } & Record<string, unknown>; bodyRequired: false; idempotencyRequired: true; };
  "admin_programmes_surveys_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_programmes_surveys_create": { path: {  }; query: {  }; queryRequired: false; body: { "title": string; "title_fr"?: string | null; "description"?: string | null; "description_fr"?: string | null; "starts_at"?: string | null; "ends_at"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_programmes_surveys_show": { path: { "survey": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_programmes_surveys_update": { path: { "survey": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "title_fr"?: string | null; "description"?: string | null; "description_fr"?: string | null; "starts_at"?: string | null; "ends_at"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_programmes_surveys_delete": { path: { "survey": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_programmes_surveys_activate": { path: { "survey": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_programmes_surveys_close": { path: { "survey": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_programmes_surveyQuestions_index": { path: { "survey": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_programmes_surveyQuestions_create": { path: { "survey": string | number; }; query: {  }; queryRequired: false; body: { "question": string; "question_fr"?: string | null; "options"?: Array<string> | null; "options_fr"?: Array<string> | null; "is_required"?: boolean; "sort_order"?: number; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_programmes_surveyQuestions_show": { path: { "survey": string | number; "question": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_programmes_surveyQuestions_update": { path: { "survey": string | number; "question": string | number; }; query: {  }; queryRequired: false; body: { "question": string; "question_fr"?: string | null; "options"?: Array<string> | null; "options_fr"?: Array<string> | null; "is_required"?: boolean; "sort_order"?: number; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_programmes_surveyQuestions_delete": { path: { "survey": string | number; "question": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_documents_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "type"?: "receipt" | "letterhead" | "contract_employee" | "contract_business"; "status"?: "draft" | "sent" | "pending_signature" | "signed" | "voided"; "search"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_documents_create": { path: {  }; query: {  }; queryRequired: false; body: { "document_template_id": number; "title": string; "valid_until"?: string | null; "requires_signature": boolean; "addressee_user_id"?: number | null; "addressee_name"?: string | null; "addressee_email"?: string | null; "variable_values"?: Array<string | null> | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_documents_templates_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_documents_templates_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "type": "receipt" | "letterhead" | "contract_employee" | "contract_business"; "body": string; "variables"?: Array<string> | null; "is_active": boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_documents_templates_show": { path: { "template": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_documents_templates_update": { path: { "template": string | number; }; query: {  }; queryRequired: false; body: { "name"?: string; "type"?: "receipt" | "letterhead" | "contract_employee" | "contract_business"; "body"?: string; "variables"?: Array<string> | null; "is_active"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_documents_templates_delete": { path: { "template": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_documents_update": { path: { "document": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "valid_until"?: string | null; "addressee_user_id"?: number | null; "addressee_name"?: string; "addressee_email"?: string | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_documents_delete": { path: { "document": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_documents_markSent": { path: { "document": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_documents_void": { path: { "document": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_content_articles_publish": { path: { "article": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_leaves_approve": { path: { "leave": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_leaves_reject": { path: { "leave": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_leaves_bulkApprove": { path: {  }; query: {  }; queryRequired: false; body: { "ids": Array<number>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_leaves_bulkReject": { path: {  }; query: {  }; queryRequired: false; body: { "ids": Array<number>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_leaves_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "pending" | "approved" | "rejected" | "cancelled"; "user_id"?: number; "from"?: string; "to"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_leaves_create": { path: {  }; query: {  }; queryRequired: false; body: { "user_id": number; "leave_type_id"?: number | null; "start_date": string; "end_date": string; "total_days": number; "reason"?: string | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_leaves_show": { path: { "leave": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_leaves_update": { path: { "leave": string | number; }; query: {  }; queryRequired: false; body: { "user_id"?: number; "leave_type_id"?: number | null; "start_date"?: string; "end_date"?: string; "total_days"?: number; "reason"?: string | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_leaveBalances_index": { path: { "employee": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_leaveBalances_create": { path: { "employee": string | number; }; query: {  }; queryRequired: false; body: { "year": number; "entitled_days": number; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_leaveBalances_show": { path: { "leaveBalance": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_leaveBalances_update": { path: { "leaveBalance": string | number; }; query: {  }; queryRequired: false; body: { "entitled_days": number; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_leaveBalances_delete": { path: { "leaveBalance": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_performanceReviews_submit": { path: { "review": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_performanceReviews_acknowledge": { path: { "review": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_performanceReviews_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "draft" | "submitted" | "acknowledged"; "user_id"?: number; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_performanceReviews_create": { path: {  }; query: {  }; queryRequired: false; body: { "user_id": number; "reviewer_id": number; "review_period": string; "review_date": string; "overall_rating": number; "goals_rating": number; "teamwork_rating": number; "technical_rating": number; "communication_rating": number; "strengths"?: string | null; "areas_for_improvement"?: string | null; "goals_for_next_period"?: string | null; "employee_comments"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_performanceReviews_show": { path: { "review": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_performanceReviews_update": { path: { "review": string | number; }; query: {  }; queryRequired: false; body: { "user_id"?: number; "reviewer_id"?: number; "review_period"?: string; "review_date"?: string; "overall_rating"?: number; "goals_rating"?: number; "teamwork_rating"?: number; "technical_rating"?: number; "communication_rating"?: number; "strengths"?: string | null; "areas_for_improvement"?: string | null; "goals_for_next_period"?: string | null; "employee_comments"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_identity_users_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "search"?: string | null; "role"?: "super_admin" | "admin" | "support" | "accountant" | "hr" | "manager" | "staff" | "employee" | "tester" | "customer" | "practitioner" | "partner"; "is_active"?: boolean; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_identity_users_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "email": string; "password": string; "phone"?: string | null; "avatar"?: string | null; "employee_id"?: string | null; "hire_date"?: string | null; "department"?: string | null; "position"?: string | null; "is_active"?: boolean; "roles"?: Array<string>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_identity_users_employeeProfile": { path: { "user": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_identity_roles_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_risks_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_risks_create": { path: {  }; query: {  }; queryRequired: false; body: { "title": string; "description"?: string | null; "owner_id": number; "review_date"?: string | null; "mitigation_plan"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_risks_show": { path: { "risk": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_risks_update": { path: { "risk": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "description"?: string | null; "owner_id"?: number; "review_date"?: string | null; "mitigation_plan"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_identity_users_update": { path: { "user": string | number; }; query: {  }; queryRequired: false; body: { "name"?: string; "email"?: string; "password"?: string; "phone"?: string | null; "avatar"?: string | null; "employee_id"?: string | null; "hire_date"?: string | null; "department"?: string | null; "position"?: string | null; "is_active"?: boolean; "roles"?: Array<string>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_identity_users_delete": { path: { "user": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_risk_mitigate": { path: { "risk": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_training_complete": { path: { "trainingRecord": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_training_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_training_create": { path: {  }; query: {  }; queryRequired: false; body: FormData; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_training_show": { path: { "trainingRecord": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_training_update": { path: { "trainingRecord": string | number; }; query: {  }; queryRequired: false; body: FormData; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_training_delete": { path: { "trainingRecord": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_identity_users_sendWelcome": { path: { "user": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_practitioners_suggestions_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "pending" | "under_review" | "accepted" | "declined" | "implemented"; "category"?: "feature_request" | "improvement" | "process" | "bug" | "other"; "search"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_practitioners_suggestions_respond": { path: { "suggestion": string | number; }; query: {  }; queryRequired: false; body: { "status": "under_review" | "accepted" | "declined" | "implemented"; "admin_response": string; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_practitioners_profiles_verify": { path: { "profile": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_practitioners_findings_publish": { path: { "finding": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_practitioners_findings_unpublish": { path: { "finding": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_practitioners_applications_approve": { path: { "application": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_practitioners_applications_reject": { path: { "application": string | number; }; query: {  }; queryRequired: false; body: { "admin_notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_practitioners_applications_placeInCohort": { path: { "application": string | number; }; query: {  }; queryRequired: false; body: { "cohort_id": number; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_support_bugReports_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "open" | "triaged" | "in_progress" | "resolved" | "closed" | "wont_fix"; "severity"?: "critical" | "high" | "medium" | "low"; "search"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_support_bugReports_respond": { path: { "bugReport": string | number; }; query: {  }; queryRequired: false; body: { "status": "triaged" | "in_progress" | "resolved" | "closed" | "wont_fix"; "admin_response": string; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_support_serviceRequests_confirm": { path: { "serviceRequest": string | number; }; query: {  }; queryRequired: false; body: { "assigned_technician_id": number; "confirmed_date": string; "confirmed_time"?: string | null; "admin_notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_support_serviceRequests_complete": { path: { "serviceRequest": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_support_tickets_reply": { path: { "ticket": string | number; }; query: {  }; queryRequired: false; body: { "body": string; "is_internal"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_support_tickets_status": { path: { "ticket": string | number; }; query: {  }; queryRequired: false; body: { "status": "open" | "in_progress" | "pending_customer" | "resolved" | "closed"; "assigned_to"?: number | null; "resolution"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_support_tickets_create": { path: {  }; query: {  }; queryRequired: false; body: { "user_id"?: number | null; "subject": string; "description": string; "type": "support" | "billing" | "technical" | "bug_report" | "other"; "priority": "low" | "medium" | "high" | "urgent"; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_support_tickets_update": { path: { "ticket": string | number; }; query: {  }; queryRequired: false; body: { "user_id"?: number | null; "subject": string; "description": string; "type": "support" | "billing" | "technical" | "bug_report" | "other"; "priority": "low" | "medium" | "high" | "urgent"; "resolution"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_metrics_cohortProgress": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_metrics_issueAnalytics": { path: {  }; query: { "page"?: number; "per_page"?: number; "cohort_id"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_metrics_developerThroughput": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_metrics_practitionerLeaderboard": { path: {  }; query: { "page"?: number; "per_page"?: number; "cohort_id"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_certificates_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "tier"?: "distinction" | "pass"; "cohort_id"?: number; "search"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_issues_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "submitted" | "clinical_review" | "product_review" | "accepted" | "rejected" | "duplicate" | "needs_more_information" | "sent_to_development" | "fixed" | "ready_for_retest" | "retest_passed" | "retest_failed" | "closed"; "severity"?: "critical" | "high" | "medium" | "low"; "issue_type"?: "bug" | "missing_feature" | "workflow_problem" | "clinical_risk" | "ui_ux_problem" | "performance_issue" | "security_concern" | "interoperability_issue" | "data_quality_issue" | "recommendation"; "search"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_developerTasks_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "open" | "in_progress" | "fixed" | "reopened" | "wont_fix"; "priority"?: "critical" | "high" | "medium" | "low"; "assigned_to"?: number; "search"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_developerTasks_retests": { path: { "task": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_dailyTestSessions_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "product_id"?: number; "cohort_member_id"?: number; "date_from"?: string; "date_to"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_learning_enrollments_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "enrolled" | "in_progress" | "completed" | "dropped"; "course_id"?: number; "search"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_learning_certificates_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "course_id"?: number; "search"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_learning_enrollments_complete": { path: { "enrollment": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_operations_assets_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "assigned_to"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_operations_assets_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "serial_number"?: string | null; "brand"?: string | null; "model"?: string | null; "purchase_date"?: string | null; "purchase_price"?: number | null; "current_value"?: number | null; "currency": "XAF" | "USD" | "EUR"; "location"?: string | null; "assigned_to"?: number | null; "warranty_expires"?: string | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_operations_assets_update": { path: { "asset": string | number; }; query: {  }; queryRequired: false; body: { "name"?: string; "serial_number"?: string | null; "brand"?: string | null; "model"?: string | null; "purchase_date"?: string | null; "purchase_price"?: number | null; "current_value"?: number | null; "currency"?: "XAF" | "USD" | "EUR"; "location"?: string | null; "assigned_to"?: number | null; "warranty_expires"?: string | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_operations_assets_delete": { path: { "asset": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_milestones_complete": { path: { "milestone": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_operations_milestones_index": { path: { "project": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_operations_milestones_create": { path: { "project": string | number; }; query: {  }; queryRequired: false; body: { "title": string; "description"?: string | null; "due_date"?: string | null; "status"?: "pending" | "in_progress" | "overdue"; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_operations_milestones_show": { path: { "project": string | number; "milestone": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_operations_milestones_update": { path: { "project": string | number; "milestone": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "description"?: string | null; "due_date"?: string | null; "status"?: "pending" | "in_progress" | "overdue"; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_operations_milestones_delete": { path: { "project": string | number; "milestone": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_enquiries_leads_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "new" | "contacted" | "qualified" | "closed"; "facility_type"?: "clinic" | "hospital" | "laboratory" | "pharmacy" | "government" | "ngo" | "other"; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_enquiries_leads_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "email": string; "phone"?: string | null; "facility_type"?: "clinic" | "hospital" | "laboratory" | "pharmacy" | "government" | "ngo" | "other"; "products"?: string | null; "message"?: string | null; "source"?: "contact" | "product-page" | "demo"; "product_slug"?: string | null; "locale"?: string | null; "status"?: "new" | "contacted" | "qualified" | "closed"; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_enquiries_leads_show": { path: { "lead": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_enquiries_leads_update": { path: { "lead": string | number; }; query: {  }; queryRequired: false; body: { "name": string; "email": string; "phone"?: string | null; "facility_type"?: "clinic" | "hospital" | "laboratory" | "pharmacy" | "government" | "ngo" | "other"; "products"?: string | null; "message"?: string | null; "source"?: "contact" | "product-page" | "demo"; "product_slug"?: string | null; "locale"?: string | null; "status": "new" | "contacted" | "qualified" | "closed"; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_enquiries_leads_delete": { path: { "lead": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_enquiries_integrationRequests_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "new" | "contacted" | "qualified" | "closed"; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_enquiries_integrationRequests_show": { path: { "lead": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_enquiries_leads_sendFollowUp": { path: { "lead": string | number; }; query: {  }; queryRequired: false; body: { "subject": string; "body": string; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_enquiries_deliveries_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "pending" | "processing" | "sent" | "failed" | "uncertain"; "event_type"?: string; "from"?: string; "to"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_enquiries_deliveries_show": { path: { "event": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_enquiries_deliveries_confirmReceived": { path: { "event": string | number; }; query: {  }; queryRequired: false; body: { "confirmed_received": string; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_settings_update": { path: {  }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_settings_show": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_dashboard_recentTickets": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_dashboard_recentLeads": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_dashboard_outstandingInvoices": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_dashboard_announcements": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_reports_operational_overview": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_reports_dashboard": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_reports_contractsExpiry": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_reports_crmSummary": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_reports_peopleSummary": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_reports_accountsPayableAging": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_reports_accountsReceivableAging": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_reports_cashFlow": { path: {  }; query: { "page"?: number; "per_page"?: number; "year"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_reports_budgetVsActual": { path: {  }; query: { "page"?: number; "per_page"?: number; "year"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_reports_payrollHeadcount": { path: {  }; query: { "page"?: number; "per_page"?: number; "year"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_reports_profitAndLoss": { path: {  }; query: { "page"?: number; "per_page"?: number; "year"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_reports_overview": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_dashboard_summary": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_reports_workforce_summary": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_crm_deals_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "owner_id"?: number; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_crm_deals_create": { path: {  }; query: {  }; queryRequired: false; body: { "title": string; "lead_id"?: number | null; "value": number; "currency": "XAF" | "USD" | "EUR"; "probability": number; "expected_close_date"?: string | null; "actual_close_date"?: string | null; "owner_id": number; "notes"?: string | null; "lost_reason"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_crm_deals_show": { path: { "deal": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_crm_deals_update": { path: { "deal": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "lead_id"?: number | null; "value"?: number; "currency"?: "XAF" | "USD" | "EUR"; "probability"?: number; "expected_close_date"?: string | null; "actual_close_date"?: string | null; "owner_id"?: number; "notes"?: string | null; "lost_reason"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_crm_deals_delete": { path: { "deal": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_crm_deals_pipeline": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_testing_assignments_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "pending" | "in_progress" | "completed" | "cancelled"; "assigned_to"?: number; "product_slug"?: string; "search"?: string; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_testing_assignments_create": { path: {  }; query: {  }; queryRequired: false; body: { "assigned_to": number; "title": string; "description": string; "due_date"?: string | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_testing_assignments_update": { path: { "testerAssignment": string | number; }; query: {  }; queryRequired: false; body: { "assigned_to"?: number; "title"?: string; "description"?: string; "due_date"?: string | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_testing_assignments_delete": { path: { "testerAssignment": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_departments_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_departments_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "code"?: string | null; "head_id"?: number | null; "parent_id"?: number | null; "is_active": boolean; "description"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_departments_show": { path: { "department": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_departments_update": { path: { "department": string | number; }; query: {  }; queryRequired: false; body: { "name": string; "code"?: string | null; "head_id"?: number | null; "parent_id"?: number | null; "is_active": boolean; "description"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_departments_delete": { path: { "department": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_leaveTypes_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_leaveTypes_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "code"?: string; "days_per_year": number; "max_carry_forward"?: number | null; "color"?: string | null; "is_paid": boolean; "requires_approval": boolean; "is_active": boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_leaveTypes_show": { path: { "leaveType": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_leaveTypes_update": { path: { "leaveType": string | number; }; query: {  }; queryRequired: false; body: { "name": string; "code"?: string; "days_per_year": number; "max_carry_forward"?: number | null; "color"?: string | null; "is_paid": boolean; "requires_approval": boolean; "is_active": boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_leaveTypes_delete": { path: { "leaveType": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_salaryGrades_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_salaryGrades_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "code": string; "min_salary": number; "max_salary": number; "currency": "XAF" | "USD" | "EUR"; "is_active": boolean; "description"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_salaryGrades_show": { path: { "salaryGrade": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_salaryGrades_update": { path: { "salaryGrade": string | number; }; query: {  }; queryRequired: false; body: { "name": string; "code": string; "min_salary": number; "max_salary": number; "currency": "XAF" | "USD" | "EUR"; "is_active": boolean; "description"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_salaryGrades_delete": { path: { "salaryGrade": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_payrollDeductionTypes_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_payrollDeductionTypes_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "code": string; "calculation_type": "percentage" | "fixed"; "rate": number; "apply_by_default": boolean; "is_active": boolean; "sort_order": number; "description"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_payrollDeductionTypes_show": { path: { "deductionType": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_payrollDeductionTypes_update": { path: { "deductionType": string | number; }; query: {  }; queryRequired: false; body: { "name": string; "code": string; "calculation_type": "percentage" | "fixed"; "rate": number; "apply_by_default": boolean; "is_active": boolean; "sort_order": number; "description"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_payrollDeductionTypes_delete": { path: { "deductionType": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_vendors_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_vendors_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "contact_name"?: string | null; "email"?: string | null; "phone"?: string | null; "tax_id"?: string | null; "is_active": boolean; "address"?: string | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_vendors_show": { path: { "vendor": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_vendors_update": { path: { "vendor": string | number; }; query: {  }; queryRequired: false; body: { "name"?: string; "contact_name"?: string | null; "email"?: string | null; "phone"?: string | null; "tax_id"?: string | null; "is_active"?: boolean; "address"?: string | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_vendors_delete": { path: { "vendor": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_support_slaRules_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_support_slaRules_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "ticket_priority": "low" | "medium" | "high" | "urgent"; "ticket_type"?: "support" | "billing" | "technical" | "bug_report" | "other"; "response_time_hours": number; "resolution_time_hours": number; "is_active": boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_support_slaRules_show": { path: { "slaRule": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_support_slaRules_update": { path: { "slaRule": string | number; }; query: {  }; queryRequired: false; body: { "name"?: string; "ticket_priority"?: "low" | "medium" | "high" | "urgent"; "ticket_type"?: "support" | "billing" | "technical" | "bug_report" | "other"; "response_time_hours"?: number; "resolution_time_hours"?: number; "is_active"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_support_slaRules_delete": { path: { "slaRule": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_validation_products_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_products_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "code": string; "description"?: string | null; "is_active": boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_products_show": { path: { "validationProduct": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_products_update": { path: { "validationProduct": string | number; }; query: {  }; queryRequired: false; body: { "name": string; "code": string; "description"?: string | null; "is_active": boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_products_delete": { path: { "validationProduct": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_communications_announcements_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_communications_announcements_create": { path: {  }; query: {  }; queryRequired: false; body: { "title": string; "body": string; "audience": "all" | "admin" | "support" | "tester"; "is_pinned": boolean; "is_active": boolean; "published_at"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_communications_announcements_show": { path: { "announcement": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_communications_announcements_update": { path: { "announcement": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "body"?: string; "audience"?: "all" | "admin" | "support" | "tester"; "is_pinned"?: boolean; "is_active"?: boolean; "published_at"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_communications_announcements_delete": { path: { "announcement": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_communications_testimonials_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_communications_testimonials_create": { path: {  }; query: {  }; queryRequired: false; body: { "author_name": string; "author_title"?: string | null; "author_facility"?: string | null; "author_country"?: string | null; "body": string; "body_fr"?: string | null; "rating": number; "sort_order": number; "is_active": boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_communications_testimonials_show": { path: { "testimonial": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_communications_testimonials_update": { path: { "testimonial": string | number; }; query: {  }; queryRequired: false; body: { "author_name"?: string; "author_title"?: string | null; "author_facility"?: string | null; "author_country"?: string | null; "body"?: string; "body_fr"?: string | null; "rating"?: number; "sort_order"?: number; "is_active"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_communications_testimonials_delete": { path: { "testimonial": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_support_knowledgeArticles_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_support_knowledgeArticles_create": { path: {  }; query: {  }; queryRequired: false; body: { "title": string; "slug": string; "category_id"?: number | null; "content": string; "is_public": boolean; "tags"?: Array<string> | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_support_knowledgeArticles_show": { path: { "article": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_support_knowledgeArticles_update": { path: { "article": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "slug"?: string; "category_id"?: number | null; "content"?: string; "is_public"?: boolean; "tags"?: Array<string> | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_support_knowledgeArticles_delete": { path: { "article": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_support_knowledgeArticles_archive": { path: { "article": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_support_knowledgeCategories_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_support_knowledgeCategories_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "slug": string; "description"?: string | null; "parent_id"?: number | null; "sort_order": number; "is_public": boolean; "is_active": boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_support_knowledgeCategories_show": { path: { "category": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_support_knowledgeCategories_update": { path: { "category": string | number; }; query: {  }; queryRequired: false; body: { "name"?: string; "slug"?: string; "description"?: string | null; "parent_id"?: number | null; "sort_order"?: number; "is_public"?: boolean; "is_active"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_support_knowledgeCategories_delete": { path: { "category": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_finance_records_summary": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_payrollEntries_index": { path: { "run": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_budgets_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_budgets_create": { path: {  }; query: {  }; queryRequired: false; body: { "year": number; "category": "payroll" | "rent" | "utilities" | "software" | "hardware" | "travel" | "marketing" | "legal" | "training" | "other"; "department": string; "allocated_amount": number; "currency": "XAF" | "USD" | "EUR"; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_budgets_show": { path: { "budget": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_budgets_update": { path: { "budget": string | number; }; query: {  }; queryRequired: false; body: { "year"?: number; "category"?: "payroll" | "rent" | "utilities" | "software" | "hardware" | "travel" | "marketing" | "legal" | "training" | "other"; "department"?: string; "allocated_amount"?: number; "currency"?: "XAF" | "USD" | "EUR"; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_finance_budgets_delete": { path: { "budget": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_attendance_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_attendance_create": { path: {  }; query: {  }; queryRequired: false; body: { "user_id": number; "date": string; "status": "present" | "absent" | "late" | "half_day" | "on_leave" | "remote" | "holiday"; "check_in"?: string | null; "check_out"?: string | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_attendance_show": { path: { "attendanceRecord": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_attendance_update": { path: { "attendanceRecord": string | number; }; query: {  }; queryRequired: false; body: { "user_id": number; "date": string; "status": "present" | "absent" | "late" | "half_day" | "on_leave" | "remote" | "holiday"; "check_in"?: string | null; "check_out"?: string | null; "notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_attendance_delete": { path: { "attendanceRecord": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_timesheets_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_timesheets_create": { path: {  }; query: {  }; queryRequired: false; body: { "user_id": number; "project_id"?: number | null; "date": string; "hours": number; "description"?: string | null; "is_billable": boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_timesheets_show": { path: { "timesheet": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_timesheets_update": { path: { "timesheet": string | number; }; query: {  }; queryRequired: false; body: { "user_id": number; "project_id"?: number | null; "date": string; "hours": number; "description"?: string | null; "is_billable": boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_workforce_timesheets_delete": { path: { "timesheet": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_catalogue_products_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_catalogue_products_create": { path: {  }; query: {  }; queryRequired: false; body: { "slug": string; "category": "core" | "diagnostics" | "specialist"; "name": string; "name_fr"?: string | null; "subtitle"?: string | null; "subtitle_fr"?: string | null; "tagline"?: string | null; "icon"?: string | null; "color"?: string | null; "sort_order"?: number; "is_active"?: boolean; "is_featured"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_catalogue_products_show": { path: { "product": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_catalogue_products_update": { path: { "product": string | number; }; query: {  }; queryRequired: false; body: { "slug"?: string; "category"?: "core" | "diagnostics" | "specialist"; "name"?: string; "name_fr"?: string | null; "subtitle"?: string | null; "subtitle_fr"?: string | null; "tagline"?: string | null; "icon"?: string | null; "color"?: string | null; "sort_order"?: number; "is_active"?: boolean; "is_featured"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_catalogue_products_archive": { path: { "product": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_validation_councilMembers_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_councilMembers_show": { path: { "member": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_councilMembers_update": { path: { "member": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "term_start"?: string; "term_end"?: string | null; "status"?: "active" | "inactive"; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_communications_emailTemplates_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "is_active"?: boolean; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_communications_emailTemplates_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "slug": string; "type": "welcome" | "invoice_sent" | "invoice_reminder" | "ticket_created" | "ticket_reply" | "leave_approval" | "leave_rejection" | "password_reset" | "announcement" | "contract_sent" | "quote_sent" | "general"; "subject": string; "body": string; "variables"?: Array<string> | null; "is_active": boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_communications_emailTemplates_show": { path: { "template": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_communications_emailTemplates_update": { path: { "template": string | number; }; query: {  }; queryRequired: false; body: { "name"?: string; "slug"?: string; "type"?: "welcome" | "invoice_sent" | "invoice_reminder" | "ticket_created" | "ticket_reply" | "leave_approval" | "leave_rejection" | "password_reset" | "announcement" | "contract_sent" | "quote_sent" | "general"; "subject"?: string; "body"?: string; "variables"?: Array<string> | null; "is_active"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_communications_emailTemplates_delete": { path: { "template": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_publishing_blogPosts_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "published"?: boolean; "category"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_publishing_blogPosts_create": { path: {  }; query: {  }; queryRequired: false; body: { "title": string; "slug": string; "title_fr"?: string | null; "excerpt"?: string | null; "excerpt_fr"?: string | null; "body": string; "body_fr"?: string | null; "cover_image"?: string | null; "category": string; "author": string; "published"?: boolean; "published_at"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_publishing_blogPosts_show": { path: { "blogPost": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_publishing_blogPosts_update": { path: { "blogPost": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "slug"?: string; "title_fr"?: string | null; "excerpt"?: string | null; "excerpt_fr"?: string | null; "body"?: string; "body_fr"?: string | null; "cover_image"?: string | null; "category"?: string; "author"?: string; "published"?: boolean; "published_at"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_publishing_blogPosts_delete": { path: { "blogPost": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_crm_demoRequests_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_crm_demoRequests_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "email": string; "phone"?: string | null; "organization_name": string; "country"?: string | null; "institution_type"?: "government_hospital" | "private_hospital" | "clinic" | "pharmacy" | "laboratory" | "ministry" | "ngo" | "other"; "institution_size"?: "1-10" | "11-50" | "51-200" | "201-500" | "500+"; "products"?: Array<string> | null; "message"?: string | null; "preferred_date"?: string | null; "locale"?: "en" | "fr"; "status"?: "new" | "contacted" | "scheduled" | "completed" | "rejected"; "admin_notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_crm_demoRequests_show": { path: { "demoRequest": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_crm_demoRequests_update": { path: { "demoRequest": string | number; }; query: {  }; queryRequired: false; body: { "name"?: string; "email"?: string; "phone"?: string | null; "organization_name"?: string; "country"?: string | null; "institution_type"?: "government_hospital" | "private_hospital" | "clinic" | "pharmacy" | "laboratory" | "ministry" | "ngo" | "other"; "institution_size"?: "1-10" | "11-50" | "51-200" | "201-500" | "500+"; "products"?: Array<string> | null; "message"?: string | null; "preferred_date"?: string | null; "locale"?: "en" | "fr"; "status"?: "new" | "contacted" | "scheduled" | "completed" | "rejected"; "admin_notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_crm_demoRequests_delete": { path: { "demoRequest": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_validation_cohorts_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "draft" | "active" | "completed"; "practitioner_program_id"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_cohorts_create": { path: {  }; query: {  }; queryRequired: false; body: { "practitioner_program_id": number; "name": string; "specialty": string; "description"?: string | null; "start_date": string; "end_date": string; "max_members"?: number | null; "status"?: "draft" | "active" | "completed"; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_cohorts_show": { path: { "cohort": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_cohorts_update": { path: { "cohort": string | number; }; query: {  }; queryRequired: false; body: { "practitioner_program_id"?: number; "name"?: string; "specialty"?: string; "description"?: string | null; "start_date"?: string; "end_date"?: string; "max_members"?: number | null; "status"?: "draft" | "active" | "completed"; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_cohorts_delete": { path: { "cohort": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_validation_cohortMembers_index": { path: { "cohort": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_cohortTestCases_index": { path: { "cohort": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_cohortTestCases_attach": { path: { "cohort": string | number; }; query: {  }; queryRequired: false; body: { "validation_test_case_id": number; "due_date"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_cohortMembers_update": { path: { "cohort": string | number; "member": string | number; }; query: {  }; queryRequired: false; body: { "status": "active" | "suspended" | "completed" | "removed"; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_cohortTestCases_detach": { path: { "cohort": string | number; "testCase": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_employees_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "search"?: string | null; "active"?: boolean; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_employees_create": { path: {  }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_workforce_employees_show": { path: { "employee": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_workforce_employees_update": { path: { "employee": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_customerProfiles_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "search"?: string | null; "country"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_customerProfiles_create": { path: {  }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_customerProfiles_show": { path: { "customerProfile": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_customerProfiles_update": { path: { "customerProfile": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_validation_modules_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "validation_product_id"?: number; "active"?: boolean; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_modules_show": { path: { "module": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_modules_update": { path: { "module": string | number; }; query: {  }; queryRequired: false; body: { "validation_product_id": number; "name": string; "code": string; "description"?: string | null; "is_active"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_modules_delete": { path: { "module": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_validation_modules_create": { path: { "product": string | number; }; query: {  }; queryRequired: false; body: { "validation_product_id": number; "name": string; "code": string; "description"?: string | null; "is_active"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_workflows_index": { path: { "module": string | number; }; query: { "page"?: number; "per_page"?: number; "active"?: boolean; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_workflows_create": { path: { "module": string | number; }; query: {  }; queryRequired: false; body: { "name": string; "code": string; "description"?: string | null; "is_active"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_workflows_show": { path: { "module": string | number; "workflow": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_workflows_update": { path: { "module": string | number; "workflow": string | number; }; query: {  }; queryRequired: false; body: { "name": string; "code": string; "description"?: string | null; "is_active"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_workflows_delete": { path: { "module": string | number; "workflow": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_validation_testCases_index": { path: { "workflow": string | number; }; query: { "page"?: number; "per_page"?: number; "active"?: boolean; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_testCases_create": { path: { "workflow": string | number; }; query: {  }; queryRequired: false; body: { "title": string; "description"?: string | null; "steps"?: string | null; "expected_result"?: string | null; "is_active"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_testCases_show": { path: { "workflow": string | number; "testCase": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_validation_testCases_update": { path: { "workflow": string | number; "testCase": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "description"?: string | null; "steps"?: string | null; "expected_result"?: string | null; "is_active"?: boolean; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_validation_testCases_delete": { path: { "workflow": string | number; "testCase": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_practitioners_profiles_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "profession"?: string; "verified"?: boolean; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_practitioners_profiles_show": { path: { "profile": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_practitioners_profiles_update": { path: { "profile": string | number; }; query: {  }; queryRequired: false; body: { "specialty"?: string | null; "registration_number"?: string | null; "years_of_experience"?: number | null; "workplace_name"?: string | null; "workplace_city"?: string | null; "workplace_country"?: string | null; "bio"?: string | null; "opes_testimonial"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_practitioners_findings_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "published"?: boolean; "application_id"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_practitioners_findings_show": { path: { "finding": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_practitioners_findings_update": { path: { "finding": string | number; }; query: {  }; queryRequired: false; body: { "overall_rating"?: number | null; "wait_time_rating"?: number | null; "data_integrity_rating"?: number | null; "usability_rating"?: number | null; "findings_text"?: string | null; "video_url"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_practitioners_applications_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "pending" | "approved" | "rejected" | "withdrawn"; "program_id"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_practitioners_applications_show": { path: { "application": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_practitioners_applications_updateNotes": { path: { "application": string | number; }; query: {  }; queryRequired: false; body: { "admin_notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_enquiries_partnerApplications_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "pending" | "reviewing" | "approved" | "rejected"; "partner_type"?: string; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_enquiries_partnerApplications_show": { path: { "partnerApplication": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_enquiries_partnerApplications_update": { path: { "partnerApplication": string | number; }; query: {  }; queryRequired: false; body: { "status"?: "pending" | "reviewing" | "approved" | "rejected"; "admin_notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_enquiries_testerApplications_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "pending" | "accepted" | "rejected" | "active"; "profession"?: string; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_enquiries_testerApplications_show": { path: { "testerApplication": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_enquiries_testerApplications_update": { path: { "testerApplication": string | number; }; query: {  }; queryRequired: false; body: { "status"?: "pending" | "accepted" | "rejected" | "active"; "admin_notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "partner_accounts_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "partner_accounts_update": { path: { "partnerApplication": string | number; }; query: {  }; queryRequired: false; body: { "phone"?: string | null; "country"?: string; "city"?: string | null; "organization_type"?: string | null; "target_market"?: string | null; "description"?: string; "website"?: string | null; } & Record<string, unknown>; bodyRequired: true; idempotencyRequired: true; };
  "partner_accounts_members_index": { path: { "partnerApplication": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "partner_accounts_members_add": { path: { "partnerApplication": string | number; }; query: {  }; queryRequired: false; body: { "user_id"?: number | null; "email"?: string | null; "membership_role": "owner" | "member"; }; bodyRequired: true; idempotencyRequired: true; };
  "partner_accounts_members_remove": { path: { "partnerApplication": string | number; "member": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_enquiries_partnerApplications_members_index": { path: { "partnerApplication": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_enquiries_partnerApplications_members_add": { path: { "partnerApplication": string | number; }; query: {  }; queryRequired: false; body: { "user_id": number; "membership_role": "owner" | "member"; } & Record<string, unknown>; bodyRequired: true; idempotencyRequired: true; };
  "admin_enquiries_partnerApplications_members_eligible": { path: {  }; query: { "page"?: number; "per_page"?: number; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_enquiries_partnerApplications_members_remove": { path: { "partnerApplication": string | number; "member": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_support_serviceRequests_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "pending" | "confirmed" | "assigned" | "completed" | "cancelled"; "type"?: "installation" | "maintenance" | "training" | "other"; "customer_id"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_support_serviceRequests_show": { path: { "serviceRequest": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_support_serviceRequests_update": { path: { "serviceRequest": string | number; }; query: {  }; queryRequired: false; body: { "type"?: "installation" | "maintenance" | "training" | "other"; "product_slug"?: string | null; "description"?: string; "preferred_date"?: string | null; "preferred_time"?: string | null; "location"?: string | null; "admin_notes"?: string | null; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_support_serviceRequests_cancel": { path: { "serviceRequest": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_publishing_partnerInstitutions_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "type"?: string; "active"?: boolean; "featured"?: boolean; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_publishing_partnerInstitutions_create": { path: {  }; query: {  }; queryRequired: false; body: FormData; bodyRequired: true; idempotencyRequired: true; };
  "admin_publishing_partnerInstitutions_show": { path: { "institution": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_publishing_partnerInstitutions_update": { path: { "institution": string | number; }; query: {  }; queryRequired: false; body: FormData; bodyRequired: true; idempotencyRequired: true; };
  "admin_practitioners_programs_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "type"?: "volunteer" | "paid"; "program_type"?: "general" | "validation"; "status"?: "draft" | "open" | "closed" | "archived"; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_practitioners_programs_create": { path: {  }; query: {  }; queryRequired: false; body: { "title": string; "title_fr"?: string | null; "description"?: string | null; "description_fr"?: string | null; "type": "volunteer" | "paid"; "program_type": "general" | "validation"; "compensation"?: string | null; "max_participants"?: number | null; "product_slug"?: string | null; "product_name"?: string | null; "starts_at"?: string | null; "ends_at"?: string | null; "status": "draft" | "open" | "closed" | "archived"; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_practitioners_programs_show": { path: { "program": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_practitioners_programs_update": { path: { "program": string | number; }; query: {  }; queryRequired: false; body: { "title"?: string; "title_fr"?: string | null; "description"?: string | null; "description_fr"?: string | null; "type"?: "volunteer" | "paid"; "program_type"?: "general" | "validation"; "compensation"?: string | null; "max_participants"?: number | null; "product_slug"?: string | null; "product_name"?: string | null; "starts_at"?: string | null; "ends_at"?: string | null; "status"?: "draft" | "open" | "closed" | "archived"; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_resourceSummaries_metric": { path: {  }; query: { "page"?: number; "per_page"?: number; "metric": "attendance.absent_today" | "announcements.active" | "audit_logs.total" | "deals.open" | "employees.staff" | "invoice_templates.due" | "jobs.open" | "knowledge.drafts" | "leads.new" | "leave.pending" | "licenses.expiring" | "performance.drafts" | "products.active" | "projects.active" | "risks.high_open" | "tickets.open" | "training.expiring" | "tester_assignments.pending" | "advisory_council.user_exists" | "validation_certificates.member_exists" | "tester_assignments.has_bug_reports" | "surveys.has_responses" | "leave_types.has_requests" | "document_templates.document_count" | "products.license_count" | "roles.permission_count" | "roles.user_count" | "practitioners.cohort_exists"; "id"?: number; "program_id"?: number; }; queryRequired: true; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_users": { path: {  }; query: { "page"?: number; "per_page"?: number; "role"?: "super_admin" | "admin" | "support" | "accountant" | "hr" | "manager" | "staff" | "employee" | "tester" | "customer" | "practitioner" | "partner" | "administrators" | "support_staff" | "workforce_staff" | "managers" | "testers" | "customers" | "leave_staff" | "admin_support" | "payroll_staff"; "id"?: number; "include_email"?: boolean; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_leads": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_departments": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_salaryGrades": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_nextEmployeeId": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_customerCountries": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_userRoles": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_rolePermissions": { path: {  }; query: { "page"?: number; "per_page"?: number; "id": number; }; queryRequired: true; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_auditLogFilters": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_projects": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_jobOpenings": { path: {  }; query: { "page"?: number; "per_page"?: number; "status"?: "open" | "all"; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_knowledgeCategories": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_validationPrograms": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_testCases": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_vendors": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_invoices": { path: {  }; query: { "page"?: number; "per_page"?: number; "id"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_licenses": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_purchaseOrders": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_documentTemplates": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_cohortMembers": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_references_cohorts": { path: {  }; query: { "page"?: number; "per_page"?: number; "program_id"?: number; "status"?: "active" | "draft" | "completed"; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "public_sitemap_data": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_invoiceItems": { path: { "invoice": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_quoteItems": { path: { "quote": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_supplierBillItems": { path: { "supplierBill": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_purchaseOrderItems": { path: { "purchaseOrder": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_finance_creditNoteItems": { path: { "creditNote": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_auditLogs_index": { path: {  }; query: { "page"?: number; "per_page"?: number; "action"?: string; "model_type"?: string; "user_id"?: number; "from"?: string; "until"?: string; "search"?: string | null; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_auditLogs_show": { path: { "auditLog": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_practitioners_applications_pay": { path: { "application": string | number; }; query: {  }; queryRequired: false; body: { "payout_amount": number; "payout_currency": string; "network_override"?: "mtn" | "manual"; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_apiClients_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_apiClients_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "owner_id": number; "scopes": Array<unknown>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_apiClients_usage": { path: { "client": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_apiClients_quota": { path: { "client": string | number; }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_apiClients_setQuota": { path: { "client": string | number; }; query: {  }; queryRequired: false; body: { "daily_request_limit": number | null; "monthly_request_limit": number | null; } & Record<string, unknown>; bodyRequired: true; idempotencyRequired: true; };
  "admin_apiClients_rotate": { path: { "client": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_apiClients_revoke": { path: { "client": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "admin_oauthClients_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "admin_oauthClients_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "owner_id": number; "redirect_uris": Array<(string | string)>; "scopes": Array<string>; }; bodyRequired: true; idempotencyRequired: true; };
  "admin_oauthClients_revoke": { path: { "client": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "developer_oauth_applications_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "developer_oauth_applications_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "redirect_uris": Array<string>; "scopes": Array<string>; } & Record<string, unknown>; bodyRequired: true; idempotencyRequired: true; };
  "developer_oauth_applications_update": { path: { "client": string | number; }; query: {  }; queryRequired: false; body: { "name": string; "redirect_uris": Array<string>; "scopes": Array<string>; } & Record<string, unknown>; bodyRequired: true; idempotencyRequired: true; };
  "developer_oauth_applications_revoke": { path: { "client": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "developer_machine_clients_index": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "developer_machine_clients_create": { path: {  }; query: {  }; queryRequired: false; body: { "name": string; "scopes": Array<unknown>; }; bodyRequired: true; idempotencyRequired: true; };
  "developer_machine_clients_rotate": { path: { "client": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "developer_machine_clients_revoke": { path: { "client": string | number; }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: true; };
  "api_product_discovery": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "api_product_status": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "api_product_changelog": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "api_product_catalogue": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "publishing_service_offerings_catalogue": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "readOwnProfile": { path: {  }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "readIntegrationQuota": { path: {  }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "readIntegrationUsage": { path: {  }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "readOwnMachineRequestMetrics": { path: {  }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "issueDelegatedUserToken": { path: {  }; query: {  }; queryRequired: false; body: URLSearchParams; bodyRequired: true; idempotencyRequired: false; };
  "readOwnBusinessActivity": { path: {  }; query: { "page"?: number; "per_page"?: number; }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "readOwnBusinessUsage": { path: {  }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
  "readOwnBusinessRequestMetrics": { path: {  }; query: {  }; queryRequired: false; body: undefined; bodyRequired: false; idempotencyRequired: false; };
}

type Entry<I extends BusinessOperationId> = BusinessOperationRequestMap[I];
type PathOption<I extends BusinessOperationId> = keyof Entry<I>['path'] extends never ? { path?: never } : { path: Entry<I>['path'] };
type QueryOption<I extends BusinessOperationId> = keyof Entry<I>['query'] extends never ? { query?: never } : Entry<I>['queryRequired'] extends true ? { query: Entry<I>['query'] } : { query?: Entry<I>['query'] };
type BodyOption<I extends BusinessOperationId> = Entry<I>['body'] extends undefined ? { body?: never } : Entry<I>['bodyRequired'] extends true ? { body: Exclude<Entry<I>['body'], undefined> } : { body?: Entry<I>['body'] };
type IdempotencyOption<I extends BusinessOperationId> = Entry<I>['idempotencyRequired'] extends true ? { idempotencyKey: string } : { idempotencyKey?: string };
export type BusinessOperationRequestOptions<I extends BusinessOperationId> = PathOption<I> & QueryOption<I> & BodyOption<I> & IdempotencyOption<I>;
