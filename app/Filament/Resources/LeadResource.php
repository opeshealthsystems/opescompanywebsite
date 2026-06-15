<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?string $navigationLabel = 'Demo Requests';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contact Details')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(100),
                Forms\Components\TextInput::make('email')->email()->required()->maxLength(150),
                Forms\Components\TextInput::make('phone')->tel()->maxLength(30),
                Forms\Components\Select::make('facility_type')->options([
                    'clinic'      => 'Clinic',
                    'hospital'    => 'Hospital',
                    'laboratory'  => 'Laboratory',
                    'pharmacy'    => 'Pharmacy',
                    'government'  => 'Government / Ministry',
                    'ngo'         => 'NGO / International',
                    'other'       => 'Other',
                ]),
            ])->columns(2),

            Forms\Components\Section::make('Inquiry')->schema([
                Forms\Components\TextInput::make('products')->label('Products of interest')->maxLength(255),
                Forms\Components\Textarea::make('message')->rows(4)->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Meta')->schema([
                Forms\Components\Select::make('status')->options([
                    'new'       => 'New',
                    'contacted' => 'Contacted',
                    'qualified' => 'Qualified',
                    'closed'    => 'Closed / Won',
                ])->required(),
                Forms\Components\Select::make('source')->options([
                    'contact'      => 'Contact page',
                    'product-page' => 'Product page',
                    'demo'         => 'Demo CTA',
                ]),
                Forms\Components\TextInput::make('product_slug')->label('Product slug'),
                Forms\Components\TextInput::make('locale')->label('Language'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('facility_type')->label('Facility')->badge(),
                Tables\Columns\TextColumn::make('products')->label('Products')->limit(30),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'new',
                        'warning' => 'contacted',
                        'success' => 'qualified',
                        'gray'    => 'closed',
                    ]),
                Tables\Columns\TextColumn::make('locale')->label('Lang')->badge(),
                Tables\Columns\TextColumn::make('created_at')->label('Received')
                    ->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'new'       => 'New',
                    'contacted' => 'Contacted',
                    'qualified' => 'Qualified',
                    'closed'    => 'Closed',
                ]),
                Tables\Filters\SelectFilter::make('facility_type')->label('Facility type')->options([
                    'clinic'     => 'Clinic',
                    'hospital'   => 'Hospital',
                    'laboratory' => 'Laboratory',
                    'pharmacy'   => 'Pharmacy',
                    'government' => 'Government',
                    'ngo'        => 'NGO',
                ]),
            ])
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'view'   => Pages\ViewLead::route('/{record}'),
            'edit'   => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
