<?php

namespace App\Contexts\Communications\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Builder;

final class AdminEmailTemplateQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function item(EmailTemplate $template): array
    {
        return $template->only(['id', 'name', 'slug', 'type', 'subject', 'body', 'variables', 'is_active', 'created_at', 'updated_at']);
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return EmailTemplate::query()->orderBy('type')->orderBy('name');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['type' => 'sometimes|nullable|in:'.implode(',', array_keys(EmailTemplate::typeOptions())), 'is_active' => 'sometimes|boolean']);
        $query = $this->query($input);
        if (!empty($filters['type'])) $query->where('type', $filters['type']);
        if (array_key_exists('is_active', $filters)) $query->where('is_active', $filters['is_active']);
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (EmailTemplate $template) => $this->item($template))->all(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, EmailTemplate $template): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['email_template' => $this->item($template)]);
    }
}
