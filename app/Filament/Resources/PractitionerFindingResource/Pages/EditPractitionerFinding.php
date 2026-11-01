<?php

namespace App\Filament\Resources\PractitionerFindingResource\Pages;

use App\Filament\Resources\PractitionerFindingResource;
use Filament\Resources\Pages\EditRecord;

class EditPractitionerFinding extends EditRecord
{
    protected static string $resource = PractitionerFindingResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.practitioners.findings.update', $data, ['finding' => $record]);
        return $record->fresh();
    }
}
