<?php

namespace App\Contexts\Support\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\KnowledgeBaseArticle;
use App\Models\KnowledgeBaseCategory;
use Illuminate\Database\Eloquent\Builder;

final class AdminKnowledgeBaseQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); }
    private function article(KnowledgeBaseArticle $a): array { return ['id' => $a->id, 'title' => $a->title, 'slug' => $a->slug, 'category_id' => $a->category_id, 'author_id' => $a->author_id, 'content' => $a->content, 'status' => $a->status, 'is_public' => (bool) $a->is_public, 'views' => $a->views, 'tags' => $a->tags, 'published_at' => $a->published_at?->toISOString()]; }
    private function category(KnowledgeBaseCategory $c): array { return ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug, 'description' => $c->description, 'parent_id' => $c->parent_id, 'sort_order' => $c->sort_order, 'is_public' => (bool) $c->is_public, 'is_active' => (bool) $c->is_active]; }
    private function page($p, callable $mapper): array { return ['items' => $p->getCollection()->map($mapper)->all(), 'pagination' => ['current_page' => $p->currentPage(), 'per_page' => $p->perPage(), 'total' => $p->total(), 'last_page' => $p->lastPage()]]; }
    public function articleQuery(WorkflowInput $input): Builder { $this->admin($input); return KnowledgeBaseArticle::query()->with(['author', 'category'])->orderByDesc('created_at'); }
    public function categoryQuery(WorkflowInput $input): Builder { $this->admin($input); return KnowledgeBaseCategory::query()->with('parent')->orderBy('sort_order')->orderBy('name'); }
    public function articles(WorkflowInput $input): WorkflowResult { return WorkflowResult::payload($this->page($input->paginate($this->articleQuery($input)), fn ($a) => $this->article($a))); }
    public function showArticle(WorkflowInput $input, KnowledgeBaseArticle $article): WorkflowResult { $this->admin($input); return WorkflowResult::payload(['article' => $this->article($article)]); }
    public function categories(WorkflowInput $input): WorkflowResult { return WorkflowResult::payload($this->page($input->paginate($this->categoryQuery($input)), fn ($c) => $this->category($c))); }
    public function showCategory(WorkflowInput $input, KnowledgeBaseCategory $category): WorkflowResult { $this->admin($input); return WorkflowResult::payload(['category' => $this->category($category)]); }
}
