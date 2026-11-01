<?php

namespace App\Filament\Resources\PractitionerProgramResource\Pages;

use App\Filament\Resources\PractitionerProgramResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePractitionerProgram extends CreateRecord
{
    protected static string $resource = PractitionerProgramResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.practitioners.programs.create', $data);
    }
}
