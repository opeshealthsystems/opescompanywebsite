<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DeveloperMachineClientsApiTest extends TestCase
{
    use RefreshDatabase;

    private static array $keys;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('customer', 'web');
        if (!isset(self::$keys)) {
            $options = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
            if (PHP_OS_FAMILY === 'Windows') $options['config'] = dirname(PHP_BINARY).'/extras/ssl/openssl.cnf';
            $key = openssl_pkey_new($options);
            openssl_pkey_export($key, $private, null, $options);
            self::$keys = ['private' => $private, 'public' => openssl_pkey_get_details($key)['key']];
        }
        config(['passport.private_key' => self::$keys['private'], 'passport.public_key' => self::$keys['public']]);
    }

    private function user(): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('customer');
        return $user;
    }

    private function delegatedToken(User $user): string
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient(
            'Developer API test client', ['https://developer.example/callback'], false, $admin,
        );
        $client->scopes = ['identity:read', 'identity:write'];
        $client->save();

        $verifier = str_repeat('m', 64);
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
        $state = str_repeat('d', 40);
        $query = http_build_query([
            'response_type' => 'code', 'client_id' => $client->id,
            'redirect_uri' => 'https://developer.example/callback',
            'scope' => 'identity:read identity:write', 'state' => $state,
            'code_challenge_method' => 'S256', 'code_challenge' => $challenge,
        ]);
        $this->actingAs($user)->get('/oauth/authorize?'.$query)->assertOk();
        $approved = $this->post('/oauth/authorize', [
            'client_id' => $client->id, 'state' => $state, 'auth_token' => session('authToken'),
        ])->assertRedirect();
        parse_str((string) parse_url($approved->headers->get('Location'), PHP_URL_QUERY), $callback);
        $response = $this->postJson('/api/v1/oauth/user-token', [
            'grant_type' => 'authorization_code', 'client_id' => $client->id,
            'redirect_uri' => 'https://developer.example/callback', 'code' => $callback['code'],
            'code_verifier' => $verifier,
        ])->assertOk();

        return $response->json('access_token');
    }

    private function createHeaders(string $token, string $key = 'machine-client-create-000001'): array
    {
        return ['Authorization' => 'Bearer '.$token, 'Idempotency-Key' => $key];
    }

    public function test_machine_clients_are_owner_scoped_secret_safe_and_limited_to_public_scopes(): void
    {
        $this->getJson('/api/v1/developer/machine-clients')->assertUnauthorized();
        $owner = $this->user();
        $other = $this->user();
        $token = $this->delegatedToken($owner);
        $otherToken = $this->delegatedToken($other);
        $headers = $this->createHeaders($token);
        $body = ['name' => 'Read-only catalogue sync', 'scopes' => ['catalogue:read', 'integration:read']];

        $created = $this->postJson('/api/v1/developer/machine-clients', $body, $headers)
            ->assertCreated()->assertJsonPath('data.secret_shown_once', true)
            ->assertJsonPath('data.scopes.0', 'catalogue:read')
            ->assertJsonPath('data.scopes.1', 'integration:read');
        $clientId = $created->json('data.client_id');
        $secret = $created->json('data.client_secret');
        $this->assertNotEmpty($secret);

        $replayed = $this->postJson('/api/v1/developer/machine-clients', $body, $headers)
            ->assertCreated()->assertHeader('Idempotency-Replayed', 'true');
        $this->assertSame($secret, $replayed->json('data.client_secret'));
        $this->assertDatabaseCount('oauth_clients', 3);
        $this->postJson('/api/v1/developer/machine-clients', [
            'name' => 'Escalated client', 'scopes' => ['customer:read'],
        ], $this->createHeaders($token, 'machine-client-scope-000001'))->assertUnprocessable();

        $listing = $this->getJson('/api/v1/developer/machine-clients', ['Authorization' => 'Bearer '.$token])
            ->assertOk()->assertJsonPath('data.items.0.client_id', $clientId);
        $this->assertArrayNotHasKey('client_secret', $listing->json('data.items.0'));
        $this->assertStringNotContainsString($secret, $listing->getContent());
        $this->getJson('/api/v1/developer/machine-clients', ['Authorization' => 'Bearer '.$otherToken])
            ->assertOk()->assertJsonCount(0, 'data.items');

        $machineToken = $this->postJson('/api/v1/oauth/token', [
            'grant_type' => 'client_credentials', 'client_id' => $clientId,
            'client_secret' => $secret, 'scope' => 'integration:read catalogue:read',
        ])->assertOk()->json('access_token');
        $this->getJson('/api/v1/integration', ['Authorization' => 'Bearer '.$machineToken])
            ->assertOk()->assertJsonPath('data.client_id', $clientId);
        $this->getJson('/api/v1/developer/machine-clients', ['Authorization' => 'Bearer '.$machineToken])
            ->assertUnauthorized();

        $rotateKey = 'machine-client-rotate-000001';
        $rotated = $this->postJson('/api/v1/developer/machine-clients/'.$clientId.'/rotate', [], $this->createHeaders($token, $rotateKey))
            ->assertOk()->assertJsonPath('data.secret_shown_once', true);
        $newSecret = $rotated->json('data.client_secret');
        $this->assertNotSame($secret, $newSecret);
        $this->getJson('/api/v1/integration', ['Authorization' => 'Bearer '.$machineToken])->assertUnauthorized();
        $this->postJson('/api/v1/developer/machine-clients/'.$clientId.'/rotate', [], $this->createHeaders($token, $rotateKey))
            ->assertOk()->assertHeader('Idempotency-Replayed', 'true')->assertJsonPath('data.client_secret', $newSecret);

        $this->deleteJson('/api/v1/developer/machine-clients/'.$clientId, [], $this->createHeaders($token, 'machine-client-revoke-0001'))
            ->assertOk()->assertJsonPath('data.revoked', true);
        $this->postJson('/api/v1/oauth/token', [
            'grant_type' => 'client_credentials', 'client_id' => $clientId,
            'client_secret' => $newSecret, 'scope' => 'integration:read',
        ])->assertUnauthorized();
    }
}
