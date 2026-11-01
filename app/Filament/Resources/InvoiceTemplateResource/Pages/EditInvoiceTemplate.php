<?php

namespace App\Filament\Resources\InvoiceTemplateResource\Pages;

use App\Filament\Resources\InvoiceTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoiceTemplate extends EditRecord
{
    protected static string $resource = InvoiceTemplateResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.finance.invoiceTemplates.update',
            new \App\Contexts\Shared\Application\WorkflowInput($data, ['invoiceTemplate' => $record], auth()->user(), app()->getLocale()),
        );
        return $record->fresh();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
