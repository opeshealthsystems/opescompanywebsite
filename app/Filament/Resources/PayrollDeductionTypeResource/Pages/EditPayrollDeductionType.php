<?php

namespace App\Filament\Resources\PayrollDeductionTypeResource\Pages;

use App\Filament\Resources\PayrollDeductionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPayrollDeductionType extends EditRecord
{
    protected static string $resource = PayrollDeductionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
