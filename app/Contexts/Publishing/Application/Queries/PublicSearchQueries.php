<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\BlogPost;
use App\Models\Course;

/** Bounded search over public, published OPES content only. */
final class PublicSearchQueries extends WorkflowHandler
{
    public function index(WorkflowInput $input): WorkflowResult
    {
        $validated = $input->validate(['q' => 'required|string|min:2|max:100']);
        $term = trim($validated['q']);
        $locale = $input->locale() === 'fr' ? 'fr' : 'en';
        $like = '%'.addcslashes($term, '%_\\').'%';

        $posts = BlogPost::published()
            ->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('title_fr', 'like', $like)
                ->orWhere('excerpt', 'like', $like)->orWhere('excerpt_fr', 'like', $like))
            ->limit(10)->get(['slug', 'title', 'title_fr', 'excerpt', 'excerpt_fr', 'category', 'published_at']);

        $results = $posts->map(fn (BlogPost $post) => [
            'type' => 'article',
            'title' => $post->getLocalizedTitle($locale),
            'summary' => $post->getLocalizedExcerpt($locale),
            'url' => url($locale.'/blog/'.$post->slug),
            'published_at' => $post->published_at?->toAtomString(),
        ])->all();

        $courses = Course::query()->where('is_active', true)
            ->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('title_fr', 'like', $like)
                ->orWhere('description', 'like', $like)->orWhere('description_fr', 'like', $like))
            ->orderBy('sort_order')->limit(8)->get(['slug', 'title', 'title_fr', 'description', 'description_fr']);
        foreach ($courses as $course) $results[] = [
            'type' => 'course', 'title' => $locale === 'fr' && $course->title_fr ? $course->title_fr : $course->title,
            'summary' => $locale === 'fr' && $course->description_fr ? $course->description_fr : ($course->description ?? ''),
            'url' => url($locale.'/courses/'.$course->slug),
        ];

        foreach (config('products', []) as $product) {
            if (!is_array($product) || empty($product['slug'])) continue;
            $title = $product['name'] ?? $product['title'] ?? ucfirst(str_replace('-', ' ', $product['slug']));
            $summary = $locale === 'fr'
                ? ($product['description_fr'] ?? $product['tagline_fr'] ?? $product['description'] ?? $product['tagline'] ?? '')
                : ($product['description'] ?? $product['tagline'] ?? '');
            $searchable = implode(' ', array_filter([$product['slug'], $title, $product['name'] ?? '', $product['title'] ?? '', $product['description'] ?? '', $product['description_fr'] ?? '', $product['tagline'] ?? '', $product['tagline_fr'] ?? '']));
            if (mb_stripos($searchable, $term) === false) continue;
            $results[] = ['type' => 'product', 'title' => $title, 'summary' => $summary, 'url' => url($locale.'/products/'.$product['slug'])];
        }

        $results = array_slice($results, 0, 30);
        return WorkflowResult::read('public_search_results_1', ['query' => $term, 'results' => $results]);
    }
}
