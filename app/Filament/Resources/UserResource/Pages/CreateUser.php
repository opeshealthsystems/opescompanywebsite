<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\WelcomeEmail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        Mail::to($this->record->email)->queue(new WelcomeEmail($this->record));
        $this->record->notify(new \App\Notifications\FeedEntry(
            'account.welcome',
            'Welcome to OPES',
            'Welcome to OPES Health Systems.',
            'sparkles',
            null,
        ));
    }
}
