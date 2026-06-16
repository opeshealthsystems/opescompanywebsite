<?php

namespace App\Filament\Resources\PayrollDeductionTypeResource\Pages;

use App\Filament\Resources\PayrollDeductionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayrollDeductionTypes extends ListRecords
{
    protected static string $resource = PayrollDeductionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
