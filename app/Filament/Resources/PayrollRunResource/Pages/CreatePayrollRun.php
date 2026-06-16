<?php

namespace App\Filament\Resources\PayrollRunResource\Pages;

use App\Filament\Resources\PayrollRunResource;
use App\Models\PayrollRun;
use Filament\Resources\Pages\CreateRecord;

class CreatePayrollRun extends CreateRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['reference'] = PayrollRun::generateReference();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
