<?php

namespace App\Contexts\Documents\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Document;
use App\Models\KnowledgeBaseArticle;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Administrative document/content transitions shared by the API and Filament. */
final class AdminPublishingCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    public function markDocumentSent(WorkflowInput $input, Document $document): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($document, $actor) {
            $record = Document::query()->lockForUpdate()->findOrFail($document->id);
            if ($record->status === 'draft') {
                $record->update(['status' => 'sent']);
                DomainOutbox::append('event', 'documents.document_sent.v1', ['references' => [['resource' => 'Document', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'sent') throw ValidationException::withMessages(['status' => 'Only draft documents can be marked sent.']);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Document marked sent.');
    }

    public function voidDocument(WorkflowInput $input, Document $document): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($document, $actor) {
            $record = Document::query()->lockForUpdate()->findOrFail($document->id);
            if ($record->status === 'voided') return $record;
            if ($record->status === 'signed') throw ValidationException::withMessages(['status' => 'Signed documents cannot be voided.']);
            $record->update(['status' => 'voided']);
            DomainOutbox::append('event', 'documents.document_voided.v1', ['references' => [['resource' => 'Document', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Document voided.');
    }

    public function publishArticle(WorkflowInput $input, KnowledgeBaseArticle $article): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($article, $actor) {
            $record = KnowledgeBaseArticle::query()->lockForUpdate()->findOrFail($article->id);
            if ($record->status !== 'published') {
                $record->update(['status' => 'published', 'published_at' => $record->published_at ?? now()]);
                DomainOutbox::append('event', 'content.knowledge_article_published.v1', ['references' => [['resource' => 'KnowledgeBaseArticle', 'id' => $record->id]]], $actor->id);
            }
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status, 'published_at' => $record->published_at?->toISOString()])->with('success', 'Article published.');
    }
}
