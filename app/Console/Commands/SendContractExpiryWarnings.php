<?php

namespace App\Console\Commands;

use App\Mail\ContractExpiryWarning;
use App\Models\Contract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendContractExpiryWarnings extends Command
{
    protected $signature   = 'contracts:send-expiry-warnings';
    protected $description = 'Email leads whose contracts expire in 30 or 7 days';

    public function handle(): int
    {
        $thresholds = [30, 7];
        $sent = 0;

        foreach ($thresholds as $days) {
            $target = now()->addDays($days)->toDateString();

            $contracts = Contract::with('lead')
                ->where('status', 'active')
                ->where('auto_renew', false)
                ->whereDate('end_date', $target)
                ->whereHas('lead')
                ->get();

            foreach ($contracts as $contract) {
                $email = $contract->lead->email ?? null;
                if (! $email) {
                    continue;
                }
                Mail::to($email)->queue(new ContractExpiryWarning($contract, $days));
                $this->line("  Queued {$days}-day warning → {$email} ({$contract->reference})");
                $sent++;
            }
        }

        $this->info("Done. {$sent} contract expiry warning(s) queued.");
        return Command::SUCCESS;
    }
}
