<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Models\Contract;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Contracts';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 12;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'active')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', now()->addDays(30))
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active contracts expiring within 30 days';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contract')->columns(2)->schema([
                Forms\Components\TextInput::make('reference')
                    ->disabled()
                    ->placeholder('Auto-generated'),
                Forms\Components\Select::make('status')
                    ->options(Contract::statusOptions())
                    ->default('draft')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(250)
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->options(Contract::typeOptions())
                    ->required(),
                Forms\Components\Select::make('lead_id')
                    ->label('Lead / Client')
                    ->options(fn () => \App\Models\Lead::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->placeholder('No lead linked'),
                Forms\Components\Select::make('created_by')
                    ->label('Managed By')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->required(),
            ]),
            Forms\Components\Section::make('Dates & Value')->columns(2)->schema([
                Forms\Components\DatePicker::make('start_date')
                    ->native(false)
                    ->nullable(),
                Forms\Components\DatePicker::make('end_date')
                    ->native(false)
                    ->nullable(),
                Forms\Components\DateTimePicker::make('signed_at')
                    ->label('Signed At')
                    ->nullable(),
                Forms\Components\Toggle::make('auto_renew')
                    ->label('Auto-Renew')
                    ->default(false),
                Forms\Components\TextInput::make('value')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                Forms\Components\Select::make('currency')
                    ->options(['XAF' => 'XAF', 'USD' => 'USD', 'EUR' => 'EUR'])
                    ->default('XAF'),
            ]),
            Forms\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Forms\Components\Textarea::make('notes')
                    ->rows(3)
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(35)
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => Contract::typeOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'     => 'success',
                        'draft'      => 'gray',
                        'sent'       => 'info',
                        'expired'    => 'danger',
                        'terminated' => 'danger',
                        'renewed'    => 'warning',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('lead.name')
                    ->label('Client')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('value')
                    ->getStateUsing(fn (Contract $record) => $record->formatValue())
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Expires')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable()
                    ->color(fn (Contract $record) => $record->isExpiringSoon() ? 'warning' : null),
                Tables\Columns\IconColumn::make('auto_renew')
                    ->label('Auto-Renew')
                    ->boolean()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Contract::statusOptions()),
                Tables\Filters\SelectFilter::make('type')
                    ->options(Contract::typeOptions()),
                Tables\Filters\TernaryFilter::make('auto_renew')
                    ->label('Auto-Renew'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (Contract $record) => ! in_array($record->status, ['draft', 'sent']))
                    ->action(fn (Contract $record) => $record->update([
                        'status'    => 'active',
                        'signed_at' => $record->signed_at ?? now(),
                    ])),
                Tables\Actions\Action::make('terminate')
                    ->label('Terminate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->hidden(fn (Contract $record) => $record->status !== 'active')
                    ->action(fn (Contract $record) => $record->update(['status' => 'terminated'])),
                Tables\Actions\DeleteAction::make(),
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
            Infolists\Components\Section::make('Contract')->columns(3)->schema([
                Infolists\Components\TextEntry::make('reference')
                    ->fontFamily('mono')
                    ->copyable(),
                Infolists\Components\TextEntry::make('title')
                    ->columnSpan(2),
                Infolists\Components\TextEntry::make('type')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => Contract::typeOptions()[$state] ?? $state),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'     => 'success',
                        'draft'      => 'gray',
                        'sent'       => 'info',
                        'expired'    => 'danger',
                        'terminated' => 'danger',
                        'renewed'    => 'warning',
                        default      => 'gray',
                    }),
                Infolists\Components\TextEntry::make('lead.name')
                    ->label('Client')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('creator.name')
                    ->label('Managed By'),
            ]),
            Infolists\Components\Section::make('Terms')->columns(4)->schema([
                Infolists\Components\TextEntry::make('start_date')
                    ->date('d M Y')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('end_date')
                    ->date('d M Y')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('signed_at')
                    ->label('Signed At')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—'),
                Infolists\Components\IconEntry::make('auto_renew')
                    ->label('Auto-Renew')
                    ->boolean(),
                Infolists\Components\TextEntry::make('value')
                    ->getStateUsing(fn ($record) => $record->formatValue())
                    ->weight('bold'),
                Infolists\Components\TextEntry::make('currency'),
            ]),
            Infolists\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Infolists\Components\TextEntry::make('notes')
                    ->placeholder('—')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'title'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'view'   => Pages\ViewContract::route('/{record}'),
            'edit'   => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
