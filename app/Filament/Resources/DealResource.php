<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DealResource\Pages;
use App\Models\Deal;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DealResource extends Resource
{
    protected static ?string $model = Deal::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Deal Pipeline';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 11;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('stage', ['prospecting', 'qualification', 'proposal', 'negotiation'])->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Deal')->columns(2)->schema([
                Forms\Components\TextInput::make('reference')->disabled()->placeholder('Auto-generated'),
                Forms\Components\Select::make('stage')
                    ->options(Deal::stageOptions())->default('prospecting')->required()
                    ->live(),

                Forms\Components\TextInput::make('title')->required()->maxLength(250)->columnSpanFull(),

                Forms\Components\Select::make('lead_id')
                    ->label('Lead')
                    ->options(fn () => \App\Models\Lead::orderBy('name')->pluck('name', 'id'))
                    ->searchable()->nullable()->placeholder('No lead linked'),

                Forms\Components\Select::make('owner_id')
                    ->label('Deal Owner')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()->required(),

                Forms\Components\TextInput::make('value')->numeric()->default(0)->minValue(0)->required(),
                Forms\Components\Select::make('currency')->options(['XAF' => 'XAF', 'USD' => 'USD', 'EUR' => 'EUR'])->default('XAF'),

                Forms\Components\TextInput::make('probability')
                    ->numeric()->default(50)->minValue(0)->maxValue(100)->suffix('%'),

                Forms\Components\DatePicker::make('expected_close_date')->label('Expected Close')->native(false)->nullable(),
                Forms\Components\DatePicker::make('actual_close_date')->label('Actual Close')->native(false)->nullable(),

                Forms\Components\TextInput::make('lost_reason')
                    ->label('Lost Reason')->nullable()->maxLength(300)
                    ->hidden(fn (Forms\Get $get) => $get('stage') !== 'closed_lost')
                    ->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Forms\Components\Textarea::make('notes')->rows(3)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->fontFamily('mono')->copyable()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(35)->weight('semibold'),
                Tables\Columns\TextColumn::make('stage')->badge()
                    ->color(fn ($state) => match ($state) {
                        'closed_won'   => 'success',
                        'closed_lost'  => 'danger',
                        'negotiation'  => 'warning',
                        'proposal'     => 'info',
                        'qualification' => 'gray',
                        'prospecting'  => 'gray',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => Deal::stageOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('value')
                    ->getStateUsing(fn (Deal $record) => $record->formatValue())->sortable(),
                Tables\Columns\TextColumn::make('probability')->suffix('%')->sortable(),
                Tables\Columns\TextColumn::make('lead.name')->label('Lead')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('owner.name')->label('Owner')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('expected_close_date')->label('Close Date')->date('d M Y')->placeholder('—')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('stage')->options(Deal::stageOptions()),
                Tables\Filters\SelectFilter::make('owner_id')->label('Owner')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Deal')->columns(3)->schema([
                Infolists\Components\TextEntry::make('reference')->fontFamily('mono')->copyable(),
                Infolists\Components\TextEntry::make('title')->columnSpan(2),
                Infolists\Components\TextEntry::make('stage')->badge()
                    ->color(fn ($state) => match ($state) {
                        'closed_won'  => 'success',
                        'closed_lost' => 'danger',
                        'negotiation' => 'warning',
                        'proposal'    => 'info',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => Deal::stageOptions()[$state] ?? $state),
                Infolists\Components\TextEntry::make('lead.name')->label('Lead')->placeholder('—'),
                Infolists\Components\TextEntry::make('owner.name')->label('Deal Owner'),
            ]),
            Infolists\Components\Section::make('Financials')->columns(4)->schema([
                Infolists\Components\TextEntry::make('value')
                    ->getStateUsing(fn ($record) => $record->formatValue())->weight('bold'),
                Infolists\Components\TextEntry::make('probability')->suffix('%'),
                Infolists\Components\TextEntry::make('expected_close_date')->label('Expected Close')->date('d M Y')->placeholder('—'),
                Infolists\Components\TextEntry::make('actual_close_date')->label('Actual Close')->date('d M Y')->placeholder('—'),
            ]),
            Infolists\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Infolists\Components\TextEntry::make('lost_reason')->label('Lost Reason')->placeholder('—')->columnSpanFull(),
                Infolists\Components\TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
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
            'index'  => Pages\ListDeals::route('/'),
            'create' => Pages\CreateDeal::route('/create'),
            'view'   => Pages\ViewDeal::route('/{record}'),
            'edit'   => Pages\EditDeal::route('/{record}/edit'),
        ];
    }
}
