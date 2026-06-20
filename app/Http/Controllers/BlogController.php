<?php

namespace App\Http\Controllers;

use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        // reorder() drops the published_at ordering injected by the published() scope:
        // a SELECT DISTINCT may only ORDER BY columns in its SELECT list (MySQL error 3065),
        // and this query selects only `category`. (Mirrors the $categories query below.)
        $availableCategories = BlogPost::published()
            ->reorder()
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->filter()
            ->values()
            ->toArray();

        $categoryInput = $request->query('category');
        $searchInput   = $request->query('search', '');

        if ($categoryInput !== null && $categoryInput !== '' && ! in_array($categoryInput, $availableCategories, true)) {
            abort(422, 'The selected category is invalid.');
        }

        if (mb_strlen((string) $searchInput) > 255) {
            abort(422, 'The search query must not be greater than 255 characters.');
        }

        $activeCategory = ($categoryInput !== null && $categoryInput !== '') ? $categoryInput : null;
        $search         = trim((string) $searchInput);

        $query = BlogPost::published();
        if ($activeCategory) {
            $query->where('category', $activeCategory);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('title_fr', 'like', '%' . $search . '%')
                  ->orWhere('excerpt', 'like', '%' . $search . '%')
                  ->orWhere('excerpt_fr', 'like', '%' . $search . '%');
            });
        }

        $featured   = BlogPost::published()->limit(4)->get();
        $posts      = $query->paginate(9)->withQueryString();
        $categories = BlogPost::published()
            ->select('category')
            ->selectRaw('count(*) as count')
            ->groupBy('category')
            ->reorder()
            ->orderByDesc('count')
            ->get();

        return view('pages.blog-index', compact('posts', 'featured', 'categories', 'activeCategory', 'search'));
    }

    public function show(string $locale, string $slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('published', true)
            ->firstOrFail();

        $post->increment('views');

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where('category', $post->category)
            ->limit(3)
            ->get();

        $comments = $post->comments()->where('approved', true)->latest()->get();

        return view('pages.blog-show', compact('post', 'related', 'comments'));
    }

    public function like(string $locale, string $slug)
    {
        $post = BlogPost::where('slug', $slug)->where('published', true)->firstOrFail();
        $post->increment('likes');

        return response()->json(['likes' => $post->likes]);
    }

    public function share(string $locale, string $slug)
    {
        $post = BlogPost::where('slug', $slug)->where('published', true)->firstOrFail();
        $post->increment('shares');

        return response()->json(['shares' => $post->shares]);
    }

    public function comment(Request $request, string $locale, string $slug)
    {
        $post = BlogPost::where('slug', $slug)->where('published', true)->firstOrFail();

        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'content' => 'required|string|max:2000',
        ]);

        $post->comments()->create($validated);

        return back()->with('comment_submitted', true);
    }
}
