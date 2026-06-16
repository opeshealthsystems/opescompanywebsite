<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetResource\Pages;
use App\Models\Budget;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Budgets';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 65;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Budget Entry')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('year')
                        ->options(
                            array_combine(
                                range(now()->year - 1, now()->year + 2),
                                range(now()->year - 1, now()->year + 2)
                            )
                        )
                        ->default(now()->year)
                        ->required(),

                    Forms\Components\Select::make('category')
                        ->options(Budget::categoryOptions())
                        ->required(),

                    Forms\Components\TextInput::make('department')
                        ->default('General')
                        ->maxLength(100)
                        ->required(),

                    Forms\Components\TextInput::make('allocated_amount')
                        ->label('Allocated Amount')
                        ->numeric()
                        ->required()
                        ->minValue(0),

                    Forms\Components\Select::make('currency')
                        ->options([
                            'XAF' => 'XAF',
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                        ])
                        ->default('XAF'),

                    Forms\Components\Textarea::make('notes')
                        ->nullable()
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => Budget::categoryOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('department')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('allocated_amount')
                    ->label('Budget')
                    ->getStateUsing(fn ($record) => $record->currency . ' ' . number_format((float) $record->allocated_amount, 0)),

                Tables\Columns\TextColumn::make('currency'),
            ])
            ->defaultSort('year', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->options(array_combine(
                        range(now()->year - 2, now()->year + 2),
                        range(now()->year - 2, now()->year + 2)
                    ))
                    ->default(now()->year),
                Tables\Filters\SelectFilter::make('category')
                    ->options(Budget::categoryOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            Infolists\Components\Section::make('Budget Entry')->columns(2)->schema([
                Infolists\Components\TextEntry::make('year'),
                Infolists\Components\TextEntry::make('category')
                    ->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => Budget::categoryOptions()[$state] ?? $state),
                Infolists\Components\TextEntry::make('department'),
                Infolists\Components\TextEntry::make('allocated_amount')
                    ->label('Budget')
                    ->getStateUsing(fn ($record) => $record->currency . ' ' . number_format((float) $record->allocated_amount, 0)),
                Infolists\Components\TextEntry::make('currency'),
            ]),
            Infolists\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Infolists\Components\TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['department'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'view'   => Pages\ViewBudget::route('/{record}'),
            'edit'   => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}
