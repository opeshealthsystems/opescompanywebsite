<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Contexts\Shared\Infrastructure\DomainMail;
use App\Models\Risk;
use App\Models\TrainingRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/** Shared admin commands for risk mitigation and staff training completion. */
final class AdminPeopleCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    public function mitigateRisk(WorkflowInput $input, Risk $risk): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($risk, $actor) {
            $record = Risk::query()->lockForUpdate()->findOrFail($risk->id);
            if ($record->status === 'mitigated') return $record;
            if ($record->status !== 'open') throw ValidationException::withMessages(['status' => 'Only open risks can be marked mitigated.']);
            $record->update(['status' => 'mitigated']);
            DomainOutbox::append('event', 'risk.mitigation_recorded.v1', ['references' => [['resource' => 'Risk', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Risk marked mitigated.');
    }

    public function createRisk(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'title' => 'required|string|max:250',
            'description' => 'nullable|string|max:5000',
            'category' => 'required|in:'.implode(',', array_keys(Risk::categoryOptions())),
            'owner_id' => 'required|integer|exists:users,id',
            'likelihood' => 'required|in:'.implode(',', array_keys(Risk::likelihoodOptions())),
            'impact' => 'required|in:'.implode(',', array_keys(Risk::impactOptions())),
            'review_date' => 'nullable|date',
            'mitigation_plan' => 'nullable|string|max:5000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'risk_score' => 'prohibited',
        ]);

        $record = DB::transaction(function () use ($data, $actor) {
            $year = now()->year;
            $sequenceName = 'risk:'.$year;
            DB::table('business_sequences')->insertOrIgnore(['name' => $sequenceName, 'last_value' => 0]);
            $sequence = DB::table('business_sequences')->where('name', $sequenceName)->lockForUpdate()->first();
            $previous = (int) $sequence->last_value;
            if ($previous === 0) {
                $prefix = 'RISK-'.$year.'-';
                $previous = Risk::query()->where('reference', 'like', $prefix.'%')->pluck('reference')
                    ->map(fn ($reference) => ctype_digit(substr($reference, strlen($prefix))) ? (int) substr($reference, strlen($prefix)) : 0)->max() ?? 0;
            }
            $next = $previous + 1;
            DB::table('business_sequences')->where('name', $sequenceName)->update(['last_value' => $next]);
            $record = Risk::create($data + [
                'reference' => 'RISK-'.$year.'-'.str_pad((string) $next, 3, '0', STR_PAD_LEFT),
                'risk_score' => Risk::computeScore($data['likelihood'], $data['impact']),
                'status' => 'open',
            ]);
            DomainOutbox::append('event', 'risk.created.v1', [
                'references' => [['resource' => 'Risk', 'id' => $record->id]],
                'changed_fields' => array_keys($data),
            ], $actor->id);
            return $record;
        }, 3);

        return WorkflowResult::payload(['id' => $record->id, 'reference' => $record->reference, 'status' => $record->status, 'risk_score' => $record->risk_score], 201)->with('success', 'Risk recorded.');
    }

    public function updateRisk(WorkflowInput $input, Risk $risk): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'title' => 'sometimes|required|string|max:250',
            'description' => 'sometimes|nullable|string|max:5000',
            'category' => 'sometimes|required|in:'.implode(',', array_keys(Risk::categoryOptions())),
            'owner_id' => 'sometimes|required|integer|exists:users,id',
            'likelihood' => 'sometimes|required|in:'.implode(',', array_keys(Risk::likelihoodOptions())),
            'impact' => 'sometimes|required|in:'.implode(',', array_keys(Risk::impactOptions())),
            'review_date' => 'sometimes|nullable|date',
            'mitigation_plan' => 'sometimes|nullable|string|max:5000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'risk_score' => 'prohibited',
        ]);
        if ($data === []) throw ValidationException::withMessages(['risk' => 'Provide at least one risk field to update.']);

        $record = DB::transaction(function () use ($risk, $data, $actor) {
            $record = Risk::query()->lockForUpdate()->findOrFail($risk->id);
            if ($record->status !== 'open') throw ValidationException::withMessages(['status' => 'Only open risks can be edited.']);
            $record->fill($data);
            $record->risk_score = Risk::computeScore($record->likelihood, $record->impact);
            $record->save();
            DomainOutbox::append('event', 'risk.updated.v1', [
                'references' => [['resource' => 'Risk', 'id' => $record->id]],
                'changed_fields' => array_keys($data),
            ], $actor->id);
            return $record;
        }, 3);

        return WorkflowResult::payload(['id' => $record->id, 'reference' => $record->reference, 'status' => $record->status, 'risk_score' => $record->risk_score])->with('success', 'Risk updated.');
    }

    private function trainingValues(WorkflowInput $input, bool $update = false): array
    {
        $p = $update ? 'sometimes|' : '';
        $data = $input->validate(['user_id' => $p.'required|integer|exists:users,id', 'title' => $p.'required|string|max:200', 'provider' => 'sometimes|nullable|string|max:150',
            'category' => $p.'required|in:compliance,technical,soft_skills,safety,clinical,management,other', 'start_date' => 'sometimes|nullable|date', 'expires_at' => 'sometimes|nullable|date',
            'certificate_path' => 'sometimes|nullable|string|max:300', 'certificate' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:10240', 'notes' => 'sometimes|nullable|string|max:10000',
            'status' => 'prohibited', 'completed_at' => 'prohibited', 'id' => 'prohibited']);
        if ($input->hasFile('certificate') && !empty($data['certificate_path'])) throw ValidationException::withMessages(['certificate' => 'Send a certificate file or a previously uploaded certificate path, not both.']);
        if ($input->hasFile('certificate')) $data['certificate_path'] = $input->file('certificate')->storePublicly('certificates', 'public');
        elseif (!empty($data['certificate_path'])) {
            $path = $data['certificate_path'];
            if (!str_starts_with($path, 'certificates/') || str_contains($path, '..') || str_contains($path, '\\') || !Storage::disk('public')->exists($path)) {
                throw ValidationException::withMessages(['certificate_path' => 'Select a certificate stored in the certificates directory.']);
            }
        }
        return $data;
    }

    public function createTraining(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->trainingValues($input);
        $record = DB::transaction(function () use ($data, $actor) { $record = TrainingRecord::create($data + ['status' => 'planned']); DomainOutbox::append('event', 'workforce.training_record_created.v1', ['references' => [['resource' => 'TrainingRecord', 'id' => $record->id]]], $actor->id); return $record; }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id], 201);
    }

    public function updateTraining(WorkflowInput $input, TrainingRecord $trainingRecord): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->trainingValues($input, true);
        if (!$data) throw ValidationException::withMessages(['training_record' => 'Provide at least one editable training field.']);
        $record = DB::transaction(function () use ($trainingRecord, $data, $actor) { $record = TrainingRecord::query()->lockForUpdate()->findOrFail($trainingRecord->id); $record->update($data); DomainOutbox::append('event', 'workforce.training_record_updated.v1', ['references' => [['resource' => 'TrainingRecord', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id); return $record; }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id]);
    }

    public function deleteTraining(WorkflowInput $input, TrainingRecord $trainingRecord): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($trainingRecord, $actor) { $record = TrainingRecord::query()->lockForUpdate()->findOrFail($trainingRecord->id); if (in_array($record->status, ['completed', 'expired'], true)) throw ValidationException::withMessages(['training_record' => 'Completed or expired training records are retained as compliance evidence.']); $id = $record->id; $record->delete(); DomainOutbox::append('event', 'workforce.training_record_deleted.v1', ['references' => [['resource' => 'TrainingRecord', 'id' => $id]]], $actor->id); return $id; }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }

    public function completeTraining(WorkflowInput $input, TrainingRecord $trainingRecord): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($trainingRecord, $actor) {
            $record = TrainingRecord::query()->lockForUpdate()->findOrFail($trainingRecord->id);
            if ($record->status === 'completed') return $record;
            if (!in_array($record->status, ['planned', 'in_progress', 'expired'], true)) throw ValidationException::withMessages(['status' => 'This training record cannot be completed from its current state.']);
            $record->update(['status' => 'completed', 'completed_at' => $record->completed_at ?? today()]);
            DomainOutbox::append('event', 'workforce.training_completed.v1', ['references' => [['resource' => 'TrainingRecord', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status, 'completed_at' => $record->completed_at?->toDateString()])->with('success', 'Training marked complete.');
    }

    public function sendWelcome(WorkflowInput $input, User $user): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($user, $actor) {
            $record = User::query()->lockForUpdate()->findOrFail($user->id);
            DomainMail::to($record->email)->queue(new \App\Mail\WelcomeEmail($record));
            $record->notify(new \App\Notifications\FeedEntry('account.welcome', 'Welcome to OPES', 'Welcome to OPES Health Systems.', 'sparkles', null));
            DomainOutbox::append('event', 'identity.welcome_sent.v1', ['references' => [['resource' => 'User', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => 'queued'])->with('success', 'Welcome message queued.');
    }

    public function createUser(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        if ($input->has('roles')) abort_unless($actor->hasRole('super_admin'), 403);
        $data = $input->validate([
            'name' => 'required|string|max:100', 'email' => 'required|email:rfc|max:150|unique:users,email',
            'password' => 'required|string|min:8|max:64', 'phone' => 'nullable|string|max:30',
            'avatar' => 'nullable|string|max:255', 'employee_id' => 'nullable|string|max:50|unique:users,employee_id',
            'hire_date' => 'nullable|date', 'department' => 'nullable|string|max:80', 'position' => 'nullable|string|max:80',
            'is_active' => 'sometimes|boolean', 'roles' => 'sometimes|array|max:10',
            'roles.*' => 'required|string|distinct|exists:roles,name',
            'email_verified_at' => 'prohibited', 'remember_token' => 'prohibited', 'id' => 'prohibited',
        ]);
        $this->validateAvatar($data['avatar'] ?? null);
        $roles = $data['roles'] ?? [];
        unset($data['roles']);
        if (in_array('super_admin', $roles, true)) abort_unless($actor->hasRole('super_admin'), 403);

        $record = DB::transaction(function () use ($data, $roles, $actor) {
            $record = User::create($data + ['is_active' => $data['is_active'] ?? true]);
            if ($roles !== []) $record->syncRoles($roles);
            \App\Contexts\Shared\Infrastructure\DomainMail::to($record->email)->queue(new \App\Mail\WelcomeEmail($record));
            $record->notify(new \App\Notifications\FeedEntry('account.welcome', 'Welcome to OPES', 'Welcome to OPES Health Systems.', 'sparkles', null));
            DomainOutbox::append('event', 'identity.user_created.v1', [
                'references' => [['resource' => 'User', 'id' => $record->id]], 'changed_fields' => array_keys($data),
            ], $actor->id);
            return $record;
        }, 3);

        return WorkflowResult::payload(['id' => $record->id, 'email' => $record->email, 'is_active' => $record->is_active, 'roles' => $record->getRoleNames()->values()], 201)->with('success', 'User account created.');
    }

    public function updateUser(WorkflowInput $input, User $user): WorkflowResult
    {
        $actor = $this->admin($input);
        if ($input->has('roles')) abort_unless($actor->hasRole('super_admin'), 403);
        $data = $input->validate([
            'name' => 'sometimes|required|string|max:100',
            'email' => ['sometimes', 'required', 'email:rfc', 'max:150', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'sometimes|required|string|min:8|max:64', 'phone' => 'sometimes|nullable|string|max:30',
            'avatar' => 'sometimes|nullable|string|max:255',
            'employee_id' => ['sometimes', 'nullable', 'string', 'max:50', \Illuminate\Validation\Rule::unique('users', 'employee_id')->ignore($user->id)],
            'hire_date' => 'sometimes|nullable|date', 'department' => 'sometimes|nullable|string|max:80',
            'position' => 'sometimes|nullable|string|max:80', 'is_active' => 'sometimes|boolean',
            'roles' => 'sometimes|array|max:10', 'roles.*' => 'required|string|distinct|exists:roles,name',
            'email_verified_at' => 'prohibited', 'remember_token' => 'prohibited', 'id' => 'prohibited',
        ]);
        if ($data === []) throw ValidationException::withMessages(['user' => 'Provide at least one user field to update.']);
        $this->validateAvatar($data['avatar'] ?? null);
        $roles = $data['roles'] ?? null;
        unset($data['roles']);
        if ($roles !== null && in_array('super_admin', $roles, true)) abort_unless($actor->hasRole('super_admin'), 403);
        if ($user->id === $actor->id && array_key_exists('is_active', $data) && !$data['is_active']) {
            throw ValidationException::withMessages(['is_active' => 'You cannot deactivate your own account.']);
        }

        $record = DB::transaction(function () use ($user, $data, $roles, $actor) {
            $record = User::query()->lockForUpdate()->findOrFail($user->id);
            if ($record->hasRole('super_admin') && $roles !== null && !in_array('super_admin', $roles, true)
                && User::role('super_admin')->where('is_active', true)->where('id', '!=', $record->id)->exists() === false) {
                throw ValidationException::withMessages(['roles' => 'The last active super administrator cannot be removed.']);
            }
            $record->fill($data)->save();
            if ($roles !== null) $record->syncRoles($roles);
            DomainOutbox::append('event', 'identity.user_updated.v1', [
                'references' => [['resource' => 'User', 'id' => $record->id]],
                'changed_fields' => array_merge(array_keys($data), $roles !== null ? ['roles'] : []),
            ], $actor->id);
            return $record->load('roles');
        }, 3);

        return WorkflowResult::payload(['id' => $record->id, 'email' => $record->email, 'is_active' => $record->is_active, 'roles' => $record->getRoleNames()->values()])->with('success', 'User account updated.');
    }

    public function deleteUser(WorkflowInput $input, User $user): WorkflowResult
    {
        $actor = $this->admin($input);
        abort_unless($actor->id !== $user->id, 403);
        DB::transaction(function () use ($user, $actor) {
            $record = User::query()->lockForUpdate()->findOrFail($user->id);
            if ($record->hasRole('super_admin')) {
                abort_unless($actor->hasRole('super_admin'), 403);
                if (User::role('super_admin')->where('is_active', true)->where('id', '!=', $record->id)->exists() === false) {
                    throw ValidationException::withMessages(['user' => 'The last active super administrator cannot be deleted.']);
                }
            }
            DomainOutbox::append('event', 'identity.user_deleted.v1', ['references' => [['resource' => 'User', 'id' => $record->id]]], $actor->id);
            $record->delete();
        }, 3);
        return WorkflowResult::payload(['id' => $user->id, 'status' => 'deleted'])->with('success', 'User deleted.');
    }

    private function validateAvatar(?string $path): void
    {
        if ($path !== null && $path !== '' && (!str_starts_with($path, 'avatars/') || !Storage::disk('public')->exists($path))) {
            throw ValidationException::withMessages(['avatar' => 'Choose an existing avatar uploaded to the avatars directory.']);
        }
    }
}
