<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.support.tickets.update',
            new \App\Contexts\Shared\Application\WorkflowInput(collect($data)->only(['user_id', 'subject', 'description', 'type', 'priority', 'resolution'])->all(), ['ticket' => $record], auth()->user(), app()->getLocale()),
        );
        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
            'admin.support.tickets.status',
            new \App\Contexts\Shared\Application\WorkflowInput(collect($data)->only(['status', 'assigned_to', 'resolution'])->all(), ['ticket' => $record->fresh()], auth()->user(), app()->getLocale()),
        );
        return $record->fresh();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}
