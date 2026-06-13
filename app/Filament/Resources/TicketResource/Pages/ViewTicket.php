<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
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
                    TicketReply::create([
                        'ticket_id'   => $this->record->id,
                        'user_id'     => auth()->id(),
                        'body'        => $data['body'],
                        'is_internal' => $data['is_internal'],
                    ]);
                    if ($this->record->status === 'open') {
                        $this->record->update(['status' => 'in_progress']);
                    }
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
                        ->options(
                            User::whereHas('roles', fn ($q) =>
                                $q->whereIn('name', ['super_admin', 'admin', 'support'])
                            )->orderBy('name')->pluck('name', 'id')
                        )
                        ->nullable()
                        ->default(fn () => $this->record->assigned_to),
                    Forms\Components\Textarea::make('resolution')
                        ->rows(3)
                        ->nullable(),
                ])
                ->action(function (array $data): void {
                    $updates = [
                        'status'      => $data['status'],
                        'assigned_to' => $data['assigned_to'],
                    ];
                    if (!empty($data['resolution'])) {
                        $updates['resolution'] = $data['resolution'];
                    }
                    if (in_array($data['status'], ['resolved', 'closed']) && !$this->record->resolved_at) {
                        $updates['resolved_at'] = now();
                    }
                    if ($data['status'] === 'closed' && !$this->record->closed_at) {
                        $updates['closed_at'] = now();
                    }
                    $this->record->update($updates);
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
