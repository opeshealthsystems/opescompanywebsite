<?php
namespace App\Filament\Resources\ValidationProductResource\Pages;

use App\Filament\Resources\ValidationProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateValidationProduct extends CreateRecord
{
    protected static string $resource = ValidationProductResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.validation.products.create', $data);
    }
}
