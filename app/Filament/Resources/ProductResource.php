<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\License;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Platform';
    protected static ?int $navigationSort = 0;
    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identity')->schema([
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(80)
                    ->helperText('URL-friendly identifier (e.g. opes-emr). Cannot be changed after creation.')
                    ->disabled(fn ($record) => $record !== null),

                Forms\Components\Select::make('category')
                    ->options(Product::categoryOptions())
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('name_fr')
                    ->label('Name (FR)')
                    ->maxLength(100)
                    ->nullable(),

                Forms\Components\TextInput::make('subtitle')
                    ->maxLength(120)
                    ->nullable()
                    ->columnSpan(2),

                Forms\Components\TextInput::make('subtitle_fr')
                    ->label('Subtitle (FR)')
                    ->maxLength(120)
                    ->nullable()
                    ->columnSpan(2),

                Forms\Components\Textarea::make('tagline')
                    ->rows(2)
                    ->maxLength(300)
                    ->nullable()
                    ->columnSpanFull(),
            ])->columns(4),

            Forms\Components\Section::make('Display')->schema([
                Forms\Components\TextInput::make('icon')
                    ->helperText('Lucide icon name (e.g. stethoscope, microscope, heart-pulse)')
                    ->nullable(),

                Forms\Components\ColorPicker::make('color')
                    ->nullable(),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->inline(false),

                Forms\Components\Toggle::make('is_featured')
                    ->label('Featured on landing page')
                    ->default(false)
                    ->inline(false),
            ])->columns(5),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->subtitle),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Product::categoryLabel($state))
                    ->color(fn ($state) => match ($state) {
                        'core'        => 'success',
                        'diagnostics' => 'info',
                        'specialist'  => 'gray',
                        default       => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('licenses_count')
                    ->label('Licenses')
                    ->counts('licenses')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(Product::categoryOptions()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            Infolists\Components\Section::make('Product Details')->schema([
                Infolists\Components\TextEntry::make('slug')
                    ->fontFamily('mono')
                    ->copyable(),
                Infolists\Components\TextEntry::make('category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Product::categoryLabel($state))
                    ->color(fn ($state) => match ($state) {
                        'core'        => 'success',
                        'diagnostics' => 'info',
                        'specialist'  => 'gray',
                        default       => 'gray',
                    }),
                Infolists\Components\TextEntry::make('name'),
                Infolists\Components\TextEntry::make('name_fr')->label('Name (FR)')->placeholder('—'),
                Infolists\Components\TextEntry::make('subtitle')->columnSpan(2)->placeholder('—'),
                Infolists\Components\TextEntry::make('subtitle_fr')->label('Subtitle (FR)')->columnSpan(2)->placeholder('—'),
                Infolists\Components\TextEntry::make('tagline')->columnSpanFull()->placeholder('—'),
            ])->columns(4),

            Infolists\Components\Section::make('Display & Status')->schema([
                Infolists\Components\TextEntry::make('icon')->placeholder('—'),
                Infolists\Components\ColorEntry::make('color')->placeholder('—'),
                Infolists\Components\IconEntry::make('is_active')->label('Active')->boolean(),
                Infolists\Components\IconEntry::make('is_featured')->label('Featured')->boolean(),
                Infolists\Components\TextEntry::make('sort_order')->label('Sort Order'),
                Infolists\Components\TextEntry::make('licenses_count')
                    ->label('Total Licenses')
                    ->state(fn ($record) => $record->licenses()->count()),
            ])->columns(6),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'subtitle'];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('is_active', true)->count() ?: null;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view'   => Pages\ViewProduct::route('/{record}'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
