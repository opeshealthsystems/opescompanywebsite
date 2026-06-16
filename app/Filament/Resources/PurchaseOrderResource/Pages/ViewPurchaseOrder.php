<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'submitted'
                    && auth()->user()?->hasAnyRole(['super_admin', 'admin']))
                ->action(function () {
                    $this->record->update([
                        'status'      => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);
                })
                ->after(fn () => $this->refreshFormData(['status', 'approved_by', 'approved_at'])),

            Actions\Action::make('mark_received')
                ->label('Mark Received')
                ->icon('heroicon-o-inbox-arrow-down')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'approved')
                ->action(function () {
                    $this->record->update([
                        'status'        => 'received',
                        'received_date' => now(),
                    ]);
                })
                ->after(fn () => $this->refreshFormData(['status', 'received_date'])),
        ];
    }
}
