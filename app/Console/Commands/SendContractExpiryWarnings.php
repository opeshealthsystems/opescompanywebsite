<?php

namespace App\Console\Commands;

use App\Contexts\Publishing\Application\Commands\SendContractExpiryWarnings as SendContractExpiryWarningsUseCase;
use Illuminate\Console\Command;

class SendContractExpiryWarnings extends Command
{
    protected $signature = 'contracts:send-expiry-warnings';
    protected $description = 'Email leads whose contracts expire in 30 or 7 days';

    public function handle(SendContractExpiryWarningsUseCase $warnings): int
    {
        $this->info('Done. ' . $warnings->execute() . ' contract expiry warning(s) queued.');
        return self::SUCCESS;
    }
}
