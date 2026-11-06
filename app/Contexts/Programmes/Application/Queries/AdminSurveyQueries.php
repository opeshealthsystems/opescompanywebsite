<?php

namespace App\Contexts\Programmes\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Database\Eloquent\Builder;

final class AdminSurveyQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function surveyItem(Survey $survey): array
    {
        return $survey->only(['id', 'title', 'title_fr', 'description', 'description_fr', 'audience', 'status', 'starts_at', 'ends_at', 'created_at', 'updated_at']);
    }

    private function questionItem(SurveyQuestion $question): array
    {
        return $question->only(['id', 'survey_id', 'question', 'question_fr', 'type', 'options', 'options_fr', 'is_required', 'sort_order', 'created_at', 'updated_at']);
    }

    public function surveyQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return Survey::query()->withCount(['responses', 'questions'])->orderByDesc('created_at');
    }

    public function surveys(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:'.implode(',', array_keys(Survey::statusOptions())),
            'audience' => 'sometimes|in:'.implode(',', array_keys(Survey::audienceOptions())), 'search' => 'sometimes|nullable|string|max:150']);
        $query = $this->surveyQuery($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['audience'])) $query->where('audience', $filters['audience']);
        if (!empty($filters['search'])) { $search = trim($filters['search']); $query->where(fn ($q) => $q->where('title', 'like', '%'.$search.'%')->orWhere('title_fr', 'like', '%'.$search.'%')); }
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (Survey $survey) => $this->surveyItem($survey) + [
            'response_count' => $survey->responses_count, 'question_count' => $survey->questions_count,
        ])->all(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function survey(WorkflowInput $input, Survey $survey): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['survey' => $this->surveyItem($survey) + [
            'response_count' => $survey->responses()->count(), 'question_count' => $survey->questions()->count(),
        ]]);
    }

    public function questions(WorkflowInput $input, Survey $survey): WorkflowResult
    {
        $this->admin($input);
        $page = $input->paginate($this->questionQuery($input, $survey));
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (SurveyQuestion $question) => $this->questionItem($question))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function questionQuery(WorkflowInput $input, Survey $survey): Builder
    {
        $this->admin($input);

        return $survey->questions()->getQuery()->orderBy('sort_order')->orderBy('id');
    }

    public function question(WorkflowInput $input, Survey $survey, SurveyQuestion $question): WorkflowResult
    {
        $this->admin($input);
        if ((int) $question->survey_id !== (int) $survey->id) abort(404);
        return WorkflowResult::payload(['question' => $this->questionItem($question)]);
    }
}
