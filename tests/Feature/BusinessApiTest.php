<?php

namespace Tests\Feature;

use App\Models\{User, Ticket, EmailTemplate};
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

    public function test_email_template_api_shares_validated_workflows_with_admin_and_requires_scopes(): void
    {
        $this->getJson('/api/v1/communications/email-templates')->assertUnauthorized();

        $admin = $this->user('admin');
        $token = $this->access($admin, ['communications:read', 'communications:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'email-template-create-0001'];
        $body = ['name' => 'Fictional welcome template', 'slug' => 'fictional-welcome-template', 'type' => 'welcome',
            'subject' => 'Welcome {{first_name}}', 'body' => 'Hello {{first_name}}', 'variables' => ['first_name'], 'is_active' => true];

        $created = $this->postJson('/api/v1/communications/email-templates', $body, $headers)->assertCreated();
        $id = $created->json('data.record.id');
        $this->postJson('/api/v1/communications/email-templates', $body, $headers)->assertCreated()->assertJsonPath('data.record.id', $id);
        $this->postJson('/api/v1/communications/email-templates', $body, [
            'Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'email-template-duplicate-slug-1',
        ])->assertUnprocessable();
        $this->assertDatabaseCount('email_templates', 1);
        $this->getJson('/api/v1/communications/email-templates', ['Authorization' => 'Bearer '.$token])
            ->assertOk()->assertJsonPath('data.items.0.id', $id);

        $this->putJson('/api/v1/communications/email-templates/'.$id, ['subject' => 'Welcome {{undeclared}}'], [
            'Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'email-template-invalid-0001',
        ])->assertUnprocessable();

        // The Filament adapter dispatches through the same command registry as the public API.
        $this->actingAs($admin);
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.communications.emailTemplates.update',
            ['subject' => 'Welcome {{first_name}} to OPES'], ['template' => $id]);
        $this->getJson('/api/v1/communications/email-templates/'.$id, ['Authorization' => 'Bearer '.$token])
            ->assertOk()->assertJsonPath('data.email_template.subject', 'Welcome {{first_name}} to OPES');

        $this->deleteJson('/api/v1/communications/email-templates/'.$id, [], [
            'Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'email-template-delete-active-1',
        ])->assertUnprocessable();

        $this->putJson('/api/v1/communications/email-templates/'.$id, ['is_active' => false], [
            'Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'email-template-deactivate-0001',
        ])->assertOk();
        $deleted = $this->deleteJson('/api/v1/communications/email-templates/'.$id, [], [
            'Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'email-template-delete-inactive-1',
        ]);
        $deleted->assertOk();
        $this->assertDatabaseMissing('email_templates', ['id' => $id]);

        $customer = $this->user('customer');
        $customerToken = $this->access($customer, ['customer:read']);
        $this->getJson('/api/v1/communications/email-templates', ['Authorization' => 'Bearer '.$customerToken])->assertForbidden();
    }

    public function test_blog_post_api_sanitizes_content_and_shares_publish_lifecycle_with_filament(): void
    {
        $this->getJson('/api/v1/publishing/blog-posts')->assertUnauthorized();
        $admin = $this->user('admin');
        $token = $this->access($admin, ['publishing:read', 'publishing:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'blog-post-create-00001'];
        $body = ['title' => 'Fictional API article', 'slug' => 'fictional-api-article', 'title_fr' => 'Article API fictif',
            'excerpt' => 'A fictional excerpt', 'body' => '<p>Fictional <strong>article</strong> body.</p><script>alert(1)</script><a href="javascript:alert(2)">unsafe link</a>',
            'category' => 'Fictional category', 'author' => 'OPES Health Systems', 'published' => true];

        $created = $this->postJson('/api/v1/publishing/blog-posts', $body, $headers)->assertCreated();
        $id = $created->json('data.record.id');
        $this->postJson('/api/v1/publishing/blog-posts', $body, $headers)->assertCreated()->assertJsonPath('data.record.id', $id);
        $this->postJson('/api/v1/publishing/blog-posts', $body, [
            'Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'blog-post-duplicate-slug-01',
        ])->assertUnprocessable();
        $post = \App\Models\BlogPost::findOrFail($id);
        $this->assertStringNotContainsString('<script', strtolower($post->body));
        $this->assertStringNotContainsString('javascript:', strtolower($post->body));
        $this->assertSame(1, $post->reading_time);
        $this->getJson('/api/v1/public/blog/fictional-api-article')->assertOk()
            ->assertJsonPath('data.post.slug', 'fictional-api-article');

        $this->deleteJson('/api/v1/publishing/blog-posts/'.$id, [], [
            'Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'blog-post-delete-published-1',
        ])->assertUnprocessable();

        $this->actingAs($admin);
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.publishing.blogPosts.update',
            ['published' => false, 'body' => '<p>Updated <em>through Filament workflow</em>.</p><script>bad()</script>'], ['blogPost' => $id]);
        $this->assertDatabaseHas('blog_posts', ['id' => $id, 'published' => false, 'reading_time' => 1]);
        $this->getJson('/api/v1/public/blog/fictional-api-article')->assertNotFound();

        $this->deleteJson('/api/v1/publishing/blog-posts/'.$id, [], [
            'Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'blog-post-delete-draft-0001',
        ])->assertOk();
        $this->assertDatabaseMissing('blog_posts', ['id' => $id]);
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

    public function test_credit_note_lifecycle_is_api_backed_and_only_accepts_server_owned_financial_totals(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['finance:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'credit-note-create-0001'];
        $body = [
            'reason' => 'Fictional test correction', 'currency' => 'XAF', 'tax_amount' => 10,
            'items' => [['description' => 'Test service adjustment', 'quantity' => 2, 'unit_price' => 50]],
            'reference' => 'spoofed', 'status' => 'issued', 'total' => 1, 'created_by' => 999,
        ];
        $this->postJson('/api/v1/finance/credit-notes', $body, $headers)->assertUnprocessable();
        unset($body['reference'], $body['status'], $body['total'], $body['created_by']);

        $created = $this->postJson('/api/v1/finance/credit-notes', $body, $headers)->assertCreated()
            ->assertJsonPath('data.status', 'draft')->assertJsonPath('data.total', '110.00');
        $this->postJson('/api/v1/finance/credit-notes', $body, $headers)->assertExactJson($created->json());
        $creditNote = \App\Models\CreditNote::query()->findOrFail($created->json('data.id'));
        $this->assertSame($admin->id, $creditNote->created_by);
        $this->assertDatabaseCount('credit_note_items', 1);

        $headers['Idempotency-Key'] = 'credit-note-update-0001';
        $updated = $this->putJson('/api/v1/finance/credit-notes/'.$creditNote->id, [
            'reason' => 'Corrected test reason', 'items' => [['description' => 'Replacement adjustment', 'quantity' => 1, 'unit_price' => 80]], 'tax_amount' => 5,
        ], $headers)->assertOk()->assertJsonPath('data.total', '85.00');
        $this->putJson('/api/v1/finance/credit-notes/'.$creditNote->id, [
            'reason' => 'Corrected test reason', 'items' => [['description' => 'Replacement adjustment', 'quantity' => 1, 'unit_price' => 80]], 'tax_amount' => 5,
        ], $headers)->assertExactJson($updated->json());
        $this->assertDatabaseCount('credit_note_items', 1);

        $headers['Idempotency-Key'] = 'credit-note-delete-0001';
        $this->deleteJson('/api/v1/finance/credit-notes/'.$creditNote->id, [], $headers)->assertOk();
        $this->assertDatabaseMissing('credit_notes', ['id' => $creditNote->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.credit_note_created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.credit_note_updated.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.credit_note_deleted.v1', 'actor_id' => $admin->id]);
        $this->postJson('/api/v1/finance/credit-notes', $body, ['Idempotency-Key' => 'credit-note-anonymous-01'])->assertUnauthorized();
    }

    public function test_quote_lifecycle_is_api_backed_and_keeps_amounts_and_identity_server_owned(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['finance:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'quote-create-000001'];
        $body = [
            'title' => 'Fictional API test quote', 'currency' => 'XAF', 'tax_rate' => 10,
            'items' => [['product_name' => 'Test service', 'quantity' => 2, 'unit_price' => 50]],
            'reference' => 'spoofed', 'status' => 'accepted', 'total' => 1, 'created_by' => 999, 'tax_amount' => 0,
        ];
        $this->postJson('/api/v1/finance/quotes', $body, $headers)->assertUnprocessable();
        unset($body['reference'], $body['status'], $body['total'], $body['created_by'], $body['tax_amount']);
        $created = $this->postJson('/api/v1/finance/quotes', $body, $headers)->assertCreated()
            ->assertJsonPath('data.status', 'draft')->assertJsonPath('data.total', '110.00');
        $this->postJson('/api/v1/finance/quotes', $body, $headers)->assertExactJson($created->json());
        $quote = \App\Models\Quote::query()->findOrFail($created->json('data.id'));
        $this->assertSame($admin->id, $quote->created_by);
        $this->assertDatabaseCount('quote_items', 1);

        $headers['Idempotency-Key'] = 'quote-update-000001';
        $update = ['title' => 'Updated test quote', 'tax_rate' => 5, 'items' => [['product_name' => 'Replacement test service', 'quantity' => 1, 'unit_price' => 80]]];
        $updated = $this->putJson('/api/v1/finance/quotes/'.$quote->id, $update, $headers)->assertOk()->assertJsonPath('data.total', '84.00');
        $this->putJson('/api/v1/finance/quotes/'.$quote->id, $update, $headers)->assertExactJson($updated->json());
        $headers['Idempotency-Key'] = 'quote-delete-000001';
        $this->deleteJson('/api/v1/finance/quotes/'.$quote->id, [], $headers)->assertOk();
        $this->assertDatabaseMissing('quotes', ['id' => $quote->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.quote_created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.quote_updated.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.quote_deleted.v1', 'actor_id' => $admin->id]);
        $this->postJson('/api/v1/finance/quotes', $body, ['Idempotency-Key' => 'quote-anonymous-0001'])->assertUnauthorized();
    }

    public function test_invoice_lifecycle_is_api_backed_with_customer_scope_and_computed_totals(): void
    {
        $admin = $this->user('admin');
        $customer = $this->user('customer');
        $token = $this->access($admin, ['finance:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'invoice-create-000001'];
        $body = [
            'customer_id' => $customer->id, 'currency' => 'XAF', 'tax_rate' => 10,
            'items' => [['description' => 'Fictional test invoice item', 'quantity' => 2, 'unit_price' => 50]],
            'invoice_number' => 'spoofed', 'status' => 'paid', 'issued_by' => 999, 'grand_total' => 1,
        ];
        $this->postJson('/api/v1/finance/invoices', $body, $headers)->assertUnprocessable();
        unset($body['invoice_number'], $body['status'], $body['issued_by'], $body['grand_total']);
        $created = $this->postJson('/api/v1/finance/invoices', $body, $headers)->assertCreated()
            ->assertJsonPath('data.status', 'draft')->assertJsonPath('data.grand_total', 110);
        $this->postJson('/api/v1/finance/invoices', $body, $headers)->assertExactJson($created->json());
        $invoice = \App\Models\Invoice::query()->findOrFail($created->json('data.id'));
        $this->assertSame($admin->id, $invoice->issued_by);
        $this->assertStringStartsWith('INV-'.now()->year.'-', $invoice->invoice_number);
        $this->assertDatabaseCount('invoice_items', 1);

        $headers['Idempotency-Key'] = 'invoice-update-000001';
        $update = ['tax_rate' => 5, 'items' => [['description' => 'Updated test invoice item', 'quantity' => 1, 'unit_price' => 80]]];
        $updated = $this->putJson('/api/v1/finance/invoices/'.$invoice->id, $update, $headers)->assertOk()->assertJsonPath('data.grand_total', 84);
        $this->putJson('/api/v1/finance/invoices/'.$invoice->id, $update, $headers)->assertExactJson($updated->json());
        $headers['Idempotency-Key'] = 'invoice-delete-000001';
        $this->deleteJson('/api/v1/finance/invoices/'.$invoice->id, [], $headers)->assertOk();
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.invoice_created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.invoice_updated.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.invoice_deleted.v1', 'actor_id' => $admin->id]);
        $this->postJson('/api/v1/finance/invoices', $body, ['Idempotency-Key' => 'invoice-anonymous-0001'])->assertUnauthorized();
    }

    public function test_purchase_order_lifecycle_is_api_backed_and_computes_line_totals(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['finance:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'po-create-00000001'];
        $body = [
            'vendor_name' => 'Fictional supplier for test', 'title' => 'Fictional API test order', 'currency' => 'XAF', 'tax_amount' => 10,
            'items' => [['description' => 'Test order line', 'quantity' => 2, 'unit_price' => 50]],
            'reference' => 'spoofed', 'status' => 'approved', 'requested_by' => 999, 'total' => 1,
        ];
        $this->postJson('/api/v1/finance/purchase-orders', $body, $headers)->assertUnprocessable();
        unset($body['reference'], $body['status'], $body['requested_by'], $body['total']);
        $created = $this->postJson('/api/v1/finance/purchase-orders', $body, $headers)->assertCreated()
            ->assertJsonPath('data.status', 'draft')->assertJsonPath('data.total', '110.00');
        $this->postJson('/api/v1/finance/purchase-orders', $body, $headers)->assertExactJson($created->json());
        $order = \App\Models\PurchaseOrder::query()->findOrFail($created->json('data.id'));
        $this->assertSame($admin->id, $order->requested_by);
        $this->assertStringStartsWith('PO-'.now()->year.'-', $order->reference);

        $headers['Idempotency-Key'] = 'po-update-000000001';
        $update = ['title' => 'Updated test order', 'items' => [['description' => 'Updated test line', 'quantity' => 1, 'unit_price' => 80]]];
        $updated = $this->putJson('/api/v1/finance/purchase-orders/'.$order->id, $update, $headers)->assertOk()->assertJsonPath('data.total', '90.00');
        $this->putJson('/api/v1/finance/purchase-orders/'.$order->id, $update, $headers)->assertExactJson($updated->json());
        $headers['Idempotency-Key'] = 'po-delete-000000001';
        $this->deleteJson('/api/v1/finance/purchase-orders/'.$order->id, [], $headers)->assertOk();
        $this->assertDatabaseMissing('purchase_orders', ['id' => $order->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.purchase_order_created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.purchase_order_updated.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.purchase_order_deleted.v1', 'actor_id' => $admin->id]);
    }

    public function test_supplier_bill_lifecycle_is_api_backed_and_restricts_preapproval_changes(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['finance:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'supplier-bill-create-01'];
        $body = [
            'vendor_name' => 'Fictional supplier for test', 'bill_number' => 'TEST-BILL-001', 'issue_date' => today()->toDateString(),
            'currency' => 'XAF', 'tax_amount' => 10,
            'items' => [['description' => 'Fictional test bill item', 'quantity' => 2, 'unit_price' => 50]],
            'reference' => 'spoofed', 'status' => 'paid', 'created_by' => 999, 'total' => 1,
        ];
        $this->postJson('/api/v1/finance/supplier-bills', $body, $headers)->assertUnprocessable();
        unset($body['reference'], $body['status'], $body['created_by'], $body['total']);
        $created = $this->postJson('/api/v1/finance/supplier-bills', $body, $headers)->assertCreated()
            ->assertJsonPath('data.status', 'received')->assertJsonPath('data.total', '110.00');
        $this->postJson('/api/v1/finance/supplier-bills', $body, $headers)->assertExactJson($created->json());
        $bill = \App\Models\SupplierBill::query()->findOrFail($created->json('data.id'));
        $this->assertSame($admin->id, $bill->created_by);
        $this->assertStringStartsWith('BILL-'.now()->year.'-', $bill->reference);

        $headers['Idempotency-Key'] = 'supplier-bill-update-01';
        $update = ['bill_number' => 'TEST-BILL-UPDATED', 'tax_amount' => 5, 'items' => [['description' => 'Updated fictional bill line', 'quantity' => 1, 'unit_price' => 80]]];
        $updated = $this->putJson('/api/v1/finance/supplier-bills/'.$bill->id, $update, $headers)->assertOk()->assertJsonPath('data.total', '85.00');
        $this->putJson('/api/v1/finance/supplier-bills/'.$bill->id, $update, $headers)->assertExactJson($updated->json());
        $headers['Idempotency-Key'] = 'supplier-bill-delete-01';
        $this->deleteJson('/api/v1/finance/supplier-bills/'.$bill->id, [], $headers)->assertOk();
        $this->assertDatabaseMissing('supplier_bills', ['id' => $bill->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.supplier_bill_created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.supplier_bill_updated.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.supplier_bill_deleted.v1', 'actor_id' => $admin->id]);
        $this->postJson('/api/v1/finance/supplier-bills', $body, ['Idempotency-Key' => 'supplier-bill-anon-001'])->assertUnauthorized();
    }

    public function test_admin_user_lifecycle_is_api_backed_and_role_changes_require_super_admin(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['administration:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'identity-user-create-001'];
        $body = ['name' => 'Fictional API test account', 'email' => 'api-user@example.test', 'password' => 'TestOnly-Password-123', 'phone' => '+237600000001'];
        $created = $this->postJson('/api/v1/admin/users', $body, $headers)->assertCreated()->assertJsonPath('data.email', $body['email']);
        $this->postJson('/api/v1/admin/users', $body, $headers)->assertExactJson($created->json());
        $user = User::query()->findOrFail($created->json('data.id'));
        $this->assertNotSame($body['password'], $user->password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check($body['password'], $user->password));
        $this->assertDatabaseHas('business_outbox', ['type' => 'identity.user_created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['kind' => 'mail', 'status' => 'completed']);

        $headers['Idempotency-Key'] = 'identity-user-admin-role-001';
        $this->putJson('/api/v1/admin/users/'.$user->id, ['roles' => ['admin']], $headers)->assertForbidden();
        $headers['Idempotency-Key'] = 'identity-user-update-0001';
        $updated = $this->putJson('/api/v1/admin/users/'.$user->id, ['phone' => '+237600000002'], $headers)->assertOk();
        $this->putJson('/api/v1/admin/users/'.$user->id, ['phone' => '+237600000002'], $headers)->assertExactJson($updated->json());

        $superAdmin = $this->user('admin');
        Role::findOrCreate('super_admin', 'web');
        $superAdmin->syncRoles(['super_admin']);
        $superToken = $this->access($superAdmin, ['administration:write']);
        $superHeaders = ['Authorization' => 'Bearer '.$superToken, 'Idempotency-Key' => 'identity-user-role-00001'];
        $this->putJson('/api/v1/admin/users/'.$user->id, ['roles' => ['support']], $superHeaders)->assertOk();
        $this->assertTrue($user->fresh()->hasRole('support'));

        $superHeaders['Idempotency-Key'] = 'identity-user-delete-0001';
        $this->deleteJson('/api/v1/admin/users/'.$user->id, [], $superHeaders)->assertOk();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->postJson('/api/v1/admin/users', $body, ['Idempotency-Key' => 'identity-user-anon-001'])->assertUnauthorized();
    }

    public function test_asset_lifecycle_is_api_backed_and_server_generates_references(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['operations:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'asset-create-00000001'];
        $body = ['name' => 'Fictional test laptop', 'category' => 'laptop', 'currency' => 'XAF', 'purchase_price' => 1000, 'current_value' => 900, 'reference' => 'spoofed'];
        $this->postJson('/api/v1/operations/assets', $body, $headers)->assertUnprocessable();
        unset($body['reference']);
        $created = $this->postJson('/api/v1/operations/assets', $body, $headers)->assertCreated()->assertJsonPath('data.status', 'active');
        $this->postJson('/api/v1/operations/assets', $body, $headers)->assertExactJson($created->json());
        $asset = \App\Models\Asset::query()->findOrFail($created->json('data.id'));
        $this->assertStringStartsWith('AST-'.now()->year.'-', $asset->reference);

        $headers['Idempotency-Key'] = 'asset-update-00000001';
        $this->putJson('/api/v1/operations/assets/'.$asset->id, ['current_value' => 700, 'status' => 'retired'], $headers)->assertOk()->assertJsonPath('data.status', 'retired');
        $headers['Idempotency-Key'] = 'asset-delete-00000001';
        $this->deleteJson('/api/v1/operations/assets/'.$asset->id, [], $headers)->assertOk();
        $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'operations.asset_created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'operations.asset_updated.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'operations.asset_deleted.v1', 'actor_id' => $admin->id]);
        $this->postJson('/api/v1/operations/assets', $body, ['Idempotency-Key' => 'asset-anonymous-000001'])->assertUnauthorized();
    }

    public function test_deal_lifecycle_is_api_backed_and_closed_deals_are_immutable(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['crm:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'deal-create-00000001'];
        $body = ['title' => 'Fictional test opportunity', 'stage' => 'prospecting', 'value' => 1000, 'currency' => 'XAF', 'probability' => 50, 'owner_id' => $admin->id];
        $created = $this->postJson('/api/v1/crm/deals', $body, $headers)->assertCreated()->assertJsonPath('data.stage', 'prospecting');
        $this->postJson('/api/v1/crm/deals', $body, $headers)->assertExactJson($created->json());
        $deal = \App\Models\Deal::query()->findOrFail($created->json('data.id'));
        $this->assertStringStartsWith('DEAL-'.now()->year.'-', $deal->reference);

        $headers['Idempotency-Key'] = 'deal-update-000000001';
        $this->putJson('/api/v1/crm/deals/'.$deal->id, ['stage' => 'negotiation', 'probability' => 75], $headers)->assertOk()->assertJsonPath('data.stage', 'negotiation');
        $headers['Idempotency-Key'] = 'deal-close-0000000001';
        $this->putJson('/api/v1/crm/deals/'.$deal->id, ['stage' => 'closed_won'], $headers)->assertOk();
        $headers['Idempotency-Key'] = 'deal-edit-closed-0001';
        $this->putJson('/api/v1/crm/deals/'.$deal->id, ['title' => 'Cannot edit closed'], $headers)->assertUnprocessable();
        $headers['Idempotency-Key'] = 'deal-delete-closed-0001';
        $this->deleteJson('/api/v1/crm/deals/'.$deal->id, [], $headers)->assertUnprocessable();
        $this->assertDatabaseHas('business_outbox', ['type' => 'crm.deal_created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'crm.deal_updated.v1', 'actor_id' => $admin->id]);
        $this->postJson('/api/v1/crm/deals', $body, ['Idempotency-Key' => 'deal-anonymous-000001'])->assertUnauthorized();
    }

    public function test_weekly_reviews_create_immutable_metrics_and_update_narrative_through_commands(): void
    {
        $admin = $this->user('admin');
        $cohort = \App\Models\Cohort::factory()->create();
        $token = $this->access($admin, ['validation:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'weekly-review-create-0001'];
        $body = ['cohort_id' => $cohort->id, 'week_start' => '2026-10-26', 'summary' => 'Fictional snapshot summary', 'metrics' => ['sessions' => 999]];
        $this->postJson('/api/v1/validation/weekly-reviews', $body, $headers)->assertUnprocessable();
        unset($body['metrics']);
        $created = $this->postJson('/api/v1/validation/weekly-reviews', $body, $headers)->assertCreated()
            ->assertJsonPath('data.week_end', '2026-11-01')->assertJsonPath('data.metrics.sessions', 0);
        $this->postJson('/api/v1/validation/weekly-reviews', $body, $headers)->assertExactJson($created->json());
        $reviewId = $created->json('data.id');
        $headers['Idempotency-Key'] = 'weekly-review-update-0001';
        $updated = $this->putJson('/api/v1/validation/weekly-reviews/'.$reviewId, ['summary' => 'Updated fictional summary'], $headers)->assertOk();
        $this->putJson('/api/v1/validation/weekly-reviews/'.$reviewId, ['summary' => 'Updated fictional summary'], $headers)->assertExactJson($updated->json());
        $headers['Idempotency-Key'] = 'weekly-review-spoof-0001';
        $this->putJson('/api/v1/validation/weekly-reviews/'.$reviewId, ['metrics' => ['sessions' => 1000]], $headers)->assertUnprocessable();
        $this->assertDatabaseHas('business_outbox', ['type' => 'validation.weekly_review_created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'validation.weekly_review_updated.v1', 'actor_id' => $admin->id]);
    }

    public function test_tester_assignment_lifecycle_is_api_backed_and_notifies_tester_once(): void
    {
        $admin = $this->user('admin');
        Role::findOrCreate('tester', 'web');
        $tester = User::factory()->create(['is_active' => true]);
        $tester->assignRole('tester');
        $token = $this->access($admin, ['testing:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'tester-assignment-create-001'];
        $body = [
            'assigned_to' => $tester->id, 'product_slug' => array_key_first(\App\Support\ProductCatalog::options()),
            'title' => 'Fictional API test assignment', 'description' => 'Test-only assignment instructions', 'due_date' => today()->addDays(7)->toDateString(),
            'assigned_by' => 999, 'status' => 'completed', 'product_name' => 'Spoofed product name',
        ];
        $this->postJson('/api/v1/admin/testing/assignments', $body, $headers)->assertUnprocessable();
        unset($body['assigned_by'], $body['status'], $body['product_name']);
        $created = $this->postJson('/api/v1/admin/testing/assignments', $body, $headers)->assertCreated()->assertJsonPath('data.status', 'pending');
        $this->postJson('/api/v1/admin/testing/assignments', $body, $headers)->assertExactJson($created->json());
        $assignmentId = $created->json('data.id');
        $this->assertDatabaseHas('business_outbox', ['kind' => 'notification', 'status' => 'completed']);

        $headers['Idempotency-Key'] = 'tester-assignment-start-001';
        $this->putJson('/api/v1/admin/testing/assignments/'.$assignmentId, ['status' => 'in_progress'], $headers)->assertOk()->assertJsonPath('data.status', 'in_progress');
        $headers['Idempotency-Key'] = 'tester-assignment-cancel-01';
        $this->putJson('/api/v1/admin/testing/assignments/'.$assignmentId, ['status' => 'cancelled'], $headers)->assertOk()->assertJsonPath('data.status', 'cancelled');
        $headers['Idempotency-Key'] = 'tester-assignment-delete-01';
        $this->deleteJson('/api/v1/admin/testing/assignments/'.$assignmentId, [], $headers)->assertOk();
        $this->assertDatabaseMissing('tester_assignments', ['id' => $assignmentId]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'testing.tester_assignment_created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'testing.tester_assignment_updated.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'testing.tester_assignment_deleted.v1', 'actor_id' => $admin->id]);
        $this->postJson('/api/v1/admin/testing/assignments', $body, ['Idempotency-Key' => 'tester-assignment-anon-01'])->assertUnauthorized();
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

    public function test_risk_create_and_update_are_shared_idempotent_commands(): void
    {
        $admin = $this->user('admin');
        $token = $this->access($admin, ['risk:write']);
        $headers = ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'risk-create-000001'];
        $body = [
            'title' => 'Fictional API test risk', 'description' => 'Test-only risk description', 'category' => 'operational',
            'owner_id' => $admin->id, 'likelihood' => 'medium', 'impact' => 'high', 'mitigation_plan' => 'Test-only mitigation',
            'reference' => 'spoofed-reference', 'status' => 'mitigated', 'risk_score' => 1,
        ];
        $this->postJson('/api/v1/risk', $body, $headers)->assertUnprocessable();

        unset($body['reference'], $body['status'], $body['risk_score']);
        $created = $this->postJson('/api/v1/risk', $body, $headers)->assertCreated()->assertJsonPath('data.status', 'open');
        $this->postJson('/api/v1/risk', $body, $headers)->assertExactJson($created->json());
        $riskId = $created->json('data.id');
        $this->assertSame(12, $created->json('data.risk_score'));
        $risk = \App\Models\Risk::query()->findOrFail($riskId);
        $this->assertStringStartsWith('RISK-'.now()->year.'-', $risk->reference);

        $headers['Idempotency-Key'] = 'risk-update-000001';
        $update = ['likelihood' => 'high', 'impact' => 'high'];
        $updated = $this->putJson('/api/v1/risk/'.$riskId, $update, $headers)->assertOk()->assertJsonPath('data.risk_score', 16);
        $this->putJson('/api/v1/risk/'.$riskId, $update, $headers)->assertExactJson($updated->json());
        $this->putJson('/api/v1/risk/'.$riskId, ['status' => 'mitigated'], ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => 'risk-update-spoof-0001'])->assertUnprocessable();
        $this->assertDatabaseHas('business_outbox', ['type' => 'risk.created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'risk.updated.v1', 'actor_id' => $admin->id]);
        $this->postJson('/api/v1/risk', $body, ['Idempotency-Key' => 'risk-anonymous-0001'])->assertUnauthorized();
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

    public function test_contract_and_payroll_run_lifecycles_use_api_commands(): void
    {
        $admin = $this->user('admin');
        $headers = ['Authorization' => 'Bearer '.$this->access($admin, ['contracts:write', 'finance:write']), 'Idempotency-Key' => 'contract-payroll-create-0001'];
        $this->postJson('/api/v1/contracts', [], ['Idempotency-Key' => 'contract-anonymous-0001'])->assertUnauthorized();
        $contractResponse = $this->postJson('/api/v1/contracts', ['title' => 'Fictional service agreement', 'type' => 'service_agreement', 'currency' => 'XAF', 'auto_renew' => false, 'value' => 1500], $headers)->assertCreated();
        $contract = \App\Models\Contract::query()->findOrFail($contractResponse->json('data.id'));
        $this->assertSame($admin->id, $contract->created_by);
        $headers['Idempotency-Key'] = 'contract-payroll-update-0001';
        $this->putJson('/api/v1/contracts/'.$contract->id, ['title' => 'Fictional updated draft'], $headers)->assertOk();
        $headers['Idempotency-Key'] = 'contract-payroll-delete-0001';
        $this->deleteJson('/api/v1/contracts/'.$contract->id, [], $headers)->assertOk();

        $headers['Idempotency-Key'] = 'payroll-run-create-000001';
        $runResponse = $this->postJson('/api/v1/finance/payroll-runs', ['period_start' => '2026-10-01', 'period_end' => '2026-10-31', 'currency' => 'XAF'], $headers)->assertCreated();
        $run = \App\Models\PayrollRun::query()->findOrFail($runResponse->json('data.id'));
        $this->assertSame($admin->id, $run->processed_by);
        $headers['Idempotency-Key'] = 'payroll-run-update-000001';
        $this->putJson('/api/v1/finance/payroll-runs/'.$run->id, ['notes' => 'Fictional test notes'], $headers)->assertOk();
        $headers['Idempotency-Key'] = 'payroll-run-delete-000001';
        $this->deleteJson('/api/v1/finance/payroll-runs/'.$run->id, [], $headers)->assertOk();
        $this->assertDatabaseMissing('payroll_runs', ['id' => $run->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'publishing.contract_created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.payroll_run_created.v1', 'actor_id' => $admin->id]);
    }

    public function test_document_lifecycle_renders_from_active_template_and_protects_issued_documents(): void
    {
        $admin = $this->user('admin');
        $headers = ['Authorization' => 'Bearer '.$this->access($admin, ['documents:write']), 'Idempotency-Key' => 'document-create-000001'];
        $template = \App\Models\DocumentTemplate::create(['name' => 'Fictional welcome note', 'type' => 'letterhead', 'body' => 'Welcome {{name}}', 'variables' => ['name'], 'is_active' => true]);
        $body = ['document_template_id' => $template->id, 'title' => 'Fictional welcome note', 'addressee_name' => 'Example Recipient', 'requires_signature' => false, 'variable_values' => ['name' => 'Example Recipient'], 'status' => 'signed', 'issued_by' => 99];
        $this->postJson('/api/v1/documents', $body, $headers)->assertUnprocessable();
        unset($body['status'], $body['issued_by']);
        $created = $this->postJson('/api/v1/documents', $body, $headers)->assertCreated()->assertJsonPath('data.status', 'draft');
        $id = $created->json('data.id');
        $document = \App\Models\Document::query()->findOrFail($id);
        $this->assertSame('Welcome Example Recipient', $document->body_rendered);
        $headers['Idempotency-Key'] = 'document-update-000001';
        $this->putJson('/api/v1/documents/'.$id, ['title' => 'Fictional updated title'], $headers)->assertOk();
        $headers['Idempotency-Key'] = 'document-delete-000001';
        $this->deleteJson('/api/v1/documents/'.$id, [], $headers)->assertOk();
        $this->assertDatabaseMissing('documents', ['id' => $id]);
        $headers['Idempotency-Key'] = 'document-signature-0001';
        $body['requires_signature'] = true;
        $signed = $this->postJson('/api/v1/documents', $body, $headers)->assertCreated()->assertJsonPath('data.status', 'pending_signature');
        $signedDocument = \App\Models\Document::query()->findOrFail($signed->json('data.id'));
        $this->assertNotEmpty($signedDocument->signature_token);
        $headers['Idempotency-Key'] = 'document-protected-delete-0001';
        $this->deleteJson('/api/v1/documents/'.$signedDocument->id, [], $headers)->assertUnprocessable();
        $headers['Idempotency-Key'] = 'document-void-000001';
        $this->postJson('/api/v1/documents/'.$signedDocument->id.'/void', [], $headers)->assertOk()->assertJsonPath('data.status', 'voided');
        $this->assertDatabaseHas('business_outbox', ['type' => 'documents.document_created.v1', 'actor_id' => $admin->id]);
        $this->postJson('/api/v1/documents', $body, ['Idempotency-Key' => 'document-anon-000001'])->assertUnauthorized();
    }

    public function test_document_template_product_api_is_paginated_and_protects_referenced_templates(): void
    {
        $admin = $this->user('admin');
        $headers = ['Authorization' => 'Bearer '.$this->access($admin, ['documents:read', 'documents:write']), 'Idempotency-Key' => 'template-create-000001'];
        $this->postJson('/api/v1/document-templates', ['name' => 'Fictional template', 'type' => 'receipt', 'body' => 'Hello {{client_name}}', 'variables' => [], 'is_active' => true], $headers)->assertUnprocessable();
        $created = $this->postJson('/api/v1/document-templates', ['name' => 'Fictional template', 'type' => 'receipt', 'body' => 'Hello {{client_name}}', 'variables' => ['client_name'], 'is_active' => true], $headers)->assertCreated();
        $templateId = $created->json('data.id');
        $this->getJson('/api/v1/document-templates?per_page=5', $headers)->assertOk()->assertJsonPath('data.pagination.per_page', 5)
            ->assertJsonPath('data.items.0.name', 'Fictional template');
        $this->getJson('/api/v1/document-templates/'.$templateId, $headers)->assertOk()->assertJsonPath('data.template.variables.0', 'client_name');
        $headers['Idempotency-Key'] = 'template-update-000001';
        $this->putJson('/api/v1/document-templates/'.$templateId, ['name' => 'Updated fictional template'], $headers)->assertOk();
        $headers['Idempotency-Key'] = 'template-document-00001';
        $doc = $this->postJson('/api/v1/documents', ['document_template_id' => $templateId, 'title' => 'Fictional receipt', 'addressee_name' => 'Example Client', 'requires_signature' => false, 'variable_values' => ['client_name' => 'Example Client']], $headers)->assertCreated();
        $headers['Idempotency-Key'] = 'template-type-change-001';
        $this->putJson('/api/v1/document-templates/'.$templateId, ['type' => 'letterhead'], $headers)->assertUnprocessable();
        $headers['Idempotency-Key'] = 'template-delete-000001';
        $this->deleteJson('/api/v1/document-templates/'.$templateId, [], $headers)->assertUnprocessable();
        $this->assertDatabaseHas('business_outbox', ['type' => 'documents.template_created.v1', 'actor_id' => $admin->id]);
        $this->postJson('/api/v1/document-templates', [], ['Idempotency-Key' => 'template-anon-000001'])->assertUnauthorized();
    }

    public function test_workforce_department_and_leave_type_apis_enforce_hierarchy_and_history_rules(): void
    {
        $admin = $this->user('admin');
        $headers = ['Authorization' => 'Bearer '.$this->access($admin, ['workforce:read', 'workforce:write']), 'Idempotency-Key' => 'department-create-0001'];
        $this->postJson('/api/v1/departments', [], ['Idempotency-Key' => 'department-anon-0001'])->assertUnauthorized();
        $parent = $this->postJson('/api/v1/departments', ['name' => 'Fictional Operations', 'code' => 'FOP', 'is_active' => true], $headers)->assertCreated();
        $parentId = $parent->json('data.id');
        $headers['Idempotency-Key'] = 'department-child-0001';
        $child = $this->postJson('/api/v1/departments', ['name' => 'Fictional Digital Unit', 'code' => 'FDU', 'parent_id' => $parentId, 'is_active' => true], $headers)->assertCreated();
        $childId = $child->json('data.id');
        $headers['Idempotency-Key'] = 'department-cycle-0001';
        $this->putJson('/api/v1/departments/'.$parentId, ['parent_id' => $childId], $headers)->assertUnprocessable();
        $headers['Idempotency-Key'] = 'department-parent-delete';
        $this->deleteJson('/api/v1/departments/'.$parentId, [], $headers)->assertUnprocessable();
        $headers['Idempotency-Key'] = 'department-child-delete';
        $this->deleteJson('/api/v1/departments/'.$childId, [], $headers)->assertOk();
        $headers['Idempotency-Key'] = 'department-root-delete';
        $this->deleteJson('/api/v1/departments/'.$parentId, [], $headers)->assertOk();

        $headers['Idempotency-Key'] = 'leave-type-create-0001';
        $leaveType = $this->postJson('/api/v1/leave-types', ['name' => 'Fictional wellness leave', 'code' => 'FWL', 'days_per_year' => 5, 'max_carry_forward' => 2, 'color' => '#123ABC', 'is_paid' => true, 'requires_approval' => true, 'is_active' => true], $headers)->assertCreated();
        $leaveTypeId = $leaveType->json('data.id');
        $this->getJson('/api/v1/leave-types?per_page=10', $headers)->assertOk()->assertJsonPath('data.items.0.code', 'FWL');
        $headers['Idempotency-Key'] = 'leave-type-history';
        \Illuminate\Support\Facades\DB::table('leave_requests')->insert(['user_id' => $admin->id, 'leave_type_id' => $leaveTypeId, 'type' => 'annual', 'start_date' => '2026-11-02', 'end_date' => '2026-11-03', 'total_days' => 2, 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()]);
        $this->deleteJson('/api/v1/leave-types/'.$leaveTypeId, [], $headers)->assertUnprocessable();
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.department_created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.leave_type_created.v1', 'actor_id' => $admin->id]);
    }

    public function test_salary_grade_and_payroll_deduction_apis_validate_ranges_and_usage(): void
    {
        $admin = $this->user('admin');
        $headers = ['Authorization' => 'Bearer '.$this->access($admin, ['workforce:read', 'workforce:write', 'finance:read', 'finance:write']), 'Idempotency-Key' => 'salary-grade-create-0001'];
        $grade = $this->postJson('/api/v1/salary-grades', ['name' => 'Fictional Grade One', 'code' => 'FG1', 'min_salary' => 200000, 'max_salary' => 400000, 'currency' => 'XAF', 'is_active' => true], $headers)->assertCreated();
        $gradeId = $grade->json('data.id');
        $this->getJson('/api/v1/salary-grades?per_page=5', $headers)->assertOk()->assertJsonPath('data.items.0.code', 'FG1');
        $headers['Idempotency-Key'] = 'salary-grade-range-0001';
        $this->putJson('/api/v1/salary-grades/'.$gradeId, ['max_salary' => 100000], $headers)->assertUnprocessable();
        $employee = $this->user('support');
        $employee->update(['salary_grade_id' => $gradeId]);
        $headers['Idempotency-Key'] = 'salary-grade-delete-0001';
        $this->deleteJson('/api/v1/salary-grades/'.$gradeId, [], $headers)->assertUnprocessable();

        $headers['Idempotency-Key'] = 'deduction-create-000001';
        $bad = ['name' => 'Fictional pension', 'code' => 'FPEN', 'calculation_type' => 'percentage', 'rate' => 150, 'apply_by_default' => true, 'is_active' => true, 'sort_order' => 1];
        $this->postJson('/api/v1/finance/payroll-deduction-types', $bad, $headers)->assertUnprocessable();
        $bad['rate'] = 5;
        $deduction = $this->postJson('/api/v1/finance/payroll-deduction-types', $bad, $headers)->assertCreated();
        $deductionId = $deduction->json('data.id');
        $this->getJson('/api/v1/finance/payroll-deduction-types?per_page=10', $headers)->assertOk()->assertJsonPath('data.items.0.code', 'FPEN');
        $headers['Idempotency-Key'] = 'deduction-deactivate-01';
        $this->putJson('/api/v1/finance/payroll-deduction-types/'.$deductionId, ['is_active' => false, 'apply_by_default' => false], $headers)->assertOk();
        $headers['Idempotency-Key'] = 'deduction-delete-00001';
        $this->deleteJson('/api/v1/finance/payroll-deduction-types/'.$deductionId, [], $headers)->assertOk();
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.salary_grade_created.v1', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'finance.payroll_deduction_type_created.v1', 'actor_id' => $admin->id]);
    }
}
