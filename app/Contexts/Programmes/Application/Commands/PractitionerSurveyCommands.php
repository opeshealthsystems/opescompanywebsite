<?php

namespace App\Contexts\Programmes\Application\Commands;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
class PractitionerSurveyCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function submit(\App\Contexts\Shared\Application\WorkflowInput $request, $locale, \App\Models\Survey $survey): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless(in_array($survey->audience, ['practitioners', 'all']), 403);
        abort_unless($survey->acceptsResponses(), 404);
        $survey->load('questions');
        $response = \App\Models\SurveyResponse::firstOrCreate(['survey_id' => $survey->id, 'user_id' => \App\Contexts\Shared\Application\CurrentWorkflow::id()]);
        if ($response->isSubmitted()) {
            return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_surveycontroller_submit_1')->with('error', 'You have already submitted this survey.');
        }
        $rules = [];
        foreach ($survey->questions as $question) {
            $key = "q_{$question->id}";
            $rules[$key] = match ($question->type) {
                'rating' => 'nullable|integer|min:1|max:5',
                'multiple_choice' => 'nullable|string|in:' . implode(',', (array) ($question->options ?? [])),
                'yes_no' => 'nullable|string|in:yes,no',
                default => 'nullable|string|max:2000',
            };
            if ($question->is_required) {
                $rules[$key] = str_replace('nullable', 'required', $rules[$key]);
            }
        }
        $request->validate($rules);
        foreach ($survey->questions as $question) {
            $key = "q_{$question->id}";
            $value = $request->input($key);
            if ($question->is_required && empty($value)) {
                return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_surveycontroller_submit_2')->withErrors(["q_{$question->id}" => 'This question is required.'])->withInput();
            }
            $answerData = ['response_id' => $response->id, 'question_id' => $question->id];
            match ($question->type) {
                'rating' => $answerData['answer_rating'] = (int) $value,
                'multiple_choice' => $answerData['answer_choice'] = $value,
                'yes_no' => $answerData['answer_bool'] = $value === 'yes',
                default => $answerData['answer_text'] = $value,
            };
            \App\Models\SurveyAnswer::updateOrCreate(['response_id' => $response->id, 'question_id' => $question->id], $answerData);
        }
        $response->update(['submitted_at' => now()]);
        return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_surveycontroller_submit_3', ['locale' => $request->locale()])->with('success', 'Survey submitted. Thank you for your feedback!');
    }
}
