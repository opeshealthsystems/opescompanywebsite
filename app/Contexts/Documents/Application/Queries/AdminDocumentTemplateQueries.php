<?php

namespace App\Contexts\Documents\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\DocumentTemplate;
use App\Models\User;

/** Admin-only, paginated template catalogue exposed to API clients. */
final class AdminDocumentTemplateQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function item(DocumentTemplate $template): array
    {
        return ['id' => $template->id, 'name' => $template->name, 'type' => $template->type,
            'variables' => $template->variables ?? [], 'is_active' => (bool) $template->is_active,
            'updated_at' => $template->updated_at?->toISOString()];
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $page = $input->paginate(DocumentTemplate::query()->orderBy('name'));
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (DocumentTemplate $item) => $this->item($item))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, DocumentTemplate $template): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['template' => $this->item($template)]);
    }
}
