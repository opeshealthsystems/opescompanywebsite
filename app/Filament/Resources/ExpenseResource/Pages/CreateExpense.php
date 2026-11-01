<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $result = app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.finance.expenses.create',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, actor: auth()->user(), language: app()->getLocale()),
        );

        return \App\Models\Expense::query()->findOrFail($result->data['id']);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
