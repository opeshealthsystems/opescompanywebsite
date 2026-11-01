<?php

namespace App\Filament\Resources\SupplierBillResource\Pages;

use App\Filament\Resources\SupplierBillResource;
use App\Models\SupplierBill;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditSupplierBill extends EditRecord
{
    protected static string $resource = SupplierBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->action(fn (SupplierBill $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.finance.supplierBills.delete', parameters: ['supplierBill' => $record->id])),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['items'] = \App\Contexts\Shared\Infrastructure\AdminReferenceOptions::routeData('admin.finance.supplierBillItems', ['supplierBill' => $this->record->id])['items'] ?? [];
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.finance.supplierBills.update', $data, ['supplierBill' => $record]);
    }
}
