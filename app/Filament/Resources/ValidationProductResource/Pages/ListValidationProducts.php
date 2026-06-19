<?php
namespace App\Filament\Resources\ValidationProductResource\Pages;

use App\Filament\Resources\ValidationProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListValidationProducts extends ListRecords
{
    protected static string $resource = ValidationProductResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
