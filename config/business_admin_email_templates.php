<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$template = ['name' => 'template', 'kind' => 'model', 'class' => 'App\\Models\\EmailTemplate', 'key' => 'id'];
$commands = 'App\\Contexts\\Communications\\Application\\Commands\\AdminEmailTemplateCommands';
$queries = 'App\\Contexts\\Communications\\Application\\Queries\\AdminEmailTemplateQueries';
$def = fn (string $handler, string $method, array $parameters, bool $command, string $verb, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $parameters, 'command' => $command, 'context' => 'Communications',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $command ? 'communications:write' : 'communications:read',
    'api_path' => 'communications/email-templates'.(in_array($method, ['show', 'update', 'delete'], true) ? '/{template}' : ''),
    'http_methods' => [$verb], 'source_controller' => 'EmailTemplateResource', 'source_action' => $action,
];

return [
    'admin.communications.emailTemplates.index' => $def($queries, 'index', [$input], false, 'GET', 'index'),
    'admin.communications.emailTemplates.show' => $def($queries, 'show', [$input, $template], false, 'GET', 'show'),
    'admin.communications.emailTemplates.create' => $def($commands, 'create', [$input], true, 'POST', 'create'),
    'admin.communications.emailTemplates.update' => $def($commands, 'update', [$input, $template], true, 'PUT', 'update'),
    'admin.communications.emailTemplates.delete' => $def($commands, 'delete', [$input, $template], true, 'DELETE', 'delete'),
];
