<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('send_email')
                ->label('Send Email')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->visible(fn () => filled($this->record->email))
                ->form([
                    Forms\Components\TextInput::make('subject')
                        ->label('Subject')
                        ->required()
                        ->default('Following up on your enquiry — OPES Health Systems')
                        ->maxLength(200),
                    Forms\Components\Textarea::make('body')
                        ->label('Message')
                        ->required()
                        ->rows(6)
                        ->placeholder('Dear ' . ($this->record->name ?? 'valued contact') . ','),
                ])
                ->action(function (array $data): void {
                    \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run(
                        'admin.enquiries.leads.sendFollowUp',
                        values: $data,
                        parameters: ['lead' => $this->record],
                    );
                    Notification::make()->title('Follow-up email queued.')->success()->send();
                }),

            Actions\EditAction::make(),
        ];
    }
}
