<?php

namespace App\Filament\Resources\LeaveTypeResource\Pages;

use App\Filament\Resources\LeaveTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveType extends CreateRecord
{
    protected static string $resource = LeaveTypeResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.workforce.leaveTypes.create', $data);
    }
}
