<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Purchase Orders';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 61;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', ['draft', 'submitted'])->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Purchase Order')->columns(2)->schema([
                Forms\Components\TextInput::make('reference')
                    ->disabled()->placeholder('Auto-generated'),

                Forms\Components\Select::make('status')
                    ->options(PurchaseOrder::statusOptions())
                    ->default('draft')->required(),

                Forms\Components\TextInput::make('title')
                    ->required()->maxLength(250)->columnSpanFull(),

                Forms\Components\Select::make('vendor_id')
                    ->label('Vendor (Registry)')
                    ->options(fn () => Vendor::activeOptions())
                    ->searchable()->nullable()->placeholder('Select or type below'),

                Forms\Components\TextInput::make('vendor_name')
                    ->label('Vendor Name (Manual)')
                    ->nullable()
                    ->helperText('Fill if vendor not in registry'),

                Forms\Components\DatePicker::make('expected_date')
                    ->label('Expected Date')->nullable()->native(false),

                Forms\Components\Select::make('currency')
                    ->options(['XAF' => 'XAF', 'USD' => 'USD', 'EUR' => 'EUR'])
                    ->default('XAF')->required(),

                Forms\Components\Select::make('requested_by')
                    ->label('Requested By')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()->required(),
            ]),

            Forms\Components\Section::make('Line Items')->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship('items')
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('description')
                            ->required()->columnSpan(2),

                        Forms\Components\TextInput::make('quantity')
                            ->numeric()->default(1)->required(),

                        Forms\Components\TextInput::make('unit_price')
                            ->numeric()->required(),
                    ])
                    ->addActionLabel('Add Item')
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Totals')->columns(3)->schema([
                Forms\Components\TextInput::make('subtotal')
                    ->numeric()->default(0)->disabled(),

                Forms\Components\TextInput::make('tax_amount')
                    ->label('Tax Amount')->numeric()->default(0),

                Forms\Components\TextInput::make('total')
                    ->numeric()->default(0)->disabled(),
            ]),

            Forms\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Forms\Components\Textarea::make('description')
                    ->rows(3)->nullable()->columnSpanFull(),

                Forms\Components\Textarea::make('notes')
                    ->label('Internal Notes')->rows(2)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->fontFamily('mono')->copyable()->searchable()->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()->limit(40),

                Tables\Columns\TextColumn::make('vendor_display')
                    ->label('Vendor')
                    ->getStateUsing(fn (PurchaseOrder $record) => $record->vendor?->name ?? $record->vendor_name ?? '—')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('total_display')
                    ->label('Total')
                    ->getStateUsing(fn (PurchaseOrder $record) => $record->formatTotal()),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'draft'     => 'gray',
                        'submitted' => 'info',
                        'approved'  => 'success',
                        'received'  => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('requester.name')
                    ->label('Requested By')->toggleable(),

                Tables\Columns\TextColumn::make('expected_date')
                    ->label('Expected')->date('d M Y')->placeholder('—')->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(PurchaseOrder::statusOptions()),
                Tables\Filters\SelectFilter::make('vendor_id')
                    ->label('Vendor')
                    ->relationship('vendor', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (PurchaseOrder $record) => $record->status !== 'submitted')
                    ->action(fn (PurchaseOrder $record) => $record->update([
                        'status'      => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ])),
                Tables\Actions\Action::make('mark_received')
                    ->label('Mark Received')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->color('info')
                    ->requiresConfirmation()
                    ->hidden(fn (PurchaseOrder $record) => $record->status !== 'approved')
                    ->action(fn (PurchaseOrder $record) => $record->update([
                        'status'        => 'received',
                        'received_date' => now(),
                    ])),
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
            Infolists\Components\Section::make('Purchase Order')->columns(3)->schema([
                Infolists\Components\TextEntry::make('reference')
                    ->fontFamily('mono')->copyable(),

                Infolists\Components\TextEntry::make('title')
                    ->columnSpan(2),

                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'draft'     => 'gray',
                        'submitted' => 'info',
                        'approved'  => 'success',
                        'received'  => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),

                Infolists\Components\TextEntry::make('currency'),

                Infolists\Components\TextEntry::make('expected_date')
                    ->date('d M Y')->placeholder('—'),
            ]),

            Infolists\Components\Section::make('Vendor')->columns(2)->schema([
                Infolists\Components\TextEntry::make('vendor.name')
                    ->label('Registered Vendor')->placeholder('—'),

                Infolists\Components\TextEntry::make('vendor_name')
                    ->label('Manual Vendor')->placeholder('—'),
            ]),

            Infolists\Components\Section::make('Totals')->columns(3)->schema([
                Infolists\Components\TextEntry::make('subtotal')
                    ->getStateUsing(fn (PurchaseOrder $record) => $record->currency . ' ' . number_format((float) $record->subtotal, 0)),

                Infolists\Components\TextEntry::make('tax_amount')
                    ->label('Tax Amount')
                    ->getStateUsing(fn (PurchaseOrder $record) => $record->currency . ' ' . number_format((float) $record->tax_amount, 0)),

                Infolists\Components\TextEntry::make('total')
                    ->getStateUsing(fn (PurchaseOrder $record) => $record->formatTotal())
                    ->weight('bold')->size('lg'),
            ]),

            Infolists\Components\Section::make('Line Items')->schema([
                Infolists\Components\RepeatableEntry::make('items')
                    ->schema([
                        Infolists\Components\TextEntry::make('description'),
                        Infolists\Components\TextEntry::make('quantity'),
                        Infolists\Components\TextEntry::make('unit_price')
                            ->getStateUsing(fn ($record) => number_format((float) $record->unit_price, 0)),
                        Infolists\Components\TextEntry::make('total')
                            ->getStateUsing(fn ($record) => number_format((float) $record->total, 0)),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]),

            Infolists\Components\Section::make('Approval')->columns(3)->schema([
                Infolists\Components\TextEntry::make('requester.name')
                    ->label('Requested By'),

                Infolists\Components\TextEntry::make('approver.name')
                    ->label('Approved By')->placeholder('—'),

                Infolists\Components\TextEntry::make('approved_at')
                    ->dateTime('d M Y H:i')->placeholder('—'),
            ]),

            Infolists\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Infolists\Components\TextEntry::make('description')
                    ->placeholder('—')->columnSpanFull(),

                Infolists\Components\TextEntry::make('notes')
                    ->label('Internal Notes')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'title', 'vendor_name'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'view'   => Pages\ViewPurchaseOrder::route('/{record}'),
            'edit'   => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
