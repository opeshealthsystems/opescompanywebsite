<?php

namespace App\Console\Commands;

use App\Mail\TrainingExpiryWarning;
use App\Models\TrainingRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTrainingExpiryWarnings extends Command
{
    protected $signature   = 'training:send-expiry-warnings';
    protected $description = 'Email employees whose training certifications expire in 30 or 7 days';

    public function handle(): int
    {
        $thresholds = [30, 7];
        $sent = 0;

        foreach ($thresholds as $days) {
            $target = now()->addDays($days)->toDateString();

            $records = TrainingRecord::with('employee')
                ->where('status', 'completed')
                ->whereDate('expires_at', $target)
                ->whereHas('employee')
                ->get();

            foreach ($records as $training) {
                $email = $training->employee->email ?? null;
                if (! $email) {
                    continue;
                }
                Mail::to($email)->queue(new TrainingExpiryWarning($training, $days));
                $this->line("  Queued {$days}-day warning → {$email} ({$training->title})");
                $sent++;
            }
        }

        $this->info("Done. {$sent} training expiry warning(s) queued.");
        return Command::SUCCESS;
    }
}
