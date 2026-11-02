<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApiIdentityTest extends TestCase
{
    use RefreshDatabase;
    private static array $keys;
    private User $owner;
    private array $files = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        if (!isset(self::$keys)) {
            $options = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
            if (PHP_OS_FAMILY === 'Windows') $options['config'] = dirname(PHP_BINARY).'/extras/ssl/openssl.cnf';
            $key = openssl_pkey_new($options);
            openssl_pkey_export($key, $private, null, $options);
            self::$keys = ['private' => $private, 'public' => openssl_pkey_get_details($key)['key']];
        }
        config(['passport.private_key' => self::$keys['private'], 'passport.public_key' => self::$keys['public']]);
        Notification::fake();
        Role::findOrCreate('admin', 'web');
        $this->owner = User::factory()->create();
        $this->owner->assignRole('admin');
    }

    protected function tearDown(): void
    {
        foreach ($this->files as $file) if (is_file($file)) unlink($file);
        parent::tearDown();
    }

    private function client(array $scopes = ['catalogue:read', 'integration:read']): Client
    {
        $client = app(ClientRepository::class)->createClientCredentialsGrantClient('Fictional integration');
        $client->owner()->associate($this->owner);
        $client->scopes = $scopes;
        $client->save();
        return $client;
    }

    private function token(Client $client, string $scope = 'catalogue:read integration:read', ?string $secret = null)
    {
        return $this->post('/api/v1/oauth/token', ['grant_type' => 'client_credentials', 'client_id' => $client->id, 'client_secret' => $secret ?? $client->plainSecret, 'scope' => $scope], ['Accept' => 'application/json']);
    }

    public function test_real_machine_token_reads_only_explicit_catalogue_fields(): void
    {
        $client = $this->client();
        $issued = $this->token($client)->assertOk()->assertJsonPath('token_type', 'Bearer');
        $this->assertGreaterThan(0, $issued->json('expires_in'));
        $this->assertLessThanOrEqual(900, $issued->json('expires_in'));
        $token = $issued->json('access_token');
        $this->getJson('/api/v1/catalogue/products?per_page=2', ['Authorization' => 'Bearer '.$token])->assertOk()
            ->assertJsonCount(2, 'data')->assertJsonStructure(['data' => [['slug', 'name', 'category', 'url']], 'meta' => ['page', 'per_page', 'total']]);
        $response = $this->getJson('/api/v1/integration', ['Authorization' => 'Bearer '.$token])->assertOk()->assertJsonPath('data.client_id', $client->id);
        $response->assertDontSee('client_secret')->assertDontSee($this->owner->email);
        $this->assertGreaterThanOrEqual(2, $response->json('data.requests_today'));
    }

    public function test_bad_credentials_and_unapproved_clients_cannot_mint_tokens(): void
    {
        $client = $this->client();
        $this->token($client, secret: 'incorrect')->assertStatus(401)->assertJsonPath('error', 'invalid_client');
        $client->owner()->dissociate(); $client->save();
        $this->token($client)->assertStatus(401);
    }

    public function test_scope_grant_and_resource_scope_are_both_enforced(): void
    {
        $client = $this->client(['integration:read']);
        $this->token($client, 'catalogue:read')->assertStatus(400)->assertJsonPath('error', 'invalid_scope');
        $token = $this->token($client, 'integration:read')->assertOk()->json('access_token');
        $this->getJson('/api/v1/catalogue/products', ['Authorization' => 'Bearer '.$token])->assertForbidden();
        $this->getJson('/api/v1/integration', ['Authorization' => 'Bearer '.$token])->assertOk();
    }

    public function test_missing_and_tampered_tokens_fail_without_redirects(): void
    {
        $this->get('/api/v1/integration')->assertStatus(401)->assertHeader('WWW-Authenticate', 'Bearer');
        $this->getJson('/api/v1/integration', ['Authorization' => 'Bearer invalid'])->assertStatus(401);
        $token = $this->token($this->client())->assertOk()->json('access_token');
        $this->getJson('/api/v1/integration', ['Authorization' => 'Bearer '.substr($token, 0, -8).'AAAAAAAA'])->assertStatus(401);
    }

    public function test_revocation_and_owner_deactivation_reject_existing_tokens(): void
    {
        $client = $this->client();
        $token = $this->token($client)->assertOk()->json('access_token');
        $this->owner->update(['is_active' => false]);
        $this->getJson('/api/v1/integration', ['Authorization' => 'Bearer '.$token])->assertStatus(401);
        $this->owner->update(['is_active' => true]);
        $this->artisan('api:client', ['action' => 'revoke', '--client' => $client->id])->assertSuccessful();
        $this->getJson('/api/v1/integration', ['Authorization' => 'Bearer '.$token])->assertStatus(401);
        $this->token($client)->assertStatus(401);
    }

    public function test_token_expiry_and_scope_removal_reject_existing_access(): void
    {
        $client = $this->client();
        $token = $this->token($client)->assertOk()->json('access_token');
        $client->update(['scopes' => ['catalogue:read']]);
        $this->getJson('/api/v1/integration', ['Authorization' => 'Bearer '.$token])->assertForbidden();
        Passport::token()->newQuery()->update(['expires_at' => now()->subMinute()]);
        $this->getJson('/api/v1/catalogue/products', ['Authorization' => 'Bearer '.$token])->assertStatus(401);
    }

    public function test_machine_endpoint_rejects_user_grants_and_unknown_resources(): void
    {
        foreach (['password', 'authorization_code', 'refresh_token', 'implicit'] as $grant) {
            $this->post('/api/v1/oauth/token', ['grant_type' => $grant])->assertStatus(400)->assertJsonPath('error', 'unsupported_grant_type');
        }
        $this->get('/oauth/authorize')->assertRedirect();
        $this->getJson('/api/v1/users')->assertNotFound();
        $this->getJson('/api/v1/enquiries')->assertStatus(405);
    }

    public function test_provision_and_rotate_hash_secrets_and_hide_them_from_output(): void
    {
        Artisan::call('api:client', ['action' => 'create', '--owner' => $this->owner->email, '--name' => 'Fictional client', '--scope' => ['catalogue:read', 'integration:read']]);
        $result = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);
        $this->files[] = $result['credential_file'];
        $credentials = json_decode(file_get_contents($result['credential_file']), true);
        $this->assertStringNotContainsString($credentials['client_secret'], Artisan::output());
        $client = Passport::client()->newQuery()->findOrFail($result['client_id']);
        $this->assertTrue(Hash::check($credentials['client_secret'], $client->secret));
        $oldToken = $this->token($client, secret: $credentials['client_secret'])->assertOk()->json('access_token');
        Artisan::call('api:client', ['action' => 'rotate', '--client' => $client->id]);
        $rotated = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);
        $this->files[] = $rotated['credential_file'];
        $newCredentials = json_decode(file_get_contents($rotated['credential_file']), true);
        $this->getJson('/api/v1/integration', ['Authorization' => 'Bearer '.$oldToken])->assertStatus(401);
        $this->token($client, secret: $credentials['client_secret'])->assertStatus(401);
        $this->token($client, secret: $newCredentials['client_secret'])->assertOk();
    }

    public function test_machine_secret_rotation_rejects_public_pkce_clients(): void
    {
        $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient(
            'Browser integration', ['https://consumer.example/callback'], false, $this->owner,
        );

        $this->artisan('api:client', ['action' => 'rotate', '--client' => $client->id])
            ->expectsOutput('Only confidential machine clients can be rotated')
            ->assertFailed();

        $this->assertSame([], glob(storage_path('app/private/api-credentials/*.json')) ?: []);
        $this->assertFalse($client->fresh()->revoked);
    }

    public function test_client_identity_cannot_be_overridden_and_quota_is_per_client(): void
    {
        $a = $this->client(); $b = $this->client();
        $tokenA = $this->token($a)->assertOk()->json('access_token');
        $tokenB = $this->token($b)->assertOk()->json('access_token');
        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/v1/integration?client_id='.$b->id, ['Authorization' => 'Bearer '.$tokenA])->assertOk()->assertJsonPath('data.client_id', $a->id);
        }
        $this->getJson('/api/v1/integration', ['Authorization' => 'Bearer '.$tokenA])->assertStatus(429)->assertHeader('Retry-After');
        $this->getJson('/api/v1/integration', ['Authorization' => 'Bearer '.$tokenB])->assertOk()->assertJsonPath('data.client_id', $b->id);
    }

    public function test_provisioning_requires_active_admin_and_supported_explicit_scopes(): void
    {
        $this->artisan('api:client', ['action' => 'create', '--owner' => $this->owner->email, '--name' => 'Example', '--scope' => ['*']])->assertFailed();
        $this->owner->removeRole('admin');
        $this->artisan('api:client', ['action' => 'create', '--owner' => $this->owner->email, '--name' => 'Example', '--scope' => ['catalogue:read']])->assertFailed();
        $this->assertDatabaseCount('oauth_clients', 0);
    }

    public function test_developer_pages_are_bilingual_and_link_the_public_contract(): void
    {
        $this->get('/en/developers')->assertOk()->assertSee('Build with OPES APIs')->assertSee('/api/v1/openapi.json')
            ->assertSee('/downloads/opes-health-api-client-0.96.0.tgz')->assertSee('SHA-256 checksum');
        $this->get('/fr/developers')->assertOk()->assertSee('Intégrez les API OPES');
    }
}

