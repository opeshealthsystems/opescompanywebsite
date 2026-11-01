<?php

$class = 'App\\Contexts\\Workforce\\Application\\Commands\\AdminPerformanceCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$review = ['name' => 'review', 'kind' => 'model', 'class' => 'App\\Models\\PerformanceReview', 'key' => 'id'];
$queries = 'App\\Contexts\\Workforce\\Application\\Queries\\AdminPerformanceQueries';
$definitions = [];
foreach ([
    ['admin.workforce.performanceReviews.submit', 'submit', 'submit'],
    ['admin.workforce.performanceReviews.acknowledge', 'acknowledge', 'acknowledge'],
] as [$name, $method, $action]) {
    $definitions[$name] = ['handler' => $class, 'method' => $method, 'parameters' => [$input, $review], 'command' => true,
        'context' => 'Workforce', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
        'scope' => 'workforce:write', 'api_path' => 'admin/performance-reviews/{review}/'.$action, 'http_methods' => ['POST'],
        'source_controller' => 'PerformanceReviewResource', 'source_action' => $action];
}
$definitions['admin.workforce.performanceReviews.index'] = ['handler' => $queries, 'method' => 'index', 'parameters' => [$input], 'command' => false,
    'context' => 'Workforce HR', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:reviews:read',
    'api_path' => 'admin/performance-reviews', 'http_methods' => ['GET'], 'source_controller' => 'PerformanceReviewResource', 'source_action' => 'index'];
$definitions['admin.workforce.performanceReviews.show'] = ['handler' => $queries, 'method' => 'show', 'parameters' => [$input, $review], 'command' => false,
    'context' => 'Workforce HR', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:reviews:read',
    'api_path' => 'admin/performance-reviews/{review}', 'http_methods' => ['GET'], 'source_controller' => 'PerformanceReviewResource', 'source_action' => 'show'];
$definitions['admin.workforce.performanceReviews.create'] = ['handler' => $class, 'method' => 'create', 'parameters' => [$input], 'command' => true,
    'context' => 'Workforce HR', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:reviews:write',
    'api_path' => 'admin/performance-reviews', 'http_methods' => ['POST'], 'source_controller' => 'PerformanceReviewResource', 'source_action' => 'create'];
$definitions['admin.workforce.performanceReviews.update'] = ['handler' => $class, 'method' => 'update', 'parameters' => [$input, $review], 'command' => true,
    'context' => 'Workforce HR', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'workforce:reviews:write',
    'api_path' => 'admin/performance-reviews/{review}', 'http_methods' => ['PUT'], 'source_controller' => 'PerformanceReviewResource', 'source_action' => 'update'];
return $definitions;
