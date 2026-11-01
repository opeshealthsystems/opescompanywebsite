<?php

namespace App\Contexts\Documents\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\KnowledgeBaseArticle;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

/** Administrative document/content transitions shared by the API and Filament. */
final class AdminPublishingCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    public function createDocument(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'document_template_id' => 'required|integer|exists:document_templates,id', 'title' => 'required|string|max:200',
            'valid_until' => 'nullable|date', 'requires_signature' => 'required|boolean',
            'addressee_user_id' => 'nullable|integer|exists:users,id', 'addressee_name' => 'required_without:addressee_user_id|nullable|string|max:150',
            'addressee_email' => 'nullable|email|max:255', 'variable_values' => 'nullable|array|max:50',
            'variable_values.*' => 'nullable|string|max:2000', 'notes' => 'nullable|string|max:20000',
            'type' => 'prohibited', 'status' => 'prohibited', 'issued_by' => 'prohibited', 'reference_number' => 'prohibited',
            'body_rendered' => 'prohibited', 'signature_token' => 'prohibited', 'signed_at' => 'prohibited',
        ]);
        $template = DocumentTemplate::query()->whereKey($data['document_template_id'])->where('is_active', true)->first();
        if (!$template) throw ValidationException::withMessages(['document_template_id' => 'Choose an active document template.']);
        $variables = $data['variable_values'] ?? [];
        $allowed = collect($template->variables ?? [])->map(fn ($value, $key) => is_string($key) ? $key : $value)->filter(fn ($value) => is_string($value))->all();
        if ($allowed && array_diff(array_keys($variables), $allowed)) throw ValidationException::withMessages(['variable_values' => 'Only variables declared by the selected template may be supplied.']);
        unset($data['variable_values']);
        $record = DB::transaction(function () use ($data, $template, $variables, $actor) {
            $recipient = !empty($data['addressee_user_id']) ? User::query()->findOrFail($data['addressee_user_id']) : null;
            $type = $template->type;
            $signature = (bool) $data['requires_signature'];
            $record = Document::create(array_replace($data, [
                'type' => $type, 'issued_by' => $actor->id, 'reference_number' => $this->nextDocumentReference($type),
                'body_rendered' => Document::renderTemplate($template, $variables), 'status' => $signature ? 'pending_signature' : 'draft',
                'signature_token' => $signature ? Str::random(64) : null,
                'signature_token_expires_at' => $signature ? now()->addDays(30) : null,
                'addressee_name' => $recipient?->name ?? $data['addressee_name'],
                'addressee_email' => $recipient?->email ?? ($data['addressee_email'] ?? null),
            ]));
            DomainOutbox::append('event', 'documents.document_created.v1', ['references' => [['resource' => 'Document', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'reference_number' => $record->reference_number, 'status' => $record->status], 201);
    }

    private function nextDocumentReference(string $type): string
    {
        $year = now()->year;
        $prefix = DocumentTemplate::referencePrefix($type).'-'.$year.'-';
        $name = 'document:'.$type.':'.$year;
        DB::table('business_sequences')->insertOrIgnore(['name' => $name, 'last_value' => 0]);
        $sequence = DB::table('business_sequences')->where('name', $name)->lockForUpdate()->first();
        $previous = (int) $sequence->last_value;
        if ($previous === 0) {
            $previous = Document::query()->where('type', $type)->where('reference_number', 'like', $prefix.'%')->pluck('reference_number')
                ->map(fn ($ref) => ctype_digit(substr($ref, strlen($prefix))) ? (int) substr($ref, strlen($prefix)) : 0)->max() ?? 0;
        }
        $next = $previous + 1;
        DB::table('business_sequences')->where('name', $name)->update(['last_value' => $next]);
        return $prefix.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function updateDocument(WorkflowInput $input, Document $document): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'title' => 'sometimes|required|string|max:200', 'valid_until' => 'sometimes|nullable|date',
            'addressee_user_id' => 'sometimes|nullable|integer|exists:users,id', 'addressee_name' => 'sometimes|required|string|max:150',
            'addressee_email' => 'sometimes|nullable|email|max:255', 'notes' => 'sometimes|nullable|string|max:20000',
            'type' => 'prohibited', 'status' => 'prohibited', 'issued_by' => 'prohibited', 'reference_number' => 'prohibited',
            'body_rendered' => 'prohibited', 'signature_token' => 'prohibited', 'signed_at' => 'prohibited', 'requires_signature' => 'prohibited',
        ]);
        if ($data === []) throw ValidationException::withMessages(['document' => 'Provide at least one editable document field.']);
        $record = DB::transaction(function () use ($document, $data, $actor) {
            $record = Document::query()->lockForUpdate()->findOrFail($document->id);
            if ($record->status !== 'draft') throw ValidationException::withMessages(['status' => 'Only draft documents can be edited.']);
            if (array_key_exists('addressee_user_id', $data) && $data['addressee_user_id']) {
                $recipient = User::query()->findOrFail($data['addressee_user_id']);
                $data['addressee_name'] = $recipient->name;
                $data['addressee_email'] = $recipient->email;
            }
            $record->update($data);
            DomainOutbox::append('event', 'documents.document_updated.v1', ['references' => [['resource' => 'Document', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'reference_number' => $record->reference_number]);
    }

    public function deleteDocument(WorkflowInput $input, Document $document): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($document, $actor) {
            $record = Document::query()->lockForUpdate()->findOrFail($document->id);
            if ($record->status !== 'draft') throw ValidationException::withMessages(['status' => 'Only draft documents can be deleted. Use the void command for issued documents.']);
            $id = $record->id;
            $record->delete();
            DomainOutbox::append('event', 'documents.document_deleted.v1', ['references' => [['resource' => 'Document', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
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
