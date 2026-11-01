<?php

$class = 'App\\Contexts\\Workforce\\Application\\Commands\\AdminPerformanceCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$review = ['name' => 'review', 'kind' => 'model', 'class' => 'App\\Models\\PerformanceReview', 'key' => 'id'];
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
return $definitions;
