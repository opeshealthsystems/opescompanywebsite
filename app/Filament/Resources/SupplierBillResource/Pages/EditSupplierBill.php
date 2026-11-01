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
        $data['items'] = $this->record->items()->get(['description', 'quantity', 'unit_price'])->toArray();
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.finance.supplierBills.update',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, parameters: ['supplierBill' => $record], actor: auth()->user(), language: app()->getLocale()),
        );

        return SupplierBill::query()->findOrFail($record->id);
    }
}
