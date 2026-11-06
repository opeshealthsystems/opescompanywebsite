<?php

namespace App\Console\Commands;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;

final class ManageApiClients extends Command
{
    protected $signature = 'api:client {action : create, rotate, revoke, limit or list} {--name=} {--owner= : Active admin email acting on this command} {--client= : Existing client UUID} {--scope=* : Explicit approved scope; repeat for each scope} {--daily-limit= : Positive request count or none} {--monthly-limit= : Positive request count or none}';
    protected $description = 'Provision approved machine clients; credentials are saved only to a private file';

    public function handle(WorkflowDispatcher $workflows): int
    {
        $action = $this->argument('action');
        if ($action === 'list') {
            $actor = User::query()->where('email', $this->option('owner'))->first();
            if (!$this->activeAdmin($actor)) {
                $this->error('An active admin owner is required for the read-only listing.');
                return self::FAILURE;
            }
            $result = $workflows->execute('admin.apiClients.index', new WorkflowInput(['per_page' => 100], actor: $actor));
            $this->line(json_encode($result->data['items'], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            return self::SUCCESS;
        }

        if (!in_array($action, ['create', 'rotate', 'revoke', 'limit'], true)) {
            $this->error('Unknown action');
            return self::FAILURE;
        }

        $owner = null;
        if ($action === 'create') {
            $owner = User::where('email', $this->option('owner'))->first();
            $scopes = array_values(array_unique($this->option('scope')));
            if (!$this->activeAdmin($owner) || !$this->option('name') || mb_strlen($this->option('name')) > 100
                || !$scopes || array_diff($scopes, ['catalogue:read', 'integration:read'])) {
                $this->error('An active admin owner, name and explicit supported scopes are required.');
                return self::FAILURE;
            }
        } else {
            // Resolve the acting administrator from the owning admin so the same shared
            // role and active-account checks run for command and HTTP callers.
            $existingClient = Passport::client()->newQuery()->whereKey($this->option('client'))
                ->with('owner')->first();
            if (!$existingClient || $existingClient->revoked) {
                $this->error($existingClient?->revoked ? 'Revoked clients cannot be rotated' : 'Only confidential machine clients can be rotated');
                return self::FAILURE;
            }
            if (in_array($action, ['rotate', 'limit'], true) && !$existingClient->hasGrantType('client_credentials')) {
                $this->error('Only confidential machine clients can be managed');
                return self::FAILURE;
            }
            $owner = $existingClient->owner;
            if (!$this->activeAdmin($owner)) {
                $this->error('The client must belong to an active administrator.');
                return self::FAILURE;
            }
        }

        $quotaLimits = null;
        if ($action === 'limit') {
            $daily = $this->option('daily-limit');
            $monthly = $this->option('monthly-limit');
            if ($daily === null || $monthly === null
                || ($daily !== 'none' && (!ctype_digit((string) $daily) || (int) $daily < 1 || (int) $daily > 1000000000))
                || ($monthly !== 'none' && (!ctype_digit((string) $monthly) || (int) $monthly < 1 || (int) $monthly > 1000000000))) {
                $this->error('Set both --daily-limit and --monthly-limit to positive integers or none.');
                return self::FAILURE;
            }
            $quotaLimits = [
                'daily_request_limit' => $daily === 'none' ? null : (int) $daily,
                'monthly_request_limit' => $monthly === 'none' ? null : (int) $monthly,
            ];
        }

        $path = null;
        $file = null;
        if ($action !== 'revoke') {
            $directory = storage_path('app/private/api-credentials');
            if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
                $this->error('Unable to create private credentials directory.');
                return self::FAILURE;
            }
            $path = $directory.'/'.Str::uuid().'.json';
            $file = fopen($path, 'x');
            if (!$file) {
                $this->error('Unable to reserve private credentials file.');
                return self::FAILURE;
            }
            chmod($path, 0600);
        }

        try {
            $input = $action === 'create'
                ? new WorkflowInput(['name' => $this->option('name'), 'owner_id' => $owner->id,
                    'scopes' => array_values(array_unique($this->option('scope')))], actor: $owner)
                : new WorkflowInput($quotaLimits ?? [], ['client' => (string) $this->option('client')], $owner);
            $operation = match ($action) {
                'create' => 'admin.apiClients.create',
                'rotate' => 'admin.apiClients.rotate',
                'revoke' => 'admin.apiClients.revoke',
                'limit' => 'admin.apiClients.setQuota',
            };
            $result = $workflows->execute($operation, $input);

            if ($file) {
                $json = json_encode([
                    'client_id' => $result->data['client_id'],
                    'client_secret' => $result->data['client_secret'],
                    'token_url' => $result->data['token_url'],
                    'scopes' => $result->data['scopes'],
                ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
                if (fwrite($file, $json) !== strlen($json) || !fflush($file)) {
                    throw new \RuntimeException('Private credentials write failed');
                }
            }

            $this->line(json_encode([
                'client_id' => $result->data['client_id'],
                'action' => $action,
                'daily_request_limit' => $result->data['daily_request_limit'] ?? null,
                'monthly_request_limit' => $result->data['monthly_request_limit'] ?? null,
                'credential_file' => $path,
            ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            return self::SUCCESS;
        } catch (\Throwable $error) {
            if ($path && is_file($path)) unlink($path);
            $this->error($error->getMessage());
            return self::FAILURE;
        } finally {
            if ($file) fclose($file);
        }
    }

    private function activeAdmin(?User $user): bool
    {
        return $user?->is_active && $user->hasAnyRole(['admin', 'super_admin']);
    }
}
