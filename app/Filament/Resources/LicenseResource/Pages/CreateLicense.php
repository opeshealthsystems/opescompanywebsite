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
            $license->customer?->notify(new \App\Notifications\FeedEntry(
                'licensing.issued',
                'License issued',
                'A license for ' . $license->product_name . ' was issued to your account.',
                'key',
                route('customer.licenses.show', ['locale' => 'en', 'id' => $license->id]),
            ));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
