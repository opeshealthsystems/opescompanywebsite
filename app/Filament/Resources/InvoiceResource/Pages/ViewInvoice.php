<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('mark_sent')
                ->label('Mark as Sent')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->visible(fn () => $this->record->status === 'draft')
                ->action(function () {
                    $this->record->update(['status' => 'sent']);
                    Notification::make()->title('Invoice marked as sent.')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('mark_paid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => in_array($this->record->status, ['sent', 'overdue']))
                ->action(function () {
                    $this->record->update(['status' => 'paid', 'paid_at' => now()]);
                    Notification::make()->title('Invoice marked as paid.')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('mark_overdue')
                ->label('Mark as Overdue')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'sent')
                ->action(function () {
                    $this->record->update(['status' => 'overdue']);
                    Notification::make()->title('Invoice marked as overdue.')->warning()->send();
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn () => route('invoices.pdf', ['invoice' => $this->record->id]))
                ->openUrlInNewTab(),
        ];
    }
}
