<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepartment extends EditRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.workforce.departments.update', $data, ['department' => $record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()->using(function ($record) {
            \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.workforce.departments.delete', parameters: ['department' => $record->id]);
            return true;
        })];
    }
}
