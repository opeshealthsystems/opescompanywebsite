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
        $data['items'] = \App\Contexts\Shared\Infrastructure\AdminReferenceOptions::routeData('admin.finance.purchaseOrderItems', ['purchaseOrder' => $this->record->id])['items'] ?? [];
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.finance.purchaseOrders.update', $data, ['purchaseOrder' => $record]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
