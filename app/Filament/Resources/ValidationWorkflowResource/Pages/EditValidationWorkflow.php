<?php
namespace App\Filament\Resources\ValidationWorkflowResource\Pages;

use App\Filament\Resources\ValidationWorkflowResource;
use Filament\Resources\Pages\EditRecord;

class EditValidationWorkflow extends EditRecord
{
    protected static string $resource = ValidationWorkflowResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.validation.workflows.update', $data, [
            'module' => $record->validation_module_id,
            'workflow' => $record,
        ]);
        return $record->fresh();
    }
}
