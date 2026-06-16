<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollDeductionTypeResource\Pages;
use App\Models\PayrollDeductionType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PayrollDeductionTypeResource extends Resource
{
    protected static ?string $model = PayrollDeductionType::class;
    protected static ?string $navigationIcon = 'heroicon-o-minus-circle';
    protected static ?string $navigationLabel = 'Deduction Types';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 13;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(150),

                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(30)
                    ->unique(ignoreRecord: true)
                    ->helperText('Short identifier, e.g. CNPS_EMP'),

                Forms\Components\Select::make('calculation_type')
                    ->options(['percentage' => 'Percentage of Gross', 'fixed' => 'Fixed Amount'])
                    ->default('percentage')
                    ->required()
                    ->live(),

                Forms\Components\TextInput::make('rate')
                    ->label(fn ($get) => $get('calculation_type') === 'fixed' ? 'Amount (XAF)' : 'Rate (%)')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                Forms\Components\Toggle::make('apply_by_default')
                    ->label('Apply by Default')
                    ->default(true),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->label('Sort Order'),

                Forms\Components\Textarea::make('description')
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
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('name')
                    ->weight('semibold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('code')
                    ->badge()
                    ->color('gray')
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('rate_display')
                    ->label('Rate/Amount')
                    ->getStateUsing(fn (PayrollDeductionType $r) =>
                        $r->calculation_type === 'percentage'
                            ? $r->rate . '%'
                            : 'XAF ' . number_format((float) $r->rate, 0)),

                Tables\Columns\IconColumn::make('apply_by_default')
                    ->label('Default')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
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
            Infolists\Components\Section::make()->columns(3)->schema([
                Infolists\Components\TextEntry::make('name')
                    ->weight('semibold'),

                Infolists\Components\TextEntry::make('code')
                    ->badge()
                    ->color('gray')
                    ->fontFamily('mono'),

                Infolists\Components\TextEntry::make('calculation_type')
                    ->formatStateUsing(fn ($state) => $state === 'percentage' ? 'Percentage' : 'Fixed Amount'),

                Infolists\Components\TextEntry::make('rate')
                    ->label('Rate/Amount')
                    ->getStateUsing(fn (PayrollDeductionType $r) =>
                        $r->calculation_type === 'percentage'
                            ? $r->rate . '%'
                            : 'XAF ' . number_format((float) $r->rate, 0)),

                Infolists\Components\IconEntry::make('apply_by_default')
                    ->label('Default')
                    ->boolean(),

                Infolists\Components\IconEntry::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Infolists\Components\TextEntry::make('description')
                    ->placeholder('—')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPayrollDeductionTypes::route('/'),
            'create' => Pages\CreatePayrollDeductionType::route('/create'),
            'view'   => Pages\ViewPayrollDeductionType::route('/{record}'),
            'edit'   => Pages\EditPayrollDeductionType::route('/{record}/edit'),
        ];
    }
}
