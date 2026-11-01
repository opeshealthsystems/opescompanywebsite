<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $result = app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.finance.invoices.create',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, actor: auth()->user(), language: app()->getLocale()),
        );

        return Invoice::query()->findOrFail($result->data['id']);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
