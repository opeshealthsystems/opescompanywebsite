<?php
// Literal output field contracts. Changes to fillable do not change these API fields.
return [
    \App\Models\AccountantProfile::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'accounting_specialization', 'certifications', 'bio', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\AdvisoryCouncilMember::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'validation_certificate_id', 'title', 'term_start', 'term_end', 'status', 'invited_by', 'invited_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Announcement::class => [
        'public' => ['id'],
        'private' => ['id', 'title', 'body', 'audience', 'is_pinned', 'is_active', 'author_id', 'published_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Asset::class => [
        'public' => ['id'],
        'private' => ['id', 'reference', 'name', 'category', 'serial_number', 'brand', 'model', 'purchase_date', 'purchase_price', 'current_value', 'currency', 'location', 'assigned_to', 'status', 'warranty_expires', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\AttendanceRecord::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'date', 'check_in', 'check_out', 'status', 'hours_worked', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\AuditLog::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'model_type', 'model_id', 'action', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\BlogComment::class => [
        'public' => ['id'],
        'private' => ['id', 'blog_post_id', 'name', 'email', 'content', 'approved', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\BlogPost::class => [
        'public' => ['id', 'title', 'title_fr', 'slug', 'excerpt', 'excerpt_fr', 'body', 'body_fr', 'cover_image', 'reading_time', 'category', 'author', 'published', 'published_at', 'created_at', 'updated_at'],
        'private' => ['id', 'title', 'title_fr', 'slug', 'excerpt', 'excerpt_fr', 'body', 'body_fr', 'cover_image', 'reading_time', 'category', 'author', 'published', 'published_at', 'created_at', 'updated_at'],
        'aggregates' => ['category', 'count'],
        'relations' => [],
    ],
    \App\Models\Budget::class => [
        'public' => ['id'],
        'private' => ['id', 'year', 'category', 'department', 'allocated_amount', 'currency', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\ClinicalReview::class => [
        'public' => ['id'],
        'private' => ['id', 'issue_report_id', 'reviewer_id', 'decision', 'notes', 'reviewed_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Cohort::class => [
        'public' => ['id'],
        'private' => ['id', 'practitioner_program_id', 'name', 'specialty', 'description', 'start_date', 'end_date', 'max_members', 'status', 'created_at', 'updated_at'],
        'relations' => ['program'],
    ],
    \App\Models\CohortMember::class => [
        'public' => ['id'],
        'private' => ['id', 'cohort_id', 'user_id', 'status', 'placed_at', 'created_at', 'updated_at'],
        'relations' => ['cohort', 'user'],
    ],
    \App\Models\CohortTestCase::class => [
        'public' => ['id'],
        'private' => ['id', 'cohort_id', 'validation_test_case_id', 'due_date', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\CompanySetting::class => [
        'public' => ['id'],
        'private' => ['id', 'company_name', 'company_email', 'company_phone', 'company_address', 'company_website', 'default_currency', 'fiscal_year_start_month', 'invoice_prefix', 'quote_prefix', 'default_tax_rate', 'timezone', 'date_format', 'logo_path', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Contract::class => [
        'public' => ['id'],
        'private' => ['id', 'reference', 'title', 'lead_id', 'type', 'status', 'value', 'currency', 'start_date', 'end_date', 'auto_renew', 'signed_at', 'created_by', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Course::class => [
        'public' => ['id', 'product_slug', 'title', 'title_fr', 'slug', 'description', 'description_fr', 'level', 'duration_hours', 'cover_image', 'is_active', 'is_featured', 'sort_order', 'created_at', 'updated_at'],
        'private' => ['id', 'product_slug', 'title', 'title_fr', 'slug', 'description', 'description_fr', 'level', 'duration_hours', 'cover_image', 'is_active', 'is_featured', 'sort_order', 'created_at', 'updated_at'],
        'relations' => ['lessons'],
    ],
    \App\Models\CourseCertificate::class => [
        'public' => ['id'],
        'private' => ['id', 'enrollment_id', 'user_id', 'course_id', 'certificate_number', 'issued_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\CourseEnrollment::class => [
        'public' => ['id'],
        'private' => ['id', 'course_id', 'user_id', 'status', 'enrolled_at', 'completed_at', 'created_at', 'updated_at'],
        'relations' => ['course', 'progress', 'certificate'],
    ],
    \App\Models\CourseLesson::class => [
        'public' => ['id', 'course_id', 'title', 'title_fr', 'content', 'content_fr', 'video_url', 'duration_minutes', 'sort_order'],
        'private' => ['id', 'course_id', 'title', 'title_fr', 'content', 'content_fr', 'video_url', 'duration_minutes', 'sort_order', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\CourseLessonProgress::class => [
        'public' => ['id'],
        'private' => ['id', 'enrollment_id', 'lesson_id', 'completed_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\CreditNote::class => [
        'public' => ['id'],
        'private' => ['id', 'reference', 'invoice_id', 'reason', 'status', 'subtotal', 'tax_amount', 'total', 'currency', 'issued_at', 'notes', 'created_by', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\CreditNoteItem::class => [
        'public' => ['id'],
        'private' => ['id', 'credit_note_id', 'description', 'quantity', 'unit_price', 'total', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\CustomerProfile::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'facility_name', 'facility_type', 'country', 'city', 'address', 'created_at', 'updated_at'],
        'relations' => ['user'],
    ],
    \App\Models\DailyTestSession::class => [
        'public' => ['id'],
        'private' => ['id', 'cohort_member_id', 'validation_product_id', 'validation_module_id', 'validation_workflow_id', 'facility_context', 'date', 'start_time', 'end_time', 'tasks_completed', 'comments', 'created_at', 'updated_at'],
        'relations' => ['product', 'module', 'workflow'],
    ],
    \App\Models\Deal::class => [
        'public' => ['id'],
        'private' => ['id', 'reference', 'title', 'lead_id', 'stage', 'value', 'currency', 'probability', 'expected_close_date', 'actual_close_date', 'owner_id', 'notes', 'lost_reason', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\DemoRequest::class => [
        'public' => ['id'],
        'private' => ['id', 'name', 'email', 'phone', 'organization_name', 'country', 'institution_type', 'institution_size', 'products', 'message', 'preferred_date', 'locale', 'status', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Department::class => [
        'public' => ['id'],
        'private' => ['id', 'name', 'code', 'head_id', 'parent_id', 'is_active', 'description', 'created_at', 'updated_at'],
        'relations' => ['head'],
    ],
    \App\Models\DeveloperTask::class => [
        'public' => ['id'],
        'private' => ['id', 'issue_report_id', 'assigned_to', 'title', 'priority', 'status', 'resolution_notes', 'started_at', 'fixed_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Document::class => [
        'public' => ['id', 'type', 'title', 'reference_number', 'body_rendered', 'status', 'requires_signature', 'signed_at', 'signed_by_name', 'valid_until'],
        'private' => ['id', 'document_template_id', 'type', 'title', 'reference_number', 'body_rendered', 'issued_by', 'addressee_user_id', 'addressee_name', 'addressee_email', 'status', 'requires_signature', 'signed_at', 'signed_by_name', 'valid_until', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\DocumentTemplate::class => [
        'public' => ['id'],
        'private' => ['id', 'name', 'type', 'body', 'variables', 'is_active', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\EmailTemplate::class => [
        'public' => ['id'],
        'private' => ['id', 'name', 'slug', 'type', 'subject', 'body', 'variables', 'is_active', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\EmployeeProfile::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'employment_type', 'contract_end_date', 'salary', 'currency', 'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Expense::class => [
        'public' => ['id'],
        'private' => ['id', 'reference', 'title', 'description', 'category', 'amount', 'currency', 'vendor', 'expense_date', 'status', 'submitted_by', 'approved_by', 'approved_at', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\FinalEvaluation::class => [
        'public' => ['id'],
        'private' => ['id', 'cohort_member_id', 'metrics', 'assessment', 'rating', 'recommendation', 'evaluator_id', 'evaluated_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Invoice::class => [
        'public' => ['id'],
        'private' => ['id', 'customer_id', 'issued_by', 'license_id', 'status', 'currency', 'tax_rate', 'notes', 'due_date', 'paid_at', 'created_at', 'updated_at', 'invoice_number', 'subtotal', 'tax_amount', 'grand_total', 'amount_paid', 'amount_outstanding'],
        'relations' => ['items', 'payments', 'customer'],
    ],
    \App\Models\InvoiceItem::class => [
        'public' => ['id'],
        'private' => ['id', 'invoice_id', 'description', 'quantity', 'unit_price', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\InvoicePayment::class => [
        'public' => ['id'],
        'private' => ['id', 'invoice_id', 'amount', 'payment_method', 'payment_date', 'reference_number', 'notes', 'recorded_by', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\InvoiceTemplate::class => [
        'public' => ['id'],
        'private' => ['id', 'name', 'customer_id', 'client_name', 'client_email', 'frequency', 'next_due_date', 'end_date', 'payment_terms_days', 'currency', 'tax_rate', 'line_items', 'notes', 'is_active', 'issued_by', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\IssueReport::class => [
        'public' => ['id'],
        'private' => ['id', 'cohort_member_id', 'daily_test_session_id', 'validation_product_id', 'validation_module_id', 'validation_workflow_id', 'validation_test_case_id', 'title', 'issue_type', 'severity', 'description', 'steps_to_reproduce', 'expected_result', 'actual_result', 'clinical_impact', 'recommendation', 'status', 'created_at', 'updated_at'],
        'relations' => ['product', 'module', 'workflow', 'testCase'],
    ],
    \App\Models\JobApplication::class => [
        'public' => ['id'],
        'private' => ['id', 'job_opening_id', 'applicant_name', 'email', 'phone', 'status', 'applied_at', 'interview_date', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\JobOpening::class => [
        'public' => ['id'],
        'private' => ['id', 'title', 'department_id', 'type', 'location', 'status', 'description', 'requirements', 'openings_count', 'posted_at', 'closes_at', 'salary_range', 'created_by', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\KnowledgeBaseArticle::class => [
        'public' => ['id'],
        'private' => ['id', 'category_id', 'author_id', 'title', 'slug', 'content', 'status', 'is_public', 'views', 'tags', 'published_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\KnowledgeBaseCategory::class => [
        'public' => ['id'],
        'private' => ['id', 'name', 'slug', 'description', 'parent_id', 'is_public', 'sort_order', 'is_active', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Lead::class => [
        'public' => ['id'],
        'private' => ['id', 'name', 'email', 'phone', 'facility_type', 'products', 'message', 'source', 'product_slug', 'locale', 'status', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\LeaveBalance::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'year', 'type', 'entitled_days', 'used_days', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\LeaveRequest::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'leave_type_id', 'type', 'start_date', 'end_date', 'total_days', 'reason', 'status', 'approved_by', 'approved_at', 'notes', 'created_at', 'updated_at'],
        'aggregates' => ['month', 'type', 'user_id', 'total_days', 'count'],
        'relations' => ['employee', 'approver', 'leaveType'],
    ],
    \App\Models\LeaveType::class => [
        'public' => ['id'],
        'private' => ['id', 'name', 'code', 'days_per_year', 'max_carry_forward', 'is_paid', 'color', 'requires_approval', 'is_active', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\License::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'issued_by', 'product_slug', 'product_name', 'plan', 'seats', 'status', 'start_date', 'end_date', 'price', 'currency', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\ManagerProfile::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'management_level', 'department_id', 'bio', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Milestone::class => [
        'public' => ['id'],
        'private' => ['id', 'project_id', 'title', 'description', 'due_date', 'status', 'completed_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\PartnerApplication::class => [
        'public' => ['id'],
        'private' => ['id', 'organization_name', 'contact_name', 'email', 'phone', 'country', 'city', 'partner_type', 'organization_type', 'annual_revenue_range', 'target_market', 'description', 'website', 'locale', 'status', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\PartnerInstitution::class => [
        'public' => ['id', 'name', 'name_fr', 'type', 'country', 'city', 'website', 'logo', 'description', 'description_fr', 'partnership_since', 'is_featured', 'is_active', 'sort_order', 'created_at', 'updated_at'],
        'private' => ['id', 'name', 'name_fr', 'type', 'country', 'city', 'website', 'logo', 'description', 'description_fr', 'partnership_since', 'is_featured', 'is_active', 'sort_order', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\PayrollDeductionType::class => [
        'public' => ['id'],
        'private' => ['id', 'name', 'code', 'calculation_type', 'rate', 'description', 'apply_by_default', 'is_active', 'sort_order', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\PayrollEntry::class => [
        'public' => ['id'],
        'private' => ['id', 'payroll_run_id', 'user_id', 'gross_salary', 'deductions', 'total_deductions', 'net_salary', 'currency', 'status', 'created_at', 'updated_at'],
        'aggregates' => ['dept_name', 'total_net', 'total_gross', 'count'],
        'relations' => ['user', 'payrollRun'],
    ],
    \App\Models\PayrollRun::class => [
        'public' => ['id'],
        'private' => ['id', 'reference', 'period_start', 'period_end', 'status', 'total_gross', 'total_net', 'currency', 'notes', 'processed_by', 'completed_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\PerformanceReview::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'reviewer_id', 'review_period', 'review_date', 'overall_rating', 'goals_rating', 'teamwork_rating', 'technical_rating', 'communication_rating', 'strengths', 'areas_for_improvement', 'goals_for_next_period', 'employee_comments', 'status', 'acknowledged_at', 'created_at', 'updated_at'],
        'relations' => ['employee', 'reviewer'],
    ],
    \App\Models\PractitionerApplication::class => [
        'public' => ['id'],
        'private' => ['id', 'practitioner_id', 'program_id', 'motivation', 'status', 'reviewed_by', 'reviewed_at', 'payout_status', 'payout_amount', 'payout_currency', 'paid_at', 'created_at', 'updated_at'],
        'aggregates' => ['practitioner_id', 'programs_count'],
        'relations' => ['program'],
    ],
    \App\Models\PractitionerBugReport::class => [
        'public' => ['id'],
        'private' => ['id', 'practitioner_id', 'product_slug', 'title', 'severity', 'description', 'steps_to_reproduce', 'status', 'responded_by', 'responded_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\PractitionerFinding::class => [
        'public' => ['id'],
        'private' => ['id', 'application_id', 'practitioner_id', 'wait_time_rating', 'data_integrity_rating', 'usability_rating', 'overall_rating', 'findings_text', 'video_url', 'is_published', 'created_at', 'updated_at'],
        'aggregates' => ['practitioner_id', 'findings_count', 'avg_rating', 'overall', 'usability', 'wait_time', 'data_integrity'],
        'relations' => [],
    ],
    \App\Models\PractitionerProfile::class => [
        'public' => ['id', 'profession', 'specialty', 'workplace_name', 'workplace_city', 'workplace_country', 'years_of_experience', 'bio', 'opes_testimonial', 'is_verified'],
        'private' => ['id', 'user_id', 'profession', 'specialty', 'workplace_name', 'workplace_city', 'workplace_country', 'years_of_experience', 'bio', 'opes_testimonial', 'is_verified', 'created_at', 'updated_at'],
        'relations' => ['user'],
    ],
    \App\Models\PractitionerProgram::class => [
        'public' => ['id', 'product_slug', 'product_name', 'title', 'title_fr', 'description', 'description_fr', 'type', 'program_type', 'compensation', 'max_participants', 'status', 'starts_at', 'ends_at', 'created_at', 'updated_at'],
        'private' => ['id', 'product_slug', 'product_name', 'title', 'title_fr', 'description', 'description_fr', 'type', 'program_type', 'compensation', 'max_participants', 'status', 'starts_at', 'ends_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Product::class => [
        'public' => ['id', 'slug', 'name', 'name_fr', 'subtitle', 'subtitle_fr', 'category', 'tagline', 'icon', 'color', 'is_active', 'is_featured', 'sort_order', 'created_at', 'updated_at'],
        'private' => ['id', 'slug', 'name', 'name_fr', 'subtitle', 'subtitle_fr', 'category', 'tagline', 'icon', 'color', 'is_active', 'is_featured', 'sort_order', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\ProductReview::class => [
        'public' => ['id'],
        'private' => ['id', 'issue_report_id', 'reviewer_id', 'decision', 'notes', 'reviewed_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Project::class => [
        'public' => ['id'],
        'private' => ['id', 'reference', 'title', 'description', 'status', 'priority', 'owner_id', 'start_date', 'end_date', 'budget', 'currency', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\PurchaseOrder::class => [
        'public' => ['id'],
        'private' => ['id', 'reference', 'vendor_id', 'vendor_name', 'title', 'description', 'status', 'requested_by', 'approved_by', 'approved_at', 'expected_date', 'received_date', 'subtotal', 'tax_amount', 'total', 'currency', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\PurchaseOrderItem::class => [
        'public' => ['id'],
        'private' => ['id', 'purchase_order_id', 'description', 'quantity', 'unit_price', 'total', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Quote::class => [
        'public' => ['id'],
        'private' => ['id', 'reference', 'lead_id', 'title', 'status', 'valid_until', 'subtotal', 'tax_rate', 'tax_amount', 'total', 'currency', 'notes', 'created_by', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\QuoteItem::class => [
        'public' => ['id'],
        'private' => ['id', 'quote_id', 'product_name', 'description', 'quantity', 'unit_price', 'total', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Retest::class => [
        'public' => ['id'],
        'private' => ['id', 'issue_report_id', 'developer_task_id', 'cohort_member_id', 'result', 'notes', 'retested_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Risk::class => [
        'public' => ['id'],
        'private' => ['id', 'reference', 'title', 'description', 'category', 'likelihood', 'impact', 'risk_score', 'status', 'owner_id', 'mitigation_plan', 'review_date', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\SalaryGrade::class => [
        'public' => ['id'],
        'private' => ['id', 'name', 'code', 'description', 'min_salary', 'max_salary', 'currency', 'is_active', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\ServiceRequest::class => [
        'public' => ['id'],
        'private' => ['id', 'customer_id', 'type', 'product_slug', 'description', 'preferred_date', 'preferred_time', 'location', 'status', 'assigned_technician_id', 'confirmed_date', 'confirmed_time', 'reference_number', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\SlaRule::class => [
        'public' => ['id'],
        'private' => ['id', 'name', 'ticket_type', 'ticket_priority', 'response_time_hours', 'resolution_time_hours', 'is_active', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Suggestion::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'title', 'category', 'body', 'status', 'responded_by', 'responded_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\SupplierBill::class => [
        'public' => ['id'],
        'private' => ['id', 'reference', 'vendor_id', 'vendor_name', 'purchase_order_id', 'bill_number', 'status', 'issue_date', 'due_date', 'subtotal', 'tax_amount', 'total', 'currency', 'paid_at', 'notes', 'created_by', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\SupplierBillItem::class => [
        'public' => ['id'],
        'private' => ['id', 'supplier_bill_id', 'description', 'quantity', 'unit_price', 'total', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\SupportProfile::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'ticket_specialization', 'shift', 'languages', 'bio', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Survey::class => [
        'public' => ['id'],
        'private' => ['id', 'title', 'title_fr', 'description', 'description_fr', 'audience', 'status', 'starts_at', 'ends_at', 'created_at', 'updated_at'],
        'relations' => ['questions'],
    ],
    \App\Models\SurveyAnswer::class => [
        'public' => ['id'],
        'private' => ['id', 'response_id', 'question_id', 'answer_text', 'answer_rating', 'answer_choice', 'answer_bool', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\SurveyQuestion::class => [
        'public' => ['id'],
        'private' => ['id', 'survey_id', 'question', 'question_fr', 'type', 'options', 'options_fr', 'is_required', 'sort_order', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\SurveyResponse::class => [
        'public' => ['id'],
        'private' => ['id', 'survey_id', 'user_id', 'submitted_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\TesterApplication::class => [
        'public' => ['id'],
        'private' => ['id', 'name', 'email', 'phone', 'profession', 'specialty', 'institution_name', 'country', 'city', 'years_experience', 'devices', 'platforms', 'motivation', 'tech_experience', 'locale', 'status', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\TesterAssignment::class => [
        'public' => ['id'],
        'private' => ['id', 'assigned_to', 'assigned_by', 'product_slug', 'product_name', 'title', 'description', 'status', 'due_date', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\TesterProfile::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'testing_specialty', 'device_types', 'portfolio_url', 'certifications', 'availability_notes', 'bio', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Testimonial::class => [
        'public' => ['id', 'author_name', 'author_title', 'author_facility', 'author_country', 'body', 'body_fr', 'rating', 'is_active', 'sort_order', 'created_at', 'updated_at'],
        'private' => ['id', 'author_name', 'author_title', 'author_facility', 'author_country', 'body', 'body_fr', 'rating', 'is_active', 'sort_order', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Ticket::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'assigned_to', 'subject', 'description', 'type', 'status', 'priority', 'resolution', 'resolved_at', 'closed_at', 'tester_assignment_id', 'sla_rule_id', 'sla_response_due_at', 'sla_resolution_due_at', 'created_at', 'updated_at'],
        'relations' => ['publicReplies', 'replies', 'author', 'assignee'],
    ],
    \App\Models\TicketReply::class => [
        'public' => ['id'],
        'private' => ['id', 'ticket_id', 'user_id', 'body', 'is_internal', 'created_at', 'updated_at'],
        'relations' => ['author'],
    ],
    \App\Models\Timesheet::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'project_id', 'date', 'hours', 'description', 'is_billable', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\TrainingRecord::class => [
        'public' => ['id'],
        'private' => ['id', 'user_id', 'title', 'provider', 'category', 'status', 'start_date', 'completed_at', 'expires_at', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\User::class => [
        'public' => ['id', 'name'],
        'private' => ['id', 'name', 'email', 'employee_id', 'phone', 'department', 'position', 'hire_date', 'is_active', 'avatar', 'department_id', 'salary_grade_id', 'base_salary', 'salary_currency', 'created_at', 'updated_at'],
        'relations' => ['customerProfile', 'practitionerProfile', 'employeeProfile', 'supportProfile', 'testerProfile', 'managerProfile', 'accountantProfile', 'department'],
    ],
    \App\Models\ValidationCertificate::class => [
        'public' => ['id'],
        'private' => ['id', 'cohort_member_id', 'final_evaluation_id', 'certificate_number', 'score', 'tier', 'issued_by', 'issued_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\ValidationModule::class => [
        'public' => ['id', 'validation_product_id', 'name', 'code', 'description', 'is_active', 'created_at', 'updated_at'],
        'private' => ['id', 'validation_product_id', 'name', 'code', 'description', 'is_active', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\ValidationProduct::class => [
        'public' => ['id', 'name', 'code', 'description', 'is_active', 'created_at', 'updated_at'],
        'private' => ['id', 'name', 'code', 'description', 'is_active', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\ValidationTestCase::class => [
        'public' => ['id', 'validation_workflow_id', 'title', 'description', 'steps', 'expected_result', 'is_active', 'created_at', 'updated_at'],
        'private' => ['id', 'validation_workflow_id', 'title', 'description', 'steps', 'expected_result', 'is_active', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\ValidationWorkflow::class => [
        'public' => ['id', 'validation_module_id', 'name', 'code', 'description', 'is_active', 'created_at', 'updated_at'],
        'private' => ['id', 'validation_module_id', 'name', 'code', 'description', 'is_active', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\Vendor::class => [
        'public' => ['id'],
        'private' => ['id', 'name', 'contact_name', 'email', 'phone', 'address', 'is_active', 'notes', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\WeeklyReview::class => [
        'public' => ['id'],
        'private' => ['id', 'cohort_id', 'week_start', 'week_end', 'metrics', 'summary', 'action_items', 'author_id', 'generated_at', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \App\Models\WebsiteFormDelivery::class => [
        'public' => ['id'],
        'private' => ['event_id', 'event_type', 'submission_type', 'submission_id', 'recipient', 'status', 'attempts',
            'next_attempt_at', 'sent_at', 'message_id', 'error_type', 'created_at', 'updated_at'],
        'relations' => [],
    ],
    \Illuminate\Notifications\DatabaseNotification::class => [
        'public' => [], 'private' => ['id', 'data', 'read_at', 'created_at'], 'relations' => [],
    ],
];
