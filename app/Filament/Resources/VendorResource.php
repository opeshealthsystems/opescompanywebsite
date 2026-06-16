<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Vendors';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 60;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Vendor')->columns(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->required()->maxLength(200)->columnSpanFull(),

                Forms\Components\TextInput::make('contact_name')
                    ->label('Contact Name')->nullable(),

                Forms\Components\TextInput::make('email')
                    ->email()->nullable(),

                Forms\Components\TextInput::make('phone')
                    ->nullable(),

                Forms\Components\TextInput::make('tax_id')
                    ->label('Tax / VAT ID')->nullable(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')->default(true),

                Forms\Components\Textarea::make('address')
                    ->rows(2)->nullable()->columnSpanFull(),

                Forms\Components\Textarea::make('notes')
                    ->rows(2)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->sortable()->weight('semibold'),

                Tables\Columns\TextColumn::make('contact_name')
                    ->label('Contact')->placeholder('—'),

                Tables\Columns\TextColumn::make('email')
                    ->copyable()->placeholder('—'),

                Tables\Columns\TextColumn::make('phone')
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')->boolean(),

                Tables\Columns\TextColumn::make('purchase_orders_count')
                    ->label('POs')->counts('purchaseOrders'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, Vendor $record) {
                        if ($record->purchaseOrders()->exists()) {
                            $action->cancel();
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Cannot delete vendor')
                                ->body('This vendor has purchase orders attached.')
                                ->send();
                        }
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
            Infolists\Components\Section::make('Vendor')->columns(2)->schema([
                Infolists\Components\TextEntry::make('name')
                    ->weight('bold')->columnSpan(2),

                Infolists\Components\TextEntry::make('contact_name')
                    ->label('Contact')->placeholder('—'),

                Infolists\Components\TextEntry::make('email')
                    ->copyable()->placeholder('—'),

                Infolists\Components\TextEntry::make('phone')
                    ->placeholder('—'),

                Infolists\Components\TextEntry::make('tax_id')
                    ->label('Tax ID')->placeholder('—'),

                Infolists\Components\IconEntry::make('is_active')
                    ->label('Active')->boolean(),

                Infolists\Components\TextEntry::make('address')
                    ->placeholder('—')->columnSpan(2),

                Infolists\Components\TextEntry::make('notes')
                    ->placeholder('—')->columnSpan(2),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'contact_name'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'view'   => Pages\ViewVendor::route('/{record}'),
            'edit'   => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}
