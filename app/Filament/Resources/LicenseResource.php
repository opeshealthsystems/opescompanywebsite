<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LicenseResource\Pages;
use App\Models\License;
use App\Models\User;
use App\Support\ProductCatalog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Licenses';
    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'support']) ?? false;
    }

    public static function getProductOptions(): array
    {
        return ProductCatalog::options();
    }

    public static function form(Form $form): Form
    {
        $productOptions = static::getProductOptions();

        return $form->schema([
            Forms\Components\Section::make('License Details')->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Customer')
                    ->options(
                        User::role('customer')->orderBy('name')->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('product_slug')
                    ->label('Product')
                    ->options($productOptions)
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                        if (!$state) return;
                        $options = $productOptions;
                        if (isset($options[$state])) {
                            $set('product_name', $options[$state]);
                        }
                    }),

                Forms\Components\Hidden::make('product_name'),

                Forms\Components\TextInput::make('license_key')
                    ->label('License Key')
                    ->required()
                    ->default(fn () => License::generateKey())
                    ->maxLength(50)
                    ->columnSpanFull(),

                Forms\Components\Select::make('plan')
                    ->options(License::planOptions())
                    ->default('standard')
                    ->required(),

                Forms\Components\TextInput::make('seats')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(9999)
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'active'    => 'Active',
                        'suspended' => 'Suspended',
                        'expired'   => 'Expired',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('active')
                    ->required(),
            ])->columns(2),

            Forms\Components\Section::make('Validity Period')->schema([
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->default(now()->toDateString()),

                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->default(now()->addYear()->toDateString())
                    ->after('start_date'),
            ])->columns(2),

            Forms\Components\Section::make('Pricing (optional)')->schema([
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->nullable()
                    ->prefix('XAF')
                    ->helperText('Leave blank for complementary licenses'),

                Forms\Components\Select::make('currency')
                    ->options(['XAF' => 'XAF (CFA Franc)', 'USD' => 'USD', 'EUR' => 'EUR'])
                    ->default('XAF'),

                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(2)->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('license_key')
                    ->label('Key')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('plan')
                    ->badge()
                    ->formatStateUsing(fn ($state) => License::planLabel($state))
                    ->color(fn ($state) => match ($state) {
                        'starter'      => 'gray',
                        'standard'     => 'info',
                        'professional' => 'warning',
                        'enterprise'   => 'success',
                        default        => 'gray',
                    }),

                Tables\Columns\TextColumn::make('seats')
                    ->label('Seats')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'success',
                        'suspended' => 'warning',
                        'expired'   => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Expires')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isExpiringSoon() ? 'warning' : null),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active'    => 'Active',
                        'suspended' => 'Suspended',
                        'expired'   => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('plan')
                    ->options(License::planOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (License $record) => $record->status === 'active')
                    ->action(fn (License $record) => $record->update(['status' => 'suspended'])),
                Tables\Actions\Action::make('reactivate')
                    ->label('Reactivate')
                    ->icon('heroicon-o-play-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (License $record) => $record->status === 'suspended')
                    ->action(fn (License $record) => $record->update(['status' => 'active'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLicenses::route('/'),
            'create' => Pages\CreateLicense::route('/create'),
            'edit'   => Pages\EditLicense::route('/{record}/edit'),
        ];
    }
}
