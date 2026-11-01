<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\InvoiceTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminInvoiceTemplateQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function item(InvoiceTemplate $record): array
    {
        return $record->only(['id', 'name', 'customer_id', 'client_name', 'client_email', 'frequency', 'next_due_date', 'end_date',
            'payment_terms_days', 'currency', 'tax_rate', 'line_items', 'notes', 'is_active', 'issued_by', 'created_at', 'updated_at']);
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return InvoiceTemplate::query()->with(['customer', 'issuer'])->orderBy('next_due_date');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['active' => 'sometimes|boolean', 'search' => 'sometimes|nullable|string|max:150']);
        $query = $this->query($input);
        if (isset($filters['active'])) $query->where('is_active', $filters['active']);
        if (!empty($filters['search'])) $query->where('name', 'like', '%'.trim($filters['search']).'%');
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (InvoiceTemplate $record) => $this->item($record))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, InvoiceTemplate $invoiceTemplate): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['invoice_template' => $this->item($invoiceTemplate)]);
    }
}
