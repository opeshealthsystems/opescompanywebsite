<?php

namespace App\Filament\Resources\LicenseResource\Pages;

use App\Filament\Resources\LicenseResource;
use App\Mail\LicenseIssued;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateLicense extends CreateRecord
{
    protected static string $resource = LicenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['issued_by'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $license = $this->record->load('customer');
        $email   = $license->customer?->email;
        if ($email) {
            Mail::to($email)->queue(new LicenseIssued($license));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
