<?php

namespace App\Contexts\Enquiries\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\PartnerApplication;
use App\Models\TesterApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminExternalApplicationCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    public function updatePartner(WorkflowInput $input, PartnerApplication $partnerApplication): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'status' => 'sometimes|in:pending,reviewing,approved,rejected', 'admin_notes' => 'sometimes|nullable|string|max:10000',
            'organization_name' => 'prohibited', 'contact_name' => 'prohibited', 'email' => 'prohibited', 'phone' => 'prohibited',
            'country' => 'prohibited', 'city' => 'prohibited', 'partner_type' => 'prohibited', 'organization_type' => 'prohibited',
            'annual_revenue_range' => 'prohibited', 'target_market' => 'prohibited', 'description' => 'prohibited', 'website' => 'prohibited',
            'locale' => 'prohibited', 'ip_address' => 'prohibited', 'id' => 'prohibited',
        ]);
        if ($data === []) throw ValidationException::withMessages(['application' => 'Provide status or admin notes to update.']);
        $record = DB::transaction(function () use ($partnerApplication, $data, $actor) {
            $record = PartnerApplication::query()->lockForUpdate()->findOrFail($partnerApplication->id);
            if (isset($data['status']) && $data['status'] !== $record->status) {
                $allowed = ['pending' => ['reviewing', 'approved', 'rejected'], 'reviewing' => ['approved', 'rejected'], 'approved' => [], 'rejected' => []];
                if (!in_array($data['status'], $allowed[$record->status] ?? [], true)) throw ValidationException::withMessages(['status' => 'This partner-application status transition is not allowed.']);
            }
            $record->update($data);
            DomainOutbox::append('event', 'enquiries.partner_application_admin_updated.v1', ['references' => [['resource' => 'PartnerApplication', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'status' => $record->status]);
    }

    public function updateTester(WorkflowInput $input, TesterApplication $testerApplication): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'status' => 'sometimes|in:pending,accepted,rejected,active', 'admin_notes' => 'sometimes|nullable|string|max:10000',
            'name' => 'prohibited', 'email' => 'prohibited', 'phone' => 'prohibited', 'profession' => 'prohibited', 'specialty' => 'prohibited',
            'institution_name' => 'prohibited', 'country' => 'prohibited', 'city' => 'prohibited', 'years_experience' => 'prohibited',
            'devices' => 'prohibited', 'platforms' => 'prohibited', 'motivation' => 'prohibited', 'tech_experience' => 'prohibited',
            'locale' => 'prohibited', 'ip_address' => 'prohibited', 'id' => 'prohibited',
        ]);
        if ($data === []) throw ValidationException::withMessages(['application' => 'Provide status or admin notes to update.']);
        $record = DB::transaction(function () use ($testerApplication, $data, $actor) {
            $record = TesterApplication::query()->lockForUpdate()->findOrFail($testerApplication->id);
            if (isset($data['status']) && $data['status'] !== $record->status) {
                $allowed = ['pending' => ['accepted', 'rejected'], 'accepted' => ['active'], 'active' => [], 'rejected' => []];
                if (!in_array($data['status'], $allowed[$record->status] ?? [], true)) throw ValidationException::withMessages(['status' => 'This tester-application status transition is not allowed.']);
            }
            $record->update($data);
            DomainOutbox::append('event', 'enquiries.tester_application_admin_updated.v1', ['references' => [['resource' => 'TesterApplication', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'status' => $record->status]);
    }
}
