<?php

namespace App\Filament\Resources\SupplierBillResource\Pages;

use App\Filament\Resources\SupplierBillResource;
use App\Models\SupplierBill;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSupplierBill extends CreateRecord
{
    protected static string $resource = SupplierBillResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $result = app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.finance.supplierBills.create',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, actor: auth()->user(), language: app()->getLocale()),
        );

        return SupplierBill::query()->findOrFail($result->data['id']);
    }
}
