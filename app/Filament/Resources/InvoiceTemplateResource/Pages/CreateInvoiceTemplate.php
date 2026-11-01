<?php

namespace App\Filament\Resources\InvoiceTemplateResource\Pages;

use App\Filament\Resources\InvoiceTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoiceTemplate extends CreateRecord
{
    protected static string $resource = InvoiceTemplateResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.finance.invoiceTemplates.create', $data);
    }
}
