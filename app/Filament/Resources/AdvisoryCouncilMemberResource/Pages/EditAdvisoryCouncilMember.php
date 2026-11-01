<?php

namespace App\Filament\Resources\AdvisoryCouncilMemberResource\Pages;

use App\Filament\Resources\AdvisoryCouncilMemberResource;
use Filament\Resources\Pages\EditRecord;

class EditAdvisoryCouncilMember extends EditRecord
{
    protected static string $resource = AdvisoryCouncilMemberResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.validation.councilMembers.update', $data, ['member' => $record->id]);
    }
}
