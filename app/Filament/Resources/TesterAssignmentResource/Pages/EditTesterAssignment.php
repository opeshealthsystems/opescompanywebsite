<?php

namespace App\Filament\Resources\TesterAssignmentResource\Pages;

use App\Filament\Resources\TesterAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTesterAssignment extends EditRecord
{
    protected static string $resource = TesterAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
