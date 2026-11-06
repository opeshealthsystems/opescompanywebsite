import { OpesBusinessApiClient, createIdempotencyKey } from '../src/index.js';

const client = new OpesBusinessApiClient({ accessToken: 'type-fixture-token' });
const key = createIdempotencyKey();

void client.request('admin_finance_quotes_create', {
  body: { title: 'Clinic proposal', currency: 'XAF', items: [] },
  idempotencyKey: key,
});
void client.request('admin_finance_quotes_update', {
  path: { quote: 123 },
  body: { title: 'Updated proposal' },
  idempotencyKey: key,
});
void client.request('readOwnBusinessActivity', { query: { page: 1, per_page: 20 } });
void client.request('admin_resourceSummaries_metric', { query: { metric: 'tickets.open' } });
void client.request('issueDelegatedUserToken', {
  body: new URLSearchParams({ grant_type: 'authorization_code' }),
});
const status = await client.request('api_product_status');
status.data.data.api_version.toUpperCase();
// @ts-expect-error Undocumented response fields are not added to inferred result types.
status.data.data.unknown_field;
const typedQuote = await client.request<'admin_finance_quotes_update', { data: { id: number } }>(
  'admin_finance_quotes_update', { path: { quote: 123 }, body: { title: 'Example' }, idempotencyKey: key },
);
typedQuote.data.data.id.toFixed();
const enquiry = await client.request('submitEnquiry', {
  body: { name: 'Example', email: 'example@example.test' },
  idempotencyKey: key,
});
enquiry.data.data.status.toUpperCase();
const quotes = await client.request('admin_finance_quotes_index');
quotes.data.data.items[0]!.items[0]!.product_name.toUpperCase();
quotes.data.data.items[0]!.lead?.email?.toLowerCase();
quotes.data.data.items[0]!.subtotal.toUpperCase();
const quoteLineItems = await client.request('admin_finance_quoteItems', { path: { quote: 99 } });
quoteLineItems.data.data.items[0]!.unit_price.toUpperCase();
const createdQuote = await client.request('admin_finance_quotes_create', {
  body: { title: 'Clinic proposal', currency: 'XAF', items: [{ product_name: 'Platform licence', quantity: 2, unit_price: 100 }] },
  idempotencyKey: key,
});
createdQuote.data.data.subtotal.toUpperCase();
const updatedQuote = await client.request('admin_finance_quotes_update', {
  path: { quote: 99 }, body: { title: 'Updated proposal' }, idempotencyKey: key,
});
updatedQuote.data.data.total.toUpperCase();
const markedQuoteSent = await client.request('admin_finance_quotes_markSent', { path: { quote: 99 }, idempotencyKey: key });
markedQuoteSent.data.data.status.toUpperCase();
const acceptedQuote = await client.request('admin_finance_quotes_accept', { path: { quote: 99 }, idempotencyKey: key });
acceptedQuote.data.data.status.toUpperCase();
const deletedQuote = await client.request('admin_finance_quotes_delete', { path: { quote: 99 }, idempotencyKey: key });
deletedQuote.data.data.status.toUpperCase();
const createdInvoice = await client.request('admin_finance_invoices_create', {
  body: { customer_id: 7, currency: 'XAF', items: [{ description: 'Platform licence', quantity: 1, unit_price: 500 }] },
  idempotencyKey: key,
});
createdInvoice.data.data.grand_total.toFixed();
const updatedInvoice = await client.request('admin_finance_invoices_update', {
  path: { invoice: 31 }, body: { notes: 'Updated billing note' }, idempotencyKey: key,
});
updatedInvoice.data.data.invoice_number.toUpperCase();
const sentInvoice = await client.request('admin_finance_invoices_markSent', { path: { invoice: 31 }, idempotencyKey: key });
sentInvoice.data.data.status.toUpperCase();
const paidInvoice = await client.request('admin_finance_invoices_markPaid', { path: { invoice: 31 }, idempotencyKey: key });
paidInvoice.data.data.status.toUpperCase();
const recordedPayment = await client.request('admin_finance_invoices_recordPayment', {
  path: { invoice: 31 }, body: { amount: 500, payment_method: 'bank_transfer', payment_date: '2026-11-06' }, idempotencyKey: key,
});
recordedPayment.data.data.status.toUpperCase();
const overdueInvoice = await client.request('admin_finance_invoices_markOverdue', { path: { invoice: 31 }, idempotencyKey: key });
overdueInvoice.data.data.status.toUpperCase();
const batchSent = await client.request('admin_finance_invoices_markManySent', {
  body: { ids: [31, 32] }, idempotencyKey: key,
});
batchSent.data.data.updated.toFixed();
const deletedInvoice = await client.request('admin_finance_invoices_delete', { path: { invoice: 31 }, idempotencyKey: key });
deletedInvoice.data.data.status.toUpperCase();
const invoiceTemplates = await client.request('admin_finance_invoiceTemplates_index', { query: { active: true, search: 'clinic' } });
invoiceTemplates.data.data.items[0]!.frequency.toUpperCase();
invoiceTemplates.data.data.items[0]!.line_items[0]!.description.toUpperCase();
const invoiceTemplate = await client.request('admin_finance_invoiceTemplates_show', { path: { invoiceTemplate: 8 } });
invoiceTemplate.data.data.invoice_template.tax_rate.toUpperCase();
const createdInvoiceTemplate = await client.request('admin_finance_invoiceTemplates_create', {
  body: { name: 'Annual facility licence', frequency: 'annual', next_due_date: '2027-01-01', payment_terms_days: 30,
    currency: 'XAF', tax_rate: 0, issued_by: 1, line_items: [{ description: 'Facility licence', quantity: 1, unit_price: 1000 }] },
  idempotencyKey: key,
});
createdInvoiceTemplate.data.data.is_active.valueOf();
const updatedInvoiceTemplate = await client.request('admin_finance_invoiceTemplates_update', {
  path: { invoiceTemplate: 8 }, body: { notes: 'Updated terms' }, idempotencyKey: key,
});
updatedInvoiceTemplate.data.data.name.toUpperCase();
const generatedTemplateInvoice = await client.request('admin_finance_invoiceTemplates_generate', {
  path: { invoiceTemplate: 8 }, idempotencyKey: key,
});
generatedTemplateInvoice.data.data.invoice_number.toUpperCase();
const customers = await client.request('admin_customerProfiles_index');
customers.data.data.items[0]!.customer?.email.toLowerCase();
const customer = await client.request('admin_customerProfiles_show', { path: { customerProfile: 12 } });
customer.data.data.customer_profile.facility_name?.toUpperCase();
customer.data.data.customer_profile.customer?.phone?.toLowerCase();
const courses = await client.request('practitioner_coursecontroller_index');
courses.data.data.courses[0]!.title.toUpperCase();
courses.data.data.my_enrollments[0]!.progress_percent.toFixed();
const course = await client.request('practitioner_coursecontroller_show', { path: { course: 'example-course' } });
course.data.data.course.lessons?.[0]!.content.toUpperCase();
const lesson = await client.request('practitioner_lessoncontroller_show', { path: { course: 'example-course', lesson: 1 } });
lesson.data.data.next_lesson?.title.toUpperCase();
const certificates = await client.request('practitioner_certificatecontroller_index');
certificates.data.data.certificates[0]!.certificate_number.toUpperCase();
certificates.data.data.validation_certificates[0]!.tier.toUpperCase();
const exams = await client.request('practitioner_assessments_index', { path: { course: 'example-course' } });
exams.data.data.assessments[0]!.attempts_remaining?.toFixed();
const exam = await client.request('practitioner_assessments_show', { path: { assessment: 7 } });
exam.data.data.assessment.questions[0]!.choices[0]!.label.toUpperCase();
// @ts-expect-error Learner exam projections never reveal correct choices before submission.
exam.data.data.assessment.questions[0]!.correct_choice_id;
const startedAttempt = await client.request('practitioner_assessments_attempts_start', {
  path: { assessment: 7 }, idempotencyKey: key,
});
startedAttempt.data.data.assessment.questions[0]!.prompt.toUpperCase();
const submittedAttempt = await client.request('practitioner_assessments_attempts_submit', {
  path: { assessment: 7, attempt: 12 }, body: { answers: { question: 'choice' } }, idempotencyKey: key,
});
submittedAttempt.data.data.review[0]!.correct.toString();
const customerProfile = await client.request('customer_profilecontroller_show');
customerProfile.data.data.profile.country?.toUpperCase();
customerProfile.data.data.user.email.toLowerCase();
const serviceRequests = await client.request('customer_servicerequestcontroller_index');
serviceRequests.data.data.service_requests[0]!.reference_number.toUpperCase();
const serviceRequest = await client.request('customer_servicerequestcontroller_show', { path: { serviceRequest: 22 } });
serviceRequest.data.data.service_request.preferred_date.toUpperCase();
const adminClientUsage = await client.request('admin_apiClients_usage', { path: { client: 12 } });
adminClientUsage.data.data.usage.requests_last_30_days.toFixed();
adminClientUsage.data.data.usage.daily_usage[0]!.date.toUpperCase();
const auditLogs = await client.request('admin_auditLogs_index');
auditLogs.data.data.items[0]!.action.toUpperCase();
const auditLog = await client.request('admin_auditLogs_show', { path: { auditLog: 12 } });
auditLog.data.data.audit_log.new_values;
// @ts-expect-error Audit-log fields are limited to the reviewed metadata projection.
auditLog.data.data.audit_log.client_secret;
const developerClients = await client.request('developer_machine_clients_index');
developerClients.data.data.items[0]!.metrics.daily[0]!.responses_4xx.toFixed();
developerClients.data.data.items[0]!.quota.daily_remaining?.toFixed();
// @ts-expect-error Developer machine-client listings never reveal credential material.
developerClients.data.data.items[0]!.client_secret;
const createdDeveloperClient = await client.request('developer_machine_clients_create', {
  body: { name: 'Research integration', scopes: ['catalogue:read', 'integration:read'] },
  idempotencyKey: key,
});
createdDeveloperClient.data.data.client_secret.toUpperCase();
createdDeveloperClient.data.data.secret_shown_once.valueOf();
const rotatedDeveloperClient = await client.request('developer_machine_clients_rotate', {
  path: { client: 18 }, idempotencyKey: key,
});
rotatedDeveloperClient.data.data.token_url.toLowerCase();
const revokedDeveloperClient = await client.request('developer_machine_clients_revoke', {
  path: { client: 18 }, idempotencyKey: key,
});
revokedDeveloperClient.data.data.revoked.valueOf();
const userReferences = await client.request('admin_references_users', { query: { role: 'customers', include_email: true } });
userReferences.data.data.items[0]!.email?.toLowerCase();
const invoiceReferences = await client.request('admin_references_invoices');
invoiceReferences.data.data.items[0]!.grand_total.toFixed();
const auditFilters = await client.request('admin_references_auditLogFilters');
auditFilters.data.data.model_types[0]?.toUpperCase();
const employeeId = await client.request('admin_references_nextEmployeeId');
employeeId.data.data.employee_id.toUpperCase();
// @ts-expect-error Administrator user-reference responses do not project phone numbers.
userReferences.data.data.items[0]!.phone;
const createdServiceRequest = await client.request('customer_servicerequestcontroller_store', {
  body: { type: 'maintenance', preferred_date: '2026-11-09', location: 'Example facility' }, idempotencyKey: key,
});
createdServiceRequest.data.data.service_request.reference_number.toUpperCase();
// @ts-expect-error Quote result fields are constrained by the published response schema.
quotes.data.data.items[0]!.invented_field;
// @ts-expect-error Customer profile detail must use its declared resource key.
customer.data.data.profile;
// @ts-expect-error Learning progress is typed to the server projection.
courses.data.data.my_enrollments[0]!.invented_progress;
// @ts-expect-error Internal service-request notes are never exposed to customer APIs.
serviceRequest.data.data.service_request.admin_notes;

// @ts-expect-error Quote currency is a contract enum.
void client.request('admin_finance_quotes_create', { body: { title: 'Clinic proposal', currency: 'GBP', items: [] }, idempotencyKey: key });
// @ts-expect-error This quote update requires an idempotency key.
void client.request('admin_finance_quotes_update', { path: { quote: 123 }, body: { title: 'Updated proposal' } });
// @ts-expect-error A quote update requires the quote path parameter.
void client.request('admin_finance_quotes_update', { body: { title: 'Updated proposal' }, idempotencyKey: key });
// @ts-expect-error Unknown query fields are rejected by the generated contract types.
void client.request('readOwnBusinessActivity', { query: { unexpected: true } });
// @ts-expect-error Required query parameters are enforced by the contract types.
void client.request('admin_resourceSummaries_metric');
