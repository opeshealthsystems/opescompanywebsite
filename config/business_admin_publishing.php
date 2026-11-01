<?php

$class = 'App\\Contexts\\Documents\\Application\\Commands\\AdminPublishingCommands';
$input = ['name' => 'workflowInput', 'kind' => 'input'];
$document = ['name' => 'document', 'kind' => 'model', 'class' => 'App\\Models\\Document', 'key' => 'id'];
$article = ['name' => 'article', 'kind' => 'model', 'class' => 'App\\Models\\KnowledgeBaseArticle', 'key' => 'id'];
$definitions = [];
$add = function (string $name, string $method, string $path, array $params, string $scope, string $source, string $action) use (&$definitions, $class): void {
    $definitions[$name] = ['handler' => $class, 'method' => $method, 'parameters' => $params, 'command' => true,
        'context' => 'Publishing', 'roles' => ['super_admin', 'admin'], 'authenticated' => true,
        'scope' => $scope, 'api_path' => $path, 'http_methods' => ['POST'],
        'source_controller' => $source, 'source_action' => $action];
};
$add('admin.documents.markSent', 'markDocumentSent', 'documents/{document}/mark-sent', [$input, $document], 'documents:write', 'DocumentResource', 'mark_sent');
$add('admin.documents.void', 'voidDocument', 'documents/{document}/void', [$input, $document], 'documents:write', 'DocumentResource', 'void');
$add('admin.content.articles.publish', 'publishArticle', 'content/articles/{article}/publish', [$input, $article], 'content:write', 'KnowledgeBaseArticleResource', 'publish');
return $definitions;
