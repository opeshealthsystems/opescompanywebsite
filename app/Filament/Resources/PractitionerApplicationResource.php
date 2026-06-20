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
                    // Qualify the column: byTierPriority() joins practitioner_profiles,
                    // which also has a created_at, so an unqualified sort is ambiguous.
                    ->sortable(query: fn (\Illuminate\Database\Eloquent\Builder $query, string $direction) =>
                        $query->orderBy('practitioner_applications.created_at', $direction)),
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
                        $record->practitioner?->notify(new \App\Notifications\FeedEntry(
                            'practitioner.application_approved',
                            'Application approved',
                            'Your programme application was approved.',
                            'check-circle',
                            route('practitioner.applications', ['locale' => 'en']),
                        ));
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
                        $record->practitioner?->notify(new \App\Notifications\FeedEntry(
                            'practitioner.application_rejected',
                            'Application update',
                            'There is an update on your programme application.',
                            'clipboard-document',
                            route('practitioner.applications', ['locale' => 'en']),
                        ));
                        Notification::make()->title('Application rejected')->danger()->send();
                    }),
                Tables\Actions\Action::make('pay_now')
                    ->label('Pay now')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (PractitionerApplication $record) => $record->isPayable())
                    ->form([
                        Forms\Components\TextInput::make('payout_amount')
                            ->label('Amount')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('payout_currency')
                            ->label('Currency')
                            ->default(config('payouts.currency', 'XAF'))
                            ->maxLength(3)
                            ->required(),
                        Forms\Components\Select::make('network_override')
                            ->label('Network')
                            ->options(['mtn' => 'MTN MoMo', 'orange' => 'Orange Money', 'manual' => 'Manual / offline'])
                            ->helperText('Leave blank to auto-detect from the practitioner\'s number.'),
                    ])
                    ->action(function (PractitionerApplication $record, array $data) {
                        if (! $record->isPayable()) {
                            Notification::make()->title('Not payable')->danger()->send();

                            return;
                        }

                        $manager = app(\App\Services\Payouts\PayoutGatewayManager::class);
                        $network = $manager->resolveNetwork($record, $data['network_override'] ?? null);

                        try {
                            $result = $manager->driverFor($network)->disburse(
                                $record,
                                (float) $data['payout_amount'],
                                $data['payout_currency'] ?? config('payouts.currency', 'XAF'),
                            );
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Payout failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title($result->status === 'paid' ? 'Payout settled' : ($result->success ? 'Payout initiated' : 'Payout failed'))
                            ->body($result->message)
                            ->{$result->success ? 'success' : 'danger'}()
                            ->send();
                    }),
                \App\Filament\Resources\PractitionerApplicationResource\Actions\PlaceInCohortAction::make(),
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
