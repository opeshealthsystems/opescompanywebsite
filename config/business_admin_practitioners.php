<?php

$class = 'App\\Contexts\\Programmes\\Application\\Commands\\AdminPractitionerCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$suggestion = ['name' => 'suggestion', 'kind' => 'model', 'class' => 'App\\Models\\Suggestion', 'key' => 'id'];
$profile = ['name' => 'profile', 'kind' => 'model', 'class' => 'App\\Models\\PractitionerProfile', 'key' => 'id'];
$finding = ['name' => 'finding', 'kind' => 'model', 'class' => 'App\\Models\\PractitionerFinding', 'key' => 'id'];
$application = ['name' => 'application', 'kind' => 'model', 'class' => 'App\\Models\\PractitionerApplication', 'key' => 'id'];
$definitions = [];
$definitions['admin.practitioners.suggestions.index'] = ['handler' => 'App\\Contexts\\Programmes\\Application\\Queries\\AdminSuggestionQueries', 'method' => 'index',
    'parameters' => [$input], 'command' => false, 'context' => 'Programmes', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
    'scope' => 'practitioners:read', 'api_path' => 'admin/practitioners/suggestions', 'http_methods' => ['GET'],
    'source_controller' => 'SuggestionResource', 'source_action' => 'index'];
$add = function (string $name, string $method, string $path, array $parameters, string $source, string $action) use (&$definitions, $class): void {
    $definitions[$name] = ['handler' => $class, 'method' => $method, 'parameters' => $parameters, 'command' => true,
        'context' => 'Programmes', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
        'scope' => 'practitioners:write', 'api_path' => $path, 'http_methods' => ['POST'],
        'source_controller' => $source, 'source_action' => $action];
};
$add('admin.practitioners.suggestions.respond', 'respondToSuggestion', 'practitioners/suggestions/{suggestion}/respond', [$input, $suggestion], 'SuggestionResource', 'respond');
$add('admin.practitioners.profiles.verify', 'verifyProfile', 'practitioners/profiles/{profile}/verify', [$input, $profile], 'PractitionerProfileResource', 'verify');
$add('admin.practitioners.findings.publish', 'publishFinding', 'practitioners/findings/{finding}/publish', [$input, $finding], 'PractitionerFindingResource', 'publish');
$add('admin.practitioners.findings.unpublish', 'unpublishFinding', 'practitioners/findings/{finding}/unpublish', [$input, $finding], 'PractitionerFindingResource', 'unpublish');
$add('admin.practitioners.applications.approve', 'approveApplication', 'practitioners/applications/{application}/approve', [$input, $application], 'PractitionerApplicationResource', 'approve');
$add('admin.practitioners.applications.reject', 'rejectApplication', 'practitioners/applications/{application}/reject', [$input, $application], 'PractitionerApplicationResource', 'reject');
$add('admin.practitioners.applications.placeInCohort', 'placeApplicationInCohort', 'practitioners/applications/{application}/place-in-cohort', [$input, $application], 'PractitionerApplicationResource', 'place_in_cohort');
return $definitions;
