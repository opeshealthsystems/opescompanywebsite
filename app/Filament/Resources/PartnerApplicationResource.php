<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerApplicationResource\Pages;
use App\Models\PartnerApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartnerApplicationResource extends Resource
{
    protected static ?string $model = PartnerApplication::class;
    protected static ?string $navigationIcon = 'heroicon-o-handshake';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Partner Applications';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Organisation')->columns(2)->schema([
                Forms\Components\TextInput::make('organization_name')->required(),
                Forms\Components\TextInput::make('contact_name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('phone'),
                Forms\Components\TextInput::make('country')->required(),
                Forms\Components\TextInput::make('city'),
                Forms\Components\TextInput::make('website'),
                Forms\Components\Select::make('organization_type')->options([
                    'private' => 'Private', 'public' => 'Public', 'ngo' => 'NGO', 'startup' => 'Startup',
                ]),
            ]),
            Forms\Components\Section::make('Partnership')->schema([
                Forms\Components\Select::make('partner_type')->required()->options(collect(PartnerApplication::$partnerTypes)->map(fn($v) => $v['en'])),
                Forms\Components\TextInput::make('annual_revenue_range'),
                Forms\Components\Textarea::make('target_market')->rows(2),
                Forms\Components\Textarea::make('description')->required()->rows(4),
            ]),
            Forms\Components\Section::make('Admin')->columns(2)->schema([
                Forms\Components\Select::make('status')->options(array_combine(PartnerApplication::$statuses, PartnerApplication::$statuses))->required(),
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
                Tables\Columns\TextColumn::make('organization_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact_name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('country')->sortable(),
                Tables\Columns\BadgeColumn::make('partner_type')
                    ->colors(['success' => 'hospital', 'primary' => 'technology', 'warning' => 'reseller', 'info' => 'implementation']),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning' => 'pending', 'primary' => 'reviewing', 'success' => 'approved', 'danger' => 'rejected']),
                Tables\Columns\TextColumn::make('created_at')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(array_combine(PartnerApplication::$statuses, PartnerApplication::$statuses)),
                Tables\Filters\SelectFilter::make('partner_type')->options(collect(PartnerApplication::$partnerTypes)->map(fn($v) => $v['en'])),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPartnerApplications::route('/'),
            'create' => Pages\CreatePartnerApplication::route('/create'),
            'edit'   => Pages\EditPartnerApplication::route('/{record}/edit'),
        ];
    }
}
