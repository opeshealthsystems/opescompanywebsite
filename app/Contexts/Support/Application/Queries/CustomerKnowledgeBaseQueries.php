<?php

namespace App\Contexts\Support\Application\Queries;

use App\Models\KnowledgeBaseArticle;
use App\Models\KnowledgeBaseCategory;
class CustomerKnowledgeBaseQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $categories = $workflowInput->collect(\App\Models\KnowledgeBaseCategory::where('is_active', true)->where('is_public', true)->whereNull('parent_id')->withCount(['articles' => fn($q) => $q->where('status', 'published')->where('is_public', true)])->orderBy('sort_order'));
        $recent = $workflowInput->collect(\App\Models\KnowledgeBaseArticle::where('status', 'published')->where('is_public', true)->orderByDesc('published_at')->limit(6));
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_knowledgebasecontroller_index_1', compact('categories', 'recent'));
    }
    public function category(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, string $slug): \App\Contexts\Shared\Application\WorkflowResult
    {
        $category = \App\Models\KnowledgeBaseCategory::where('slug', $slug)->where('is_public', true)->firstOrFail();
        $articles = $workflowInput->paginate(\App\Models\KnowledgeBaseArticle::where('category_id', $category->id)->where('status', 'published')->where('is_public', true)->orderByDesc('published_at'), 12);
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_knowledgebasecontroller_category_1', compact('category', 'articles'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, string $slug): \App\Contexts\Shared\Application\WorkflowResult
    {
        $article = \App\Models\KnowledgeBaseArticle::where('slug', $slug)->where('status', 'published')->where('is_public', true)->firstOrFail();
        $article->increment('views');
        $related = $workflowInput->collect(\App\Models\KnowledgeBaseArticle::where('status', 'published')->where('is_public', true)->where('id', '!=', $article->id)->when($article->category_id, fn($q) => $q->where('category_id', $article->category_id))->limit(4));
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_knowledgebasecontroller_show_1', compact('article', 'related'));
    }
}
