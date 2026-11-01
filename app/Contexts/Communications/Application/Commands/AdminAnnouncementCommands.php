<?php

namespace App\Contexts\Communications\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminAnnouncementCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function values(WorkflowInput $input, bool $update = false): array
    {
        $prefix = $update ? 'sometimes|' : '';
        return $input->validate([
            'title' => $prefix.'required|string|max:200', 'body' => $prefix.'required|string|max:50000',
            'audience' => $prefix.'required|in:all,admin,support,tester', 'is_pinned' => $prefix.'required|boolean',
            'is_active' => $prefix.'required|boolean', 'published_at' => 'sometimes|nullable|date',
            'author_id' => 'prohibited', 'id' => 'prohibited',
        ]);
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input);
        $record = DB::transaction(function () use ($data, $actor) {
            $record = Announcement::create($data + ['author_id' => $actor->id]);
            DomainOutbox::append('event', 'communications.announcement_created.v1', ['references' => [['resource' => 'Announcement', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function update(WorkflowInput $input, Announcement $announcement): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input, true);
        if (!$data) throw ValidationException::withMessages(['announcement' => 'Provide at least one announcement field to update.']);
        $record = DB::transaction(function () use ($announcement, $data, $actor) {
            $record = Announcement::query()->lockForUpdate()->findOrFail($announcement->id);
            $record->update($data);
            DomainOutbox::append('event', 'communications.announcement_updated.v1', ['references' => [['resource' => 'Announcement', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function delete(WorkflowInput $input, Announcement $announcement): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($announcement, $actor) {
            $record = Announcement::query()->lockForUpdate()->findOrFail($announcement->id); $id = $record->id; $record->delete();
            DomainOutbox::append('event', 'communications.announcement_deleted.v1', ['references' => [['resource' => 'Announcement', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
