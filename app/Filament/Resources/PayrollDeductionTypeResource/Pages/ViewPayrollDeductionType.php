<?php

namespace App\Filament\Resources\PayrollDeductionTypeResource\Pages;

use App\Filament\Resources\PayrollDeductionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPayrollDeductionType extends ViewRecord
{
    protected static string $resource = PayrollDeductionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
