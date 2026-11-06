<?php

namespace Tests\Feature;

use App\Contexts\ApiIdentity\Application\BusinessScopes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DeveloperOAuthConsoleTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): User
    {
        Role::findOrCreate('customer', 'web');
        $user = User::factory()->create();
        $user->assignRole('customer');
        return $user;
    }

    public function test_console_requires_sign_in_and_lists_only_the_signed_in_users_apps(): void
    {
        $this->get('/en/developers/console')->assertRedirect('/login');

        $owner = $this->customer();
        $other = $this->customer();
        $client = app(\Laravel\Passport\ClientRepository::class)->createAuthorizationCodeGrantClient(
            'Private to another account', ['https://other.example/callback'], false, $other,
        );

        $this->actingAs($owner)->get('/en/developers/console')->assertOk()
            ->assertSee('Your OAuth applications')->assertSee('does not issue a client secret')
            ->assertDontSee('Private to another account')->assertDontSee((string) $client->getKey());
    }

    public function test_console_create_update_and_revoke_use_the_registered_application_workflow(): void
    {
        $owner = $this->customer();
        $scope = BusinessScopes::allowed($owner)[0] ?? null;
        $this->assertNotNull($scope, 'Customer role should have at least one API scope.');
        $createData = [
            'name' => 'Clinic scheduling app',
            'redirect_uris' => ['https://clinic.example/oauth/callback'],
            'scopes' => [$scope],
            '_command_key' => 'developer-console-create-0001',
        ];

        $this->actingAs($owner)->post('/en/developers/console/applications', $createData)
            ->assertRedirect(route('developers.console.index', ['locale' => 'en']))
            ->assertSessionHas('developer_oauth_application.client_id');
        $client = Passport::client()->newQuery()->where('owner_id', $owner->id)->firstOrFail();
        $this->assertSame('Clinic scheduling app', $client->name);
        $this->assertNull($client->secret, 'Public OAuth clients must not have a client secret.');
        $this->assertDatabaseCount('oauth_clients', 1);
        DB::table('api_user_usage')->insert([
            'client_id' => $client->getKey(), 'user_id' => $owner->id,
            'usage_date' => now('UTC')->toDateString(), 'requests' => 7,
        ]);
        $this->get('/en/developers/console')->assertOk()
            ->assertSee('Requests today')->assertSee('Last 30 days')->assertSee('View daily totals for 30 days')->assertSee('>7<', false);

        $this->post('/en/developers/console/applications', $createData)
            ->assertRedirect(route('developers.console.index', ['locale' => 'en']))
            ->assertSessionHas('developer_oauth_application.client_id', (string) $client->getKey());
        $this->assertDatabaseCount('oauth_clients', 1);

        $this->post('/en/developers/console/applications/'.$client->getKey().'/update', [
            'name' => 'Clinic scheduling app v2',
            'redirect_uris' => ['https://clinic.example/new-callback'],
            'scopes' => [$scope],
            '_command_key' => 'developer-console-update-0001',
        ])->assertRedirect(route('developers.console.index', ['locale' => 'en']));
        $this->assertSame('Clinic scheduling app v2', $client->fresh()->name);

        $this->post('/en/developers/console/applications/'.$client->getKey().'/revoke', [
            '_command_key' => 'developer-console-revoke-0001',
        ])->assertRedirect(route('developers.console.index', ['locale' => 'en']));
        $this->assertTrue((bool) $client->fresh()->revoked);
    }

    public function test_machine_client_console_discloses_secret_once_and_uses_the_shared_workflows(): void
    {
        $owner = $this->customer();
        $response = $this->actingAs($owner)->post('/en/developers/console/machine-clients', [
            'name' => 'Server catalogue integration',
            'scopes' => ['catalogue:read', 'integration:read'],
            '_command_key' => 'console-machine-client-create-01',
        ])->assertRedirect(route('developers.console.index', ['locale' => 'en']));

        $client = Passport::client()->newQuery()->where('owner_id', $owner->id)
            ->where('grant_types', 'like', '%client_credentials%')->firstOrFail();
        $secret = $response->getSession()->get('developer_machine_client.credentials.client_secret');
        $this->assertNotEmpty($secret);
        $this->get('/en/developers/console')->assertOk()
            ->assertSee('Machine API clients')->assertSee($client->name)
            ->assertSee($secret)->assertSee('save it now');
        $this->get('/en/developers/console')->assertOk()->assertDontSee($secret);

        $this->actingAs($owner)->post('/en/developers/console/machine-clients/'.$client->getKey().'/revoke', [
            '_command_key' => 'console-machine-client-revoke-01',
        ])->assertRedirect(route('developers.console.index', ['locale' => 'en']));
        $this->assertTrue((bool) $client->fresh()->revoked);
    }
}
