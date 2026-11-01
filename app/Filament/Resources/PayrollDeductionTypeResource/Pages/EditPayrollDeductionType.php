<?php

namespace App\Filament\Resources\PayrollDeductionTypeResource\Pages;

use App\Filament\Resources\PayrollDeductionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPayrollDeductionType extends EditRecord
{
    protected static string $resource = PayrollDeductionTypeResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.finance.payrollDeductionTypes.update', $data, ['deductionType' => $record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->using(function ($record) {
                \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.finance.payrollDeductionTypes.delete', parameters: ['deductionType' => $record->id]);
                return true;
            }),
        ];
    }
}
