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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Server-side defence: strip super_admin from roles if the acting user is not super_admin.
        if (! auth()->user()?->hasRole('super_admin')) {
            $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super_admin')->first();
            if ($superAdminRole && isset($data['roles'])) {
                $data['roles'] = array_values(array_filter(
                    (array) $data['roles'],
                    fn ($id) => (int) $id !== $superAdminRole->id
                ));
            }
        }

        return $data;
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
