<?php

namespace App\Filament\Resources\SalaryGradeResource\Pages;

use App\Filament\Resources\SalaryGradeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSalaryGrade extends CreateRecord
{
    protected static string $resource = SalaryGradeResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.workforce.salaryGrades.create', $data);
    }
}
