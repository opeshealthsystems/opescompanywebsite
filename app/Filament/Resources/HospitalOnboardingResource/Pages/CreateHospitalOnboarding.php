<?php

namespace App\Filament\Resources\HospitalOnboardingResource\Pages;

use App\Filament\Resources\HospitalOnboardingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHospitalOnboarding extends CreateRecord
{
    protected static string $resource = HospitalOnboardingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['locale'] ??= app()->getLocale();

        return $data;
    }
}
