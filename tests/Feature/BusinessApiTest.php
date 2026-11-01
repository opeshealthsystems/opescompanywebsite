<?php

namespace Tests\Feature;

use App\Models\{User, Ticket};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Mail, Notification};
use Laravel\Passport\{ClientRepository, Passport};
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BusinessApiTest extends TestCase
{
    use RefreshDatabase;
    private static array $keys;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite(); Mail::fake(); Notification::fake();
        foreach (['admin', 'customer', 'support'] as $role) Role::findOrCreate($role, 'web');
        if (!isset(self::$keys)) {
            $options = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
            if (PHP_OS_FAMILY === 'Windows') $options['config'] = dirname(PHP_BINARY).'/extras/ssl/openssl.cnf';
            $key = openssl_pkey_new($options); openssl_pkey_export($key, $private, null, $options);
            self::$keys = ['private' => $private, 'public' => openssl_pkey_get_details($key)['key']];
        }
        config(['passport.private_key' => self::$keys['private'], 'passport.public_key' => self::$keys['public']]);
    }

    private function user(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]); $user->assignRole($role); return $user;
    }

    private function access(User $user, array $scopes): string
    {
        $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient('Fictional test client', ['https://client.example/callback'], false, $this->user('admin'));
        $client->scopes = $scopes; $client->save();
        $verifier = str_repeat('a', 64);
        $query = ['response_type' => 'code', 'client_id' => $client->id, 'redirect_uri' => 'https://client.example/callback',
            'scope' => implode(' ', $scopes), 'state' => str_repeat('s', 40), 'code_challenge_method' => 'S256',
            'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=')];
        $this->actingAs($user)->get('/oauth/authorize?'.http_build_query($query))->assertOk()->assertSee('Allow access');
        $approved = $this->post('/oauth/authorize', ['client_id' => $client->id, 'state' => $query['state'], 'auth_token' => session('authToken')])->assertRedirect();
        parse_str(parse_url($approved->headers->get('Location'), PHP_URL_QUERY), $callback);
        $this->assertSame($query['state'], $callback['state']);
        return $this->postJson('/api/v1/oauth/user-token', ['grant_type' => 'authorization_code', 'client_id' => $client->id,
            'redirect_uri' => 'https://client.example/callback', 'code' => $callback['code'], 'code_verifier' => $verifier])->assertOk()->json('access_token');
    }

    public function test_real_pkce_token_reads_own_tickets_and_rejects_other_users_records(): void
    {
        $user = $this->user('customer');
        $own = Ticket::create(['user_id' => $user->id, 'subject' => 'Fictional own ticket', 'description' => 'Test only', 'type' => 'support', 'priority' => 'normal', 'status' => 'open']);
        $other = Ticket::create(['user_id' => $this->user('customer')->id, 'subject' => 'Fictional other ticket', 'description' => 'Test only', 'type' => 'support', 'priority' => 'normal', 'status' => 'open']);
        $token = $this->access($user, ['customer:read']); $headers = ['Authorization' => 'Bearer '.$token];
        $this->getJson('/api/v1/customer/tickets', $headers)->assertOk()->assertJsonPath('data.tickets.items.0.id', $own->id)->assertJsonPath('data.tickets.meta.total', 1);
        $this->getJson('/api/v1/customer/tickets/'.$other->id, $headers)->assertForbidden();
        $this->getJson('/api/v1/customer/tickets/999999', $headers)->assertNotFound();
        $this->getJson('/api/v1/support/tickets', $headers)->assertForbidden();
        $user->update(['is_active' => false]);
        $this->getJson('/api/v1/customer/tickets', $headers)->assertUnauthorized();
    }

    public function test_missing_token_never_uses_browser_session(): void
    {
        $this->actingAs($this->user('customer'))->getJson('/api/v1/customer/tickets')->assertUnauthorized();
    }

    public function test_commands_require_keys_replay_once_and_reject_changed_payloads(): void
    {
        $user = $this->user('customer');
        $token = $this->access($user, ['customer:write', 'identity:read']);
        $headers = ['Authorization' => 'Bearer '.$token];
        $body = ['subject' => 'Fictional API ticket', 'description' => 'Test only', 'type' => 'support', 'priority' => 'medium'];
        $this->postJson('/api/v1/customer/tickets', $body, $headers)->assertUnprocessable();
        $headers['Idempotency-Key'] = 'fictional-command-000001';
        $first = $this->postJson('/api/v1/customer/tickets', $body, $headers)->assertOk();
        $repeat = $this->postJson('/api/v1/customer/tickets', $body, $headers)->assertOk();
        $this->assertSame($first->json(), $repeat->json());
        $this->assertDatabaseCount('tickets', 1);
        $this->postJson('/api/v1/customer/tickets', [...$body, 'subject' => 'Changed payload'], $headers)->assertConflict();
        $this->getJson('/api/v1/me/activity', $headers)->assertOk()->assertJsonPath('meta.total', 1);
        $this->assertDatabaseCount('business_event_inbox', 1);
        $this->assertDatabaseMissing('business_outbox', ['status' => 'uncertain']);
    }

    public function test_authorization_requires_s256_and_unpredictable_state(): void
    {
        $this->actingAs($this->user('customer'))->getJson('/oauth/authorize?response_type=code')->assertRedirect()->assertSessionHasErrors(['state', 'code_challenge', 'code_challenge_method']);
    }

    public function test_project_administration_uses_shared_commands_and_whitelisted_outputs(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['administration:read', 'administration:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'fictional-project-000001'];
        $body = ['title' => 'Fictional implementation project', 'status' => 'planning', 'priority' => 'medium', 'owner_id' => $admin->id, 'currency' => 'XAF', 'budget' => 1000];
        $first = $this->postJson('/api/v1/administration/projects', $body, $headers)->assertCreated();
        $id = $first->json('data.record.id');
        $this->postJson('/api/v1/administration/projects', $body, $headers)->assertCreated()->assertJsonPath('data.record.id', $id);
        $this->assertDatabaseCount('projects', 1);
        $this->getJson('/api/v1/administration/projects/'.$id, $headers)->assertOk()->assertJsonPath('data.project.title', $body['title'])->assertDontSee('password');
        $headers['Idempotency-Key'] = 'fictional-project-000002';
        $this->putJson('/api/v1/administration/projects/'.$id, [...$body, 'status' => 'active'], $headers)->assertOk();
        $this->assertDatabaseHas('projects', ['id' => $id, 'status' => 'active']);
    }

    public function test_license_issuance_replays_once_and_uses_server_product_metadata(): void
    {
        $admin = $this->user('admin'); $customer = $this->user('customer');
        $token = $this->access($admin, ['licensing:read', 'licensing:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'fictional-license-000001'];
        $product = array_key_first(\App\Support\ProductCatalog::options());
        $body = ['user_id' => $customer->id, 'product_slug' => $product, 'product_name' => 'Forged product name',
            'plan' => 'standard', 'seats' => 1, 'status' => 'active', 'start_date' => today()->toDateString(), 'end_date' => today()->addYear()->toDateString(), 'currency' => 'XAF'];
        $id = $this->postJson('/api/v1/administration/licenses', $body, $headers)->assertCreated()->json('data.record.id');
        $this->postJson('/api/v1/administration/licenses', $body, $headers)->assertCreated()->assertJsonPath('data.record.id', $id);
        $this->assertDatabaseCount('licenses', 1);
        $this->getJson('/api/v1/administration/licenses/'.$id, $headers)->assertOk()
            ->assertJsonPath('data.license.product_name', \App\Support\ProductCatalog::options()[$product]);
        Mail::assertSent(\App\Mail\LicenseIssued::class, 1);
        $this->assertDatabaseMissing('business_outbox', ['status' => 'uncertain']);
    }

    public function test_public_api_registration_never_accepts_an_administrator_role(): void
    {
        $body = ['account_type' => 'individual', 'name' => 'Fictional API registrant', 'email' => 'fictional-registration@example.test',
            'password' => 'Fictional-test-secret-456!', 'password_confirmation' => 'Fictional-test-secret-456!', 'country' => 'CM', 'role' => 'admin'];
        $headers = ['Idempotency-Key' => 'fictional-registration-00001'];
        $id = $this->postJson('/api/v1/public/accounts', $body, $headers)->assertCreated()->json('data.user_id');
        $this->postJson('/api/v1/public/accounts', $body, $headers)->assertCreated()->assertJsonPath('data.user_id', $id);
        $user = User::findOrFail($id);
        $this->assertTrue($user->hasRole('customer'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertDatabaseCount('users', 1);
    }

    public function test_finance_approval_transitions_share_services_and_replay_once(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['finance:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'finance-expense-000001'];
        $expense = \App\Models\Expense::create([
            'reference' => \App\Models\Expense::generateReference(), 'title' => 'Fictional travel expense',
            'category' => 'travel', 'amount' => 125, 'currency' => 'XAF', 'expense_date' => today(),
            'status' => 'pending', 'submitted_by' => $admin->id,
        ]);

        $approved = $this->postJson('/api/v1/finance/expenses/'.$expense->id.'/approve', [], $headers)->assertOk();
        $this->postJson('/api/v1/finance/expenses/'.$expense->id.'/approve', [], $headers)->assertOk()->assertExactJson($approved->json());
        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'status' => 'approved', 'approved_by' => $admin->id]);

        $headers['Idempotency-Key'] = 'finance-expense-000002';
        $this->postJson('/api/v1/finance/expenses/'.$expense->id.'/mark-paid', [], $headers)->assertOk();
        $this->postJson('/api/v1/finance/expenses/'.$expense->id.'/mark-paid', [], $headers)->assertOk();
        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'status' => 'paid']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.expense_approved.v1', 'actor_id' => $admin->id]);

        $other = \App\Models\Expense::create([
            'reference' => \App\Models\Expense::generateReference(), 'title' => 'Fictional pending expense',
            'category' => 'travel', 'amount' => 50, 'currency' => 'XAF', 'expense_date' => today(),
            'status' => 'pending', 'submitted_by' => $admin->id,
        ]);
        $headers['Idempotency-Key'] = 'finance-expense-000003';
        $this->postJson('/api/v1/finance/expenses/'.$other->id.'/mark-paid', [], $headers)->assertUnprocessable();
        $this->assertSame('pending', $other->fresh()->status);
    }

    public function test_expense_create_and_update_are_idempotent_and_keep_approval_fields_server_owned(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['finance:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'finance-expense-create-0001'];
        $body = [
            'title' => 'Fictional API travel expense', 'category' => 'travel', 'amount' => 275.50,
            'currency' => 'XAF', 'expense_date' => today()->toDateString(), 'submitted_by' => $admin->id,
            'notes' => 'Test-only expense', 'status' => 'paid', 'approved_by' => $admin->id,
        ];

        $this->postJson('/api/v1/finance/expenses', $body, ['Idempotency-Key' => 'finance-expense-anonymous-01'])->assertUnauthorized();
        $created = $this->postJson('/api/v1/finance/expenses', $body, $headers)->assertUnprocessable();
        $this->assertDatabaseCount('expenses', 0);
        unset($body['status'], $body['approved_by']);
        $created = $this->postJson('/api/v1/finance/expenses', $body, $headers)->assertCreated();
        $id = $created->json('data.id');
        $this->postJson('/api/v1/finance/expenses', $body, $headers)->assertCreated()->assertExactJson($created->json());
        $expense = \App\Models\Expense::findOrFail($id);
        $this->assertSame('pending', $expense->status);
        $this->assertSame('00001', substr($expense->reference, -5));
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.expense_created.v1', 'actor_id' => $admin->id]);

        $headers['Idempotency-Key'] = 'finance-expense-update-0001';
        $patch = ['amount' => 300, 'notes' => 'Revised test-only amount'];
        $updated = $this->putJson('/api/v1/finance/expenses/'.$id, $patch, $headers)->assertOk();
        $this->putJson('/api/v1/finance/expenses/'.$id, $patch, $headers)->assertOk()->assertExactJson($updated->json());
        $this->assertDatabaseHas('expenses', ['id' => $id, 'amount' => '300.00', 'status' => 'pending']);

        $headers['Idempotency-Key'] = 'finance-expense-update-0002';
        $this->putJson('/api/v1/finance/expenses/'.$id, ['status' => 'paid'], $headers)->assertUnprocessable();
        $this->assertDatabaseHas('expenses', ['id' => $id, 'status' => 'pending']);
    }

    public function test_payroll_generation_completion_and_payment_are_shared_and_idempotent(): void
    {
        $admin = $this->user('admin');
        $staff = $this->user('support');
        $staff->employeeProfile()->create(['employment_type' => 'full_time', 'salary' => 275000, 'currency' => 'XAF']);
        $inactive = $this->user('support');
        $inactive->update(['is_active' => false]);
        $token = $this->access($admin, ['finance:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'finance-payroll-000001'];
        $run = \App\Models\PayrollRun::create([
            'reference' => 'PAY-'.now()->year.'-F001', 'period_start' => today()->startOfMonth(), 'period_end' => today(),
            'status' => 'draft', 'total_gross' => 0, 'total_net' => 0, 'currency' => 'XAF', 'processed_by' => $admin->id,
        ]);

        $generated = $this->postJson('/api/v1/finance/payroll-runs/'.$run->id.'/generate-entries', [], $headers)->assertOk();
        $this->postJson('/api/v1/finance/payroll-runs/'.$run->id.'/generate-entries', [], $headers)->assertOk()->assertExactJson($generated->json());
        $this->assertSame(3, $run->entries()->count());
        $this->assertSame('275000.00', $run->fresh()->entries()->where('user_id', $staff->id)->firstOrFail()->gross_salary);
        $this->assertSame('processing', $run->fresh()->status);
        $this->assertSame(0, $run->entries()->where('user_id', $inactive->id)->count());

        $headers['Idempotency-Key'] = 'finance-payroll-000002';
        $this->postJson('/api/v1/finance/payroll-runs/'.$run->id.'/complete', [], $headers)->assertOk()->assertJsonPath('data.status', 'completed');
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\PayrollProcessed::class, 3);
        $entry = $run->entries()->firstOrFail();
        $headers['Idempotency-Key'] = 'finance-payroll-000003';
        $this->postJson('/api/v1/finance/payroll-runs/'.$run->id.'/entries/'.$entry->id.'/mark-paid', [], $headers)->assertOk();
        $this->assertSame('paid', $entry->fresh()->status);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.payroll_completed.v1', 'actor_id' => $admin->id]);
    }

    public function test_contract_transitions_are_shared_guarded_and_replay_safe(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['contracts:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'contract-activate-00001'];
        $contract = \App\Models\Contract::create([
            'reference' => 'CONT-'.now()->year.'-T001', 'title' => 'Fictional test agreement',
            'type' => 'service_agreement', 'status' => 'draft', 'created_by' => $admin->id,
        ]);

        $activated = $this->postJson('/api/v1/contracts/'.$contract->id.'/activate', [], $headers)->assertOk();
        $this->postJson('/api/v1/contracts/'.$contract->id.'/activate', [], $headers)->assertExactJson($activated->json());
        $this->assertDatabaseHas('contracts', ['id' => $contract->id, 'status' => 'active']);
        $this->assertNotNull($contract->fresh()->signed_at);

        $headers['Idempotency-Key'] = 'contract-terminate-0001';
        $this->postJson('/api/v1/contracts/'.$contract->id.'/terminate', [], $headers)->assertOk();
        $this->assertDatabaseHas('contracts', ['id' => $contract->id, 'status' => 'terminated']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'publishing.contract_activated.v1']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'publishing.contract_terminated.v1']);
    }

    public function test_validation_reviews_and_developer_task_share_idempotent_commands(): void
    {
        $admin = $this->user('admin');
        $assignee = $this->user('support');
        $token = $this->access($admin, ['validation:write']);
        $headers = ['Authorization' => 'Bearer '.$token];
        $issue = \App\Models\IssueReport::factory()->create(['status' => 'submitted']);
        $send = function (string $key, string $path, array $body = []) use (&$headers): \Illuminate\Testing\TestResponse {
            $headers['Idempotency-Key'] = $key;
            return $this->postJson($path, $body, $headers);
        };

        $review = $send('validation-review-0001', '/api/v1/validation/issues/'.$issue->id.'/clinical-review', ['decision' => 'approved_for_product_review', 'notes' => 'Reviewed in test.'])->assertOk();
        $send('validation-review-0001', '/api/v1/validation/issues/'.$issue->id.'/clinical-review', ['decision' => 'approved_for_product_review', 'notes' => 'Reviewed in test.'])->assertExactJson($review->json());
        $send('validation-send-000001', '/api/v1/validation/issues/'.$issue->id.'/send-to-product-review')->assertOk();
        $send('validation-decision-0001', '/api/v1/validation/issues/'.$issue->id.'/product-decision', ['decision' => 'sent_to_development', 'notes' => 'Accepted for test.'])->assertOk();
        $task = \App\Models\DeveloperTask::where('issue_report_id', $issue->id)->firstOrFail();

        $send('validation-assign-00001', '/api/v1/validation/developer-tasks/'.$task->id.'/assign', ['assigned_to' => $assignee->id])->assertOk();
        $send('validation-start-000001', '/api/v1/validation/developer-tasks/'.$task->id.'/start')->assertOk();
        $send('validation-fixed-000001', '/api/v1/validation/developer-tasks/'.$task->id.'/mark-fixed', ['resolution_notes' => 'Fix verified.'])->assertOk();
        $send('validation-reopen-00001', '/api/v1/validation/developer-tasks/'.$task->id.'/reopen')->assertOk();
        $send('validation-wontfix-00001', '/api/v1/validation/developer-tasks/'.$task->id.'/wont-fix', ['notes' => 'Not proceeding in test.'])->assertOk();
        $send('validation-close-000001', '/api/v1/validation/issues/'.$issue->id.'/close')->assertOk();

        $this->assertDatabaseHas('issue_reports', ['id' => $issue->id, 'status' => 'closed']);
        $this->assertDatabaseHas('developer_tasks', ['id' => $task->id, 'status' => 'wont_fix', 'assigned_to' => $assignee->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'validation.issue_clinical_reviewed.v1']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'validation.developer_task_fixed.v1']);
    }

    public function test_recruitment_lifecycle_uses_shared_transition_commands(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['recruitment:write']);
        $headers = ['Authorization' => 'Bearer '.$token];
        $opening = \App\Models\JobOpening::create(['title' => 'Fictional API test role', 'status' => 'open', 'created_by' => $admin->id]);
        $application = \App\Models\JobApplication::create([
            'job_opening_id' => $opening->id, 'applicant_name' => 'Test Applicant', 'email' => 'applicant@example.test',
            'status' => 'received', 'applied_at' => today(),
        ]);
        $send = function (string $key, string $path, array $body = []) use (&$headers): \Illuminate\Testing\TestResponse {
            $headers['Idempotency-Key'] = $key;
            return $this->postJson($path, $body, $headers);
        };
        $base = '/api/v1/recruitment/applications/'.$application->id;
        $send('recruitment-shortlist-01', $base.'/shortlist')->assertOk();
        $send('recruitment-interview-01', $base.'/schedule-interview', ['interview_date' => today()->addDays(5)->toDateString()])->assertOk();
        $send('recruitment-offer-0001', $base.'/make-offer')->assertOk();
        $send('recruitment-hire-00001', $base.'/hire')->assertOk()->assertJsonPath('data.status', 'hired');
        $this->assertDatabaseHas('job_applications', ['id' => $application->id, 'status' => 'hired']);

        $other = \App\Models\JobApplication::create(['job_opening_id' => $opening->id, 'applicant_name' => 'Other Applicant', 'email' => 'other@example.test', 'status' => 'received']);
        $send('recruitment-reject-0001', '/api/v1/recruitment/applications/'.$other->id.'/reject')->assertOk();
        $headers['Idempotency-Key'] = 'recruitment-close-00001';
        $closed = $this->postJson('/api/v1/recruitment/openings/'.$opening->id.'/close', [], $headers)->assertOk();
        $this->postJson('/api/v1/recruitment/openings/'.$opening->id.'/close', [], $headers)->assertExactJson($closed->json());
        $this->assertDatabaseHas('job_openings', ['id' => $opening->id, 'status' => 'closed']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.recruitment_application_hired.v1']);
    }

    public function test_document_and_knowledge_article_publishing_are_shared_transitions(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['documents:write', 'content:write']);
        $headers = ['Authorization' => 'Bearer '.$token];
        $document = \App\Models\Document::create([
            'type' => 'receipt', 'title' => 'Fictional receipt', 'reference_number' => 'DOC-TEST-0001',
            'body_rendered' => 'Test only', 'issued_by' => $admin->id, 'addressee_name' => 'Test recipient', 'status' => 'draft',
        ]);
        $headers['Idempotency-Key'] = 'document-mark-sent-0001';
        $sent = $this->postJson('/api/v1/documents/'.$document->id.'/mark-sent', [], $headers)->assertOk();
        $this->postJson('/api/v1/documents/'.$document->id.'/mark-sent', [], $headers)->assertExactJson($sent->json());
        $headers['Idempotency-Key'] = 'document-void-000001';
        $this->postJson('/api/v1/documents/'.$document->id.'/void', [], $headers)->assertOk();
        $this->assertDatabaseHas('documents', ['id' => $document->id, 'status' => 'voided']);

        $signed = \App\Models\Document::create([
            'type' => 'contract_business', 'title' => 'Signed test contract', 'reference_number' => 'DOC-TEST-0002',
            'body_rendered' => 'Test only', 'issued_by' => $admin->id, 'addressee_name' => 'Test recipient', 'status' => 'signed',
        ]);
        $headers['Idempotency-Key'] = 'document-signed-void-0001';
        $this->postJson('/api/v1/documents/'.$signed->id.'/void', [], $headers)->assertUnprocessable();
        $this->assertSame('signed', $signed->fresh()->status);

        $article = \App\Models\KnowledgeBaseArticle::create([
            'author_id' => $admin->id, 'title' => 'Fictional help article', 'slug' => 'fictional-help-article',
            'content' => 'Test only', 'status' => 'draft',
        ]);
        $headers['Idempotency-Key'] = 'content-publish-000001';
        $this->postJson('/api/v1/content/articles/'.$article->id.'/publish', [], $headers)->assertOk()->assertJsonPath('data.status', 'published');
        $this->assertNotNull($article->fresh()->published_at);
        $this->assertDatabaseHas('business_outbox', ['type' => 'content.knowledge_article_published.v1']);
    }

    public function test_admin_leave_decisions_and_bulk_actions_use_the_shared_service(): void
    {
        $admin = $this->user('admin');
        $employee = $this->user('support');
        $token = $this->access($admin, ['workforce:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'admin-leave-approve-01'];
        $approved = \App\Models\LeaveRequest::create(['user_id' => $employee->id, 'type' => 'annual', 'start_date' => today(), 'end_date' => today()->addDay(), 'total_days' => 2, 'status' => 'pending']);
        $response = $this->postJson('/api/v1/admin/leaves/'.$approved->id.'/approve', [], $headers)->assertOk();
        $this->postJson('/api/v1/admin/leaves/'.$approved->id.'/approve', [], $headers)->assertExactJson($response->json());
        $this->assertDatabaseHas('leave_requests', ['id' => $approved->id, 'status' => 'approved', 'approved_by' => $admin->id]);

        $rejected = \App\Models\LeaveRequest::create(['user_id' => $employee->id, 'type' => 'annual', 'start_date' => today(), 'end_date' => today()->addDay(), 'total_days' => 2, 'status' => 'pending']);
        $headers['Idempotency-Key'] = 'admin-leave-reject-001';
        $this->postJson('/api/v1/admin/leaves/'.$rejected->id.'/reject', [], $headers)->assertOk();
        $bulkA = \App\Models\LeaveRequest::create(['user_id' => $employee->id, 'type' => 'sick', 'start_date' => today(), 'end_date' => today(), 'total_days' => 1, 'status' => 'pending']);
        $bulkB = \App\Models\LeaveRequest::create(['user_id' => $employee->id, 'type' => 'other', 'start_date' => today(), 'end_date' => today(), 'total_days' => 1, 'status' => 'pending']);
        $headers['Idempotency-Key'] = 'admin-leave-bulk-001';
        $this->postJson('/api/v1/admin/leaves/bulk-approve', ['ids' => [$bulkA->id, $bulkB->id]], $headers)->assertOk()->assertJsonPath('data.count', 2);
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.leave_decided.v1']);
    }

    public function test_performance_review_state_changes_are_shared_and_idempotent(): void
    {
        $admin = $this->user('admin');
        $employee = $this->user('support');
        $token = $this->access($admin, ['workforce:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'performance-submit-001'];
        $review = \App\Models\PerformanceReview::create([
            'user_id' => $employee->id, 'reviewer_id' => $admin->id, 'review_period' => 'Test period',
            'review_date' => today(), 'status' => 'draft',
        ]);
        $submitted = $this->postJson('/api/v1/admin/performance-reviews/'.$review->id.'/submit', [], $headers)->assertOk();
        $this->postJson('/api/v1/admin/performance-reviews/'.$review->id.'/submit', [], $headers)->assertExactJson($submitted->json());
        $headers['Idempotency-Key'] = 'performance-acknowledge-01';
        $this->postJson('/api/v1/admin/performance-reviews/'.$review->id.'/acknowledge', [], $headers)->assertOk()->assertJsonPath('data.status', 'acknowledged');
        $this->assertNotNull($review->fresh()->acknowledged_at);
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.performance_review_submitted.v1']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.performance_review_acknowledged.v1']);
    }

    public function test_risk_and_training_transitions_are_shared_idempotent_commands(): void
    {
        $admin = $this->user('admin');
        $employee = $this->user('support');
        $token = $this->access($admin, ['risk:write', 'workforce:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'risk-mitigate-0001'];
        $risk = \App\Models\Risk::create([
            'reference' => 'RISK-TEST-01', 'title' => 'Fictional API test risk', 'category' => 'operational',
            'likelihood' => 'medium', 'impact' => 'medium', 'risk_score' => 9, 'status' => 'open', 'owner_id' => $admin->id,
        ]);
        $this->postJson('/api/v1/risk/'.$risk->id.'/mitigate', [], $headers)->assertOk();
        $this->postJson('/api/v1/risk/'.$risk->id.'/mitigate', [], $headers)->assertOk();
        $this->assertDatabaseHas('risks', ['id' => $risk->id, 'status' => 'mitigated']);

        $training = \App\Models\TrainingRecord::create(['user_id' => $employee->id, 'title' => 'Fictional safety course', 'category' => 'safety', 'status' => 'planned']);
        $headers['Idempotency-Key'] = 'training-complete-0001';
        $this->postJson('/api/v1/training-records/'.$training->id.'/complete', [], $headers)->assertOk()->assertJsonPath('data.status', 'completed');
        $this->postJson('/api/v1/training-records/'.$training->id.'/complete', [], $headers)->assertOk();
        $this->assertNotNull($training->fresh()->completed_at);
        $this->assertDatabaseHas('business_outbox', ['type' => 'risk.mitigation_recorded.v1']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.training_completed.v1']);
    }

    public function test_practitioner_reviews_and_publication_share_api_commands(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['practitioners:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'practitioner-verify-01'];
        $profile = \App\Models\PractitionerProfile::factory()->create();
        $this->postJson('/api/v1/practitioners/profiles/'.$profile->id.'/verify', [], $headers)->assertOk();
        $this->postJson('/api/v1/practitioners/profiles/'.$profile->id.'/verify', [], $headers)->assertOk();
        $this->assertTrue($profile->fresh()->is_verified);

        $finding = \App\Models\PractitionerFinding::factory()->create();
        $headers['Idempotency-Key'] = 'practitioner-publish-001';
        $this->postJson('/api/v1/practitioners/findings/'.$finding->id.'/publish', [], $headers)->assertOk()->assertJsonPath('data.is_published', true);
        $headers['Idempotency-Key'] = 'practitioner-unpublish-01';
        $this->postJson('/api/v1/practitioners/findings/'.$finding->id.'/unpublish', [], $headers)->assertOk()->assertJsonPath('data.is_published', false);

        $suggestion = \App\Models\Suggestion::factory()->create();
        $headers['Idempotency-Key'] = 'practitioner-response-01';
        $this->postJson('/api/v1/practitioners/suggestions/'.$suggestion->id.'/respond', ['status' => 'accepted', 'admin_response' => 'Fictional response for test.'], $headers)->assertOk()->assertJsonPath('data.status', 'accepted');
        $this->assertNotNull($suggestion->fresh()->responded_at);
        $this->assertDatabaseHas('business_outbox', ['type' => 'practitioners.profile_verified.v1']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'practitioners.finding_published.v1']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'practitioners.suggestion_responded.v1']);
    }

    public function test_support_replies_and_service_request_transitions_use_shared_commands(): void
    {
        $admin = $this->user('admin');
        $customer = $this->user('customer');
        $technician = $this->user('support');
        $token = $this->access($admin, ['support:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'support-reply-000001'];

        $bug = \App\Models\PractitionerBugReport::create([
            'practitioner_id' => $customer->id, 'title' => 'Fictional report', 'description' => 'Test only', 'status' => 'open',
        ]);
        $reply = $this->postJson('/api/v1/practitioners/bug-reports/'.$bug->id.'/respond', [
            'status' => 'in_progress', 'admin_response' => 'We reviewed this test report.',
        ], $headers)->assertOk()->assertJsonPath('data.status', 'in_progress');
        $this->postJson('/api/v1/practitioners/bug-reports/'.$bug->id.'/respond', [
            'status' => 'in_progress', 'admin_response' => 'We reviewed this test report.',
        ], $headers)->assertExactJson($reply->json());
        $this->assertSame($admin->id, $bug->fresh()->responded_by);

        $request = \App\Models\ServiceRequest::create([
            'customer_id' => $customer->id, 'type' => 'maintenance', 'preferred_date' => today()->addDays(2), 'status' => 'pending',
        ]);
        $headers['Idempotency-Key'] = 'support-confirm-00001';
        $this->postJson('/api/v1/support/service-requests/'.$request->id.'/confirm-assign', [
            'assigned_technician_id' => $technician->id, 'confirmed_date' => today()->addDays(3)->toDateString(), 'confirmed_time' => '09:30',
        ], $headers)->assertOk()->assertJsonPath('data.status', 'confirmed');
        $this->assertDatabaseHas('service_requests', ['id' => $request->id, 'assigned_technician_id' => $technician->id, 'status' => 'confirmed']);

        $request->update(['status' => 'assigned']);
        $headers['Idempotency-Key'] = 'support-complete-0001';
        $complete = $this->postJson('/api/v1/support/service-requests/'.$request->id.'/complete', [], $headers)->assertOk()->assertJsonPath('data.status', 'completed');
        $this->postJson('/api/v1/support/service-requests/'.$request->id.'/complete', [], $headers)->assertExactJson($complete->json());
        $this->assertDatabaseHas('business_outbox', ['type' => 'support.practitioner_bug_report_responded.v1']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'support.service_request_confirmed.v1']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'support.service_request_completed.v1']);
    }

    public function test_final_evaluation_certificate_issuance_is_an_authorized_idempotent_command(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['validation:write']);
        $evaluation = \App\Models\FinalEvaluation::factory()->create([
            'rating' => 'outstanding',
            'metrics' => ['issues_accepted' => 8, 'sessions' => 10, 'retests' => 4],
        ]);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'validation-certificate-001'];

        $issued = $this->postJson('/api/v1/validation/final-evaluations/'.$evaluation->id.'/issue-certificate', [], $headers)
            ->assertOk()->assertJsonPath('data.tier', 'distinction');
        $this->postJson('/api/v1/validation/final-evaluations/'.$evaluation->id.'/issue-certificate', [], $headers)->assertExactJson($issued->json());
        $this->assertDatabaseCount('validation_certificates', 1);
        $this->assertDatabaseHas('business_outbox', ['kind' => 'notification', 'status' => 'completed']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'validation.certificate_issued.v1']);
    }

    public function test_invoice_template_generation_requires_due_date_and_is_command_idempotent(): void
    {
        $admin = $this->user('admin');
        $customer = $this->user('customer');
        $token = $this->access($admin, ['finance:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'finance-template-000001'];
        $template = \App\Models\InvoiceTemplate::create([
            'name' => 'Fictional monthly support', 'customer_id' => $customer->id, 'frequency' => 'monthly',
            'next_due_date' => today(), 'payment_terms_days' => 30, 'currency' => 'XAF', 'tax_rate' => 0,
            'line_items' => [['description' => 'Fictional monthly support', 'quantity' => 1, 'unit_price' => 1000]],
            'is_active' => true, 'issued_by' => $admin->id,
        ]);

        $first = $this->postJson('/api/v1/finance/invoice-templates/'.$template->id.'/generate-invoice', [], $headers)->assertCreated();
        $invoiceId = $first->json('data.id');
        $this->postJson('/api/v1/finance/invoice-templates/'.$template->id.'/generate-invoice', [], $headers)->assertCreated()->assertExactJson($first->json());
        $this->assertDatabaseCount('invoices', 1);
        $this->assertDatabaseHas('invoices', ['id' => $invoiceId, 'customer_id' => $customer->id]);

        $headers['Idempotency-Key'] = 'finance-template-000002';
        $this->postJson('/api/v1/finance/invoice-templates/'.$template->id.'/generate-invoice', [], $headers)->assertUnprocessable();
        $this->assertDatabaseCount('invoices', 1);
    }
}
