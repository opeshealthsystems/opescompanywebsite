<?php

namespace App\Contexts\ApiIdentity\Application\Commands;

use App\Contexts\ApiIdentity\Application\BusinessScopes;
use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

/** Self-service lifecycle for public OAuth applications owned by a signed-in user. */
final class DeveloperOAuthApplicationCommands extends WorkflowHandler
{
    private const MAX_ACTIVE_APPS = 5;

    public function __construct(private ClientRepository $clients) {}

    private function authenticatedActor(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active, 403);
        return $actor;
    }

    private function rejectUnknownFields(WorkflowInput $input): void
    {
        $unknown = array_diff(array_keys($input->all()), ['name', 'redirect_uris', 'scopes']);
        if ($unknown !== []) {
            $field = $unknown[array_key_first($unknown)];
            throw ValidationException::withMessages([(string) $field => ['This field is not accepted.']]);
        }
    }

    private function validateRedirectUris(array $redirectUris): array
    {
        foreach ($redirectUris as $redirect) {
            $parts = parse_url($redirect);
            $https = filter_var($redirect, FILTER_VALIDATE_URL) && ($parts['scheme'] ?? null) === 'https'
                && !empty($parts['host']) && !isset($parts['fragment']) && !isset($parts['user']) && !isset($parts['pass']);
            $loopback = filter_var($redirect, FILTER_VALIDATE_URL) && ($parts['scheme'] ?? null) === 'http'
                && in_array(strtolower($parts['host'] ?? ''), ['localhost', '127.0.0.1', '[::1]'], true)
                && !isset($parts['fragment']) && !isset($parts['user']) && !isset($parts['pass']);
            $ohsNative = $redirect === 'opeshealth://oauth/callback';
            if (!$https && !$loopback && !$ohsNative) {
                throw ValidationException::withMessages(['redirect_uris' => ['Use HTTPS, a loopback URL for local development, or the reserved OPES Health callback.']]);
            }
        }
        return array_values($redirectUris);
    }

    private function validateScopes(array $scopes, User $actor): array
    {
        $allowed = BusinessScopes::allowed($actor);
        if (array_diff($scopes, $allowed)) {
            throw ValidationException::withMessages(['scopes' => ['Choose only API scopes currently available to your account.']]);
        }
        return array_values(array_unique($scopes));
    }

    private function ownedPublicClient(User $actor, string $id, bool $lock = false): object
    {
        $query = Passport::client()->newQuery()
            ->whereKey($id)->where('owner_type', $actor->getMorphClass())->where('owner_id', $actor->getKey());
        if ($lock) $query->lockForUpdate();
        $client = $query->firstOrFail();
        abort_unless($client->hasGrantType('authorization_code') && !$client->hasGrantType('client_credentials'), 404);
        return $client;
    }

    private function revokeTokens(object $client): void
    {
        $tokenIds = $client->tokens()->pluck('id');
        if ($tokenIds->isEmpty()) return;

        $client->tokens()->whereIn('id', $tokenIds)->update(['revoked' => true]);
        DB::table('oauth_refresh_tokens')->whereIn('access_token_id', $tokenIds)->update(['revoked' => true]);
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->authenticatedActor($input);
        $this->rejectUnknownFields($input);
        $data = $input->validate([
            'name' => 'required|string|min:3|max:150',
            'redirect_uris' => 'required|array|min:1|max:10',
            'redirect_uris.*' => 'required|string|max:255|distinct',
            'scopes' => 'required|array|min:1|max:100',
            'scopes.*' => 'required|string|distinct',
        ]);
        $activeApps = Passport::client()->newQuery()
            ->where('owner_type', $actor->getMorphClass())->where('owner_id', $actor->getKey())
            ->where('grant_types', 'like', '%authorization_code%')->where('revoked', false)->count();
        if ($activeApps >= self::MAX_ACTIVE_APPS) {
            throw ValidationException::withMessages(['name' => ['An account can have up to five active public OAuth applications. Revoke one before registering another.']]);
        }

        $redirects = $this->validateRedirectUris($data['redirect_uris']);
        $scopes = $this->validateScopes($data['scopes'], $actor);
        $client = $this->clients->createAuthorizationCodeGrantClient($data['name'], $redirects, false, $actor);
        $client->scopes = $scopes;
        $client->save();
        DomainOutbox::append('event', 'api_identity.developer_application_registered.v1', [
            'references' => [['resource' => 'OAuthClient', 'id' => (string) $client->getKey()]],
            'scopes' => $scopes, 'pkce' => 'S256',
        ], $actor->id);

        return WorkflowResult::payload($this->projection($client), 201);
    }

    public function update(WorkflowInput $input, string $clientId): WorkflowResult
    {
        $actor = $this->authenticatedActor($input);
        $this->rejectUnknownFields($input);
        $data = $input->validate([
            'name' => 'required|string|min:3|max:150',
            'redirect_uris' => 'required|array|min:1|max:10',
            'redirect_uris.*' => 'required|string|max:255|distinct',
            'scopes' => 'required|array|min:1|max:100',
            'scopes.*' => 'required|string|distinct',
        ]);
        $client = $this->ownedPublicClient($actor, $clientId, true);
        if ($client->revoked) throw ValidationException::withMessages(['client' => ['Revoked applications cannot be changed.']]);
        $redirects = $this->validateRedirectUris($data['redirect_uris']);
        $scopes = $this->validateScopes($data['scopes'], $actor);

        $this->revokeTokens($client);
        $client->name = $data['name'];
        $client->redirect_uris = $redirects;
        $client->scopes = $scopes;
        $client->save();
        DomainOutbox::append('event', 'api_identity.developer_application_updated.v1', [
            'references' => [['resource' => 'OAuthClient', 'id' => (string) $client->getKey()]],
            'scopes' => $scopes, 'tokens_revoked' => true,
        ], $actor->id);

        return WorkflowResult::payload($this->projection($client));
    }

    public function revoke(WorkflowInput $input, string $clientId): WorkflowResult
    {
        $actor = $this->authenticatedActor($input);
        $client = $this->ownedPublicClient($actor, $clientId, true);
        if (!$client->revoked) {
            $this->revokeTokens($client);
            $client->revoked = true;
            $client->save();
            DomainOutbox::append('event', 'api_identity.developer_application_revoked.v1', [
                'references' => [['resource' => 'OAuthClient', 'id' => (string) $client->getKey()]],
            ], $actor->id);
        }
        return WorkflowResult::payload(['client_id' => (string) $client->getKey(), 'revoked' => true]);
    }

    private function projection(object $client): array
    {
        return [
            'client_id' => (string) $client->getKey(), 'name' => $client->name,
            'redirect_uris' => array_values($client->redirect_uris ?? []),
            'scopes' => array_values($client->scopes ?? []), 'revoked' => (bool) $client->revoked,
            'authorization_url' => url('/oauth/authorize'), 'token_url' => url('/api/v1/oauth/user-token'),
            'pkce_method' => 'S256', 'client_secret_issued' => false,
        ];
    }
}
