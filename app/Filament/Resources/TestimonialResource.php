<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'author_name';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Author')->schema([
                Forms\Components\TextInput::make('author_name')
                    ->required()
                    ->maxLength(120),

                Forms\Components\TextInput::make('author_title')
                    ->label('Title / Role')
                    ->maxLength(120)
                    ->nullable(),

                Forms\Components\TextInput::make('author_facility')
                    ->maxLength(150)
                    ->nullable(),

                Forms\Components\TextInput::make('author_country')
                    ->maxLength(80)
                    ->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Content')->schema([
                Forms\Components\Textarea::make('body')
                    ->label('Quote (EN)')
                    ->required()
                    ->rows(4)
                    ->maxLength(600)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('body_fr')
                    ->label('Quote (FR)')
                    ->rows(4)
                    ->maxLength(600)
                    ->nullable()
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Settings')->schema([
                Forms\Components\Select::make('rating')
                    ->options([1 => '★', 2 => '★★', 3 => '★★★', 4 => '★★★★', 5 => '★★★★★'])
                    ->default(5)
                    ->required(),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower number = shown first'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Visible on landing page')
                    ->default(true)
                    ->inline(false),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author_name')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn ($record) => implode(' · ', array_filter([$record->author_title, $record->author_facility]))),

                Tables\Columns\TextColumn::make('body')
                    ->label('Quote')
                    ->limit(80)
                    ->wrap(),

                Tables\Columns\TextColumn::make('rating')
                    ->formatStateUsing(fn ($state) => str_repeat('★', $state))
                    ->html(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Live')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
            Infolists\Components\Section::make('Author')->schema([
                Infolists\Components\TextEntry::make('author_name'),
                Infolists\Components\TextEntry::make('author_title')->label('Title / Role')->placeholder('—'),
                Infolists\Components\TextEntry::make('author_facility')->placeholder('—'),
                Infolists\Components\TextEntry::make('author_country')->placeholder('—'),
            ])->columns(4),

            Infolists\Components\Section::make('Quote')->schema([
                Infolists\Components\TextEntry::make('body')->label('Quote (EN)')->columnSpanFull(),
                Infolists\Components\TextEntry::make('body_fr')->label('Quote (FR)')->placeholder('Not translated')->columnSpanFull(),
            ]),

            Infolists\Components\Section::make('Settings')->schema([
                Infolists\Components\TextEntry::make('rating')
                    ->formatStateUsing(fn ($state) => str_repeat('★', $state) . ' (' . $state . '/5)'),
                Infolists\Components\IconEntry::make('is_active')->label('Visible on landing page')->boolean(),
                Infolists\Components\TextEntry::make('sort_order')->label('Display Order'),
            ])->columns(3),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'view'   => Pages\ViewTestimonial::route('/{record}'),
            'edit'   => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
