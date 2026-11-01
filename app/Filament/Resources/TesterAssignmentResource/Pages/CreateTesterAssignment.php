<?php

namespace App\Filament\Resources\TesterAssignmentResource\Pages;

use App\Filament\Resources\TesterAssignmentResource;
use App\Models\TesterAssignment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTesterAssignment extends CreateRecord
{
    protected static string $resource = TesterAssignmentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.testing.assignments.create', $data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
