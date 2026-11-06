<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DeveloperOAuthApplicationsApiTest extends TestCase
{
    use RefreshDatabase;

    private static array $keys;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        foreach (['admin', 'customer', 'practitioner'] as $role) Role::findOrCreate($role, 'web');
        if (!isset(self::$keys)) {
            $options = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
            if (PHP_OS_FAMILY === 'Windows') $options['config'] = dirname(PHP_BINARY).'/extras/ssl/openssl.cnf';
            $key = openssl_pkey_new($options);
            openssl_pkey_export($key, $private, null, $options);
            self::$keys = ['private' => $private, 'public' => openssl_pkey_get_details($key)['key']];
        }
        config(['passport.private_key' => self::$keys['private'], 'passport.public_key' => self::$keys['public']]);
    }

    private function user(string $role = 'customer'): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);
        return $user;
    }

    private function token(User $user, array $scopes = ['identity:read', 'identity:write']): string
    {
        $owner = $this->user('admin');
        $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient('Developer API test client', ['https://test-client.example/callback'], false, $owner);
        $client->scopes = $scopes;
        $client->save();

        $verifier = str_repeat('b', 64);
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
        $state = str_repeat('s', 40);
        $query = http_build_query([
            'response_type' => 'code', 'client_id' => $client->id, 'redirect_uri' => 'https://test-client.example/callback',
            'scope' => implode(' ', $scopes), 'state' => $state, 'code_challenge_method' => 'S256', 'code_challenge' => $challenge,
        ]);
        $this->actingAs($user)->get('/oauth/authorize?'.$query)->assertOk();
        $approved = $this->post('/oauth/authorize', ['client_id' => $client->id, 'state' => $state, 'auth_token' => session('authToken')])->assertRedirect();
        parse_str((string) parse_url($approved->headers->get('Location'), PHP_URL_QUERY), $callback);
        $this->assertSame($state, $callback['state']);
        return $this->postJson('/api/v1/oauth/user-token', [
            'grant_type' => 'authorization_code', 'client_id' => $client->id,
            'redirect_uri' => 'https://test-client.example/callback', 'code' => $callback['code'], 'code_verifier' => $verifier,
        ])->assertOk()->json('access_token');
    }

    private function registerHeaders(User $user, string $key = 'developer-app-registration-0001'): array
    {
        return ['Authorization' => 'Bearer '.$this->token($user), 'Idempotency-Key' => $key];
    }

    private function body(string $name = 'Customer learning app'): array
    {
        return ['name' => $name, 'redirect_uris' => ['https://customer.example/callback'], 'scopes' => ['identity:read', 'identity:write', 'customer:read']];
    }

    public function test_signed_in_user_can_register_list_update_and_revoke_only_their_public_apps(): void
    {
        $user = $this->user();
        $headers = $this->registerHeaders($user);
        $body = $this->body();

        $created = $this->postJson('/api/v1/developer/applications', $body, $headers);
        $this->assertSame(201, $created->status(), $created->getContent());
        $created->assertCreated()
            ->assertJsonPath('data.name', $body['name'])->assertJsonPath('data.client_secret_issued', false)
            ->assertJsonPath('data.pkce_method', 'S256');
        $this->postJson('/api/v1/developer/applications', $body, $headers)->assertCreated()->assertExactJson($created->json());
        $clientId = $created->json('data.client_id');
        $client = Passport::client()->newQuery()->findOrFail($clientId);
        $this->assertSame($user->id, $client->owner_id);
        $this->assertTrue($client->hasGrantType('authorization_code'));
        $this->assertFalse($client->hasGrantType('client_credentials'));
        $this->assertNull($client->secret);

        $other = $this->user('practitioner');
        $otherHeaders = $this->registerHeaders($other, 'developer-app-registration-0002');
        $otherApp = $this->postJson('/api/v1/developer/applications', [
            'name' => 'Practitioner learning app', 'redirect_uris' => ['https://practitioner.example/callback'], 'scopes' => ['identity:read'],
        ], $otherHeaders);
        $otherApp->assertCreated();
        $otherClientId = $otherApp->json('data.client_id');

        $this->getJson('/api/v1/developer/applications', ['Authorization' => $headers['Authorization']])->assertOk()
            ->assertJsonPath('data.pagination.total', 1)->assertJsonPath('data.items.0.client_id', $clientId)
            ->assertDontSee($otherClientId);

        $oldToken = $this->tokenForClient($user, $client, ['identity:read']);
        $updateHeaders = ['Authorization' => $headers['Authorization'], 'Idempotency-Key' => 'developer-app-update-0001'];
        $updatedBody = ['name' => 'Customer learning app v2', 'redirect_uris' => ['https://customer.example/new-callback'], 'scopes' => ['identity:read', 'customer:read']];
        $this->patchJson('/api/v1/developer/applications/'.$clientId, $updatedBody, $updateHeaders)->assertOk()
            ->assertJsonPath('data.name', 'Customer learning app v2')
            ->assertJsonPath('data.redirect_uris.0', 'https://customer.example/new-callback');
        $this->assertDatabaseHas('oauth_access_tokens', ['id' => $oldToken['id'], 'revoked' => true]);
        $this->patchJson('/api/v1/developer/applications/'.$otherClientId, $updatedBody, [
            'Authorization' => $headers['Authorization'], 'Idempotency-Key' => 'developer-app-update-0002',
        ])->assertNotFound();

        $this->deleteJson('/api/v1/developer/applications/'.$clientId, ['unused' => true], [
            'Authorization' => $headers['Authorization'], 'Idempotency-Key' => 'developer-app-revoke-0001',
        ])->assertOk()->assertJsonPath('data.revoked', true);
        $this->assertDatabaseHas('oauth_clients', ['id' => $clientId, 'revoked' => true]);
    }

    /** @return array{id: string} */
    private function tokenForClient(User $user, Client $client, array $scopes): array
    {
        $verifier = str_repeat('c', 64);
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
        $state = str_repeat('t', 40);
        $this->actingAs($user)->get('/oauth/authorize?'.http_build_query([
            'response_type' => 'code', 'client_id' => $client->id, 'redirect_uri' => $client->redirect_uris[0],
            'scope' => implode(' ', $scopes), 'state' => $state, 'code_challenge_method' => 'S256', 'code_challenge' => $challenge,
        ]))->assertOk();
        $approved = $this->post('/oauth/authorize', ['client_id' => $client->id, 'state' => $state, 'auth_token' => session('authToken')])->assertRedirect();
        parse_str((string) parse_url($approved->headers->get('Location'), PHP_URL_QUERY), $callback);
        $tokenResponse = $this->postJson('/api/v1/oauth/user-token', [
            'grant_type' => 'authorization_code', 'client_id' => $client->id, 'redirect_uri' => $client->redirect_uris[0],
            'code' => $callback['code'], 'code_verifier' => $verifier,
        ])->assertOk();
        $tokenId = \Illuminate\Support\Facades\DB::table('oauth_access_tokens')->where('user_id', $user->id)->where('client_id', $client->id)->value('id');
        $this->assertNotEmpty($tokenResponse->json('access_token'));
        return ['id' => $tokenId];
    }

    public function test_application_registration_enforces_scope_callback_and_active_app_limits(): void
    {
        $user = $this->user();
        $headers = $this->registerHeaders($user, 'developer-app-guard-tests-001');
        $this->postJson('/api/v1/developer/applications', $this->body() + ['extra' => 'rejected'], $headers)->assertUnprocessable();

        $this->postJson('/api/v1/developer/applications', [
            'name' => 'Out of scope app', 'redirect_uris' => ['https://customer.example/callback'], 'scopes' => ['finance:read'],
        ], ['Authorization' => $headers['Authorization'], 'Idempotency-Key' => 'developer-app-invalid-scope-001'])->assertUnprocessable();
        $this->postJson('/api/v1/developer/applications', [
            'name' => 'Unsafe callback app', 'redirect_uris' => ['http://attacker.example/callback'], 'scopes' => ['identity:read'],
        ], ['Authorization' => $headers['Authorization'], 'Idempotency-Key' => 'developer-app-invalid-uri-001'])->assertUnprocessable();

        for ($i = 2; $i <= 6; $i++) {
            $next = $this->body('Customer app '.$i);
            $this->postJson('/api/v1/developer/applications', $next, [
                'Authorization' => $headers['Authorization'], 'Idempotency-Key' => 'developer-app-limit-000'.$i,
            ])->assertCreated();
        }
        $this->postJson('/api/v1/developer/applications', $this->body('Customer app 7'), [
            'Authorization' => $headers['Authorization'], 'Idempotency-Key' => 'developer-app-limit-0007',
        ])->assertUnprocessable();
        $this->getJson('/api/v1/developer/applications')->assertUnauthorized();
    }

}
