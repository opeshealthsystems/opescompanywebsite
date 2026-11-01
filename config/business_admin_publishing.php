<?php

$class = 'App\\Contexts\\Documents\\Application\\Commands\\AdminPublishingCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$document = ['name' => 'document', 'kind' => 'model', 'class' => 'App\\Models\\Document', 'key' => 'id'];
$article = ['name' => 'article', 'kind' => 'model', 'class' => 'App\\Models\\KnowledgeBaseArticle', 'key' => 'id'];
$template = ['name' => 'template', 'kind' => 'model', 'class' => 'App\\Models\\DocumentTemplate', 'key' => 'id'];
$definitions = [];
$add = function (string $name, string $method, string $path, array $params, string $scope, string $source, string $action) use (&$definitions, $class): void {
    $definitions[$name] = ['handler' => $class, 'method' => $method, 'parameters' => $params, 'command' => true,
        'context' => 'Publishing', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
        'scope' => $scope, 'api_path' => $path, 'http_methods' => ['POST'],
        'source_controller' => $source, 'source_action' => $action];
};
$definitions['admin.documents.templates.index'] = ['handler' => 'App\\Contexts\\Documents\\Application\\Queries\\AdminDocumentTemplateQueries',
    'method' => 'index', 'parameters' => [['name' => 'workflowInput', 'kind' => 'input']], 'command' => false,
    'context' => 'Documents', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'documents:read',
    'api_path' => 'document-templates', 'http_methods' => ['GET'], 'source_controller' => 'DocumentTemplateResource', 'source_action' => 'index'];
$definitions['admin.documents.templates.show'] = ['handler' => 'App\\Contexts\\Documents\\Application\\Queries\\AdminDocumentTemplateQueries',
    'method' => 'show', 'parameters' => [['name' => 'workflowInput', 'kind' => 'input'], $template], 'command' => false,
    'context' => 'Documents', 'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => 'documents:read',
    'api_path' => 'document-templates/{template}', 'http_methods' => ['GET'], 'source_controller' => 'DocumentTemplateResource', 'source_action' => 'show'];
$add('admin.documents.templates.create', 'createDocumentTemplate', 'document-templates', [$input], 'documents:write', 'DocumentTemplateResource', 'create');
$add('admin.documents.templates.update', 'updateDocumentTemplate', 'document-templates/{template}', [$input, $template], 'documents:write', 'DocumentTemplateResource', 'update');
$definitions['admin.documents.templates.update']['http_methods'] = ['PUT'];
$add('admin.documents.templates.delete', 'deleteDocumentTemplate', 'document-templates/{template}', [$input, $template], 'documents:write', 'DocumentTemplateResource', 'delete');
$definitions['admin.documents.templates.delete']['http_methods'] = ['DELETE'];
$add('admin.documents.create', 'createDocument', 'documents', [$input], 'documents:write', 'DocumentResource', 'create');
$add('admin.documents.update', 'updateDocument', 'documents/{document}', [$input, $document], 'documents:write', 'DocumentResource', 'update');
$definitions['admin.documents.update']['http_methods'] = ['PUT'];
$add('admin.documents.delete', 'deleteDocument', 'documents/{document}', [$input, $document], 'documents:write', 'DocumentResource', 'delete');
$definitions['admin.documents.delete']['http_methods'] = ['DELETE'];
$add('admin.documents.markSent', 'markDocumentSent', 'documents/{document}/mark-sent', [$input, $document], 'documents:write', 'DocumentResource', 'mark_sent');
$add('admin.documents.void', 'voidDocument', 'documents/{document}/void', [$input, $document], 'documents:write', 'DocumentResource', 'void');
$add('admin.content.articles.publish', 'publishArticle', 'content/articles/{article}/publish', [$input, $article], 'content:write', 'KnowledgeBaseArticleResource', 'publish');
return $definitions;
