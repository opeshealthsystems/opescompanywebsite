<?php

namespace App\Filament\Resources\ServiceRequestResource\Pages;

use App\Filament\Resources\ServiceRequestResource;
use Filament\Resources\Pages\EditRecord;

class EditServiceRequest extends EditRecord
{
    protected static string $resource = ServiceRequestResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.support.serviceRequests.update', $data, ['serviceRequest' => $record]);
        return $record->fresh();
    }
}
