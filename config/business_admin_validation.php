<?php

$class = 'App\\Contexts\\Validation\\Application\\Commands\\AdminValidationCommands';
$issue = ['name' => 'issue', 'kind' => 'model', 'class' => 'App\\Models\\IssueReport', 'key' => 'id'];
$task = ['name' => 'task', 'kind' => 'model', 'class' => 'App\\Models\\DeveloperTask', 'key' => 'id'];
$evaluation = ['name' => 'evaluation', 'kind' => 'model', 'class' => 'App\\Models\\FinalEvaluation', 'key' => 'id'];
$certificate = ['name' => 'certificate', 'kind' => 'model', 'class' => 'App\\Models\\ValidationCertificate', 'key' => 'id'];
$weeklyReview = ['name' => 'weeklyReview', 'kind' => 'model', 'class' => 'App\\Models\\WeeklyReview', 'key' => 'id'];
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$definitions = [];
$add = function (string $name, string $method, string $path, array $params, string $source, string $action) use (&$definitions, $class): void {
    $definitions[$name] = ['handler' => $class, 'method' => $method, 'parameters' => $params, 'command' => true,
        'context' => 'Validation', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
        'scope' => 'validation:write', 'api_path' => $path, 'http_methods' => ['POST'],
        'source_controller' => $source, 'source_action' => $action];
};
$add('admin.validation.issues.clinicalReview', 'clinicalReview', 'validation/issues/{issue}/clinical-review', [$input, $issue], 'IssueReportResource', 'start_clinical_review');
$add('admin.validation.issues.sendToProductReview', 'sendToProductReview', 'validation/issues/{issue}/send-to-product-review', [$input, $issue], 'IssueReportResource', 'send_to_product_review');
$add('admin.validation.issues.productDecision', 'productDecision', 'validation/issues/{issue}/product-decision', [$input, $issue], 'IssueReportResource', 'product_decision');
$add('admin.validation.issues.close', 'closeIssue', 'validation/issues/{issue}/close', [$input, $issue], 'IssueReportResource', 'close');
$add('admin.validation.developerTasks.assign', 'assignDeveloperTask', 'validation/developer-tasks/{task}/assign', [$input, $task], 'DeveloperTaskResource', 'assign');
$add('admin.validation.developerTasks.start', 'startDeveloperTask', 'validation/developer-tasks/{task}/start', [$input, $task], 'DeveloperTaskResource', 'start');
$add('admin.validation.developerTasks.markFixed', 'fixDeveloperTask', 'validation/developer-tasks/{task}/mark-fixed', [$input, $task], 'DeveloperTaskResource', 'mark_fixed');
$add('admin.validation.developerTasks.reopen', 'reopenDeveloperTask', 'validation/developer-tasks/{task}/reopen', [$input, $task], 'DeveloperTaskResource', 'reopen');
$add('admin.validation.developerTasks.wontFix', 'wontFixDeveloperTask', 'validation/developer-tasks/{task}/wont-fix', [$input, $task], 'DeveloperTaskResource', 'wont_fix');
$add('admin.validation.finalEvaluations.issueCertificate', 'issueCertificate', 'validation/final-evaluations/{evaluation}/issue-certificate', [$input, $evaluation], 'FinalEvaluationResource', 'issue_certificate');
$add('admin.validation.finalEvaluations.create', 'createEvaluation', 'validation/final-evaluations', [$input], 'FinalEvaluationResource', 'create');
$add('admin.validation.finalEvaluations.update', 'updateEvaluation', 'validation/final-evaluations/{evaluation}', [$input, $evaluation], 'FinalEvaluationResource', 'update');
$add('admin.validation.certificates.inviteToCouncil', 'inviteToCouncil', 'validation/certificates/{certificate}/invite-to-council', [$input, $certificate], 'ValidationCertificateResource', 'invite_to_council');
$weeklyClass = 'App\\Contexts\\Validation\\Application\\Commands\\AdminWeeklyReviewCommands';
$definitions['admin.validation.weeklyReviews.create'] = ['handler' => $weeklyClass, 'method' => 'create', 'parameters' => [$input], 'command' => true,
    'context' => 'Validation', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'validation:write',
    'api_path' => 'validation/weekly-reviews', 'http_methods' => ['POST'], 'source_controller' => 'WeeklyReviewResource', 'source_action' => 'create'];
$definitions['admin.validation.weeklyReviews.update'] = ['handler' => $weeklyClass, 'method' => 'update', 'parameters' => [$input, $weeklyReview], 'command' => true,
    'context' => 'Validation', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'validation:write',
    'api_path' => 'validation/weekly-reviews/{weeklyReview}', 'http_methods' => ['PUT'], 'source_controller' => 'WeeklyReviewResource', 'source_action' => 'update'];
return $definitions;
