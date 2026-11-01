<?php
namespace App\Console\Commands;

use App\Services\WebsiteFormMailer;
use Illuminate\Console\Command;

class DeliverWebsiteForms extends Command
{
    protected $signature = 'website-forms:deliver {--recover : Forward stored submissions without successful delivery records} {--retry-only : Process pending and safely retryable deliveries} {--retry-uncertain : Explicitly resend ambiguous attempts after checking the inbox; duplicates are possible} {--limit=100}';
    protected $description = 'Deliver website enquiries to the contact inbox with persistent delivery tracking';

    public function handle(WebsiteFormMailer $mailer): int
    {
        $directory = dirname(config('website_forms.ledger_path'));
        if (!is_dir($directory)) mkdir($directory, 0700, true);
        $lock = fopen($directory.'/website-form-retry.lock', 'c');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) return self::SUCCESS;
        try {
            return $this->runDelivery($mailer);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function runDelivery(WebsiteFormMailer $mailer): int
    {
        $results = ['sent' => 0, 'already-sent' => 0, 'failed' => 0, 'deferred' => 0, 'uncertain' => 0];
        $remaining = max(1, (int) $this->option('limit'));
        if ($this->option('recover')) {
            foreach (WebsiteFormMailer::MODELS as $model) {
                foreach ($model::orderBy('id')->cursor() as $submission) {
                    $result = $mailer->deliver($submission, true, (bool) $this->option('retry-uncertain'));
                    $results[$result]++;
                    if ($result !== 'already-sent' && --$remaining <= 0) break 2;
                }
            }
        } else {
            $due = \App\Models\WebsiteFormDelivery::where(function ($query) {
                    $query->where(function ($ready) {
                        $ready->whereIn('status', ['pending', 'failed'])->where(fn ($time) => $time->whereNull('next_attempt_at')->orWhere('next_attempt_at', '<=', now()));
                    })->orWhere(fn ($stale) => $stale->where('status', 'processing')->where('locked_at', '<=', now()->subMinutes(2)))
                        ->orWhere('status', 'uncertain');
                })->orderByRaw("CASE WHEN status = 'uncertain' THEN 1 ELSE 0 END")->orderBy('id')->limit($remaining)->get();
            foreach ($due as $entry) {
                if (!in_array($entry->submission_type, WebsiteFormMailer::MODELS, true)) continue;
                if ($submission = $entry->submission_type::find($entry->submission_id)) {
                    $result = $entry->event_type === WebsiteFormMailer::QUOTE_RECEIPT_EVENT && $submission instanceof \App\Models\Lead
                        ? $mailer->deliverQuoteReceipt($submission, true, (bool) $this->option('retry-uncertain'))
                        : $mailer->deliver($submission, true, (bool) $this->option('retry-uncertain'));
                    $results[$result]++;
                    if (--$remaining <= 0) break;
                }
            }
        }
        $this->line(json_encode(['recipient' => config('website_forms.recipient'), 'results' => $results]));
        return $results['failed'] + $results['uncertain'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
