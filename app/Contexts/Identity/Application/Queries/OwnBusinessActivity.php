<?php

namespace App\Contexts\Identity\Application\Queries;

use App\Contexts\Shared\Application\WorkflowInput;
use Illuminate\Support\Facades\DB;

/** Read projection populated by the versioned event consumer, not transaction tables. */
final class OwnBusinessActivity
{
    public function execute(WorkflowInput $input): array
    {
        abort_unless($input->user()?->is_active && in_array('identity:read', $input->scopes, true), 403);
        $page = $input->paginate(DB::table('business_activity')->where('actor_id', $input->user()->id)->orderByDesc('occurred_at'), 20);
        return ['data' => array_map(fn ($row) => ['event_id' => $row->id, 'operation' => $row->operation,
            'references' => json_decode($row->references, true, 512, JSON_THROW_ON_ERROR), 'occurred_at' => $row->occurred_at], $page->items()),
            'meta' => ['page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total()]];
    }
}
