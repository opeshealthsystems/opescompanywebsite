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
        $data['items'] = \App\Contexts\Shared\Infrastructure\AdminReferenceOptions::routeData('admin.finance.invoiceItems', ['invoice' => $this->record->id])['items'] ?? [];
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()->action(fn (Invoice $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.finance.invoices.delete', parameters: ['invoice' => $record->id]))];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.finance.invoices.update', $data, ['invoice' => $record]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
