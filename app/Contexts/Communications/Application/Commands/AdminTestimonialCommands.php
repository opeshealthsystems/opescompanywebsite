<?php

namespace App\Contexts\Communications\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminTestimonialCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); return $u; }
    private function values(WorkflowInput $input, bool $update = false): array
    {
        $p = $update ? 'sometimes|' : '';
        return $input->validate(['author_name' => $p.'required|string|max:120', 'author_title' => 'sometimes|nullable|string|max:120', 'author_facility' => 'sometimes|nullable|string|max:150',
            'author_country' => 'sometimes|nullable|string|max:80', 'body' => $p.'required|string|max:600', 'body_fr' => 'sometimes|nullable|string|max:600', 'rating' => $p.'required|integer|min:1|max:5',
            'sort_order' => $p.'required|integer|min:0|max:100000', 'is_active' => $p.'required|boolean', 'id' => 'prohibited']);
    }
    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input);
        $record = DB::transaction(function () use ($data, $actor) { $r = Testimonial::create($data); DomainOutbox::append('event', 'communications.testimonial_created.v1', ['references' => [['resource' => 'Testimonial', 'id' => $r->id]]], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }
    public function update(WorkflowInput $input, Testimonial $testimonial): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input, true); if (!$data) throw ValidationException::withMessages(['testimonial' => 'Provide at least one testimonial field to update.']);
        $r = DB::transaction(function () use ($testimonial, $data, $actor) { $r = Testimonial::query()->lockForUpdate()->findOrFail($testimonial->id); $r->update($data); DomainOutbox::append('event', 'communications.testimonial_updated.v1', ['references' => [['resource' => 'Testimonial', 'id' => $r->id]], 'changed_fields' => array_keys($data)], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $r, 'id' => $r->id]);
    }
    public function delete(WorkflowInput $input, Testimonial $testimonial): WorkflowResult
    {
        $actor = $this->admin($input); $id = DB::transaction(function () use ($testimonial, $actor) { $r = Testimonial::query()->lockForUpdate()->findOrFail($testimonial->id); $id = $r->id; $r->delete(); DomainOutbox::append('event', 'communications.testimonial_deleted.v1', ['references' => [['resource' => 'Testimonial', 'id' => $id]]], $actor->id); return $id; }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
