<?php

namespace App\Filament\Resources\TesterApplicationResource\Pages;

use App\Filament\Resources\TesterApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTesterApplication extends EditRecord
{
    protected static string $resource = TesterApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
