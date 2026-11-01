<?php

namespace App\Contexts\Programmes\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AdminSurveyCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function surveyValues(WorkflowInput $input, bool $update = false): array
    {
        return $input->validate([
            'title' => ($update ? 'sometimes|required|' : 'required|').'string|max:200',
            'title_fr' => 'sometimes|nullable|string|max:200', 'description' => 'sometimes|nullable|string|max:20000',
            'description_fr' => 'sometimes|nullable|string|max:20000',
            'audience' => [...($update ? ['sometimes', 'required'] : ['required']), Rule::in(array_keys(Survey::audienceOptions()))],
            'starts_at' => 'sometimes|nullable|date', 'ends_at' => 'sometimes|nullable|date',
            'status' => 'prohibited', 'id' => 'prohibited', 'response_count' => 'prohibited',
        ]);
    }

    private function assertDateWindow(array $data, ?Survey $survey = null): void
    {
        $start = $data['starts_at'] ?? $survey?->starts_at;
        $end = $data['ends_at'] ?? $survey?->ends_at;
        if ($start && $end && \Illuminate\Support\Carbon::parse($end)->lt(\Illuminate\Support\Carbon::parse($start))) {
            throw ValidationException::withMessages(['ends_at' => 'The survey end time must be at or after the start time.']);
        }
    }

    private function assertDraftWithoutResponses(Survey $survey): void
    {
        if ($survey->status !== 'draft') throw ValidationException::withMessages(['survey' => 'Only draft surveys can be edited.']);
        if ($survey->responses()->exists()) throw ValidationException::withMessages(['survey' => 'Surveys with response records cannot be structurally changed.']);
    }

    public function createSurvey(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->surveyValues($input);
        $this->assertDateWindow($data);
        $record = DB::transaction(function () use ($data, $actor) {
            $record = Survey::create($data + ['status' => 'draft']);
            DomainOutbox::append('event', 'programmes.survey_created.v1', ['references' => [['resource' => 'Survey', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function updateSurvey(WorkflowInput $input, Survey $survey): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->surveyValues($input, true);
        if (!$data) throw ValidationException::withMessages(['survey' => 'Provide at least one editable draft-survey field.']);
        $record = DB::transaction(function () use ($survey, $data, $actor) {
            $record = Survey::query()->lockForUpdate()->findOrFail($survey->id);
            $this->assertDraftWithoutResponses($record);
            $this->assertDateWindow($data, $record);
            $record->update($data);
            DomainOutbox::append('event', 'programmes.survey_updated.v1', ['references' => [['resource' => 'Survey', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function deleteSurvey(WorkflowInput $input, Survey $survey): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($survey, $actor) {
            $record = Survey::query()->lockForUpdate()->findOrFail($survey->id);
            $this->assertDraftWithoutResponses($record);
            $id = $record->id; $record->delete();
            DomainOutbox::append('event', 'programmes.survey_deleted.v1', ['references' => [['resource' => 'Survey', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }

    public function activateSurvey(WorkflowInput $input, Survey $survey): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($survey, $actor) {
            $record = Survey::query()->lockForUpdate()->findOrFail($survey->id);
            if ($record->status === 'active') return $record;
            if ($record->status !== 'draft') throw ValidationException::withMessages(['status' => 'Closed surveys cannot be reactivated.']);
            if (!$record->questions()->exists()) throw ValidationException::withMessages(['questions' => 'Add at least one question before activating the survey.']);
            if ($record->ends_at && $record->ends_at->lte(now())) throw ValidationException::withMessages(['ends_at' => 'The survey end time must be in the future.']);
            $record->update(['status' => 'active']);
            DomainOutbox::append('event', 'programmes.survey_activated.v1', ['references' => [['resource' => 'Survey', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Survey activated.');
    }

    public function closeSurvey(WorkflowInput $input, Survey $survey): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($survey, $actor) {
            $record = Survey::query()->lockForUpdate()->findOrFail($survey->id);
            if ($record->status === 'closed') return $record;
            if ($record->status !== 'active') throw ValidationException::withMessages(['status' => 'Only active surveys can be closed.']);
            $record->update(['status' => 'closed']);
            DomainOutbox::append('event', 'programmes.survey_closed.v1', ['references' => [['resource' => 'Survey', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Survey closed.');
    }

    private function questionValues(WorkflowInput $input, ?SurveyQuestion $question = null): array
    {
        $update = $question !== null;
        $data = $input->validate([
            'question' => ($update ? 'sometimes|required|' : 'required|').'string|max:500',
            'question_fr' => 'sometimes|nullable|string|max:500',
            'type' => [...($update ? ['sometimes', 'required'] : ['required']), Rule::in(array_keys(SurveyQuestion::typeOptions()))],
            'options' => 'sometimes|nullable|array|min:2|max:50', 'options.*' => 'required|string|max:200|distinct:strict',
            'options_fr' => 'sometimes|nullable|array|min:2|max:50', 'options_fr.*' => 'required|string|max:200|distinct:strict',
            'is_required' => 'sometimes|boolean', 'sort_order' => 'sometimes|integer|min:0|max:65535',
            'survey_id' => 'prohibited', 'id' => 'prohibited',
        ]);
        $type = $data['type'] ?? $question?->type;
        $options = $data['options'] ?? $question?->options;
        $optionsFr = $data['options_fr'] ?? $question?->options_fr;
        if ($type === 'multiple_choice') {
            if (!is_array($options) || count($options) < 2) throw ValidationException::withMessages(['options' => 'Multiple-choice questions require at least two choices.']);
        } elseif (!empty($data['options']) || !empty($data['options_fr'])) {
            throw ValidationException::withMessages(['options' => 'Choices are only valid for multiple-choice questions.']);
        } elseif ($question && isset($data['type']) && $type !== 'multiple_choice') {
            $data['options'] = null; $data['options_fr'] = null;
        }
        if ($type === 'multiple_choice' && is_array($optionsFr) && count($optionsFr) > 0 && count($optionsFr) !== count($options ?? [])) {
            throw ValidationException::withMessages(['options_fr' => 'Provide a translated choice for every default-language choice.']);
        }
        return $data;
    }

    private function assertQuestionEditable(Survey $survey, ?SurveyQuestion $question = null): void
    {
        if ($survey->status !== 'draft') throw ValidationException::withMessages(['survey' => 'Questions can only be changed while their survey is a draft.']);
        if ($survey->responses()->exists()) throw ValidationException::withMessages(['survey' => 'Questions cannot change after response records exist.']);
        if ($question && (int) $question->survey_id !== (int) $survey->id) abort(404);
    }

    public function createQuestion(WorkflowInput $input, Survey $survey): WorkflowResult
    {
        $actor = $this->admin($input); $this->assertQuestionEditable($survey); $data = $this->questionValues($input);
        $record = DB::transaction(function () use ($survey, $data, $actor) {
            $parent = Survey::query()->lockForUpdate()->findOrFail($survey->id); $this->assertQuestionEditable($parent);
            $record = $parent->questions()->create($data + ['is_required' => true, 'sort_order' => 0]);
            DomainOutbox::append('event', 'programmes.survey_question_created.v1', ['references' => [['resource' => 'Survey', 'id' => $parent->id], ['resource' => 'SurveyQuestion', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function updateQuestion(WorkflowInput $input, Survey $survey, SurveyQuestion $question): WorkflowResult
    {
        $actor = $this->admin($input); $this->assertQuestionEditable($survey, $question); $data = $this->questionValues($input, $question);
        if (!$data) throw ValidationException::withMessages(['question' => 'Provide at least one editable question field.']);
        $record = DB::transaction(function () use ($survey, $question, $data, $actor) {
            $parent = Survey::query()->lockForUpdate()->findOrFail($survey->id); $this->assertQuestionEditable($parent);
            $record = SurveyQuestion::query()->where('survey_id', $parent->id)->lockForUpdate()->findOrFail($question->id);
            $record->update($data);
            DomainOutbox::append('event', 'programmes.survey_question_updated.v1', ['references' => [['resource' => 'Survey', 'id' => $parent->id], ['resource' => 'SurveyQuestion', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function deleteQuestion(WorkflowInput $input, Survey $survey, SurveyQuestion $question): WorkflowResult
    {
        $actor = $this->admin($input); $this->assertQuestionEditable($survey, $question);
        $id = DB::transaction(function () use ($survey, $question, $actor) {
            $parent = Survey::query()->lockForUpdate()->findOrFail($survey->id); $this->assertQuestionEditable($parent);
            $record = SurveyQuestion::query()->where('survey_id', $parent->id)->lockForUpdate()->findOrFail($question->id);
            if ($record->answers()->exists()) throw ValidationException::withMessages(['question' => 'Questions with recorded answers must be retained.']);
            $id = $record->id; $record->delete();
            DomainOutbox::append('event', 'programmes.survey_question_deleted.v1', ['references' => [['resource' => 'Survey', 'id' => $parent->id], ['resource' => 'SurveyQuestion', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
