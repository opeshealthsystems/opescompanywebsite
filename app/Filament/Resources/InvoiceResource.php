<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\License;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 40;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo('manage_accounting') ?? false;
    }

    public static function canCreate(): bool { return static::canAccess(); }
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool { return static::canAccess(); }
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool { return static::canAccess(); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Invoice Details')->schema([
                Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->options(fn () => User::role('customer')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('issued_by')
                    ->label('Issued By')
                    ->options(fn () => User::whereHas('roles', fn ($q) =>
                        $q->whereIn('name', ['super_admin', 'admin'])
                    )->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                Forms\Components\Select::make('status')
                    ->options(Invoice::statusOptions())
                    ->default('draft')
                    ->required(),

                Forms\Components\Select::make('license_id')
                    ->label('Linked License (optional)')
                    ->options(fn () => License::with('customer')
                        ->orderByDesc('created_at')
                        ->get()
                        ->mapWithKeys(fn ($l) => [$l->id => $l->license_key . ' — ' . $l->product_name])
                    )
                    ->searchable()
                    ->nullable(),

                Forms\Components\Select::make('currency')
                    ->options(['XAF' => 'XAF (CFA Franc)', 'USD' => 'USD', 'EUR' => 'EUR'])
                    ->default('XAF')
                    ->required(),

                Forms\Components\TextInput::make('tax_rate')
                    ->label('Tax Rate (%)')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100),

                Forms\Components\DatePicker::make('due_date')
                    ->nullable(),

                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->nullable()
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Line Items')->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required(),
                        Forms\Components\TextInput::make('unit_price')
                            ->label('Unit Price')
                            ->numeric()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $set('total', (int) $get('quantity') * (int) $get('unit_price'));
                            }),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(6)
                    ->defaultItems(1)
                    ->reorderable()
                    ->cloneable()
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'draft'     => 'gray',
                        'sent'      => 'info',
                        'paid'      => 'success',
                        'overdue'   => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->money('USD')
                    ->getStateUsing(fn ($record) => $record->grand_total),

                Tables\Columns\TextColumn::make('currency')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Invoice::statusOptions()),
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

    public static function getGloballySearchableAttributes(): array
    {
        return ['invoice_number'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view'   => Pages\ViewInvoice::route('/{record}'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
