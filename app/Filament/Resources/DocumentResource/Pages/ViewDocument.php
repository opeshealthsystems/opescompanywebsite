<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Document Details')->schema([
                Infolists\Components\TextEntry::make('reference_number')->copyable(),
                Infolists\Components\TextEntry::make('title'),
                Infolists\Components\TextEntry::make('type')
                    ->formatStateUsing(fn ($state) => \App\Models\DocumentTemplate::typeLabel($state)),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'signed'            => 'success',
                        'pending_signature' => 'warning',
                        'voided'            => 'danger',
                        'sent'              => 'primary',
                        default             => 'gray',
                    }),
                Infolists\Components\TextEntry::make('addressee_name')->label('Recipient'),
                Infolists\Components\TextEntry::make('addressee_email')->label('Recipient Email'),
                Infolists\Components\TextEntry::make('issuer.name')->label('Issued By'),
                Infolists\Components\TextEntry::make('created_at')->label('Issued At')->dateTime('d M Y H:i'),
                Infolists\Components\TextEntry::make('valid_until')->date('d M Y')->placeholder('—'),
                Infolists\Components\IconEntry::make('requires_signature')->boolean(),
            ])->columns(2),

            Infolists\Components\Section::make('Signature Status')
                ->hidden(fn () => !$this->record->requires_signature)
                ->schema([
                    Infolists\Components\TextEntry::make('signature_token')
                        ->label('Signing Link')
                        ->formatStateUsing(fn ($state) => $state ? route('documents.sign', $state) : '—')
                        ->copyable(),
                    Infolists\Components\TextEntry::make('signature_token_expires_at')
                        ->label('Token Expires')
                        ->dateTime('d M Y H:i'),
                    Infolists\Components\TextEntry::make('signed_by_name')->placeholder('Not signed yet'),
                    Infolists\Components\TextEntry::make('signed_at')->dateTime('d M Y H:i')->placeholder('—'),
                    Infolists\Components\TextEntry::make('signed_ip')->label('Signed From IP')->placeholder('—'),
                ])->columns(2),

            Infolists\Components\Section::make('Document Preview')->schema([
                Infolists\Components\ViewEntry::make('body_rendered')
                    ->view('filament.infolists.document-preview')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn () => route('documents.pdf', $this->record))
                ->openUrlInNewTab(),
            Actions\Action::make('mark_sent')
                ->label('Mark as Sent')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->hidden(fn () => $this->record->status !== 'draft')
                ->action(fn () => $this->record->update(['status' => 'sent']))
                ->after(fn () => $this->refreshFormData(['status'])),
            Actions\Action::make('void')
                ->label('Void Document')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(fn () => in_array($this->record->status, ['signed', 'voided']))
                ->action(fn () => $this->record->update(['status' => 'voided']))
                ->after(fn () => $this->refreshFormData(['status'])),
        ];
    }
}
