<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewExpense extends ViewRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->hidden(fn () => $this->record->status !== 'pending')
                ->action(fn () => $this->record->update([
                    'status'      => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]))
                ->after(fn () => $this->refreshFormData(['status', 'approved_by', 'approved_at'])),
            Actions\Action::make('mark_paid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-banknotes')
                ->color('info')
                ->requiresConfirmation()
                ->hidden(fn () => $this->record->status !== 'approved')
                ->action(fn () => $this->record->update(['status' => 'paid']))
                ->after(fn () => $this->refreshFormData(['status'])),
        ];
    }
}
