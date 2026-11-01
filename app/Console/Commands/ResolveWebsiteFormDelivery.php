<?php

namespace App\Console\Commands;

use App\Models\WebsiteFormDelivery;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResolveWebsiteFormDelivery extends Command
{
    protected $signature = 'website-forms:resolve {event : Delivery event UUID} {--sent : Confirm this event was found in the inbox; mark sent without sending again}';
    protected $description = 'Inspect an ambiguous delivery or record operator-confirmed inbox receipt';

    public function handle(): int
    {
        return DB::transaction(function () {
            $row = WebsiteFormDelivery::where('event_id', $this->argument('event'))->lockForUpdate()->first();
            if (!$row) { $this->error('Delivery event not found'); return self::FAILURE; }
            if ($this->option('sent')) {
                if ($row->status !== 'uncertain') { $this->error('Only uncertain deliveries may be resolved'); return self::FAILURE; }
                $row->update(['status' => 'sent', 'sent_at' => now(), 'error_type' => null, 'next_attempt_at' => null]);
            }
            $this->line(json_encode(['event_id' => $row->event_id, 'status' => $row->status, 'attempts' => $row->attempts]));
            return self::SUCCESS;
        });
    }
}
