<?php

namespace App\Contexts\Publishing\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\PartnerInstitution;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final class AdminPartnerInstitutionCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function values(WorkflowInput $input, bool $update = false): array
    {
        $optional = $update ? 'sometimes|' : '';
        $data = $input->validate([
            'name' => $optional.'required|string|max:200', 'name_fr' => $optional.'nullable|string|max:200',
            'type' => $optional.'required|in:university,research_institute,ngo,government,hospital_network,other',
            'country' => $optional.'nullable|string|max:80', 'city' => $optional.'nullable|string|max:80',
            'website' => $optional.'nullable|url|max:300', 'description' => $optional.'nullable|string|max:10000',
            'description_fr' => $optional.'nullable|string|max:10000', 'partnership_since' => $optional.'nullable|integer|min:1990|max:2030',
            'is_featured' => $optional.'boolean', 'is_active' => $optional.'boolean', 'sort_order' => $optional.'integer|min:0|max:65535', 'id' => 'prohibited',
        ]);
        if ($input->hasFile('logo')) {
            $input->validate(['logo' => 'image|max:1024']);
            $data['logo'] = $input->file('logo')->store('partners', 'public');
        } elseif ($input->has('logo')) {
            $logo = $input->input('logo');
            if ($logo !== null && (!is_string($logo) || !str_starts_with($logo, 'partners/') || str_contains($logo, '..') || !Storage::disk('public')->exists($logo))) {
                throw ValidationException::withMessages(['logo' => 'Choose an existing managed partner logo or upload an image.']);
            }
            $data['logo'] = $logo;
        }
        return $data;
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input);
        $record = DB::transaction(function () use ($data, $actor) {
            $record = PartnerInstitution::create($data + ['country' => 'CM', 'is_active' => true, 'is_featured' => false, 'sort_order' => 0]);
            DomainOutbox::append('event', 'publishing.partner_institution_created.v1', ['references' => [['resource' => 'PartnerInstitution', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function update(WorkflowInput $input, PartnerInstitution $institution): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input, true);
        if ($data === []) throw ValidationException::withMessages(['institution' => 'Provide at least one field to update.']);
        $record = DB::transaction(function () use ($institution, $data, $actor) {
            $record = PartnerInstitution::query()->lockForUpdate()->findOrFail($institution->id);
            $record->update($data);
            DomainOutbox::append('event', 'publishing.partner_institution_updated.v1', ['references' => [['resource' => 'PartnerInstitution', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }
}
