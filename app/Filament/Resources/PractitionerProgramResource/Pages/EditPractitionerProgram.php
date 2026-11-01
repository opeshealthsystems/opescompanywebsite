<?php

namespace App\Filament\Resources\PractitionerProgramResource\Pages;

use App\Filament\Resources\PractitionerProgramResource;
use Filament\Resources\Pages\EditRecord;

class EditPractitionerProgram extends EditRecord
{
    protected static string $resource = PractitionerProgramResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.practitioners.programs.update', $data, ['program' => $record]);
        return $record->fresh();
    }
}
