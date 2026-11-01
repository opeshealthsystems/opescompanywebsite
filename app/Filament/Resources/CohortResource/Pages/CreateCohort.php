<?php

namespace App\Filament\Resources\CohortResource\Pages;

use App\Filament\Resources\CohortResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCohort extends CreateRecord
{
    protected static string $resource = CohortResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.validation.cohorts.create', $data);
    }
}
