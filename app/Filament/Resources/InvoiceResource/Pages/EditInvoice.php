<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['items'] = $this->record->items()->get(['description', 'quantity', 'unit_price'])->toArray();
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()->action(fn (Invoice $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.finance.invoices.delete', parameters: ['invoice' => $record->id]))];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.finance.invoices.update',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, parameters: ['invoice' => $record], actor: auth()->user(), language: app()->getLocale()),
        );

        return Invoice::query()->findOrFail($record->id);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
