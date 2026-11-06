<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Builder;

final class AdminBlogPostQueries extends WorkflowHandler
{
    private function authorizeAdministrator(WorkflowInput $input): void
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function item(BlogPost $post): array
    {
        return $post->only(['id', 'title', 'title_fr', 'slug', 'excerpt', 'excerpt_fr', 'body', 'body_fr', 'cover_image',
            'reading_time', 'category', 'author', 'published', 'published_at', 'created_at', 'updated_at']);
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->authorizeAdministrator($input);

        return BlogPost::query()->orderByDesc('created_at');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->authorizeAdministrator($input);
        $filters = $input->validate(['published' => 'sometimes|boolean', 'category' => 'sometimes|nullable|string|max:100']);
        $query = $this->query($input);
        if (array_key_exists('published', $filters)) $query->where('published', $filters['published']);
        if (!empty($filters['category'])) $query->where('category', $filters['category']);
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (BlogPost $post) => $this->item($post))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, BlogPost $blogPost): WorkflowResult
    {
        $this->authorizeAdministrator($input);
        return WorkflowResult::payload(['blog_post' => $this->item($blogPost)]);
    }
}
