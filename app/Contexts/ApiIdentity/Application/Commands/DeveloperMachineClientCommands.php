<?php

namespace App\Contexts\ApiIdentity\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

/** Owner-scoped self-service credentials for non-clinical product integrations. */
final class DeveloperMachineClientCommands extends WorkflowHandler
{
    private const ALLOWED_SCOPES = ['catalogue:read', 'integration:read'];
    private const MAX_ACTIVE_CLIENTS = 5;

    public function __construct(private ClientRepository $clients) {}

    private function currentUser(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active, 403);
        return $actor;
    }

    private function ownedClient(User $actor, string $id, bool $lock = false): object
    {
        $query = Passport::client()->newQuery()
            ->whereKey($id)
            ->where('owner_type', $actor->getMorphClass())
            ->where('owner_id', $actor->getKey());
        if ($lock) $query->lockForUpdate();
        $client = $query->firstOrFail();
        abort_unless($client->hasGrantType('client_credentials') && !$client->hasGrantType('authorization_code'), 404);
        return $client;
    }

    private function revokeTokens(object $client): void
    {
        $tokenIds = $client->tokens()->pluck('id');
        if ($tokenIds->isEmpty()) return;
        $client->tokens()->whereIn('id', $tokenIds)->update(['revoked' => true]);
        \Illuminate\Support\Facades\DB::table('oauth_refresh_tokens')->whereIn('access_token_id', $tokenIds)->update(['revoked' => true]);
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->currentUser($input);
        $data = $input->validate([
            'name' => 'required|string|min:3|max:100',
            'scopes' => 'required|array|min:1|max:2',
            'scopes.*' => 'required|string|distinct|in:'.implode(',', self::ALLOWED_SCOPES),
        ]);

        User::query()->whereKey($actor->getKey())->lockForUpdate()->firstOrFail();
        $active = Passport::client()->newQuery()
            ->where('owner_type', $actor->getMorphClass())
            ->where('owner_id', $actor->getKey())
            ->where('grant_types', 'like', '%client_credentials%')
            ->where('revoked', false)
            ->count();
        if ($active >= self::MAX_ACTIVE_CLIENTS) {
            abort(422, 'An account can have up to five active machine API clients. Revoke one before creating another.');
        }

        $scopes = array_values(array_unique($data['scopes']));
        $client = $this->clients->createClientCredentialsGrantClient($data['name']);
        $client->owner()->associate($actor);
        $client->scopes = $scopes;
        $client->save();

        DomainOutbox::append('event', 'api_identity.developer_machine_client_created.v1', [
            'references' => [['resource' => 'OAuthClient', 'id' => (string) $client->getKey()]],
            'scopes' => $scopes,
        ], $actor->id);

        return WorkflowResult::payload([
            'client_id' => (string) $client->getKey(),
            'name' => $client->name,
            'client_secret' => $client->plainSecret,
            'token_url' => url('/api/v1/oauth/token'),
            'scopes' => $scopes,
            'secret_shown_once' => true,
        ], 201)->with('success', 'Machine API client created. Save the secret now; it is not shown in client listings.');
    }

    public function rotate(WorkflowInput $input, string $clientId): WorkflowResult
    {
        $actor = $this->currentUser($input);
        $client = $this->ownedClient($actor, $clientId, true);
        abort_unless(!$client->revoked, 422, 'Revoked machine clients cannot be rotated.');

        $this->revokeTokens($client);
        $client->secret = Str::random(64);
        $client->save();
        DomainOutbox::append('event', 'api_identity.developer_machine_client_secret_rotated.v1', [
            'references' => [['resource' => 'OAuthClient', 'id' => (string) $client->getKey()]],
        ], $actor->id);

        return WorkflowResult::payload([
            'client_id' => (string) $client->getKey(),
            'name' => $client->name,
            'client_secret' => $client->plainSecret,
            'token_url' => url('/api/v1/oauth/token'),
            'scopes' => array_values(array_intersect($client->scopes ?? [], self::ALLOWED_SCOPES)),
            'secret_shown_once' => true,
        ])->with('success', 'Machine API client secret rotated. Save the new secret now; existing tokens were revoked.');
    }

    public function revoke(WorkflowInput $input, string $clientId): WorkflowResult
    {
        $actor = $this->currentUser($input);
        $client = $this->ownedClient($actor, $clientId, true);
        if (!$client->revoked) {
            $this->revokeTokens($client);
            $client->revoked = true;
            $client->save();
            DomainOutbox::append('event', 'api_identity.developer_machine_client_revoked.v1', [
                'references' => [['resource' => 'OAuthClient', 'id' => (string) $client->getKey()]],
            ], $actor->id);
        }

        return WorkflowResult::payload(['client_id' => (string) $client->getKey(), 'revoked' => true]);
    }
}
