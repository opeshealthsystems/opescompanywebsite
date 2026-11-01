<?php
namespace App\Filament\Resources\ValidationProductResource\Pages;

use App\Filament\Resources\ValidationProductResource;
use Filament\Resources\Pages\EditRecord;

class EditValidationProduct extends EditRecord
{
    protected static string $resource = ValidationProductResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.validation.products.update', $data, ['validationProduct' => $record->id]);
    }
}
