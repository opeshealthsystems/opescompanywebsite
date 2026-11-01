<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDepartment extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make(), Actions\DeleteAction::make()->using(function ($record) {
            \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.workforce.departments.delete', parameters: ['department' => $record->id]);
            return true;
        })];
    }
}
