<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Hash, Mail, Notification};
use Laravel\Passport\{Client, ClientRepository};
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Native in-app sign-in: the mobile app exchanges email + password directly for a scoped token through the
 * public password grant (no website redirect), reusing the same per-role scope trimming as the OAuth flow.
 */
class MobilePasswordLoginTest extends TestCase
{
    use RefreshDatabase;

    private const MOBILE_SCOPES = ['identity:read', 'identity:write', 'finance:read', 'finance:write', 'administration:read', 'administration:write', 'crm:read', 'crm:write', 'enquiries:read', 'enquiries:write', 'customer:read', 'customer:write', 'practitioner:read', 'practitioner:write', 'testing:read', 'testing:write', 'partner:read', 'partner:write'];
    private const CALLBACK = 'opeshealth://oauth/callback';
    private const PASSWORD = 'Str0ng-Pass-123';
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

    private function user(string $role, array $attrs = []): User
    {
        $user = User::factory()->create(array_merge(['is_active' => true, 'password' => Hash::make(self::PASSWORD)], $attrs));
        $user->assignRole($role);
        return $user;
    }

    private function mobileClient(array $ceiling = self::MOBILE_SCOPES): Client
    {
        // Mirror the real OPES Health mobile client: a public PKCE auth-code client (no secret) that also
        // allows the password grant for native in-app sign-in.
        $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient('Fictional mobile client', [self::CALLBACK], false, $this->user('admin'));
        $client->scopes = $ceiling;
        $client->grant_types = ['authorization_code', 'refresh_token', 'password'];
        $client->save();
        return $client;
    }

    private function login(Client $client, string $email, string $password, array $scopes = self::MOBILE_SCOPES)
    {
        return $this->postJson('/api/v1/oauth/user-token', [
            'grant_type' => 'password', 'client_id' => $client->getKey(),
            'username' => $email, 'password' => $password, 'scope' => implode(' ', $scopes),
        ]);
    }

    public function test_customer_signs_in_with_email_and_password_and_receives_role_scopes(): void
    {
        $client = $this->mobileClient();
        $this->user('customer', ['email' => 'cust@example.test']);

        $response = $this->login($client, 'cust@example.test', self::PASSWORD)->assertOk()
            ->assertJsonStructure(['access_token', 'refresh_token', 'token_type', 'expires_in']);
        $headers = ['Authorization' => 'Bearer '.$response->json('access_token')];

        $this->getJson('/api/v1/customer/tickets', $headers)->assertOk();
        $this->getJson('/api/v1/finance/quotes', $headers)->assertForbidden();
        $scopes = $this->getJson('/api/v1/me', $headers)->assertOk()->json('data.scopes');
        $this->assertContains('customer:read', $scopes);
        $this->assertNotContains('finance:read', $scopes);
    }

    public function test_wrong_password_is_rejected_generically(): void
    {
        $client = $this->mobileClient();
        $this->user('customer', ['email' => 'cust@example.test']);
        $this->login($client, 'cust@example.test', 'not-the-password')->assertStatus(400)->assertJsonPath('error', 'invalid_grant');
    }

    public function test_unknown_email_is_rejected_the_same_way(): void
    {
        $client = $this->mobileClient();
        $this->login($client, 'nobody@example.test', self::PASSWORD)->assertStatus(400)->assertJsonPath('error', 'invalid_grant');
    }

    public function test_inactive_account_cannot_sign_in(): void
    {
        $client = $this->mobileClient();
        $this->user('customer', ['email' => 'inactive@example.test', 'is_active' => false]);
        $this->login($client, 'inactive@example.test', self::PASSWORD)->assertStatus(403);
    }

    public function test_password_grant_token_is_trimmed_to_partner_scopes(): void
    {
        $client = $this->mobileClient();
        $this->user('partner', ['email' => 'partner@example.test']);
        $token = $this->login($client, 'partner@example.test', self::PASSWORD)->assertOk()->json('access_token');
        $scopes = $this->getJson('/api/v1/me', ['Authorization' => 'Bearer '.$token])->assertOk()->json('data.scopes');
        $this->assertContains('partner:read', $scopes);
        $this->assertNotContains('customer:write', $scopes);
    }
}
