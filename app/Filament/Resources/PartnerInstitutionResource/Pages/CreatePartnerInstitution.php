<?php

namespace App\Filament\Resources\PartnerInstitutionResource\Pages;

use App\Filament\Resources\PartnerInstitutionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePartnerInstitution extends CreateRecord
{
    protected static string $resource = PartnerInstitutionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.publishing.partnerInstitutions.create', $data);
    }
}
