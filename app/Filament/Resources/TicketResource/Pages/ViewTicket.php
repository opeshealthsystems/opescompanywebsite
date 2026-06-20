<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Mail\TicketReplied;
use App\Mail\TicketStatusChanged;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;

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
                    $reply = TicketReply::create([
                        'ticket_id'   => $this->record->id,
                        'user_id'     => auth()->id(),
                        'body'        => $data['body'],
                        'is_internal' => $data['is_internal'],
                    ]);
                    if ($this->record->status === 'open') {
                        $this->record->update(['status' => 'in_progress']);
                    }
                    if (!$data['is_internal']) {
                        $customerEmail = $this->record->user?->email;
                        if ($customerEmail) {
                            Mail::to($customerEmail)->queue(new TicketReplied($this->record, $reply));
                            $this->record->user?->notify(new \App\Notifications\FeedEntry(
                                'support.ticket_replied',
                                'New reply on your ticket',
                                'There is a new reply on "' . $this->record->subject . '".',
                                'chat-bubble-left-right',
                                route('customer.tickets.show', ['locale' => 'en', 'id' => $this->record->id]),
                            ));
                        }
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
                        ->options(fn () => \App\Filament\Resources\TicketResource::staffOptions())
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
                    $oldStatus = $this->record->status;
                    $this->record->update($updates);
                    $notifyStatuses = ['resolved', 'closed', 'pending_customer'];
                    if ($data['status'] !== $oldStatus && in_array($data['status'], $notifyStatuses)) {
                        $customerEmail = $this->record->user?->email;
                        if ($customerEmail) {
                            Mail::to($customerEmail)->queue(new TicketStatusChanged($this->record, $data['status']));
                            $this->record->user?->notify(new \App\Notifications\FeedEntry(
                                'support.ticket_status',
                                'Ticket status updated',
                                '"' . $this->record->subject . '" is now ' . str_replace('_', ' ', $data['status']) . '.',
                                'arrow-path',
                                route('customer.tickets.show', ['locale' => 'en', 'id' => $this->record->id]),
                            ));
                        }
                    }
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
