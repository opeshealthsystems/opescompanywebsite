<?php

namespace App\Contexts\Support\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\KnowledgeBaseArticle;
use App\Models\KnowledgeBaseCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AdminKnowledgeBaseCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); return $u; }

    public function createArticle(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['title' => 'required|string|max:250', 'slug' => ['required', 'string', 'max:250', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('knowledge_base_articles', 'slug')],
            'category_id' => 'nullable|integer|exists:knowledge_base_categories,id', 'content' => 'required|string|max:100000', 'is_public' => 'required|boolean', 'tags' => 'nullable|array|max:50', 'tags.*' => 'string|max:50',
            'author_id' => 'prohibited', 'status' => 'prohibited', 'views' => 'prohibited', 'published_at' => 'prohibited', 'id' => 'prohibited']);
        $record = DB::transaction(function () use ($data, $actor) { $record = KnowledgeBaseArticle::create($data + ['author_id' => $actor->id, 'status' => 'draft', 'views' => 0]); DomainOutbox::append('event', 'support.knowledge_article_created.v1', ['references' => [['resource' => 'KnowledgeBaseArticle', 'id' => $record->id]]], $actor->id); return $record; }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function updateArticle(WorkflowInput $input, KnowledgeBaseArticle $article): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['title' => 'sometimes|required|string|max:250', 'slug' => ['sometimes', 'required', 'string', 'max:250', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('knowledge_base_articles', 'slug')->ignore($article->id)],
            'category_id' => 'sometimes|nullable|integer|exists:knowledge_base_categories,id', 'content' => 'sometimes|required|string|max:100000', 'is_public' => 'sometimes|required|boolean', 'tags' => 'sometimes|nullable|array|max:50', 'tags.*' => 'string|max:50',
            'author_id' => 'prohibited', 'status' => 'prohibited', 'views' => 'prohibited', 'published_at' => 'prohibited', 'id' => 'prohibited']);
        if (!$data) throw ValidationException::withMessages(['article' => 'Provide at least one editable article field.']);
        $record = DB::transaction(function () use ($article, $data, $actor) { $record = KnowledgeBaseArticle::query()->lockForUpdate()->findOrFail($article->id); $record->update($data); DomainOutbox::append('event', 'support.knowledge_article_updated.v1', ['references' => [['resource' => 'KnowledgeBaseArticle', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id); return $record; }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function deleteArticle(WorkflowInput $input, KnowledgeBaseArticle $article): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($article, $actor) { $record = KnowledgeBaseArticle::query()->lockForUpdate()->findOrFail($article->id); if ($record->status === 'published') throw ValidationException::withMessages(['article' => 'Archive a published article before deleting it.']); $id = $record->id; $record->delete(); DomainOutbox::append('event', 'support.knowledge_article_deleted.v1', ['references' => [['resource' => 'KnowledgeBaseArticle', 'id' => $id]]], $actor->id); return $id; }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }

    public function archiveArticle(WorkflowInput $input, KnowledgeBaseArticle $article): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($article, $actor) { $record = KnowledgeBaseArticle::query()->lockForUpdate()->findOrFail($article->id); if ($record->status !== 'archived') { $record->update(['status' => 'archived']); DomainOutbox::append('event', 'support.knowledge_article_archived.v1', ['references' => [['resource' => 'KnowledgeBaseArticle', 'id' => $record->id]]], $actor->id); } return $record; }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status]);
    }

    public function createCategory(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $input->validate(['name' => 'required|string|max:150', 'slug' => ['required', 'string', 'max:150', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('knowledge_base_categories', 'slug')],
            'description' => 'nullable|string|max:5000', 'parent_id' => 'nullable|integer|exists:knowledge_base_categories,id', 'sort_order' => 'required|integer|min:0|max:100000', 'is_public' => 'required|boolean', 'is_active' => 'required|boolean', 'id' => 'prohibited']);
        $record = DB::transaction(function () use ($data, $actor) { $record = KnowledgeBaseCategory::create($data); DomainOutbox::append('event', 'support.knowledge_category_created.v1', ['references' => [['resource' => 'KnowledgeBaseCategory', 'id' => $record->id]]], $actor->id); return $record; }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function updateCategory(WorkflowInput $input, KnowledgeBaseCategory $category): WorkflowResult
    {
        $actor = $this->admin($input); $data = $input->validate(['name' => 'sometimes|required|string|max:150', 'slug' => ['sometimes', 'required', 'string', 'max:150', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('knowledge_base_categories', 'slug')->ignore($category->id)],
            'description' => 'sometimes|nullable|string|max:5000', 'parent_id' => 'sometimes|nullable|integer|exists:knowledge_base_categories,id', 'sort_order' => 'sometimes|required|integer|min:0|max:100000', 'is_public' => 'sometimes|required|boolean', 'is_active' => 'sometimes|required|boolean', 'id' => 'prohibited']);
        if (!$data) throw ValidationException::withMessages(['category' => 'Provide at least one category field to update.']);
        $record = DB::transaction(function () use ($category, $data, $actor) {
            $record = KnowledgeBaseCategory::query()->lockForUpdate()->findOrFail($category->id);
            $parentId = array_key_exists('parent_id', $data) ? $data['parent_id'] : $record->parent_id;
            $seen = [];
            while ($parentId !== null) {
                $parentId = (int) $parentId;
                if ($parentId === (int) $record->id || isset($seen[$parentId])) throw ValidationException::withMessages(['parent_id' => 'A category cannot be nested beneath itself.']);
                $seen[$parentId] = true;
                $parentId = KnowledgeBaseCategory::query()->whereKey($parentId)->value('parent_id');
            }
            $record->update($data);
            DomainOutbox::append('event', 'support.knowledge_category_updated.v1', ['references' => [['resource' => 'KnowledgeBaseCategory', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function deleteCategory(WorkflowInput $input, KnowledgeBaseCategory $category): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($category, $actor) { $record = KnowledgeBaseCategory::query()->lockForUpdate()->findOrFail($category->id); if ($record->articles()->exists() || $record->children()->exists()) throw ValidationException::withMessages(['category' => 'Categories with articles or child categories cannot be deleted.']); $id = $record->id; $record->delete(); DomainOutbox::append('event', 'support.knowledge_category_deleted.v1', ['references' => [['resource' => 'KnowledgeBaseCategory', 'id' => $id]]], $actor->id); return $id; }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
