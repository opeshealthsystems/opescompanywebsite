<?php

namespace App\Filament\Resources\SupplierBillResource\Pages;

use App\Filament\Resources\SupplierBillResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSupplierBill extends ViewRecord
{
    protected static string $resource = SupplierBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->url(fn () => route('supplier-bills.pdf', $this->record))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
            Actions\DeleteAction::make()->action(fn () => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.finance.supplierBills.delete', parameters: ['supplierBill' => $this->record->id])),
        ];
    }
}
