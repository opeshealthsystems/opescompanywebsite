<?php

namespace App\Filament\Resources\LeaveTypeResource\Pages;

use App\Filament\Resources\LeaveTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeaveType extends EditRecord
{
    protected static string $resource = LeaveTypeResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.workforce.leaveTypes.update', $data, ['leaveType' => $record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->using(function ($record) {
                \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.workforce.leaveTypes.delete', parameters: ['leaveType' => $record->id]);
                return true;
            }),
        ];
    }
}
