<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\Expense;

class EditExpense extends EditRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.finance.expenses.update',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, parameters: ['expense' => $record], actor: auth()->user(), language: app()->getLocale()),
        );

        return Expense::query()->findOrFail($record->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
