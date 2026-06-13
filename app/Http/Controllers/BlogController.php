<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = \App\Models\BlogPost::published()->paginate(9);
        return view('pages.blog-index', compact('posts'));
    }

    public function show(string $locale, string $slug)
    {
        $post = \App\Models\BlogPost::where('slug', $slug)
            ->where('published', true)
            ->firstOrFail();

        $related = \App\Models\BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where('category', $post->category)
            ->limit(3)
            ->get();

        return view('pages.blog-show', compact('post', 'related'));
    }
}
