<?php

namespace App\Console\Commands;

use App\Contexts\Shared\Infrastructure\DomainOutbox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class RecoverBusinessOutbox extends Command
{
    protected $signature = 'business:outbox {--limit=100} {--status}';
    protected $description = 'Recover pending domain events and messages; ambiguous external deliveries require review';

    public function handle(): int
    {
        if ($this->option('status')) {
            $this->line(json_encode(DB::table('business_outbox')->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')));
            return self::SUCCESS;
        }
        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT);
        if (!$limit || $limit < 1 || $limit > 1000) { $this->error('Limit must be between 1 and 1000.'); return self::FAILURE; }
        $ids = DB::table('business_outbox')->whereIn('status', ['pending', 'failed', 'processing'])->orderBy('created_at')->limit($limit)->pluck('id');
        $results = [];
        foreach ($ids as $id) { $status = DomainOutbox::deliver($id); $results[$status] = ($results[$status] ?? 0) + 1; }
        $this->line(json_encode(['processed' => $ids->count(), 'results' => $results]));
        return self::SUCCESS;
    }
}
