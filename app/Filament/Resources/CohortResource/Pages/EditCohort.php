<?php

namespace App\Filament\Resources\CohortResource\Pages;

use App\Filament\Resources\CohortResource;
use Filament\Resources\Pages\EditRecord;

class EditCohort extends EditRecord
{
    protected static string $resource = CohortResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.validation.cohorts.update', $data, ['cohort' => $record->id]);
    }

    protected function handleRecordDeletion(\Illuminate\Database\Eloquent\Model $record): void
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.validation.cohorts.delete', parameters: ['cohort' => $record->id]);
    }
}
