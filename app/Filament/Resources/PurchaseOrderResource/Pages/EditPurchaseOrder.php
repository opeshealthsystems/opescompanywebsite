<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()->action(fn (PurchaseOrder $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.finance.purchaseOrders.delete', parameters: ['purchaseOrder' => $record->id])),
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
            'admin.finance.purchaseOrders.update',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, parameters: ['purchaseOrder' => $record], actor: auth()->user(), language: app()->getLocale()),
        );

        return PurchaseOrder::query()->findOrFail($record->id);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
