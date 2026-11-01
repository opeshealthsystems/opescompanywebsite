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
                ->action(fn () => app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
                    'admin.finance.expenses.approve',
                    new \App\Contexts\Shared\Application\WorkflowInput([], ['expense' => $this->record], auth()->user(), app()->getLocale()),
                ))
                ->after(fn () => $this->refreshFormData(['status', 'approved_by', 'approved_at'])),
            Actions\Action::make('mark_paid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-banknotes')
                ->color('info')
                ->requiresConfirmation()
                ->hidden(fn () => $this->record->status !== 'approved')
                ->action(fn () => app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
                    'admin.finance.expenses.markPaid',
                    new \App\Contexts\Shared\Application\WorkflowInput([], ['expense' => $this->record], auth()->user(), app()->getLocale()),
                ))
                ->after(fn () => $this->refreshFormData(['status'])),
        ];
    }
}
