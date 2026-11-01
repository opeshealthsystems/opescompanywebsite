<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()->action(fn (Quote $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.finance.quotes.delete', parameters: ['quote' => $record->id]))];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['items'] = \App\Contexts\Shared\Infrastructure\AdminReferenceOptions::routeData('admin.finance.quoteItems', ['quote' => $this->record->id])['items'] ?? [];
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.finance.quotes.update', $data, ['quote' => $record]);
    }
}
