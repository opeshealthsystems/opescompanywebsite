<?php

namespace App\Contexts\Communications\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminEmailTemplateCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
        return $user;
    }

    private function values(WorkflowInput $input, bool $update = false, ?EmailTemplate $record = null): array
    {
        $required = $update ? 'sometimes|required|' : 'required|';
        $data = $input->validate([
            'name' => $required.'string|max:150',
            'slug' => $required.'string|max:100|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'type' => $required.'string|in:welcome,invoice_sent,invoice_reminder,ticket_created,ticket_reply,leave_approval,leave_rejection,password_reset,announcement,contract_sent,quote_sent,general',
            'subject' => $required.'string|max:250', 'body' => $required.'string|max:50000',
            'variables' => 'sometimes|nullable|array|max:50',
            'variables.*' => 'required|string|max:80|regex:/^[A-Za-z][A-Za-z0-9_]*$/|distinct:strict',
            'is_active' => $required.'boolean', 'id' => 'prohibited',
        ]);
        $slugExists = isset($data['slug']) && EmailTemplate::query()->where('slug', $data['slug'])
            ->when($record, fn ($query) => $query->where('id', '!=', $record->id))->exists();
        if ($slugExists) throw ValidationException::withMessages(['slug' => 'This slug is already in use.']);

        $variables = array_values(array_key_exists('variables', $data) ? ($data['variables'] ?? []) : ($record?->variables ?? []));
        $subject = $data['subject'] ?? $record?->subject ?? '';
        $body = $data['body'] ?? $record?->body ?? '';
        preg_match_all('/{{([A-Za-z][A-Za-z0-9_]*)}}/', $subject."\n".$body, $matches);
        $declared = array_flip($variables);
        foreach (array_unique($matches[1]) as $placeholder) {
            if (!isset($declared[$placeholder])) {
                throw ValidationException::withMessages(['variables' => "Declare the {{".$placeholder."}} placeholder in the variables list before saving."]);
            }
        }
        if (preg_match('/{{|}}/', preg_replace('/{{[A-Za-z][A-Za-z0-9_]*}}/', '', $subject."\n".$body))) {
            throw ValidationException::withMessages(['body' => 'Template placeholders must use the form {{variable_name}}.']);
        }

        return $data;
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->values($input);
        $record = DB::transaction(function () use ($data, $actor) {
            $record = EmailTemplate::create($data);
            DomainOutbox::append('event', 'communications.email_template_created.v1', ['references' => [['resource' => 'EmailTemplate', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function update(WorkflowInput $input, EmailTemplate $template): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->values($input, true, $template);
        if (!$data) throw ValidationException::withMessages(['template' => 'Provide at least one email-template field to update.']);
        $updated = DB::transaction(function () use ($template, $data, $actor) {
            $record = EmailTemplate::query()->lockForUpdate()->findOrFail($template->id);
            $record->update($data);
            DomainOutbox::append('event', 'communications.email_template_updated.v1', ['references' => [['resource' => 'EmailTemplate', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $updated, 'id' => $updated->id]);
    }

    public function delete(WorkflowInput $input, EmailTemplate $template): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($template, $actor) {
            $record = EmailTemplate::query()->lockForUpdate()->findOrFail($template->id);
            if ($record->is_active) throw ValidationException::withMessages(['template' => 'Deactivate an email template before deleting it.']);
            $id = $record->id;
            $record->delete();
            DomainOutbox::append('event', 'communications.email_template_deleted.v1', ['references' => [['resource' => 'EmailTemplate', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
