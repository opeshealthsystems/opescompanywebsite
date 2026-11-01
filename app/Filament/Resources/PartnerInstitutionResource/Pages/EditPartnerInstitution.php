<?php

namespace App\Filament\Resources\PartnerInstitutionResource\Pages;

use App\Filament\Resources\PartnerInstitutionResource;
use Filament\Resources\Pages\EditRecord;

class EditPartnerInstitution extends EditRecord
{
    protected static string $resource = PartnerInstitutionResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.publishing.partnerInstitutions.update', $data, ['institution' => $record]);
        return $record->fresh();
    }
}
