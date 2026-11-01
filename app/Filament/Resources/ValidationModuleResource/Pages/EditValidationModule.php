<?php
namespace App\Filament\Resources\ValidationModuleResource\Pages;

use App\Filament\Resources\ValidationModuleResource;
use Filament\Resources\Pages\EditRecord;

class EditValidationModule extends EditRecord
{
    protected static string $resource = ValidationModuleResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.validation.modules.update', $data, ['module' => $record]);
        return $record->fresh();
    }
}
