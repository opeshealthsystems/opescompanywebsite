<?php

namespace App\Contexts\Publishing\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

final class AdminBlogPostCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function values(WorkflowInput $input, bool $update = false, ?BlogPost $post = null): array
    {
        $required = $update ? 'sometimes|required|' : 'required|';
        $data = $input->validate([
            'title' => $required.'string|max:255',
            'slug' => $required.'string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'title_fr' => 'sometimes|nullable|string|max:255',
            'excerpt' => 'sometimes|nullable|string|max:255',
            'excerpt_fr' => 'sometimes|nullable|string|max:255',
            'body' => $required.'string|max:100000',
            'body_fr' => 'sometimes|nullable|string|max:100000',
            'cover_image' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:/^blog\/[A-Za-z0-9._\/-]+$/', 'not_regex:/\.\./'],
            'category' => $required.'string|max:100',
            'author' => $required.'string|max:100',
            'published' => $update ? 'sometimes|boolean' : 'sometimes|boolean',
            'published_at' => 'sometimes|nullable|date',
            'reading_time' => 'prohibited', 'id' => 'prohibited',
        ]);
        $slugExists = BlogPost::query()->where('slug', $data['slug'] ?? null)
            ->when($post, fn ($query) => $query->where('id', '!=', $post->id))->exists();
        if ($slugExists) throw ValidationException::withMessages(['slug' => 'This slug is already in use.']);

        $sanitizer = new HtmlSanitizer((new HtmlSanitizerConfig())
            ->allowSafeElements()->allowLinkSchemes(['http', 'https', 'mailto'])->allowRelativeLinks());
        foreach (['body', 'body_fr'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null) {
                $data[$field] = $sanitizer->sanitize($data[$field]);
                if ($field === 'body' && trim(strip_tags($data[$field])) === '') {
                    throw ValidationException::withMessages([$field => 'Add readable article content using supported formatting.']);
                }
            }
        }

        $body = $data['body'] ?? $post?->body ?? '';
        $data['reading_time'] = BlogPost::calculateReadingTime($body);
        if (!$update) $data['published'] = (bool) ($data['published'] ?? false);
        return $data;
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->values($input);
        $post = DB::transaction(function () use ($data, $actor) {
            $post = BlogPost::create($data);
            DomainOutbox::append('event', $post->published ? 'publishing.blog_post_published.v1' : 'publishing.blog_post_created.v1',
                ['references' => [['resource' => 'BlogPost', 'id' => $post->id]], 'published' => $post->published], $actor->id);
            return $post;
        }, 3);
        return WorkflowResult::payload(['record' => $post, 'id' => $post->id], 201);
    }

    public function update(WorkflowInput $input, BlogPost $blogPost): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->values($input, true, $blogPost);
        if (!$data) throw ValidationException::withMessages(['blog_post' => 'Provide at least one blog post field to update.']);
        $post = DB::transaction(function () use ($blogPost, $data, $actor) {
            $post = BlogPost::query()->lockForUpdate()->findOrFail($blogPost->id);
            $wasPublished = (bool) $post->published;
            $post->update($data);
            $event = !$wasPublished && $post->published ? 'publishing.blog_post_published.v1'
                : ($wasPublished && !$post->published ? 'publishing.blog_post_unpublished.v1' : 'publishing.blog_post_updated.v1');
            DomainOutbox::append('event', $event, ['references' => [['resource' => 'BlogPost', 'id' => $post->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $post;
        }, 3);
        return WorkflowResult::payload(['record' => $post, 'id' => $post->id]);
    }

    public function delete(WorkflowInput $input, BlogPost $blogPost): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($blogPost, $actor) {
            $post = BlogPost::query()->lockForUpdate()->findOrFail($blogPost->id);
            if ($post->published) throw ValidationException::withMessages(['blog_post' => 'Unpublish an article before deleting it.']);
            $id = $post->id;
            $post->delete();
            DomainOutbox::append('event', 'publishing.blog_post_deleted.v1', ['references' => [['resource' => 'BlogPost', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
