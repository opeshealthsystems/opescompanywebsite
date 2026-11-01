<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(fn (User $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.identity.users.delete', parameters: ['user' => $record->id]))
                ->hidden(fn () => $this->record->id === auth()->id()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['roles'] = $this->record->getRoleNames()->values()->all();
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.identity.users.update',
            new \App\Contexts\Shared\Application\WorkflowInput(values: $data, parameters: ['user' => $record], actor: auth()->user(), language: app()->getLocale()),
        );

        return User::query()->findOrFail($record->id);
    }
}
