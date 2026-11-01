<?php

namespace App\Contexts\CustomerServices\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AdminCustomerProfileCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function rules(?CustomerProfile $profile = null): array
    {
        $p = $profile ? 'sometimes|' : '';
        return [
            'user_id' => [$profile ? 'sometimes' : 'required', 'required', 'integer', 'exists:users,id', Rule::unique('customer_profiles', 'user_id')->ignore($profile?->id)],
            'facility_name' => $p.'required|string|max:200',
            'facility_type' => $p.'nullable|in:clinic,hospital,laboratory,pharmacy,specialist_centre,ngos,government,other',
            'country' => $p.'nullable|string|max:80', 'city' => $p.'nullable|string|max:80', 'address' => $p.'nullable|string|max:255',
        ];
    }

    private function customer(int|string $id): void
    {
        if (!User::query()->whereKey($id)->whereHas('roles', fn ($roles) => $roles->where('name', 'customer'))->exists()) {
            throw ValidationException::withMessages(['user_id' => 'Select a customer account.']);
        }
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate($this->rules());
        $this->customer($data['user_id']);
        $data['country'] = filled($data['country'] ?? null) ? $data['country'] : 'CM';
        $profile = DB::transaction(function () use ($data, $actor) {
            $record = CustomerProfile::create($data);
            DomainOutbox::append('event', 'customer.profile_created.v1', ['references' => [['resource' => 'CustomerProfile', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $profile->id, 'user_id' => $profile->user_id], 201)->with('success', 'Customer profile created.');
    }

    public function update(WorkflowInput $input, CustomerProfile $customerProfile): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate($this->rules($customerProfile));
        if ($data === []) throw ValidationException::withMessages(['customer_profile' => 'Provide at least one profile field to update.']);
        $this->customer($data['user_id'] ?? $customerProfile->user_id);
        if (array_key_exists('country', $data) && !filled($data['country'])) $data['country'] = 'CM';
        $record = DB::transaction(function () use ($customerProfile, $data, $actor) {
            $record = CustomerProfile::query()->lockForUpdate()->findOrFail($customerProfile->id);
            $record->update($data);
            DomainOutbox::append('event', 'customer.profile_updated.v1', ['references' => [['resource' => 'CustomerProfile', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'user_id' => $record->user_id])->with('success', 'Customer profile updated.');
    }
}
