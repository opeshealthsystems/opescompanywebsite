<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Ticket;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reply')
                ->label('Add Reply')
                ->icon('heroicon-o-chat-bubble-left')
                ->color('primary')
                ->form([
                    Forms\Components\Textarea::make('body')
                        ->label('Reply')
                        ->required()
                        ->rows(4),
                    Forms\Components\Toggle::make('is_internal')
                        ->label('Internal note (not visible to customer)')
                        ->default(false),
                ])
                ->action(function (array $data): void {
                    app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
                        'admin.support.tickets.reply',
                        new \App\Contexts\Shared\Application\WorkflowInput($data, ['ticket' => $this->record], auth()->user(), app()->getLocale()),
                    );
                    Notification::make()->title('Reply added')->success()->send();
                }),

            Actions\Action::make('change_status')
                ->label('Change Status')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('status')
                        ->options(Ticket::statusOptions())
                        ->default(fn () => $this->record->status)
                        ->required(),
                    Forms\Components\Select::make('assigned_to')
                        ->label('Assigned To')
                        ->options(fn () => \App\Filament\Resources\TicketResource::staffOptions())
                        ->nullable()
                        ->default(fn () => $this->record->assigned_to),
                    Forms\Components\Textarea::make('resolution')
                        ->rows(3)
                        ->nullable(),
                ])
                ->action(function (array $data): void {
                    app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
                        'admin.support.tickets.status',
                        new \App\Contexts\Shared\Application\WorkflowInput($data, ['ticket' => $this->record], auth()->user(), app()->getLocale()),
                    );
                    Notification::make()->title('Ticket updated')->success()->send();
                    $this->refreshFormData(['status', 'assigned_to', 'resolution']);
                }),
        ];
    }

    public function getTitle(): string
    {
        return $this->record->reference_number . ' — ' . $this->record->subject;
    }
}
