<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Announcements';
    protected static ?string $navigationGroup = 'Communications';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Announcement')->schema([
                Forms\Components\TextInput::make('title')
                    ->required()->maxLength(200)->columnSpanFull(),

                Forms\Components\RichEditor::make('body')
                    ->required()->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Settings')->columns(2)->schema([
                Forms\Components\Select::make('audience')
                    ->options(Announcement::audienceOptions())
                    ->default('all')->required(),

                Forms\Components\Select::make('author_id')
                    ->label('Author')
                    ->options(fn () => User::whereHas('roles', fn ($q) =>
                        $q->whereIn('name', ['super_admin', 'admin'])
                    )->orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->required(),

                Forms\Components\Toggle::make('is_pinned')->label('Pin to top')->default(false),
                Forms\Components\Toggle::make('is_active')->label('Active')->default(true),

                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Publish At')->nullable()->default(now()),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_pinned')
                    ->label('📌')->boolean()
                    ->trueIcon('heroicon-o-star')->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')->falseColor('gray'),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()->limit(50)->weight('semibold'),

                Tables\Columns\TextColumn::make('audience')
                    ->badge()->color('info')
                    ->formatStateUsing(fn ($state) => Announcement::audienceOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('author.name')->label('By'),

                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')->dateTime('d M Y H:i')->sortable()->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')->since()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('is_pinned', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\TernaryFilter::make('is_pinned')->label('Pinned'),
                Tables\Filters\SelectFilter::make('audience')
                    ->options(Announcement::audienceOptions()),
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
            Infolists\Components\Section::make('Announcement')->schema([
                Infolists\Components\TextEntry::make('title')
                    ->weight('bold')->size('xl')->columnSpanFull(),

                Infolists\Components\TextEntry::make('body')
                    ->html()->label('')->columnSpanFull(),
            ]),

            Infolists\Components\Section::make('Meta')->columns(4)->schema([
                Infolists\Components\TextEntry::make('audience')
                    ->badge()->color('info')
                    ->formatStateUsing(fn ($state) => Announcement::audienceOptions()[$state] ?? $state),

                Infolists\Components\TextEntry::make('author.name')->label('Posted By'),

                Infolists\Components\IconEntry::make('is_pinned')->label('Pinned')->boolean(),
                Infolists\Components\IconEntry::make('is_active')->label('Active')->boolean(),

                Infolists\Components\TextEntry::make('published_at')
                    ->label('Published At')->dateTime('d M Y H:i')->placeholder('—'),

                Infolists\Components\TextEntry::make('created_at')
                    ->label('Created')->dateTime('d M Y H:i'),
            ]),
        ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $active = static::getModel()::where('is_active', true)->count();
        return $active > 0 ? (string) $active : null;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'view'   => Pages\ViewAnnouncement::route('/{record}'),
            'edit'   => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
