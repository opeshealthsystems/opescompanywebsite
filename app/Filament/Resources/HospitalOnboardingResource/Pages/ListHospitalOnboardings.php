<?php

namespace App\Filament\Resources\HospitalOnboardingResource\Pages;

use App\Filament\Resources\HospitalOnboardingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHospitalOnboardings extends ListRecords
{
    protected static string $resource = HospitalOnboardingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
