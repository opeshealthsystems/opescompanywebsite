<?php

namespace App\Contexts\ApiIdentity\Application\Commands;

use App\Contexts\ApiIdentity\Application\BusinessScopes;
use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

final class AdminOAuthClientCommands extends WorkflowHandler
{
    public function __construct(private ClientRepository $clients) {}

    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function publicClient(string $clientId, bool $lock = false): object
    {
        $query = Passport::client()->newQuery()->whereKey($clientId);
        if ($lock) $query->lockForUpdate();
        $client = $query->firstOrFail();
        abort_unless($client->hasGrantType('authorization_code') && ! $client->hasGrantType('client_credentials'), 422,
            'Only public authorization-code clients can be managed through this operation.');
        return $client;
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'name' => 'required|string|min:3|max:150',
            'owner_id' => 'required|integer|exists:users,id',
            'redirect_uris' => 'required|array|min:1|max:10',
            'redirect_uris.*' => 'required|string|max:255|distinct',
            'scopes' => 'required|array|min:1|max:100',
            'scopes.*' => 'required|string|distinct',
        ]);

        $owner = User::query()->whereKey($data['owner_id'])->where('is_active', true)
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['admin', 'super_admin']))->first();
        if (! $owner) throw ValidationException::withMessages(['owner_id' => ['Select an active administrator.']]);

        foreach ($data['redirect_uris'] as $redirect) {
            $url = parse_url($redirect);
            $isHttps = filter_var($redirect, FILTER_VALIDATE_URL) && ($url['scheme'] ?? null) === 'https'
                && ! empty($url['host']) && ! isset($url['fragment']) && ! isset($url['user']) && ! isset($url['pass']);
            $isOhsNative = $redirect === 'opeshealth://oauth/callback';
            if (! $isHttps && ! $isOhsNative) {
                throw ValidationException::withMessages(['redirect_uris' => ['Use an exact HTTPS URL or the OPES Health app callback opeshealth://oauth/callback.']]);
            }
        }

        // A reviewed public app can serve several account types. Register its requested
        // scopes as the client ceiling; ApprovedScopeRepository still intersects each
        // delegated user's consent with the scopes allowed by that user's active roles.
        $registeredScopes = array_keys(BusinessScopes::definitions());
        if (array_diff($data['scopes'], $registeredScopes)) {
            throw ValidationException::withMessages(['scopes' => ['Select only scopes defined by the API product.']]);
        }

        $redirects = array_values($data['redirect_uris']);
        $scopes = array_values($data['scopes']);
        $client = $this->clients->createAuthorizationCodeGrantClient($data['name'], $redirects, false, $owner);
        $client->scopes = $scopes;
        $client->save();
        DomainOutbox::append('event', 'api_identity.oauth_client_created.v1', [
            'references' => [['resource' => 'OAuthClient', 'id' => (string) $client->getKey()]],
            'scopes' => $scopes,
            'pkce' => 'S256',
        ], $actor->id);

        return WorkflowResult::payload([
            'client_id' => (string) $client->getKey(),
            'name' => $client->name,
            'redirect_uris' => $redirects,
            'scopes' => $scopes,
            'authorization_url' => url('/oauth/authorize'),
            'token_url' => url('/api/v1/oauth/user-token'),
            'pkce_method' => 'S256',
        ], 201)->with('success', 'Public OAuth client created. No client secret is issued; use authorization code with S256 PKCE.');
    }

    public function revoke(WorkflowInput $input, string $clientId): WorkflowResult
    {
        $actor = $this->admin($input);
        $client = $this->publicClient($clientId, true);
        if (! $client->revoked) {
            $client->tokens()->update(['revoked' => true]);
            $client->revoked = true;
            $client->save();
            DomainOutbox::append('event', 'api_identity.oauth_client_revoked.v1', [
                'references' => [['resource' => 'OAuthClient', 'id' => (string) $client->getKey()]],
            ], $actor->id);
        }

        return WorkflowResult::payload(['client_id' => (string) $client->getKey(), 'revoked' => true])
            ->with('success', 'OAuth client and its existing tokens are revoked.');
    }
}
