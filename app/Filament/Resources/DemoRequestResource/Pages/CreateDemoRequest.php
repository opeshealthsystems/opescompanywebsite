<?php

namespace App\Filament\Resources\DemoRequestResource\Pages;

use App\Filament\Resources\DemoRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDemoRequest extends CreateRecord
{
    protected static string $resource = DemoRequestResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.crm.demoRequests.create', $data);
    }
}
