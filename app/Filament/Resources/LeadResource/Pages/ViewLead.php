<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;

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
                    $lead = $this->record;
                    Mail::html(
                        '<html><body style="font-family:sans-serif;max-width:600px;margin:auto;padding:20px;">'
                        . '<h3 style="color:#00C896;">OPES Health Systems</h3>'
                        . '<p>' . nl2br(e($data['body'])) . '</p>'
                        . '<hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0;">'
                        . '<p style="color:#94a3b8;font-size:11px;">OPES Health Systems — automated via admin panel</p>'
                        . '</body></html>',
                        function ($message) use ($lead, $data) {
                            $message->to($lead->email, $lead->name)->subject($data['subject']);
                        }
                    );
                    Notification::make()->title('Email sent to ' . $lead->email)->success()->send();
                }),

            Actions\EditAction::make(),
        ];
    }
}
