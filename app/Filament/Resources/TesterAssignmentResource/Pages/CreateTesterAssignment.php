<?php

namespace App\Filament\Resources\TesterAssignmentResource\Pages;

use App\Filament\Resources\TesterAssignmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTesterAssignment extends CreateRecord
{
    protected static string $resource = TesterAssignmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['assigned_by'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
