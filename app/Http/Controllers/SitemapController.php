<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class SitemapController extends Controller
{
    public function index()
    {
        $locales  = config('locale.supported', ['en', 'fr']);
        $products = array_merge(config('products'), config('products_specialist'));
        $posts    = BlogPost::published()->get(['slug', 'updated_at']);

        $staticRoutes = ['', '/products', '/solutions', '/about', '/partnerships', '/blog', '/contact'];

        return response()
            ->view('sitemap', compact('locales', 'products', 'posts', 'staticRoutes'))
            ->header('Content-Type', 'application/xml');
    }
}
