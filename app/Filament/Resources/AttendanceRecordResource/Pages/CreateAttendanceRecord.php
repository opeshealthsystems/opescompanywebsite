<?php

namespace App\Filament\Resources\AttendanceRecordResource\Pages;

use App\Filament\Resources\AttendanceRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendanceRecord extends CreateRecord
{
    protected static string $resource = AttendanceRecordResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.workforce.attendance.create', $data);
    }
}
