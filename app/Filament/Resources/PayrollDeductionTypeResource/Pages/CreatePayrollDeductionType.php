<?php

namespace App\Filament\Resources\PayrollDeductionTypeResource\Pages;

use App\Filament\Resources\PayrollDeductionTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayrollDeductionType extends CreateRecord
{
    protected static string $resource = PayrollDeductionTypeResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.finance.payrollDeductionTypes.create', $data);
    }
}
