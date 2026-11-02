<?php

namespace App\Console\Commands;

use App\Contexts\Licensing\Application\Commands\SendLicenseExpiryWarnings as SendLicenseExpiryWarningsUseCase;
use Illuminate\Console\Command;

class SendLicenseExpiryWarnings extends Command
{
    protected $signature = 'licenses:send-expiry-warnings';
    protected $description = 'Email customers whose licenses expire in 30 or 7 days';

    public function handle(SendLicenseExpiryWarningsUseCase $warnings): int
    {
        $this->info('Done. ' . $warnings->execute() . ' expiry warning(s) queued.');
        return self::SUCCESS;
    }
}
