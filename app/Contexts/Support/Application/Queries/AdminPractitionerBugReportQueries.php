<?php

namespace App\Contexts\Support\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\PractitionerBugReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminPractitionerBugReportQueries extends WorkflowHandler
{
    private function support(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin', 'support']), 403);
    }

    public function bugReportQuery(WorkflowInput $input): Builder
    {
        $this->support($input);
        return PractitionerBugReport::query()->with('practitioner:id,name')->orderByDesc('created_at');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->support($input);
        $filters = $input->validate([
            'status' => 'sometimes|in:open,triaged,in_progress,resolved,closed,wont_fix',
            'severity' => 'sometimes|in:critical,high,medium,low',
            'search' => 'sometimes|string|max:120',
        ]);
        $query = $this->bugReportQuery($input);
        foreach (['status', 'severity'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        if (isset($filters['search'])) $query->where(fn (Builder $builder) => $builder->where('title', 'like', '%'.$filters['search'].'%')->orWhere('product_slug', 'like', '%'.$filters['search'].'%'));
        $page = $input->paginate($query);

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (PractitionerBugReport $report) => [
                'id' => $report->id,
                'practitioner_id' => $report->practitioner_id,
                'practitioner_name' => $report->practitioner?->name,
                'product_slug' => $report->product_slug,
                'title' => $report->title,
                'severity' => $report->severity,
                'status' => $report->status,
                'has_screenshot' => (bool) ($report->screenshot_path || $report->screenshot_url),
                'created_at' => $report->created_at?->toIso8601String(),
            ])->values()->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()],
        ]);
    }
}
