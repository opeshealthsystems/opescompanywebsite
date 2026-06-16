<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBaseArticle;
use App\Models\KnowledgeBaseCategory;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        $categories = KnowledgeBaseCategory::where('is_active', true)
            ->where('is_public', true)
            ->whereNull('parent_id')
            ->withCount(['articles' => fn ($q) => $q->where('status', 'published')->where('is_public', true)])
            ->orderBy('sort_order')
            ->get();

        $recent = KnowledgeBaseArticle::where('status', 'published')
            ->where('is_public', true)
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        return view('customer.knowledge-base.index', compact('categories', 'recent'));
    }

    public function show(string $slug)
    {
        $article = KnowledgeBaseArticle::where('slug', $slug)
            ->where('status', 'published')
            ->where('is_public', true)
            ->firstOrFail();

        $article->increment('views');

        $related = KnowledgeBaseArticle::where('status', 'published')
            ->where('is_public', true)
            ->where('id', '!=', $article->id)
            ->when($article->category_id, fn ($q) => $q->where('category_id', $article->category_id))
            ->limit(4)
            ->get();

        return view('customer.knowledge-base.show', compact('article', 'related'));
    }

    public function category(string $slug)
    {
        $category = KnowledgeBaseCategory::where('slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();

        $articles = KnowledgeBaseArticle::where('category_id', $category->id)
            ->where('status', 'published')
            ->where('is_public', true)
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('customer.knowledge-base.category', compact('category', 'articles'));
    }
}
