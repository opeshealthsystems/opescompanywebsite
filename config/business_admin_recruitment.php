<?php

$class = 'App\\Contexts\\Workforce\\Application\\Commands\\AdminRecruitmentCommands';
$application = ['name' => 'application', 'kind' => 'model', 'class' => 'App\\Models\\JobApplication', 'key' => 'id'];
$opening = ['name' => 'opening', 'kind' => 'model', 'class' => 'App\\Models\\JobOpening', 'key' => 'id'];
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$definitions = [];
$add = function (string $name, string $method, string $path, array $params, string $source, string $action) use (&$definitions, $class): void {
    $definitions[$name] = ['handler' => $class, 'method' => $method, 'parameters' => $params, 'command' => true,
        'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
        'scope' => 'recruitment:write', 'api_path' => $path, 'http_methods' => ['POST'],
        'source_controller' => $source, 'source_action' => $action];
};
$add('admin.recruitment.applications.shortlist', 'shortlist', 'recruitment/applications/{application}/shortlist', [$input, $application], 'JobApplicationResource', 'shortlist');
$add('admin.recruitment.applications.scheduleInterview', 'scheduleInterview', 'recruitment/applications/{application}/schedule-interview', [$input, $application], 'JobApplicationResource', 'schedule_interview');
$add('admin.recruitment.applications.makeOffer', 'makeOffer', 'recruitment/applications/{application}/make-offer', [$input, $application], 'JobApplicationResource', 'make_offer');
$add('admin.recruitment.applications.hire', 'hire', 'recruitment/applications/{application}/hire', [$input, $application], 'JobApplicationResource', 'hire');
$add('admin.recruitment.applications.reject', 'reject', 'recruitment/applications/{application}/reject', [$input, $application], 'JobApplicationResource', 'reject');
$add('admin.recruitment.openings.close', 'closeOpening', 'recruitment/openings/{opening}/close', [$input, $opening], 'JobOpeningResource', 'close');
return $definitions;
