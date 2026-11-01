<?php

namespace App\Contexts\Operations\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\CompanySetting;
use App\Models\User;

final class AdminCompanySettingsQueries extends WorkflowHandler
{
    public function show(WorkflowInput $input): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        $settings = CompanySetting::query()->firstOrNew(['id' => 1], ['company_name' => 'OPES Health Systems'])->only([
            'company_name', 'company_email', 'company_phone', 'company_address', 'company_website',
            'default_currency', 'fiscal_year_start_month', 'invoice_prefix', 'quote_prefix',
            'default_tax_rate', 'timezone', 'date_format', 'logo_path',
        ]);

        return WorkflowResult::payload(['settings' => $settings]);
    }
}
