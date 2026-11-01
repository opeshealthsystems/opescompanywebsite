<?php

namespace App\Contexts\Operations\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final class AdminCompanySettingsCommands extends WorkflowHandler
{
    public function update(WorkflowInput $input): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        $rules = [
            'company_name' => 'sometimes|required|string|max:200',
            'company_email' => 'sometimes|nullable|email|max:150',
            'company_phone' => 'sometimes|nullable|string|max:40',
            'company_address' => 'sometimes|nullable|string|max:2000',
            'company_website' => 'sometimes|nullable|url|max:255',
            'default_currency' => 'sometimes|required|in:XAF,USD,EUR,GBP',
            'fiscal_year_start_month' => 'sometimes|required|integer|between:1,12',
            'invoice_prefix' => 'sometimes|nullable|string|max:20',
            'quote_prefix' => 'sometimes|nullable|string|max:20',
            'default_tax_rate' => 'sometimes|nullable|numeric|between:0,100',
            'timezone' => ['sometimes', 'required', 'string', 'in:'.implode(',', array_keys(CompanySetting::timezoneOptions()))],
            'date_format' => 'sometimes|nullable|string|max:30',
            'logo_path' => 'sometimes|nullable|string|max:255',
        ];
        $data = $input->validate($rules);
        if ($data === []) throw ValidationException::withMessages(['settings' => 'Provide at least one setting to update.']);
        if (isset($data['logo_path']) && $data['logo_path'] !== null
            && (!str_starts_with($data['logo_path'], 'company/') || !Storage::disk('public')->exists($data['logo_path']))) {
            throw ValidationException::withMessages(['logo_path' => 'Choose an existing company logo uploaded through the admin portal.']);
        }

        $record = DB::transaction(function () use ($data, $actor) {
            $record = CompanySetting::query()->lockForUpdate()->firstOrCreate(['id' => 1], ['company_name' => 'OPES Health Systems']);
            $record->fill($data);
            if (!filled($record->company_name)) throw ValidationException::withMessages(['company_name' => 'Company name is required.']);
            $record->save();
            DomainOutbox::append('event', 'operations.company_settings_updated.v1', [
                'references' => [['resource' => 'CompanySetting', 'id' => $record->id]],
                'changed_fields' => array_values(array_diff(array_keys($data), ['logo_path'])),
            ], $actor->id);
            return $record;
        }, 3);

        return WorkflowResult::payload(['id' => $record->id, 'status' => 'updated', 'updated_fields' => array_keys($data)])->with('success', 'Company settings saved.');
    }
}
