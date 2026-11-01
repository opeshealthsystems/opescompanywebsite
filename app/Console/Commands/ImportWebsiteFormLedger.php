<?php

namespace App\Console\Commands;

use App\Models\WebsiteFormDelivery;
use App\Services\WebsiteFormMailer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportWebsiteFormLedger extends Command
{
    protected $signature = 'website-forms:import-ledger {--apply : Persist validated legacy records; default is validation only}';
    protected $description = 'Reconcile the private legacy mail ledger without sending any email';

    public function handle(): int
    {
        $path = config('website_forms.ledger_path');
        if (!is_file($path)) {
            $this->error('Legacy ledger missing; import aborted.');
            return self::FAILURE;
        }
        $lock = fopen($path.'.lock', 'c');
        if (!$lock || !flock($lock, LOCK_EX)) throw new \RuntimeException('Unable to lock legacy ledger');
        try {
            $records = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($records)) throw new \RuntimeException('Invalid ledger');
            foreach ($records as $key => $record) {
                if (!in_array($record['type'] ?? '', WebsiteFormMailer::MODELS, true)
                    || !isset($record['id']) || !ctype_digit((string) $record['id'])
                    || !filter_var($record['recipient'] ?? '', FILTER_VALIDATE_EMAIL)
                    || strlen($record['recipient']) > 191
                    || !in_array($record['status'] ?? '', ['pending', 'sent', 'failed', 'sending'], true)
                    || $key !== $record['type'].'|'.$record['id'].'|'.$record['recipient']
                    || !$record['type']::whereKey($record['id'])->exists()) {
                    throw new \RuntimeException('Invalid or orphaned ledger entry; no records imported');
                }
                if ($record['status'] === 'sent' && empty($record['sent_at'])) throw new \RuntimeException('Successful legacy record has no timestamp');
                foreach (['sent_at', 'next_attempt_at'] as $date) {
                    if (!empty($record[$date])) \Carbon\Carbon::parse($record[$date]);
                }
            }
            if ($this->option('apply')) {
                DB::transaction(function () use ($records) {
                    foreach ($records as $record) {
                        $row = WebsiteFormDelivery::firstOrCreate([
                            'submission_type' => $record['type'], 'submission_id' => $record['id'], 'recipient' => $record['recipient'],
                        ], ['event_id' => (string) Str::uuid(), 'event_type' => 'website.submission.received.v1']);
                        if ($row->status === 'sent') continue;
                        if (!$row->wasRecentlyCreated && ($row->attempts > 0 || $row->status !== 'pending')) {
                            // Never overwrite attempts made after cutover with an older ledger snapshot.
                            continue;
                        }
                        $row->forceFill([
                            'status' => $record['status'] === 'sending' ? 'uncertain' : $record['status'],
                            'attempts' => max($row->attempts ?? 0, $record['attempts'] ?? 1),
                            'sent_at' => $record['sent_at'] ?? null, 'message_id' => $record['message_id'] ?? null,
                            'next_attempt_at' => $record['next_attempt_at'] ?? null,
                        ])->save();
                    }
                });
            }
            $this->line(json_encode(['validated' => count($records), 'applied' => (bool) $this->option('apply'),
                'legacy_sent' => count(array_filter($records, fn ($row) => $row['status'] === 'sent'))]));
            return self::SUCCESS;
        } finally { flock($lock, LOCK_UN); fclose($lock); }
    }
}
