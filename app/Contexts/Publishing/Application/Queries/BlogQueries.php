<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Models\BlogComment;
use App\Models\BlogPost;
class BlogQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        // reorder() drops the published_at ordering injected by the published() scope:
        // a SELECT DISTINCT may only ORDER BY columns in its SELECT list (MySQL error 3065),
        // and this query selects only `category`. (Mirrors the $categories query below.)
        $availableCategories = \App\Models\BlogPost::published()->reorder()->distinct()->orderBy('category')->pluck('category')->filter()->values()->toArray();
        $categoryInput = $request->query('category');
        $searchInput = $request->query('search', '');
        if ($categoryInput !== null && $categoryInput !== '' && !in_array($categoryInput, $availableCategories, true)) {
            abort(422, 'The selected category is invalid.');
        }
        if (mb_strlen((string) $searchInput) > 255) {
            abort(422, 'The search query must not be greater than 255 characters.');
        }
        $activeCategory = $categoryInput !== null && $categoryInput !== '' ? $categoryInput : null;
        $search = trim((string) $searchInput);
        $query = \App\Models\BlogPost::published();
        if ($activeCategory) {
            $query->where('category', $activeCategory);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')->orWhere('title_fr', 'like', '%' . $search . '%')->orWhere('excerpt', 'like', '%' . $search . '%')->orWhere('excerpt_fr', 'like', '%' . $search . '%');
            });
        }
        $featured = $request->collect(\App\Models\BlogPost::published()->limit(4));
        $posts = $request->paginate($query, 9);
        $categories = $request->collect(\App\Models\BlogPost::published()->select('category')->selectRaw('count(*) as count')->groupBy('category')->reorder()->orderByDesc('count'));
        return \App\Contexts\Shared\Application\WorkflowResult::read('blogcontroller_index_1', compact('posts', 'featured', 'categories', 'activeCategory', 'search'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, string $locale, string $slug): \App\Contexts\Shared\Application\WorkflowResult
    {
        $post = \App\Models\BlogPost::published()->where('slug', $slug)->firstOrFail();
        \App\Models\BlogPost::withoutTimestamps(fn() => $post->increment('views'));
        $related = $workflowInput->collect(\App\Models\BlogPost::published()->where('id', '!=', $post->id)->where('category', $post->category)->limit(3));
        $comments = $workflowInput->collect($post->comments()->where('approved', true)->latest());
        return \App\Contexts\Shared\Application\WorkflowResult::read('blogcontroller_show_1', compact('post', 'related', 'comments'));
    }
}
