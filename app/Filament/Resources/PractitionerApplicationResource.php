<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PractitionerApplicationResource\Pages;
use App\Models\PractitionerApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class PractitionerApplicationResource extends Resource
{
    protected static ?string $model = PractitionerApplication::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Applications';
    protected static ?string $navigationGroup = 'Practitioners';
    protected static ?int    $navigationSort  = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Application')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('practitioner_id')
                        ->label('Practitioner')
                        ->relationship('practitioner', 'name')
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('program_id')
                        ->label('Programme')
                        ->relationship('program', 'title')
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options(PractitionerApplication::statusOptions())
                        ->default('pending')
                        ->required(),
                    Forms\Components\Textarea::make('motivation')->nullable()->columnSpanFull(),
                    Forms\Components\Textarea::make('admin_notes')->nullable()->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('practitioner.name')
                    ->label('Practitioner')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.title')
                    ->label('Programme')
                    ->limit(40),
                Tables\Columns\TextColumn::make('tier')
                    ->label('Tier')
                    ->badge()
                    ->state(fn (PractitionerApplication $record): string => $record->practitioner->practitionerTier()->label())
                    ->color(fn (PractitionerApplication $record): string => $record->practitioner->practitionerTier()->filamentColor()),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'pending'   => 'warning',
                        'withdrawn' => 'gray',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payout_status')
                    ->label('Payout')
                    ->badge()
                    ->formatStateUsing(fn ($state) => PractitionerApplication::payoutStatusOptions()[$state] ?? $state)
                    ->color(fn ($state) => match($state) {
                        'paid'    => 'success',
                        'pending' => 'warning',
                        default   => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payout_amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state, PractitionerApplication $record) =>
                        $state === null ? '—' : number_format((float) $state, 2) . ' ' . $record->payout_currency)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Reviewed')
                    ->dateTime('d M Y')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->byTierPriority())
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(PractitionerApplication::statusOptions()),
                Tables\Filters\SelectFilter::make('program')
                    ->relationship('program', 'title'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn (PractitionerApplication $record) => $record->status !== 'pending')
                    ->requiresConfirmation()
                    ->action(function (PractitionerApplication $record) {
                        $record->loadMissing('program');
                        $record->markApproved(auth()->id());
                        // Mail queued if mailable exists
                        if (class_exists(\App\Mail\PractitionerApplicationApproved::class)) {
                            Mail::to($record->practitioner->email)
                                ->queue(new \App\Mail\PractitionerApplicationApproved($record));
                        }
                        Notification::make()->title('Application approved')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->hidden(fn (PractitionerApplication $record) => $record->status !== 'pending')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Reason (optional)')
                            ->rows(3),
                    ])
                    ->action(function (PractitionerApplication $record, array $data) {
                        $record->update([
                            'status'      => 'rejected',
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                            'admin_notes' => $data['admin_notes'] ?? null,
                        ]);
                        if (class_exists(\App\Mail\PractitionerApplicationRejected::class)) {
                            Mail::to($record->practitioner->email)
                                ->queue(new \App\Mail\PractitionerApplicationRejected($record));
                        }
                        Notification::make()->title('Application rejected')->danger()->send();
                    }),
                Tables\Actions\Action::make('record_payout')
                    ->label('Record Payout')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (PractitionerApplication $record) =>
                        $record->isPaidProgram()
                        && $record->status === 'approved'
                        && $record->payout_status !== 'paid')
                    ->form([
                        Forms\Components\TextInput::make('payout_amount')
                            ->label('Amount')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('payout_currency')
                            ->label('Currency')
                            ->default('XAF')
                            ->maxLength(3)
                            ->required(),
                        Forms\Components\TextInput::make('payout_reference')
                            ->label('Reference')
                            ->helperText('e.g. mobile-money transaction id')
                            ->maxLength(60),
                    ])
                    ->action(function (PractitionerApplication $record, array $data) {
                        $record->update([
                            'payout_status'    => 'paid',
                            'payout_amount'    => $data['payout_amount'],
                            'payout_currency'  => $data['payout_currency'] ?? 'XAF',
                            'payout_reference' => $data['payout_reference'] ?? null,
                            'paid_at'          => now(),
                        ]);
                        Notification::make()->title('Payout recorded')->success()->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Application Details')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('practitioner.name')->label('Practitioner'),
                    Infolists\Components\TextEntry::make('program.title')->label('Programme'),
                    Infolists\Components\TextEntry::make('status')->badge(),
                    Infolists\Components\TextEntry::make('reviewed_at')->dateTime('d M Y H:i')->placeholder('Not reviewed'),
                    Infolists\Components\TextEntry::make('motivation')->columnSpanFull()->placeholder('—'),
                    Infolists\Components\TextEntry::make('admin_notes')->label('Admin Notes')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPractitionerApplications::route('/'),
            'view'  => Pages\ViewPractitionerApplication::route('/{record}'),
            'edit'  => Pages\EditPractitionerApplication::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['practitioner', 'program']);
    }
}
