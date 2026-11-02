<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Mail\TrainingExpiryWarning;
use App\Models\TrainingRecord;
use App\Models\User;
use App\Notifications\FeedEntry;
use Illuminate\Support\Facades\Mail;

/** Scheduled application use case for training certification expiry reminders. */
final class SendTrainingExpiryWarnings
{
    public function execute(): int
    {
        $sent = 0;
        foreach ([30, 7] as $days) {
            $target = now()->addDays($days)->toDateString();
            $records = TrainingRecord::with('employee')->where('status', 'completed')->whereDate('expires_at', $target)
                ->whereHas('employee')->get();

            foreach ($records as $training) {
                $email = $training->employee->email ?? null;
                if (!$email) continue;

                Mail::to($email)->queue(new TrainingExpiryWarning($training, $days));
                User::where('email', $email)->first()?->notify(new FeedEntry(
                    'hr.training_expiry', 'Training expiring soon', $training->title . ' expires in ' . $days . ' days.', 'academic-cap', null,
                ));
                $sent++;
            }
        }
        return $sent;
    }
}
