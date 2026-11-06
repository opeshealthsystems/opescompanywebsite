<?php

namespace App\Console\Commands;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\User;
use Illuminate\Console\Command;

final class CreateUserApiClient extends Command
{
    protected $signature = 'api:user-client {--owner=} {--name=} {--redirect=*} {--scope=*}';
    protected $description = 'Provision an administrator-reviewed public OAuth client using S256 PKCE';

    public function handle(WorkflowDispatcher $workflows): int
    {
        $owner = User::where('email', $this->option('owner'))->first();
        if (!$owner?->is_active || !$owner->hasAnyRole(['admin', 'super_admin'])) {
            $this->error('An active administrator owner is required.');
            return self::FAILURE;
        }

        $name = trim((string) $this->option('name'));
        $redirects = $this->option('redirect');
        $scopes = array_values(array_unique($this->option('scope')));
        if ($name === '' || strlen($name) > 150 || !$redirects || !$scopes) {
            $this->error('Supply a name, exact redirect URI(s), and explicit registered business scopes.');
            return self::FAILURE;
        }

        try {
            $result = $workflows->execute('admin.oauthClients.create', new WorkflowInput([
                'name' => $name,
                'owner_id' => $owner->id,
                'redirect_uris' => array_values($redirects),
                'scopes' => $scopes,
            ], actor: $owner));
        } catch (\Throwable $error) {
            $this->error($error->getMessage());
            return self::FAILURE;
        }

        $this->line(json_encode([
            'client_id' => $result->data['client_id'],
            'redirect_uris' => $result->data['redirect_uris'],
            'scopes' => $result->data['scopes'],
            'authorization_url' => $result->data['authorization_url'],
            'token_url' => $result->data['token_url'],
            'pkce' => $result->data['pkce_method'],
        ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }
}
