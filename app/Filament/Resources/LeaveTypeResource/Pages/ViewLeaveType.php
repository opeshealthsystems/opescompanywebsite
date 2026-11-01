<?php

namespace App\Filament\Resources\LeaveTypeResource\Pages;

use App\Filament\Resources\LeaveTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLeaveType extends ViewRecord
{
    protected static string $resource = LeaveTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()->using(function ($record) {
                \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.workforce.leaveTypes.delete', parameters: ['leaveType' => $record->id]);
                return true;
            }),
        ];
    }
}
