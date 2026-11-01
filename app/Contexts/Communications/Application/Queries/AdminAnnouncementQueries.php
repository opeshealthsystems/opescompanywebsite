<?php

namespace App\Contexts\Communications\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Announcement;

final class AdminAnnouncementQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); }
    private function item(Announcement $a): array { return ['id' => $a->id, 'title' => $a->title, 'body' => $a->body, 'audience' => $a->audience, 'is_pinned' => (bool) $a->is_pinned, 'is_active' => (bool) $a->is_active, 'author' => $a->author?->only(['id', 'name']), 'published_at' => $a->published_at?->toISOString()]; }
    public function index(WorkflowInput $input): WorkflowResult { $this->admin($input); $p = $input->paginate(Announcement::query()->with('author')->orderByDesc('is_pinned')->orderByDesc('published_at')); return WorkflowResult::payload(['items' => $p->getCollection()->map(fn ($a) => $this->item($a))->all(), 'pagination' => ['current_page' => $p->currentPage(), 'per_page' => $p->perPage(), 'total' => $p->total(), 'last_page' => $p->lastPage()]]); }
    public function show(WorkflowInput $input, Announcement $announcement): WorkflowResult { $this->admin($input); $announcement->loadMissing('author'); return WorkflowResult::payload(['announcement' => $this->item($announcement)]); }
}
