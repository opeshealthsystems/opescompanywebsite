<?php

namespace App\Filament\Resources\PartnerInstitutionResource\Pages;

use App\Filament\Resources\PartnerInstitutionResource;
use Filament\Resources\Pages\ListRecords;

class ListPartnerInstitutions extends ListRecords
{
    protected static string $resource = PartnerInstitutionResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
