<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DemoRequestResource\Pages;
use App\Models\DemoRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DemoRequestResource extends Resource
{
    protected static ?string $model = DemoRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Demo Requests';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contact')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('phone'),
                Forms\Components\TextInput::make('organization_name')->required()->columnSpanFull(),
                Forms\Components\Select::make('country')->searchable()->options(\App\Models\DemoRequest::$institutionTypes)->placeholder('Select country'),
                Forms\Components\Select::make('institution_type')->options(collect(DemoRequest::$institutionTypes)->map(fn($v) => $v['en'])),
                Forms\Components\Select::make('institution_size')->options(array_combine(DemoRequest::$sizes, DemoRequest::$sizes)),
            ]),
            Forms\Components\Section::make('Request')->schema([
                Forms\Components\TagsInput::make('products')->placeholder('Add product'),
                Forms\Components\DatePicker::make('preferred_date'),
                Forms\Components\Textarea::make('message')->rows(3),
            ]),
            Forms\Components\Section::make('Admin')->columns(2)->schema([
                Forms\Components\Select::make('status')->options(array_combine(DemoRequest::$statuses, DemoRequest::$statuses))->required(),
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
                Tables\Columns\TextColumn::make('organization_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('institution_type')->badge(),
                Tables\Columns\TextColumn::make('institution_size'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning' => 'new', 'primary' => 'contacted', 'info' => 'scheduled', 'success' => 'completed', 'danger' => 'rejected']),
                Tables\Columns\TextColumn::make('preferred_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(array_combine(DemoRequest::$statuses, DemoRequest::$statuses)),
                Tables\Filters\SelectFilter::make('institution_type')->options(collect(DemoRequest::$institutionTypes)->map(fn($v) => $v['en'])),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDemoRequests::route('/'),
            'create' => Pages\CreateDemoRequest::route('/create'),
            'edit'   => Pages\EditDemoRequest::route('/{record}/edit'),
        ];
    }
}
