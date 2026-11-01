<?php

namespace App\Filament\Resources\LeaveRequestResource\Pages;

use App\Filament\Resources\LeaveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeaveRequest extends EditRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.workforce.leaves.update',
            new \App\Contexts\Shared\Application\WorkflowInput($data, ['leave' => $record], auth()->user(), app()->getLocale()),
        );
        return $record->fresh();
    }

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
