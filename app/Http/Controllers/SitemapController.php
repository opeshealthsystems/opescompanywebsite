<?php

namespace App\Http\Controllers;

class SitemapController extends Controller
{
    public function index()
    {
        $locales  = config('locale.supported', ['en', 'fr']);
        $products = array_merge(config('products'), config('products_specialist'), config('products_drugtrace'));
        $posts = collect();
        $pageNumber = 1;
        do {
            $result = app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
                'public.sitemap.data',
                new \App\Contexts\Shared\Application\WorkflowInput(values: ['page' => $pageNumber, 'per_page' => 100], language: app()->getLocale()),
            );
            foreach ($result->data['items'] ?? [] as $post) $posts->push($post);
            $pagination = $result->data['pagination'] ?? ['last_page' => 1];
            $pageNumber++;
        } while ($pageNumber <= (int) ($pagination['last_page'] ?? 1));

        $staticRoutes = array_map(fn ($path) => $path === '' ? '' : '/'.$path, array_keys(config('seo.pages')));
        if ($posts->isEmpty()) {
            $staticRoutes = array_values(array_diff($staticRoutes, ['/blog']));
        }
        if (!($result->data['has_active_courses'] ?? false)) {
            $staticRoutes = array_values(array_diff($staticRoutes, ['/courses']));
        }
        if (!($result->data['has_public_practitioners'] ?? false)) {
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
