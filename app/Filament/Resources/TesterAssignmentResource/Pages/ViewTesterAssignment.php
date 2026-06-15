<?php

namespace App\Filament\Resources\TesterAssignmentResource\Pages;

use App\Filament\Resources\TesterAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTesterAssignment extends ViewRecord
{
    protected static string $resource = TesterAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
