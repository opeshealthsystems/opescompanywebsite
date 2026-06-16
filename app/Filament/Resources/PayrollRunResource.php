<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollRunResource\Pages;
use App\Models\PayrollRun;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PayrollRunResource extends Resource
{
    protected static ?string $model = PayrollRun::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Payroll Runs';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 55;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'draft')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Payroll Run')->columns(2)->schema([
                Forms\Components\TextInput::make('reference')
                    ->disabled()
                    ->placeholder('Auto-generated'),

                Forms\Components\Select::make('status')
                    ->options(PayrollRun::statusOptions())
                    ->default('draft')
                    ->required(),

                Forms\Components\DatePicker::make('period_start')
                    ->label('Period Start')
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('period_end')
                    ->label('Period End')
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('currency')
                    ->options(['XAF' => 'XAF', 'USD' => 'USD', 'EUR' => 'EUR'])
                    ->default('XAF')
                    ->required(),

                Forms\Components\Select::make('processed_by')
                    ->label('Processed By')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->rows(2)
                    ->nullable()
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->fontFamily('mono')
                    ->copyable()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('period_start')
                    ->label('Period')
                    ->date('M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_gross')
                    ->label('Gross')
                    ->getStateUsing(fn (PayrollRun $record) => $record->currency . ' ' . number_format((float) $record->total_gross, 0)),

                Tables\Columns\TextColumn::make('total_net')
                    ->label('Net')
                    ->getStateUsing(fn (PayrollRun $record) => $record->currency . ' ' . number_format((float) $record->total_net, 0))
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('entries_count')
                    ->counts('entries')
                    ->label('Employees'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'      => 'gray',
                        'processing' => 'warning',
                        'completed'  => 'success',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('processor.name')
                    ->label('Processed By')
                    ->toggleable(),
            ])
            ->defaultSort('period_start', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->hidden(fn (PayrollRun $record) => $record->status === 'completed'),
                Tables\Actions\Action::make('complete')
                    ->label('Mark Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (PayrollRun $record) => $record->status !== 'processing')
                    ->action(fn (PayrollRun $record) => $record->update([
                        'status'       => 'completed',
                        'completed_at' => now(),
                    ])),
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
            Infolists\Components\Section::make('Payroll Run')->columns(3)->schema([
                Infolists\Components\TextEntry::make('reference')
                    ->fontFamily('mono')
                    ->copyable(),

                Infolists\Components\TextEntry::make('period_start')
                    ->label('Period Start')
                    ->date('d M Y'),

                Infolists\Components\TextEntry::make('period_end')
                    ->label('Period End')
                    ->date('d M Y'),

                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'      => 'gray',
                        'processing' => 'warning',
                        'completed'  => 'success',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    }),

                Infolists\Components\TextEntry::make('currency'),

                Infolists\Components\TextEntry::make('processor.name')
                    ->label('Processed By'),
            ]),

            Infolists\Components\Section::make('Totals')->columns(3)->schema([
                Infolists\Components\TextEntry::make('total_gross')
                    ->label('Gross Total')
                    ->getStateUsing(fn (PayrollRun $record) => $record->currency . ' ' . number_format((float) $record->total_gross, 0)),

                Infolists\Components\TextEntry::make('total_deductions')
                    ->label('Total Deductions')
                    ->getStateUsing(function (PayrollRun $record) {
                        $sum = $record->entries()->sum('total_deductions');
                        return $sum > 0
                            ? $record->currency . ' ' . number_format((float) $sum, 0)
                            : '—';
                    }),

                Infolists\Components\TextEntry::make('total_net')
                    ->label('Net Total')
                    ->getStateUsing(fn (PayrollRun $record) => $record->currency . ' ' . number_format((float) $record->total_net, 0))
                    ->weight('bold'),
            ]),

            Infolists\Components\Section::make('Notes')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Infolists\Components\TextEntry::make('notes')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\PayrollRunResource\RelationManagers\EntriesRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPayrollRuns::route('/'),
            'create' => Pages\CreatePayrollRun::route('/create'),
            'view'   => Pages\ViewPayrollRun::route('/{record}'),
            'edit'   => Pages\EditPayrollRun::route('/{record}/edit'),
        ];
    }
}
