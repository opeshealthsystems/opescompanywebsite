<?php

namespace App\Contexts\Validation\Application\Queries;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\FinalEvaluation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminFinalEvaluationQueries
{
    public function evaluationQuery(WorkflowInput $input): Builder
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        $filters = $input->validate([
            'cohort_member_id' => 'sometimes|integer|min:1',
            'rating' => 'sometimes|string|in:outstanding,strong,satisfactory,needs_improvement',
        ]);
        $query = FinalEvaluation::query()->with(['cohortMember.user', 'cohortMember.cohort', 'evaluator'])->latest('evaluated_at');
        if (isset($filters['cohort_member_id'])) $query->where('cohort_member_id', $filters['cohort_member_id']);
        if (isset($filters['rating'])) $query->where('rating', $filters['rating']);

        return $query;
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $query = $this->evaluationQuery($input);
        $page = $input->validate(['page' => 'sometimes|integer|min:1|max:10000', 'per_page' => 'sometimes|integer|min:1|max:100']);
        $items = $query->paginate((int) ($page['per_page'] ?? 25), ['*'], 'page', (int) ($page['page'] ?? 1));
        return WorkflowResult::payload([
            'items' => $items->getCollection()->map(fn (FinalEvaluation $record) => [
                'id' => $record->id,
                'cohort_member_id' => $record->cohort_member_id,
                'member_name' => $record->cohortMember?->user?->name,
                'cohort_name' => $record->cohortMember?->cohort?->name,
                'rating' => $record->rating,
                'assessment' => $record->assessment,
                'recommendation' => $record->recommendation,
                'metrics' => $record->metrics,
                'evaluator_id' => $record->evaluator_id,
                'evaluated_at' => $record->evaluated_at?->toISOString(),
            ])->values()->all(),
            'meta' => ['total' => $items->total(), 'page' => $items->currentPage(), 'per_page' => $items->perPage(), 'last_page' => $items->lastPage()],
        ]);
    }
}
