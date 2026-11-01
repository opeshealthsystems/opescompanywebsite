<?php

namespace App\Contexts\CustomerServices\Application\Queries;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
class CustomerSurveyQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $surveys = $workflowInput->collect(\App\Models\Survey::where('status', 'active')->whereIn('audience', ['customers', 'all']));
        $myResponseIds = \App\Models\SurveyResponse::where('user_id', \App\Contexts\Shared\Application\CurrentWorkflow::id())->whereIn('survey_id', $surveys->pluck('id'))->whereNotNull('submitted_at')->pluck('survey_id')->toArray();
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_surveycontroller_index_1', compact('surveys', 'myResponseIds'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $locale, \App\Models\Survey $survey): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless(in_array($survey->audience, ['customers', 'all']), 403);
        $survey->load('questions');
        $response = \App\Models\SurveyResponse::firstOrNew(['survey_id' => $survey->id, 'user_id' => \App\Contexts\Shared\Application\CurrentWorkflow::id()]);
        $existingAnswers = $response->answers()->pluck('answer_text', 'question_id')->toArray();
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_surveycontroller_show_1', compact('survey', 'response', 'existingAnswers'));
    }
}
