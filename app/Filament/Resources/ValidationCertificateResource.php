<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ValidationCertificateResource\Pages;
use App\Models\AdvisoryCouncilMember;
use App\Models\ValidationCertificate;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ValidationCertificateResource extends Resource
{
    protected static ?string $model = ValidationCertificate::class;
    protected static ?string $navigationIcon  = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 16;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cohortMember.user.name')->label('Member')->searchable(),
                Tables\Columns\TextColumn::make('cohortMember.cohort.name')->label('Cohort'),
                Tables\Columns\TextColumn::make('certificate_number')->label('Number')->searchable(),
                Tables\Columns\TextColumn::make('score'),
                Tables\Columns\TextColumn::make('tier')->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => ValidationCertificate::tierBadgeColors()[$state] ?? 'gray'),
                Tables\Columns\TextColumn::make('issuedBy.name')->label('Issued by')->placeholder('—'),
                Tables\Columns\TextColumn::make('issued_at')->dateTime(),
            ])
            ->defaultSort('issued_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('download_pdf')
                    ->label('Download PDF')->icon('heroicon-o-arrow-down-tray')->color('gray')
                    ->action(function (ValidationCertificate $record) {
                        $record->load('cohortMember.user', 'cohortMember.cohort');
                        return \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.validation-certificate', ['certificate' => $record])
                            ->setPaper('a4', 'landscape')
                            ->download($record->certificate_number . '.pdf');
                    }),
                Tables\Actions\Action::make('invite_to_council')
                    ->label('Invite to Council')->icon('heroicon-o-user-plus')->color('success')
                    ->visible(fn (ValidationCertificate $r) => $r->tier === 'distinction'
                        && ! AdvisoryCouncilMember::where('user_id', $r->cohortMember->user_id)->exists())
                    ->form([
                        Forms\Components\TextInput::make('title')->default('Clinical Validation Advisor')->required(),
                        Forms\Components\DatePicker::make('term_start')->native(false)->default(now())->required(),
                        Forms\Components\DatePicker::make('term_end')->native(false),
                    ])
                    ->action(function (ValidationCertificate $r, array $data) {
                        $member = AdvisoryCouncilMember::create([
                            'user_id'                   => $r->cohortMember->user_id,
                            'validation_certificate_id' => $r->id,
                            'title'                     => $data['title'],
                            'term_start'                => $data['term_start'],
                            'term_end'                  => $data['term_end'] ?? null,
                            'status'                    => 'active',
                            'invited_by'                => auth()->id(),
                            'invited_at'                => now(),
                        ]);
                        $r->cohortMember->user?->notify(new \App\Notifications\CouncilInvitation($member));
                        Notification::make()->title('Practitioner invited to the Advisory Council.')->success()->send();
                    }),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Certificate')->columns(3)->schema([
                Infolists\Components\TextEntry::make('cohortMember.user.name')->label('Member'),
                Infolists\Components\TextEntry::make('cohortMember.cohort.name')->label('Cohort'),
                Infolists\Components\TextEntry::make('certificate_number')->label('Number'),
                Infolists\Components\TextEntry::make('score'),
                Infolists\Components\TextEntry::make('tier')->badge()->formatStateUsing(fn ($s) => ucfirst($s)),
                Infolists\Components\TextEntry::make('issuedBy.name')->label('Issued by')->placeholder('—'),
                Infolists\Components\TextEntry::make('issued_at')->dateTime(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListValidationCertificates::route('/'),
            'view'  => Pages\ViewValidationCertificate::route('/{record}'),
        ];
    }
}
