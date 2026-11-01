<?php

namespace App\Filament\Resources\CustomerProfileResource\Pages;

use App\Filament\Resources\CustomerProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerProfile extends EditRecord
{
    protected static string $resource = CustomerProfileResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.customerProfiles.update',
            new \App\Contexts\Shared\Application\WorkflowInput($data, ['customerProfile' => $record], auth()->user(), app()->getLocale()),
        );
        return $record->fresh();
    }

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make()];
    }
}
