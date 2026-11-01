<?php

namespace App\Filament\Resources\PractitionerApplicationResource\Pages;

use App\Filament\Resources\PractitionerApplicationResource;
use Filament\Resources\Pages\EditRecord;

class EditPractitionerApplication extends EditRecord
{
    protected static string $resource = PractitionerApplicationResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.practitioners.applications.updateNotes', $data, ['application' => $record]);
        return $record->fresh();
    }
}
