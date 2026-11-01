<?php

namespace App\Contexts\ApiIdentity\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

final class AdminApiClientCommands extends WorkflowHandler
{
    private const ALLOWED_SCOPES = ['catalogue:read', 'integration:read'];

    public function __construct(private ClientRepository $clients) {}

    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function client(string $id, bool $lock = false): object
    {
        $query = Passport::client()->newQuery()->whereKey($id);
        if ($lock) $query->lockForUpdate();
        $client = $query->firstOrFail();
        abort_unless($client->hasGrantType('client_credentials'), 422, 'Only machine clients can be managed through this operation.');
        return $client;
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'name' => 'required|string|min:3|max:100',
            'owner_id' => 'required|integer|exists:users,id',
            'scopes' => 'required|array|min:1|max:2',
            'scopes.*' => 'required|string|distinct|in:'.implode(',', self::ALLOWED_SCOPES),
        ]);
        $owner = User::query()->whereKey($data['owner_id'])->where('is_active', true)
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['admin', 'super_admin']))->first();
        if (! $owner) throw ValidationException::withMessages(['owner_id' => ['Select an active administrator.']]);

        $client = $this->clients->createClientCredentialsGrantClient($data['name']);
        $client->owner()->associate($owner);
        $client->scopes = array_values($data['scopes']);
        $client->save();
        DomainOutbox::append('event', 'api_identity.machine_client_created.v1', [
            'references' => [['resource' => 'OAuthClient', 'id' => (string) $client->getKey()]],
            'scopes' => array_values($data['scopes']),
        ], $actor->id);

        return WorkflowResult::payload([
            'client_id' => (string) $client->getKey(),
            'client_secret' => $client->plainSecret,
            'token_url' => url('/api/v1/oauth/token'),
            'scopes' => array_values($data['scopes']),
        ], 201)->with('success', 'Client created. Store the secret now; it will not appear in client listings.');
    }

    public function rotate(WorkflowInput $input, string $clientId): WorkflowResult
    {
        $actor = $this->admin($input);
        $client = $this->client($clientId, true);
        abort_unless(! $client->revoked, 422, 'Revoked clients cannot be rotated.');
        $owner = $client->owner;
        abort_unless($owner instanceof User && $owner->is_active && $owner->hasAnyRole(['admin', 'super_admin']), 422, 'Client must have an active administrator owner.');

        $client->tokens()->update(['revoked' => true]);
        $client->secret = Str::random(64);
        $client->save();
        DomainOutbox::append('event', 'api_identity.machine_client_secret_rotated.v1', [
            'references' => [['resource' => 'OAuthClient', 'id' => (string) $client->getKey()]],
        ], $actor->id);

        return WorkflowResult::payload([
            'client_id' => (string) $client->getKey(),
            'client_secret' => $client->plainSecret,
            'token_url' => url('/api/v1/oauth/token'),
            'scopes' => array_values(array_intersect($client->scopes ?? [], self::ALLOWED_SCOPES)),
        ])->with('success', 'Client secret rotated. Store the new secret now; existing tokens were revoked.');
    }

    public function revoke(WorkflowInput $input, string $clientId): WorkflowResult
    {
        $actor = $this->admin($input);
        $client = $this->client($clientId, true);
        if (! $client->revoked) {
            $client->tokens()->update(['revoked' => true]);
            $client->revoked = true;
            $client->save();
            DomainOutbox::append('event', 'api_identity.machine_client_revoked.v1', [
                'references' => [['resource' => 'OAuthClient', 'id' => (string) $client->getKey()]],
            ], $actor->id);
        }

        return WorkflowResult::payload(['client_id' => (string) $client->getKey(), 'revoked' => true])
            ->with('success', 'Client and its existing tokens are revoked.');
    }
}
