<?php

namespace App\Contexts\Publishing\Application\Commands;

use App\Models\BlogComment;
use App\Models\BlogPost;
class BlogCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function comment(\App\Contexts\Shared\Application\WorkflowInput $request, string $locale, string $slug): \App\Contexts\Shared\Application\WorkflowResult
    {
        $post = \App\Models\BlogPost::where('slug', $slug)->where('published', true)->firstOrFail();
        $validated = $request->validate(['name' => 'required|string|max:100', 'email' => 'required|email|max:150', 'content' => 'required|string|max:2000']);
        $post->comments()->create($validated);
        return \App\Contexts\Shared\Application\WorkflowResult::next('blogcontroller_comment_1')->with('comment_submitted', true);
    }
    public function like(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, string $locale, string $slug): \App\Contexts\Shared\Application\WorkflowResult
    {
        $post = \App\Models\BlogPost::where('slug', $slug)->where('published', true)->firstOrFail();
        $post->increment('likes');
        return \App\Contexts\Shared\Application\WorkflowResult::payload(['likes' => $post->likes]);
    }
    public function share(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, string $locale, string $slug): \App\Contexts\Shared\Application\WorkflowResult
    {
        $post = \App\Models\BlogPost::where('slug', $slug)->where('published', true)->firstOrFail();
        $post->increment('shares');
        return \App\Contexts\Shared\Application\WorkflowResult::payload(['shares' => $post->shares]);
    }
}
