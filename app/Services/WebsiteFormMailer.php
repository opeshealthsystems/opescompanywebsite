<?php
namespace App\Services;

use App\Mail\WebsiteSubmissionReceived;
use App\Models\WebsiteFormDelivery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WebsiteFormMailer
{
    public const MODELS = [\App\Models\Lead::class, \App\Models\DemoRequest::class, \App\Models\PartnerApplication::class, \App\Models\TesterApplication::class];

    public function ensure(Model $submission): WebsiteFormDelivery
    {
        if (!in_array(get_class($submission), self::MODELS, true) || !$submission->exists) throw new \InvalidArgumentException('Unsupported website form');
        $recipient = config('website_forms.recipient');
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL) || strlen($recipient) > 191) throw new \RuntimeException('Invalid website form recipient');
        return WebsiteFormDelivery::firstOrCreate([
            'submission_type' => get_class($submission), 'submission_id' => $submission->getKey(), 'recipient' => $recipient,
        ], ['event_id' => (string) Str::uuid(), 'event_type' => 'website.submission.received.v1', 'status' => 'pending']);
    }

    /** Compatibility view for the recovery command; the private file is now an import source only. */
    public function records(): array
    {
        return WebsiteFormDelivery::orderBy('id')->get()->mapWithKeys(fn ($row) => [
            $row->submission_type.'|'.$row->submission_id.'|'.$row->recipient => [
                'type' => $row->submission_type, 'id' => $row->submission_id, 'recipient' => $row->recipient,
                'status' => $row->status, 'attempts' => $row->attempts, 'event_id' => $row->event_id,
            ],
        ])->all();
    }

    public function deliver(Model $submission, bool $recovered = false, bool $retryUncertain = false): string
    {
        $row = $this->ensure($submission);
        if ($row->status === 'sent') return 'already-sent';
        // An abandoned SMTP attempt may already have been accepted. Never silently reclaim it.
        WebsiteFormDelivery::whereKey($row->id)->where('status', 'processing')
            ->where('locked_at', '<=', now()->subMinutes(2))->update(['status' => 'uncertain', 'updated_at' => now()]);
        $row->refresh();
        if ($row->status === 'uncertain' && !$retryUncertain) return 'uncertain';
        $token = (string) Str::uuid();
        $claimable = ['pending', 'failed'];
        if ($retryUncertain) $claimable[] = 'uncertain';
        $claimed = WebsiteFormDelivery::whereKey($row->id)->whereIn('status', $claimable)
            ->where(fn ($query) => $query->whereNull('next_attempt_at')->orWhere('next_attempt_at', '<=', now()))
            ->update(['status' => 'processing', 'claim_token' => $token, 'locked_at' => now(), 'updated_at' => now(), 'attempts' => \Illuminate\Support\Facades\DB::raw('attempts + 1')]);
        if (!$claimed) return 'deferred';
        $row->refresh();
        $transportStarted = false;
        try {
            $pending = Mail::to($row->recipient);
            $transportStarted = true;
            $sent = $pending->send(new WebsiteSubmissionReceived($submission, $recovered, $row->event_id));
        } catch (\Throwable $error) {
            $status = $transportStarted ? 'uncertain' : 'failed';
            WebsiteFormDelivery::whereKey($row->id)->where('claim_token', $token)->where('status', 'processing')->update([
                'status' => $status, 'error_type' => get_class($error), 'updated_at' => now(),
                'next_attempt_at' => $transportStarted ? null : now()->addSeconds(min(3600, 60 * 2 ** min($row->attempts - 1, 6))),
            ]);
            Log::warning('Website form delivery requires recovery', ['delivery_id' => $row->id, 'status' => $status, 'error_type' => get_class($error)]);
            return $status;
        }
        $saved = WebsiteFormDelivery::whereKey($row->id)->where('claim_token', $token)->where('status', 'processing')->update([
            'status' => 'sent', 'sent_at' => now(), 'message_id' => $sent?->getSymfonySentMessage()->getMessageId(),
            'next_attempt_at' => null, 'error_type' => null, 'updated_at' => now(),
        ]);
        return $saved ? 'sent' : 'uncertain';
    }
}
