<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn () => $this->record->id === auth()->id()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
}
