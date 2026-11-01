<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$article = ['name' => 'article', 'kind' => 'model', 'class' => 'App\\Models\\KnowledgeBaseArticle', 'key' => 'id'];
$category = ['name' => 'category', 'kind' => 'model', 'class' => 'App\\Models\\KnowledgeBaseCategory', 'key' => 'id'];
$announcement = ['name' => 'announcement', 'kind' => 'model', 'class' => 'App\\Models\\Announcement', 'key' => 'id'];
$def = fn (string $handler, string $method, array $params, bool $command, string $scope, string $path, string $verb, string $context, string $source, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $params, 'command' => $command, 'context' => $context,
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $scope, 'api_path' => $path, 'http_methods' => [$verb],
    'source_controller' => $source, 'source_action' => $action,
];
$announcementCommands = 'App\\Contexts\\Communications\\Application\\Commands\\AdminAnnouncementCommands';
$announcementQueries = 'App\\Contexts\\Communications\\Application\\Queries\\AdminAnnouncementQueries';
$testimonialCommands = 'App\\Contexts\\Communications\\Application\\Commands\\AdminTestimonialCommands';
$testimonialQueries = 'App\\Contexts\\Communications\\Application\\Queries\\AdminTestimonialQueries';
$knowledgeCommands = 'App\\Contexts\\Support\\Application\\Commands\\AdminKnowledgeBaseCommands';
$knowledgeQueries = 'App\\Contexts\\Support\\Application\\Queries\\AdminKnowledgeBaseQueries';

return [
    'admin.communications.announcements.index' => $def($announcementQueries, 'index', [$input], false, 'communications:read', 'communications/announcements', 'GET', 'Communications', 'AnnouncementResource', 'index'),
    'admin.communications.announcements.show' => $def($announcementQueries, 'show', [$input, $announcement], false, 'communications:read', 'communications/announcements/{announcement}', 'GET', 'Communications', 'AnnouncementResource', 'show'),
    'admin.communications.announcements.create' => $def($announcementCommands, 'create', [$input], true, 'communications:write', 'communications/announcements', 'POST', 'Communications', 'AnnouncementResource', 'create'),
    'admin.communications.announcements.update' => $def($announcementCommands, 'update', [$input, $announcement], true, 'communications:write', 'communications/announcements/{announcement}', 'PUT', 'Communications', 'AnnouncementResource', 'update'),
    'admin.communications.announcements.delete' => $def($announcementCommands, 'delete', [$input, $announcement], true, 'communications:write', 'communications/announcements/{announcement}', 'DELETE', 'Communications', 'AnnouncementResource', 'delete'),
    'admin.communications.testimonials.index' => $def($testimonialQueries, 'index', [$input], false, 'communications:read', 'communications/testimonials', 'GET', 'Communications', 'TestimonialResource', 'index'),
    'admin.communications.testimonials.show' => $def($testimonialQueries, 'show', [$input, ['name' => 'testimonial', 'kind' => 'model', 'class' => 'App\\Models\\Testimonial', 'key' => 'id']], false, 'communications:read', 'communications/testimonials/{testimonial}', 'GET', 'Communications', 'TestimonialResource', 'show'),
    'admin.communications.testimonials.create' => $def($testimonialCommands, 'create', [$input], true, 'communications:write', 'communications/testimonials', 'POST', 'Communications', 'TestimonialResource', 'create'),
    'admin.communications.testimonials.update' => $def($testimonialCommands, 'update', [$input, ['name' => 'testimonial', 'kind' => 'model', 'class' => 'App\\Models\\Testimonial', 'key' => 'id']], true, 'communications:write', 'communications/testimonials/{testimonial}', 'PUT', 'Communications', 'TestimonialResource', 'update'),
    'admin.communications.testimonials.delete' => $def($testimonialCommands, 'delete', [$input, ['name' => 'testimonial', 'kind' => 'model', 'class' => 'App\\Models\\Testimonial', 'key' => 'id']], true, 'communications:write', 'communications/testimonials/{testimonial}', 'DELETE', 'Communications', 'TestimonialResource', 'delete'),
    'admin.support.knowledgeArticles.index' => $def($knowledgeQueries, 'articles', [$input], false, 'support:read', 'support/knowledge-base/articles', 'GET', 'Support', 'KnowledgeBaseArticleResource', 'index'),
    'admin.support.knowledgeArticles.show' => $def($knowledgeQueries, 'showArticle', [$input, $article], false, 'support:read', 'support/knowledge-base/articles/{article}', 'GET', 'Support', 'KnowledgeBaseArticleResource', 'show'),
    'admin.support.knowledgeArticles.create' => $def($knowledgeCommands, 'createArticle', [$input], true, 'support:write', 'support/knowledge-base/articles', 'POST', 'Support', 'KnowledgeBaseArticleResource', 'create'),
    'admin.support.knowledgeArticles.update' => $def($knowledgeCommands, 'updateArticle', [$input, $article], true, 'support:write', 'support/knowledge-base/articles/{article}', 'PUT', 'Support', 'KnowledgeBaseArticleResource', 'update'),
    'admin.support.knowledgeArticles.delete' => $def($knowledgeCommands, 'deleteArticle', [$input, $article], true, 'support:write', 'support/knowledge-base/articles/{article}', 'DELETE', 'Support', 'KnowledgeBaseArticleResource', 'delete'),
    'admin.support.knowledgeArticles.archive' => $def($knowledgeCommands, 'archiveArticle', [$input, $article], true, 'support:write', 'support/knowledge-base/articles/{article}/archive', 'POST', 'Support', 'KnowledgeBaseArticleResource', 'archive'),
    'admin.support.knowledgeCategories.index' => $def($knowledgeQueries, 'categories', [$input], false, 'support:read', 'support/knowledge-base/categories', 'GET', 'Support', 'KnowledgeBaseCategoryResource', 'index'),
    'admin.support.knowledgeCategories.show' => $def($knowledgeQueries, 'showCategory', [$input, $category], false, 'support:read', 'support/knowledge-base/categories/{category}', 'GET', 'Support', 'KnowledgeBaseCategoryResource', 'show'),
    'admin.support.knowledgeCategories.create' => $def($knowledgeCommands, 'createCategory', [$input], true, 'support:write', 'support/knowledge-base/categories', 'POST', 'Support', 'KnowledgeBaseCategoryResource', 'create'),
    'admin.support.knowledgeCategories.update' => $def($knowledgeCommands, 'updateCategory', [$input, $category], true, 'support:write', 'support/knowledge-base/categories/{category}', 'PUT', 'Support', 'KnowledgeBaseCategoryResource', 'update'),
    'admin.support.knowledgeCategories.delete' => $def($knowledgeCommands, 'deleteCategory', [$input, $category], true, 'support:write', 'support/knowledge-base/categories/{category}', 'DELETE', 'Support', 'KnowledgeBaseCategoryResource', 'delete'),
];
