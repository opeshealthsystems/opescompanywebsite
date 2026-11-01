<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

class ManageApiClients extends Command
{
    protected $signature = 'api:client {action : create, rotate, revoke or list} {--name=} {--owner= : Active admin email for a new client} {--client= : Existing client UUID} {--scope=* : Explicit approved scope; repeat for each scope}';
    protected $description = 'Provision approved machine clients; credentials are saved only to a private file';

    public function handle(ClientRepository $clients): int
    {
        $action = $this->argument('action');
        if ($action === 'list') {
            $this->line(Passport::client()->newQuery()->get()->map(fn ($client) => ['id' => $client->id, 'name' => $client->name, 'scopes' => $client->scopes, 'revoked' => $client->revoked])->toJson());
            return self::SUCCESS;
        }
        if (!in_array($action, ['create', 'rotate', 'revoke'], true)) { $this->error('Unknown action'); return self::FAILURE; }
        $owner = null;
        if ($action === 'create') {
            $owner = User::where('email', $this->option('owner'))->first();
            $scopes = array_values(array_unique($this->option('scope')));
            if (!$owner || !$owner->is_active || !$owner->hasAnyRole(['admin', 'super_admin']) || !$this->option('name') || mb_strlen($this->option('name')) > 100
                || !$scopes || array_diff($scopes, ['catalogue:read', 'integration:read'])) {
                $this->error('An active admin owner, name and explicit supported scopes are required.'); return self::FAILURE;
            }
        } elseif ($action === 'rotate') {
            $client = Passport::client()->newQuery()->find($this->option('client'));
            if (!$client || $client->revoked || !$client->hasGrantType('client_credentials')) {
                $this->error($client?->revoked ? 'Revoked clients cannot be rotated' : 'Only confidential machine clients can be rotated');
                return self::FAILURE;
            }
        }
        $path = null; $file = null;
        if ($action !== 'revoke') {
            $directory = storage_path('app/private/api-credentials');
            if (!is_dir($directory)) mkdir($directory, 0700, true);
            $path = $directory.'/'.Str::uuid().'.json';
            $file = fopen($path, 'x');
            if (!$file) throw new \RuntimeException('Unable to reserve private credentials file');
            chmod($path, 0600);
        }
        try {
            $id = DB::transaction(function () use ($action, $owner, $clients, $file) {
                if ($action === 'create') {
                    $client = $clients->createClientCredentialsGrantClient($this->option('name'));
                    $client->owner()->associate($owner);
                    $client->scopes = array_values(array_unique($this->option('scope')));
                } else {
                    $client = Passport::client()->newQuery()->whereKey($this->option('client'))->lockForUpdate()->firstOrFail();
                    if ($action === 'rotate' && $client->revoked) throw new \RuntimeException('Revoked clients cannot be rotated');
                    if ($action === 'rotate' && !$client->hasGrantType('client_credentials')) throw new \RuntimeException('Only confidential machine clients can be rotated');
                    $client->tokens()->update(['revoked' => true]);
                    if ($action === 'revoke') $client->revoked = true;
                    else $client->secret = Str::random(64);
                }
                $client->save();
                if ($file) {
                    $json = json_encode(['client_id' => $client->id, 'client_secret' => $client->plainSecret, 'token_url' => url('/api/v1/oauth/token'), 'scopes' => $client->scopes], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
                    if (fwrite($file, $json) !== strlen($json) || !fflush($file)) throw new \RuntimeException('Private credentials write failed');
                }
                return $client->id;
            });
            $this->line(json_encode(['client_id' => $id, 'action' => $action, 'credential_file' => $path]));
            return self::SUCCESS;
        } catch (\Throwable $error) {
            if ($file) { fclose($file); $file = null; }
            if ($path && is_file($path)) unlink($path);
            throw $error;
        } finally { if ($file) fclose($file); }
    }
}
