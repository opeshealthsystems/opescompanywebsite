<?php

namespace App\Contexts\Shared\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Announcement;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminDashboardReadQueries extends WorkflowHandler
{
    private function authorizedActor(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active, 403);
        return $actor;
    }

    public function ticketQuery(WorkflowInput $input): Builder
    {
        abort_unless($this->authorizedActor($input)->hasAnyRole(['super_admin', 'admin', 'support']), 403);
        return Ticket::with('customer')->whereIn('status', ['open', 'in_progress', 'pending_customer'])->orderByDesc('created_at');
    }

    public function tickets(WorkflowInput $input): WorkflowResult
    {
        $page = $input->paginate($this->ticketQuery($input), 8);
        return WorkflowResult::payload(['items' => $page->getCollection(), 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }

    public function leadQuery(WorkflowInput $input): Builder
    {
        abort_unless($this->authorizedActor($input)->hasAnyRole(['super_admin', 'admin']), 403);
        return Lead::whereIn('status', ['new', 'contacted'])->orderByDesc('created_at');
    }

    public function leads(WorkflowInput $input): WorkflowResult
    {
        $page = $input->paginate($this->leadQuery($input), 8);
        return WorkflowResult::payload(['items' => $page->getCollection(), 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }

    public function invoiceQuery(WorkflowInput $input): Builder
    {
        $actor = $this->authorizedActor($input);
        abort_unless($actor->hasPermissionTo('manage_accounting'), 403);
        return Invoice::with('customer')->whereIn('status', ['sent', 'overdue'])->orderByDesc('created_at');
    }

    public function invoices(WorkflowInput $input): WorkflowResult
    {
        $page = $input->paginate($this->invoiceQuery($input), 8);
        return WorkflowResult::payload(['items' => $page->getCollection(), 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }

    public function announcementQuery(WorkflowInput $input): Builder
    {
        $roles = $this->authorizedActor($input)->getRoleNames()->all();
        return Announcement::with('author:id,name')->where('is_active', true)
            ->where(fn (Builder $query) => $query->where('audience', 'all')->orWhereIn('audience', $roles))
            ->orderByDesc('is_pinned')->orderByDesc('published_at');
    }

    public function announcements(WorkflowInput $input): WorkflowResult
    {
        $page = $input->paginate($this->announcementQuery($input), 20);
        $items = $page->getCollection()->map(fn (Announcement $announcement) => [
            'id' => $announcement->id, 'title' => $announcement->title, 'body' => $announcement->body,
            'audience' => $announcement->audience, 'is_pinned' => (bool) $announcement->is_pinned,
            'author' => $announcement->author?->only(['id', 'name']), 'published_at' => $announcement->published_at?->toISOString(),
        ]);
        return WorkflowResult::payload(['items' => $items, 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }
}
