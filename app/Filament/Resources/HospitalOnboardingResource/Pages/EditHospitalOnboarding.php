<?php

namespace App\Filament\Resources\HospitalOnboardingResource\Pages;

use App\Filament\Resources\HospitalOnboardingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHospitalOnboarding extends EditRecord
{
    protected static string $resource = HospitalOnboardingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
