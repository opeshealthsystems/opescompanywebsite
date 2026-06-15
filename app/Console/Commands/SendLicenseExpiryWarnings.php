<?php

namespace App\Console\Commands;

use App\Mail\LicenseExpiryWarning;
use App\Models\License;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendLicenseExpiryWarnings extends Command
{
    protected $signature   = 'licenses:send-expiry-warnings';
    protected $description = 'Email customers whose licenses expire in 30 or 7 days';

    public function handle(): int
    {
        $thresholds = [30, 7];
        $sent = 0;

        foreach ($thresholds as $days) {
            $target = now()->addDays($days)->toDateString();

            $licenses = License::with('customer')
                ->where('status', 'active')
                ->whereDate('end_date', $target)
                ->whereHas('customer')
                ->get();

            foreach ($licenses as $license) {
                $email = $license->customer->email ?? null;
                if (!$email) {
                    continue;
                }
                Mail::to($email)->queue(new LicenseExpiryWarning($license, $days));
                $this->line("  Queued {$days}-day warning → {$email} ({$license->product_name})");
                $sent++;
            }
        }

        $this->info("Done. {$sent} expiry warning(s) queued.");
        return Command::SUCCESS;
    }
}
