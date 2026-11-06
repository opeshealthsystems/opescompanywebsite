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
// @ts-expect-error Quote result fields are constrained by the published response schema.
quotes.data.data.items[0]!.invented_field;
// @ts-expect-error Customer profile detail must use its declared resource key.
customer.data.data.profile;
// @ts-expect-error Learning progress is typed to the server projection.
courses.data.data.my_enrollments[0]!.invented_progress;

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
