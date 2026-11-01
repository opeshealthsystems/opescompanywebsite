<?php

namespace App\Filament\Resources\PartnerApplicationResource\Pages;

use App\Filament\Resources\PartnerApplicationResource;
use Filament\Resources\Pages\EditRecord;

class EditPartnerApplication extends EditRecord
{
    protected static string $resource = PartnerApplicationResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.enquiries.partnerApplications.update', $data, ['partnerApplication' => $record]);
        return $record->fresh();
    }
}
