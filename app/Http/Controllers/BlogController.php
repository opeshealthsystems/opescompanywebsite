<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $activeCategory = $request->query('category');

        $query = BlogPost::published();
        if ($activeCategory) {
            $query->where('category', $activeCategory);
        }

        $featured       = BlogPost::published()->limit(4)->get();
        $posts          = $query->paginate(9)->withQueryString();
        $categories     = BlogPost::published()
            ->select('category')
            ->selectRaw('count(*) as count')
            ->groupBy('category')
            ->reorder()
            ->orderByDesc('count')
            ->get();

        return view('pages.blog-index', compact('posts', 'featured', 'categories', 'activeCategory'));
    }

    public function show(string $locale, string $slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('published', true)
            ->firstOrFail();

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where('category', $post->category)
            ->limit(3)
            ->get();

        return view('pages.blog-show', compact('post', 'related'));
    }
}
