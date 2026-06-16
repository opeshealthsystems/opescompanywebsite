<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierBillResource\Pages;
use App\Models\PurchaseOrder;
use App\Models\SupplierBill;
use App\Models\User;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierBillResource extends Resource
{
    protected static ?string $model = SupplierBill::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Supplier Bills';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 55;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'received')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Supplier Bill')->columns(2)->schema([
                Forms\Components\TextInput::make('reference')
                    ->disabled()
                    ->placeholder('Auto-generated'),
                Forms\Components\Select::make('status')
                    ->options(SupplierBill::statusOptions())
                    ->default('received')
                    ->required(),
                Forms\Components\TextInput::make('bill_number')
                    ->label("Supplier's Invoice #")
                    ->nullable()
                    ->maxLength(100),
                Forms\Components\Select::make('created_by')
                    ->label('Recorded By')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->required(),
            ]),

            Forms\Components\Section::make('Vendor')->columns(2)->schema([
                Forms\Components\Select::make('vendor_id')
                    ->label('Vendor (Registry)')
                    ->options(fn () => Vendor::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\TextInput::make('vendor_name')
                    ->label('Vendor Name (Manual)')
                    ->nullable()
                    ->helperText('Fill if not in registry'),
                Forms\Components\Select::make('purchase_order_id')
                    ->label('Linked PO')
                    ->options(fn () => PurchaseOrder::whereIn('status', ['approved', 'received'])
                        ->orderByDesc('created_at')
                        ->get()
                        ->mapWithKeys(fn ($po) => [$po->id => $po->reference . ' — ' . $po->title]))
                    ->searchable()
                    ->nullable()
                    ->placeholder('No PO'),
            ]),

            Forms\Components\Section::make('Dates & Currency')->columns(3)->schema([
                Forms\Components\DatePicker::make('issue_date')
                    ->required()
                    ->default(now())
                    ->native(false),
                Forms\Components\DatePicker::make('due_date')
                    ->nullable()
                    ->native(false),
                Forms\Components\Select::make('currency')
                    ->options(['XAF' => 'XAF', 'USD' => 'USD', 'EUR' => 'EUR'])
                    ->default('XAF')
                    ->required(),
            ]),

            Forms\Components\Section::make('Line Items')->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship('items')
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\TextInput::make('unit_price')
                            ->numeric()
                            ->required()
                            ->label('Unit Price'),
                    ])
                    ->addActionLabel('Add Item')
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Totals')->columns(3)->schema([
                Forms\Components\TextInput::make('subtotal')
                    ->numeric()
                    ->default(0)
                    ->disabled(),
                Forms\Components\TextInput::make('tax_amount')
                    ->label('Tax Amount')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->default(0)
                    ->disabled(),
            ]),

            Forms\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->nullable()
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->fontFamily('mono')
                    ->copyable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bill_number')
                    ->label("Supplier #")
                    ->placeholder('—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vendor_display')
                    ->label('Vendor')
                    ->getStateUsing(fn (SupplierBill $r) => $r->vendor?->name ?? $r->vendor_name ?? '—'),
                Tables\Columns\TextColumn::make('total')
                    ->getStateUsing(fn (SupplierBill $r) => $r->formatTotal()),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'paid'     => 'success',
                        'approved' => 'info',
                        'received' => 'warning',
                        'overdue'  => 'danger',
                        'disputed' => 'danger',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label('Issued')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable()
                    ->color(fn (SupplierBill $r) => $r->isOverdue() ? 'danger' : null),
            ])
            ->defaultSort('issue_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(SupplierBill::statusOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (SupplierBill $r) => $r->status !== 'received')
                    ->action(fn (SupplierBill $r) => $r->update(['status' => 'approved'])),
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-banknotes')
                    ->color('info')
                    ->requiresConfirmation()
                    ->hidden(fn (SupplierBill $r) => $r->status !== 'approved')
                    ->action(fn (SupplierBill $r) => $r->update(['status' => 'paid', 'paid_at' => now()])),
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
            Infolists\Components\Section::make('Bill')->columns(3)->schema([
                Infolists\Components\TextEntry::make('reference')
                    ->fontFamily('mono')
                    ->copyable(),
                Infolists\Components\TextEntry::make('bill_number')
                    ->label("Supplier Invoice #")
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'paid'     => 'success',
                        'approved' => 'info',
                        'received' => 'warning',
                        'overdue'  => 'danger',
                        'disputed' => 'danger',
                        default    => 'gray',
                    }),
                Infolists\Components\TextEntry::make('vendor.name')
                    ->label('Vendor')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('vendor_name')
                    ->label('Manual Vendor')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('purchaseOrder.reference')
                    ->label('Linked PO')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('issue_date')
                    ->date('d M Y'),
                Infolists\Components\TextEntry::make('due_date')
                    ->date('d M Y')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('paid_at')
                    ->label('Paid On')
                    ->date('d M Y')
                    ->placeholder('—'),
            ]),

            Infolists\Components\Section::make('Totals')->columns(3)->schema([
                Infolists\Components\TextEntry::make('subtotal')
                    ->getStateUsing(fn ($r) => $r->currency . ' ' . number_format((float) $r->subtotal, 0)),
                Infolists\Components\TextEntry::make('tax_amount')
                    ->getStateUsing(fn ($r) => $r->currency . ' ' . number_format((float) $r->tax_amount, 0)),
                Infolists\Components\TextEntry::make('total')
                    ->getStateUsing(fn ($r) => $r->formatTotal())
                    ->weight('bold')
                    ->size('lg'),
            ]),

            Infolists\Components\Section::make('Line Items')->schema([
                Infolists\Components\RepeatableEntry::make('items')->schema([
                    Infolists\Components\TextEntry::make('description'),
                    Infolists\Components\TextEntry::make('quantity'),
                    Infolists\Components\TextEntry::make('unit_price')
                        ->getStateUsing(fn ($r) => number_format((float) $r->unit_price, 0)),
                    Infolists\Components\TextEntry::make('total')
                        ->getStateUsing(fn ($r) => number_format((float) $r->total, 0)),
                ])->columns(4)->columnSpanFull(),
            ]),

            Infolists\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Infolists\Components\TextEntry::make('notes')
                    ->placeholder('—')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'bill_number', 'vendor_name'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSupplierBills::route('/'),
            'create' => Pages\CreateSupplierBill::route('/create'),
            'view'   => Pages\ViewSupplierBill::route('/{record}'),
            'edit'   => Pages\EditSupplierBill::route('/{record}/edit'),
        ];
    }
}
