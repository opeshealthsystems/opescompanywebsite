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
        $data['items'] = $this->record->items()->get(['product_name', 'quantity', 'unit_price'])->toArray();
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.finance.quotes.update',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, parameters: ['quote' => $record], actor: auth()->user(), language: app()->getLocale()),
        );

        return Quote::query()->findOrFail($record->id);
    }
}
