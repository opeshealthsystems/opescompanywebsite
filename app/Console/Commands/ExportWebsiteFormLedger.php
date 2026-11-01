<?php

namespace App\Console\Commands;

use App\Models\WebsiteFormDelivery;
use Illuminate\Console\Command;

class ExportWebsiteFormLedger extends Command
{
    protected $signature = 'website-forms:export-ledger {--apply : Replace the private legacy file for a controlled source rollback}';
    protected $description = 'Export current database delivery history before rolling back mailer code; run in maintenance mode';

    public function handle(): int
    {
        if ($this->option('apply') && !app()->isDownForMaintenance() && !app()->environment('testing')) {
            $this->error('Put the application into maintenance mode before exporting.');
            return self::FAILURE;
        }
        if (WebsiteFormDelivery::whereIn('status', ['processing', 'uncertain'])->exists()) {
            $this->error('Resolve active or uncertain deliveries before rollback; export refused.');
            return self::FAILURE;
        }
        $records = [];
        foreach (WebsiteFormDelivery::orderBy('id')->cursor() as $row) {
            $records[$row->submission_type.'|'.$row->submission_id.'|'.$row->recipient] = [
                'type' => $row->submission_type, 'id' => $row->submission_id, 'recipient' => $row->recipient,
                'status' => $row->status, 'attempts' => $row->attempts,
                'sent_at' => $row->sent_at?->toIso8601String(), 'message_id' => $row->message_id,
                'next_attempt_at' => $row->next_attempt_at?->toIso8601String(),
            ];
        }
        if ($this->option('apply')) {
            $path = config('website_forms.ledger_path');
            if (!is_dir(dirname($path))) mkdir(dirname($path), 0700, true);
            $lock = fopen($path.'.lock', 'c');
            if (!$lock || !flock($lock, LOCK_EX)) throw new \RuntimeException('Unable to lock legacy ledger');
            try {
                if (is_file($path)) {
                    $backup = $path.'.before-export-'.now()->format('YmdHis').'-'.bin2hex(random_bytes(4));
                    if (!copy($path, $backup)) throw new \RuntimeException('Legacy backup failed');
                    chmod($backup, 0600);
                }
                $temporary = tempnam(dirname($path), 'delivery-export-');
                chmod($temporary, 0600);
                if (file_put_contents($temporary, json_encode($records, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)) === false || !rename($temporary, $path)) {
                    throw new \RuntimeException('Legacy export failed');
                }
            } finally { flock($lock, LOCK_UN); fclose($lock); }
        }
        $this->line(json_encode(['records' => count($records), 'applied' => (bool) $this->option('apply')]));
        return self::SUCCESS;
    }
}
