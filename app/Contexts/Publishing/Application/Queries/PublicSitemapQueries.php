<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\BlogPost;
use App\Models\Course;
use App\Models\PractitionerProfile;

/** Published-content data shared by the sitemap presenter and the public API. */
final class PublicSitemapQueries extends WorkflowHandler
{
    public function index(WorkflowInput $input): WorkflowResult
    {
        $query = BlogPost::published()->where(fn ($posts) => $posts->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->orderBy('slug')->select(['slug', 'updated_at']);
        $page = $input->paginate($query, 100);
        $items = array_map(fn (BlogPost $post) => ['slug' => $post->slug, 'updated_at' => $post->updated_at?->toAtomString()], $page->items());

        $hasPractitioners = PractitionerProfile::query()
            ->whereHas('user', fn ($users) => $users->where('email', 'not like', '%.test')->where('email', 'not like', '%@demo.opes'))
            ->whereHas('user.practitionerApplications', fn ($applications) => $applications->where('status', 'approved'))
            ->exists();

        return WorkflowResult::payload([
            'items' => $items,
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()],
            'has_active_courses' => Course::query()->where('is_active', true)->exists(),
            'has_public_practitioners' => $hasPractitioners,
        ]);
    }
}
