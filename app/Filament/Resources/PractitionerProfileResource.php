<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PractitionerProfileResource\Pages;
use App\Models\PractitionerProfile;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PractitionerProfileResource extends Resource
{
    protected static ?string $model = PractitionerProfile::class;
    protected static ?string $navigationIcon  = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Practitioners';
    protected static ?string $navigationGroup = 'Practitioners';
    protected static ?int    $navigationSort  = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Professional Information')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('User')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('profession')
                        ->options(PractitionerProfile::professionOptions())
                        ->required(),
                    Forms\Components\TextInput::make('specialty')
                        ->maxLength(120)
                        ->nullable(),
                    Forms\Components\TextInput::make('registration_number')
                        ->label('Professional Reg. Number')
                        ->maxLength(60)
                        ->nullable(),
                    Forms\Components\TextInput::make('years_of_experience')
                        ->label('Years of Experience')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(60)
                        ->nullable(),
                    Forms\Components\Toggle::make('is_verified')
                        ->label('Verified'),
                ]),
            Forms\Components\Section::make('Workplace')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('workplace_name')
                        ->label('Hospital / Facility Name')
                        ->maxLength(150)
                        ->nullable(),
                    Forms\Components\TextInput::make('workplace_city')
                        ->maxLength(80)
                        ->nullable(),
                    Forms\Components\TextInput::make('workplace_country')
                        ->maxLength(80)
                        ->default('CM')
                        ->nullable(),
                ]),
            Forms\Components\Section::make('Bio & Testimonial')
                ->schema([
                    Forms\Components\Textarea::make('bio')
                        ->rows(4)
                        ->nullable(),
                    Forms\Components\Textarea::make('opes_testimonial')
                        ->label('What they say about OPES')
                        ->rows(3)
                        ->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('profession')
                    ->badge()
                    ->formatStateUsing(fn ($state) => PractitionerProfile::professionOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('workplace_name')
                    ->label('Workplace')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('workplace_country')
                    ->label('Country')
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean(),
                Tables\Columns\TextColumn::make('tier')
                    ->label('Tier')
                    ->badge()
                    ->state(fn (PractitionerProfile $record): string => $record->user->practitionerTier()->label())
                    ->color(fn (PractitionerProfile $record): string => $record->user->practitionerTier()->filamentColor()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('profession')
                    ->options(PractitionerProfile::professionOptions()),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verified'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->hidden(fn (PractitionerProfile $record) => $record->is_verified)
                    ->requiresConfirmation()
                    ->action(function (PractitionerProfile $record) {
                        $record->update(['is_verified' => true]);
                        Notification::make()->title('Practitioner verified')->success()->send();
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
            Infolists\Components\Section::make('Professional')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('user.name')->label('Name'),
                    Infolists\Components\TextEntry::make('user.email')->label('Email')->copyable(),
                    Infolists\Components\TextEntry::make('profession')
                        ->formatStateUsing(fn ($state) => PractitionerProfile::professionOptions()[$state] ?? $state)
                        ->badge(),
                    Infolists\Components\TextEntry::make('specialty')->placeholder('—'),
                    Infolists\Components\TextEntry::make('registration_number')->label('Reg. Number')->placeholder('—'),
                    Infolists\Components\TextEntry::make('years_of_experience')->label('Years Exp.')->placeholder('—'),
                    Infolists\Components\IconEntry::make('is_verified')->label('Verified')->boolean(),
                ]),
            Infolists\Components\Section::make('Workplace')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('workplace_name')->label('Facility')->placeholder('—'),
                    Infolists\Components\TextEntry::make('workplace_city')->label('City')->placeholder('—'),
                    Infolists\Components\TextEntry::make('workplace_country')->label('Country')->placeholder('—'),
                ]),
            Infolists\Components\Section::make('Bio & Testimonial')
                ->schema([
                    Infolists\Components\TextEntry::make('bio')->placeholder('—'),
                    Infolists\Components\TextEntry::make('opes_testimonial')->label('What they say about OPES')->placeholder('—'),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPractitionerProfiles::route('/'),
            'view'   => Pages\ViewPractitionerProfile::route('/{record}'),
            'edit'   => Pages\EditPractitionerProfile::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['user']);
    }
}
