<?php

return [
    'public.sitemap.data' => [
        'handler' => 'App\\Contexts\\Publishing\\Application\\Queries\\PublicSitemapQueries',
        'method' => 'index',
        'parameters' => [['name' => 'workflowInput', 'kind' => 'input']],
        'command' => false,
        'context' => 'Publishing',
        'roles' => [],
        'authenticated' => false,
        'scope' => 'sitemap:read',
        'api_path' => 'public/sitemap-data',
        'http_methods' => ['GET'],
        'source_controller' => 'SitemapController',
        'source_action' => 'data',
        'web_route' => 'sitemap',
    ],
];
