<?php

namespace App\Filament\Resources\PractitionerProfileResource\Pages;

use App\Filament\Resources\PractitionerProfileResource;
use Filament\Resources\Pages\EditRecord;

class EditPractitionerProfile extends EditRecord
{
    protected static string $resource = PractitionerProfileResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.practitioners.profiles.update', $data, ['profile' => $record]);
        return $record->fresh();
    }
}
