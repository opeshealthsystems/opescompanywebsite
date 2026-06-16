<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['reference'] = \App\Models\Quote::generateReference();
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->recalculateTotals();
    }
}
