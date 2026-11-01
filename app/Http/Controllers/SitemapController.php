<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class SitemapController extends Controller
{
    public function index()
    {
        $locales  = config('locale.supported', ['en', 'fr']);
        $products = array_merge(config('products'), config('products_specialist'), config('products_drugtrace'));
        $posts    = BlogPost::published()->where(function ($q) {
            $q->whereNull('published_at')->orWhere('published_at', '<=', now());
        })->get(['slug', 'updated_at']);

        $staticRoutes = array_map(fn ($path) => $path === '' ? '' : '/'.$path, array_keys(config('seo.pages')));
        if ($posts->isEmpty()) {
            $staticRoutes = array_values(array_diff($staticRoutes, ['/blog']));
        }
        if (!\App\Models\Course::where('is_active', true)->exists()) {
            $staticRoutes = array_values(array_diff($staticRoutes, ['/courses']));
        }
        if (!\App\Models\PractitionerProfile::whereHas('user', fn ($q) => $q->where('email', 'not like', '%.test')->where('email', 'not like', '%@demo.opes'))->whereHas('user.practitionerApplications', fn ($q) => $q->where('status', 'approved'))->exists()) {
            $staticRoutes = array_values(array_diff($staticRoutes, ['/practitioners']));
        }
        foreach (array_keys(config('markets', [])) as $country) {
            $staticRoutes[] = '/markets/'.$country;
        }

        return response()
            ->view('sitemap', compact('locales', 'products', 'posts', 'staticRoutes'))
            ->header('Content-Type', 'application/xml');
    }
}
