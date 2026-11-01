<?php

namespace App\Filament\Resources\JobApplicationResource\Pages;

use App\Filament\Resources\JobApplicationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateJobApplication extends CreateRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.recruitment.applications.create', $data);
    }
}
