<?php

$class = 'App\\Contexts\\Workforce\\Application\\Commands\\AdminRecruitmentCommands';
$queries = 'App\\Contexts\\Workforce\\Application\\Queries\\AdminRecruitmentQueries';
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
$read = fn (string $method, string $path, array $params, string $source, string $action) => [
    'handler' => $queries, 'method' => $method, 'parameters' => $params, 'command' => false, 'context' => 'Workforce',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'recruitment:read',
    'api_path' => $path, 'http_methods' => ['GET'], 'source_controller' => $source, 'source_action' => $action,
];
$addCrud = function (string $key, string $method, string $path, array $params, string $source, string $action, string $verb) use (&$definitions, $class): void {
    $definitions[$key] = ['handler' => $class, 'method' => $method, 'parameters' => $params, 'command' => true,
        'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'recruitment:write',
        'api_path' => $path, 'http_methods' => [$verb], 'source_controller' => $source, 'source_action' => $action];
};
$definitions['admin.recruitment.openings.index'] = $read('openings', 'recruitment/openings', [$input], 'JobOpeningResource', 'index');
$definitions['admin.recruitment.openings.show'] = $read('opening', 'recruitment/openings/{opening}', [$input, $opening], 'JobOpeningResource', 'show');
$addCrud('admin.recruitment.openings.create', 'createOpening', 'recruitment/openings', [$input], 'JobOpeningResource', 'create', 'POST');
$addCrud('admin.recruitment.openings.update', 'updateOpening', 'recruitment/openings/{opening}', [$input, $opening], 'JobOpeningResource', 'update', 'PUT');
$addCrud('admin.recruitment.openings.delete', 'deleteOpening', 'recruitment/openings/{opening}', [$input, $opening], 'JobOpeningResource', 'delete', 'DELETE');
$definitions['admin.recruitment.applications.index'] = $read('applications', 'recruitment/applications', [$input], 'JobApplicationResource', 'index');
$definitions['admin.recruitment.applications.show'] = $read('application', 'recruitment/applications/{application}', [$input, $application], 'JobApplicationResource', 'show');
$addCrud('admin.recruitment.applications.create', 'createApplication', 'recruitment/applications', [$input], 'JobApplicationResource', 'create', 'POST');
$addCrud('admin.recruitment.applications.update', 'updateApplication', 'recruitment/applications/{application}', [$input, $application], 'JobApplicationResource', 'update', 'PUT');
$addCrud('admin.recruitment.applications.delete', 'deleteApplication', 'recruitment/applications/{application}', [$input, $application], 'JobApplicationResource', 'delete', 'DELETE');
return $definitions;
