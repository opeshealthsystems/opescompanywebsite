<?php

namespace App\Filament\Resources\CreditNoteResource\Pages;

use App\Filament\Resources\CreditNoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCreditNote extends CreateRecord
{
    protected static string $resource = CreditNoteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['reference'] = \App\Models\CreditNote::generateReference();
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->recalculateTotals();
    }
}
