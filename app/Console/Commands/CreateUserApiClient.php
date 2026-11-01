<?php

namespace App\Console\Commands;

use App\Contexts\ApiIdentity\Application\BusinessScopes;
use App\Models\User;
use Illuminate\Console\Command;
use Laravel\Passport\ClientRepository;

final class CreateUserApiClient extends Command
{
    protected $signature = 'api:user-client {--owner=} {--name=} {--redirect=*} {--scope=*}';
    protected $description = 'Provision an administrator-reviewed public OAuth client using S256 PKCE';

    public function handle(ClientRepository $clients): int
    {
        $owner = User::where('email', $this->option('owner'))->first();
        if (!$owner?->is_active || !$owner->hasAnyRole(['admin', 'super_admin'])) { $this->error('An active administrator owner is required.'); return self::FAILURE; }
        $name = trim((string) $this->option('name')); $redirects = $this->option('redirect'); $scopes = array_values(array_unique($this->option('scope')));
        if ($name === '' || strlen($name) > 150 || !$redirects || !$scopes || array_diff($scopes, array_keys(BusinessScopes::definitions()))) {
            $this->error('Supply a name, exact redirect URI(s), and explicit registered business scopes.'); return self::FAILURE;
        }
        foreach ($redirects as $redirect) {
            $url = parse_url($redirect);
            if (!filter_var($redirect, FILTER_VALIDATE_URL) || ($url['scheme'] ?? '') !== 'https' || isset($url['fragment']) || isset($url['user']) || isset($url['pass'])) {
                $this->error('Redirects must be exact HTTPS URLs without credentials or fragments.'); return self::FAILURE;
            }
        }
        $client = $clients->createAuthorizationCodeGrantClient($name, $redirects, false, $owner);
        $client->scopes = $scopes; $client->save();
        $this->line(json_encode(['client_id' => $client->id, 'redirect_uris' => $redirects, 'scopes' => $scopes,
            'authorization_url' => url('/oauth/authorize'), 'token_url' => url('/api/v1/oauth/user-token'), 'pkce' => 'S256'], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        return self::SUCCESS;
    }
}
