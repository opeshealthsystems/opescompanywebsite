<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$blogPost = ['name' => 'blogPost', 'kind' => 'model', 'class' => 'App\\Models\\BlogPost', 'key' => 'id'];
$commands = 'App\\Contexts\\Publishing\\Application\\Commands\\AdminBlogPostCommands';
$queries = 'App\\Contexts\\Publishing\\Application\\Queries\\AdminBlogPostQueries';
$def = fn (string $handler, string $method, array $parameters, bool $command, string $verb, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $parameters, 'command' => $command, 'context' => 'Publishing',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $command ? 'publishing:write' : 'publishing:read',
    'api_path' => 'publishing/blog-posts'.(in_array($method, ['show', 'update', 'delete'], true) ? '/{blogPost}' : ''),
    'http_methods' => [$verb], 'source_controller' => 'BlogPostResource', 'source_action' => $action,
];

return [
    'admin.publishing.blogPosts.index' => $def($queries, 'index', [$input], false, 'GET', 'index'),
    'admin.publishing.blogPosts.show' => $def($queries, 'show', [$input, $blogPost], false, 'GET', 'show'),
    'admin.publishing.blogPosts.create' => $def($commands, 'create', [$input], true, 'POST', 'create'),
    'admin.publishing.blogPosts.update' => $def($commands, 'update', [$input, $blogPost], true, 'PUT', 'update'),
    'admin.publishing.blogPosts.delete' => $def($commands, 'delete', [$input, $blogPost], true, 'DELETE', 'delete'),
];
