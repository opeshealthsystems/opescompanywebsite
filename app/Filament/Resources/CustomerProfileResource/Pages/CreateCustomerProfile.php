<?php

namespace App\Filament\Resources\CustomerProfileResource\Pages;

use App\Filament\Resources\CustomerProfileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerProfile extends CreateRecord
{
    protected static string $resource = CustomerProfileResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.customerProfiles.create', $data);
    }
}
