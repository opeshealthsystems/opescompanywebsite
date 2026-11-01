<?php

namespace App\Contexts\Validation\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Cohort;
use App\Models\User;
use App\Support\ValidationMetrics;

/** Admin validation dashboard projections shared by Filament and delegated API clients. */
final class AdminValidationMetricsQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    public function cohortProgress(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $page = $input->paginate(Cohort::query()->orderBy('name')->orderBy('id'));
        $metrics = app(ValidationMetrics::class);
        $items = $page->getCollection()->flatMap(fn (Cohort $cohort) => $metrics->cohortProgress($cohort))->values()->all();

        return WorkflowResult::payload(['items' => $items, 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }

    public function issueAnalytics(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['cohort_id' => 'sometimes|integer|exists:cohorts,id']);
        $cohort = isset($filters['cohort_id']) ? Cohort::query()->findOrFail($filters['cohort_id']) : null;

        return WorkflowResult::payload(['analytics' => app(ValidationMetrics::class)->issueAnalytics($cohort)]);
    }

    public function developerThroughput(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);

        return WorkflowResult::payload(['throughput' => app(ValidationMetrics::class)->developerThroughput()]);
    }

    public function practitionerLeaderboard(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'cohort_id' => 'sometimes|integer|exists:cohorts,id',
            'page' => 'sometimes|integer|min:1|max:10000',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);
        $cohort = isset($filters['cohort_id']) ? Cohort::query()->findOrFail($filters['cohort_id']) : null;
        $rows = app(ValidationMetrics::class)->practitionerLeaderboard($cohort);
        $page = (int) ($filters['page'] ?? 1);
        $perPage = (int) ($filters['per_page'] ?? 20);
        $items = array_slice($rows, ($page - 1) * $perPage, $perPage);
        $total = count($rows);

        return WorkflowResult::payload(['items' => $items, 'pagination' => [
            'current_page' => $page, 'per_page' => $perPage, 'total' => $total,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ]]);
    }
}
