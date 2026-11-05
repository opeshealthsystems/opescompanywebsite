<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Mail, Notification};
use Laravel\Passport\{Client, ClientRepository};
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * The OPES Health mobile app is one reviewed public client serving every account type, so it requests its
 * whole scope ceiling. Each delegated token must be trimmed to the user's role-allowed scopes, not rejected.
 */
class MobileOAuthScopeTest extends TestCase
{
    use RefreshDatabase;
    private const CALLBACK = 'opeshealth://oauth/callback';
    private const MOBILE_SCOPES = ['identity:read', 'identity:write', 'finance:read', 'finance:write', 'administration:read', 'administration:write', 'crm:read', 'crm:write', 'enquiries:read', 'enquiries:write', 'customer:read', 'customer:write', 'practitioner:read', 'practitioner:write', 'testing:read', 'testing:write', 'partner:read', 'partner:write'];
    private static array $keys;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite(); Mail::fake(); Notification::fake();
        foreach (['admin', 'customer', 'partner'] as $role) Role::findOrCreate($role, 'web');
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

    private function mobileClient(array $ceiling = self::MOBILE_SCOPES): Client
    {
        $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient('Fictional mobile client', [self::CALLBACK], false, $this->user('admin'));
        $client->scopes = $ceiling; $client->save();
        return $client;
    }

    private function exchange(User $user, Client $client, array $requested)
    {
        $verifier = str_repeat('v', 64);
        $query = ['response_type' => 'code', 'client_id' => $client->id, 'redirect_uri' => self::CALLBACK, 'scope' => implode(' ', $requested),
            'state' => str_repeat('s', 40), 'code_challenge_method' => 'S256', 'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=')];
        $this->actingAs($user)->get('/oauth/authorize?'.http_build_query($query))->assertOk();
        $approved = $this->post('/oauth/authorize', ['client_id' => $client->id, 'state' => $query['state'], 'auth_token' => session('authToken')])->assertRedirect();
        parse_str((string) parse_url($approved->headers->get('Location'), PHP_URL_QUERY), $callback);
        return $this->postJson('/api/v1/oauth/user-token', ['grant_type' => 'authorization_code', 'client_id' => $client->id, 'redirect_uri' => self::CALLBACK, 'code' => $callback['code'], 'code_verifier' => $verifier]);
    }

    public function test_full_mobile_request_is_trimmed_to_the_customers_role_scopes(): void
    {
        $customer = $this->user('customer');
        $token = $this->exchange($customer, $this->mobileClient(), self::MOBILE_SCOPES)->assertOk()->json('access_token');
        $headers = ['Authorization' => 'Bearer '.$token];

        $this->getJson('/api/v1/customer/tickets', $headers)->assertOk();
        $this->getJson('/api/v1/finance/quotes', $headers)->assertForbidden();
        $scopes = $this->getJson('/api/v1/me', $headers)->assertOk()->json('data.scopes');
        $this->assertContains('customer:read', $scopes);
        $this->assertNotContains('finance:read', $scopes);
        $this->assertNotContains('partner:write', $scopes);
    }

    public function test_partner_receives_partner_scopes_from_the_same_request(): void
    {
        $token = $this->exchange($this->user('partner'), $this->mobileClient(), self::MOBILE_SCOPES)->assertOk()->json('access_token');
        $scopes = $this->getJson('/api/v1/me', ['Authorization' => 'Bearer '.$token])->assertOk()->json('data.scopes');
        $this->assertContains('partner:read', $scopes);
        $this->assertContains('partner:write', $scopes);
        $this->assertNotContains('customer:write', $scopes);
    }

    public function test_scope_outside_the_client_ceiling_is_still_rejected(): void
    {
        $client = $this->mobileClient(['identity:read', 'customer:read']);
        $this->exchange($this->user('customer'), $client, ['identity:read', 'customer:read', 'finance:read'])->assertStatus(400)->assertJsonPath('error', 'invalid_scope');
    }

    public function test_request_with_no_role_allowed_scope_is_rejected(): void
    {
        $this->exchange($this->user('customer'), $this->mobileClient(), ['finance:read', 'crm:write'])->assertStatus(400)->assertJsonPath('error', 'invalid_scope');
    }
}
