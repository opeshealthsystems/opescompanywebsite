<?php

namespace App\Filament\Resources\HospitalOnboardingResource\Pages;

use App\Filament\Resources\HospitalOnboardingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHospitalOnboarding extends ViewRecord
{
    protected static string $resource = HospitalOnboardingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
