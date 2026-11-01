<?php

namespace App\Contexts\Documents\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminDocumentQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    public function documentQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return Document::query()->with(['template:id,name,type', 'issuer:id,name'])->orderByDesc('created_at');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'type' => 'sometimes|in:receipt,letterhead,contract_employee,contract_business',
            'status' => 'sometimes|in:draft,sent,pending_signature,signed,voided',
            'search' => 'sometimes|string|max:120',
        ]);
        $query = $this->documentQuery($input);
        foreach (['type', 'status'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        if (isset($filters['search'])) $query->where(fn (Builder $builder) => $builder->where('reference_number', 'like', '%'.$filters['search'].'%')->orWhere('title', 'like', '%'.$filters['search'].'%')->orWhere('addressee_name', 'like', '%'.$filters['search'].'%'));
        $page = $input->paginate($query);

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (Document $document) => [
                'id' => $document->id,
                'reference_number' => $document->reference_number,
                'title' => $document->title,
                'type' => $document->type,
                'template_name' => $document->template?->name,
                'addressee_name' => $document->addressee_name,
                'status' => $document->status,
                'requires_signature' => $document->requires_signature,
                'valid_until' => $document->valid_until?->toDateString(),
                'issued_by' => $document->issued_by,
                'issuer_name' => $document->issuer?->name,
                'created_at' => $document->created_at?->toIso8601String(),
            ])->values()->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()],
        ]);
    }
}
