<?php

namespace App\Filament\Resources\SalaryGradeResource\Pages;

use App\Filament\Resources\SalaryGradeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSalaryGrade extends ViewRecord
{
    protected static string $resource = SalaryGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make(), Actions\DeleteAction::make()->using(function ($record) {
            \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.workforce.salaryGrades.delete', parameters: ['salaryGrade' => $record->id]);
            return true;
        })];
    }
}
