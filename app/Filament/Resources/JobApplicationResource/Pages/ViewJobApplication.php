<?php

namespace App\Filament\Resources\JobApplicationResource\Pages;

use App\Filament\Resources\JobApplicationResource;
use App\Models\JobApplication;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewJobApplication extends ViewRecord
{
    protected static string $resource = JobApplicationResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Applicant')->columns(2)->schema([
                Infolists\Components\TextEntry::make('applicant_name')->weight('semibold'),
                Infolists\Components\TextEntry::make('email')->copyable(),
                Infolists\Components\TextEntry::make('phone')->placeholder('—'),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'hired', 'offered'        => 'success',
                        'shortlisted', 'interviewed' => 'info',
                        'rejected'                => 'danger',
                        'reviewing'               => 'warning',
                        default                   => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => JobApplication::statusOptions()[$state] ?? ucfirst($state)),
            ]),

            Infolists\Components\Section::make('Position')->columns(2)->schema([
                Infolists\Components\TextEntry::make('jobOpening.title')->label('Position'),
                Infolists\Components\TextEntry::make('jobOpening.department')->label('Department')->placeholder('—'),
                Infolists\Components\TextEntry::make('applied_at')->date('d M Y'),
                Infolists\Components\TextEntry::make('interview_date')->date('d M Y')->placeholder('Not scheduled'),
            ]),

            Infolists\Components\Section::make('Documents')->schema([
                Infolists\Components\TextEntry::make('resume_path')
                    ->label('Resume / CV')
                    ->placeholder('No file uploaded')
                    ->formatStateUsing(fn ($state) => $state ? basename($state) : '—'),
                Infolists\Components\TextEntry::make('notes')
                    ->placeholder('—')
                    ->columnSpanFull(),
            ])->columns(2)->collapsible(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->hidden(fn () => in_array($this->record->status, ['hired', 'rejected'])),
            Actions\Action::make('shortlist')
                ->label('Shortlist')
                ->icon('heroicon-o-star')
                ->color('info')
                ->hidden(fn () => !in_array($this->record->status, ['received', 'reviewing']))
                ->action(fn () => $this->record->update(['status' => 'shortlisted']))
                ->after(fn () => $this->refreshFormData(['status'])),
            Actions\Action::make('make_offer')
                ->label('Make Offer')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->hidden(fn () => $this->record->status !== 'interviewed')
                ->requiresConfirmation()
                ->action(fn () => $this->record->update(['status' => 'offered']))
                ->after(fn () => $this->refreshFormData(['status'])),
            Actions\Action::make('hire')
                ->label('Hire')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->hidden(fn () => $this->record->status !== 'offered')
                ->action(fn () => $this->record->update(['status' => 'hired']))
                ->after(fn () => $this->refreshFormData(['status'])),
            Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(fn () => in_array($this->record->status, ['hired', 'rejected']))
                ->action(fn () => $this->record->update(['status' => 'rejected']))
                ->after(fn () => $this->refreshFormData(['status'])),
        ];
    }
}
