<?php

namespace App\Console\Commands;

use App\Contexts\Workforce\Application\Commands\SendTrainingExpiryWarnings as SendTrainingExpiryWarningsUseCase;
use Illuminate\Console\Command;

class SendTrainingExpiryWarnings extends Command
{
    protected $signature = 'training:send-expiry-warnings';
    protected $description = 'Email employees whose training certifications expire in 30 or 7 days';

    public function handle(SendTrainingExpiryWarningsUseCase $warnings): int
    {
        $this->info('Done. ' . $warnings->execute() . ' training expiry warning(s) queued.');
        return self::SUCCESS;
    }
}
