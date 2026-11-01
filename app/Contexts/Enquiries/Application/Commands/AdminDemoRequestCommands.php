<?php

namespace App\Contexts\Enquiries\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\DemoRequest;
use App\Models\User;
use App\Models\WebsiteFormDelivery;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AdminDemoRequestCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
        return $user;
    }

    private function values(WorkflowInput $input, bool $update = false): array
    {
        $required = $update ? 'sometimes|required|' : 'required|';
        $data = $input->validate([
            'name' => $required.'string|max:100', 'email' => $required.'email|max:150', 'phone' => 'sometimes|nullable|string|max:30',
            'organization_name' => $required.'string|max:150', 'country' => 'sometimes|nullable|string|max:60',
            'institution_type' => ['sometimes', 'nullable', 'string', 'max:40', Rule::in(['government_hospital', 'private_hospital', 'clinic', 'pharmacy', 'laboratory', 'ministry', 'ngo', 'other'])],
            'institution_size' => ['sometimes', 'nullable', Rule::in(['1-10', '11-50', '51-200', '201-500', '500+'])],
            'products' => 'sometimes|nullable|array|max:20', 'products.*' => 'required|string|max:60|distinct:strict',
            'message' => 'sometimes|nullable|string|max:2000', 'preferred_date' => 'sometimes|nullable|date',
            'locale' => 'sometimes|in:en,fr', 'status' => ['sometimes', Rule::in(['new', 'contacted', 'scheduled', 'completed', 'rejected'])],
            'admin_notes' => 'sometimes|nullable|string|max:20000', 'id' => 'prohibited', 'ip_address' => 'prohibited',
        ]);
        if (!$update) {
            $data['locale'] = $data['locale'] ?? $input->locale();
            $data['status'] = $data['status'] ?? 'new';
        }
        return $data;
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->values($input);
        $request = DB::transaction(function () use ($data, $actor) {
            $request = DemoRequest::create($data);
            DomainOutbox::append('event', 'enquiries.demo_request_recorded_by_admin.v1', ['references' => [['resource' => 'DemoRequest', 'id' => $request->id]]], $actor->id);
            return $request;
        }, 3);
        return WorkflowResult::payload(['record' => $request, 'id' => $request->id], 201);
    }

    public function update(WorkflowInput $input, DemoRequest $demoRequest): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->values($input, true);
        if (!$data) throw ValidationException::withMessages(['demo_request' => 'Provide at least one demo-request field to update.']);
        $request = DB::transaction(function () use ($demoRequest, $data, $actor) {
            $request = DemoRequest::query()->lockForUpdate()->findOrFail($demoRequest->id);
            $request->update($data);
            DomainOutbox::append('event', 'enquiries.demo_request_updated.v1', ['references' => [['resource' => 'DemoRequest', 'id' => $request->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $request;
        }, 3);
        return WorkflowResult::payload(['record' => $request, 'id' => $request->id]);
    }

    public function delete(WorkflowInput $input, DemoRequest $demoRequest): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($demoRequest, $actor) {
            $request = DemoRequest::query()->lockForUpdate()->findOrFail($demoRequest->id);
            $pending = WebsiteFormDelivery::query()->where('submission_type', DemoRequest::class)->where('submission_id', $request->id)
                ->where('status', '!=', 'sent')->exists();
            if ($pending) throw ValidationException::withMessages(['demo_request' => 'Resolve the contact-mail delivery before deleting this request.']);
            $id = $request->id;
            $request->delete();
            DomainOutbox::append('event', 'enquiries.demo_request_deleted.v1', ['references' => [['resource' => 'DemoRequest', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
