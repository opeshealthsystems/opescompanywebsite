<?php

namespace App\Filament\Resources\SalaryGradeResource\Pages;

use App\Filament\Resources\SalaryGradeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalaryGrade extends EditRecord
{
    protected static string $resource = SalaryGradeResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.workforce.salaryGrades.update', $data, ['salaryGrade' => $record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()->using(function ($record) {
            \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.workforce.salaryGrades.delete', parameters: ['salaryGrade' => $record->id]);
            return true;
        })];
    }
}
