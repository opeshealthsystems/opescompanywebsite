<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBudget extends ViewRecord
{
    protected static string $resource = BudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()->using(function ($record) {
                \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.finance.budgets.delete', parameters: ['budget' => $record->id]);
                return true;
            }),
        ];
    }
}
