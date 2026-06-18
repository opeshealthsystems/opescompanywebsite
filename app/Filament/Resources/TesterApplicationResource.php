<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TesterApplicationResource\Pages;
use App\Models\TesterApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TesterApplicationResource extends Resource
{
    protected static ?string $model = TesterApplication::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Practitioners';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationLabel = 'Tester Applications';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Applicant')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('phone'),
                Forms\Components\Select::make('profession')->required()->options(collect(TesterApplication::$professions)->map(fn($v) => $v['en'])),
                Forms\Components\TextInput::make('specialty'),
                Forms\Components\TextInput::make('institution_name'),
                Forms\Components\TextInput::make('country')->required(),
                Forms\Components\TextInput::make('city'),
                Forms\Components\TextInput::make('years_experience')->numeric()->minValue(0)->maxValue(50),
            ]),
            Forms\Components\Section::make('Technical Profile')->schema([
                Forms\Components\CheckboxList::make('devices')->options(['smartphone' => 'Smartphone', 'tablet' => 'Tablet', 'laptop' => 'Laptop', 'desktop' => 'Desktop']),
                Forms\Components\CheckboxList::make('platforms')->options(['android' => 'Android', 'ios' => 'iOS', 'windows' => 'Windows', 'macos' => 'macOS', 'web' => 'Web Browser']),
                Forms\Components\Textarea::make('tech_experience')->rows(3),
                Forms\Components\Textarea::make('motivation')->required()->rows(4),
            ]),
            Forms\Components\Section::make('Admin')->columns(2)->schema([
                Forms\Components\Select::make('status')->options(array_combine(TesterApplication::$statuses, TesterApplication::$statuses))->required(),
                Forms\Components\TextInput::make('locale')->disabled(),
                Forms\Components\Textarea::make('admin_notes')->columnSpanFull()->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('profession')->badge()->sortable(),
                Tables\Columns\TextColumn::make('country')->sortable(),
                Tables\Columns\TextColumn::make('years_experience')->suffix(' yrs')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning' => 'pending', 'success' => 'accepted', 'danger' => 'rejected', 'primary' => 'active']),
                Tables\Columns\TextColumn::make('created_at')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(array_combine(TesterApplication::$statuses, TesterApplication::$statuses)),
                Tables\Filters\SelectFilter::make('profession')->options(collect(TesterApplication::$professions)->map(fn($v) => $v['en'])),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTesterApplications::route('/'),
            'create' => Pages\CreateTesterApplication::route('/create'),
            'edit'   => Pages\EditTesterApplication::route('/{record}/edit'),
        ];
    }
}
