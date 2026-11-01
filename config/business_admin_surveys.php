<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$survey = ['name' => 'survey', 'kind' => 'model', 'class' => 'App\\Models\\Survey', 'key' => 'id'];
$question = ['name' => 'question', 'kind' => 'model', 'class' => 'App\\Models\\SurveyQuestion', 'key' => 'id'];
$commands = 'App\\Contexts\\Programmes\\Application\\Commands\\AdminSurveyCommands';
$queries = 'App\\Contexts\\Programmes\\Application\\Queries\\AdminSurveyQueries';
$def = fn (string $handler, string $method, array $parameters, bool $command, string $path, string $verb, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $parameters, 'command' => $command, 'context' => 'Programmes',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $command ? 'programmes:write' : 'programmes:read',
    'api_path' => $path, 'http_methods' => [$verb], 'source_controller' => 'SurveyResource', 'source_action' => $action,
];

return [
    'admin.programmes.surveys.index' => $def($queries, 'surveys', [$input], false, 'programmes/surveys', 'GET', 'index'),
    'admin.programmes.surveys.show' => $def($queries, 'survey', [$input, $survey], false, 'programmes/surveys/{survey}', 'GET', 'show'),
    'admin.programmes.surveys.create' => $def($commands, 'createSurvey', [$input], true, 'programmes/surveys', 'POST', 'create'),
    'admin.programmes.surveys.update' => $def($commands, 'updateSurvey', [$input, $survey], true, 'programmes/surveys/{survey}', 'PUT', 'update'),
    'admin.programmes.surveys.delete' => $def($commands, 'deleteSurvey', [$input, $survey], true, 'programmes/surveys/{survey}', 'DELETE', 'delete'),
    'admin.programmes.surveys.activate' => $def($commands, 'activateSurvey', [$input, $survey], true, 'programmes/surveys/{survey}/activate', 'POST', 'activate'),
    'admin.programmes.surveys.close' => $def($commands, 'closeSurvey', [$input, $survey], true, 'programmes/surveys/{survey}/close', 'POST', 'close'),
    'admin.programmes.surveyQuestions.index' => $def($queries, 'questions', [$input, $survey], false, 'programmes/surveys/{survey}/questions', 'GET', 'questions.index'),
    'admin.programmes.surveyQuestions.show' => $def($queries, 'question', [$input, $survey, $question], false, 'programmes/surveys/{survey}/questions/{question}', 'GET', 'questions.show'),
    'admin.programmes.surveyQuestions.create' => $def($commands, 'createQuestion', [$input, $survey], true, 'programmes/surveys/{survey}/questions', 'POST', 'questions.create'),
    'admin.programmes.surveyQuestions.update' => $def($commands, 'updateQuestion', [$input, $survey, $question], true, 'programmes/surveys/{survey}/questions/{question}', 'PUT', 'questions.update'),
    'admin.programmes.surveyQuestions.delete' => $def($commands, 'deleteQuestion', [$input, $survey, $question], true, 'programmes/surveys/{survey}/questions/{question}', 'DELETE', 'questions.delete'),
];
