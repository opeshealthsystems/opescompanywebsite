<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditNoteResource\Pages;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CreditNoteResource extends Resource
{
    protected static ?string $model = CreditNote::class;
    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
    protected static ?string $navigationLabel = 'Credit Notes';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 52;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Credit Note')->columns(2)->schema([
                Forms\Components\TextInput::make('reference')
                    ->disabled()
                    ->placeholder('Auto-generated'),

                Forms\Components\Select::make('status')
                    ->options(CreditNote::statusOptions())
                    ->default('draft')
                    ->required(),

                Forms\Components\Select::make('invoice_id')
                    ->label('Original Invoice')
                    ->options(fn () => Invoice::orderByDesc('created_at')->get()->mapWithKeys(
                        fn ($inv) => [
                            $inv->id => ($inv->invoice_number ?? $inv->id)
                                . ' — '
                                . ($inv->currency ?? 'XAF')
                                . ' '
                                . number_format((float) $inv->grand_total, 0),
                        ]
                    ))
                    ->searchable()
                    ->nullable()
                    ->placeholder('No invoice linked'),

                Forms\Components\Select::make('created_by')
                    ->label('Issued By')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('reason')
                    ->required()
                    ->maxLength(300)
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('issued_at')
                    ->label('Issue Date')
                    ->native(false)
                    ->nullable(),

                Forms\Components\Select::make('currency')
                    ->options(['XAF' => 'XAF', 'USD' => 'USD', 'EUR' => 'EUR'])
                    ->default('XAF'),
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

            Forms\Components\Section::make('Notes')
                ->collapsible()
                ->collapsed()
                ->schema([
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

                Tables\Columns\TextColumn::make('reason')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('Invoice')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('total')
                    ->getStateUsing(fn (CreditNote $record) => $record->formatTotal()),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'issued'  => 'success',
                        'applied' => 'info',
                        'draft'   => 'gray',
                        'void'    => 'danger',
                        default   => 'gray',
                    }),

                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Issued')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(CreditNote::statusOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('issue')
                    ->label('Issue')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (CreditNote $record) => $record->status !== 'draft')
                    ->action(fn (CreditNote $record) => $record->update([
                        'status'    => 'issued',
                        'issued_at' => $record->issued_at ?? now(),
                    ])),
                Tables\Actions\Action::make('void')
                    ->label('Void')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->hidden(fn (CreditNote $record) => ! in_array($record->status, ['draft', 'issued']))
                    ->action(fn (CreditNote $record) => $record->update(['status' => 'void'])),
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
            Infolists\Components\Section::make('Credit Note')->columns(3)->schema([
                Infolists\Components\TextEntry::make('reference')
                    ->fontFamily('mono')
                    ->copyable(),
                Infolists\Components\TextEntry::make('reason')
                    ->columnSpan(2),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'issued'  => 'success',
                        'applied' => 'info',
                        'draft'   => 'gray',
                        'void'    => 'danger',
                        default   => 'gray',
                    }),
                Infolists\Components\TextEntry::make('invoice.invoice_number')
                    ->label('Original Invoice')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('issued_at')
                    ->date('d M Y')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('creator.name')
                    ->label('Issued By'),
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

            Infolists\Components\Section::make('Totals')->columns(3)->schema([
                Infolists\Components\TextEntry::make('subtotal')
                    ->getStateUsing(fn ($record) => $record->currency . ' ' . number_format((float) $record->subtotal, 0)),
                Infolists\Components\TextEntry::make('tax_amount')
                    ->getStateUsing(fn ($record) => $record->currency . ' ' . number_format((float) $record->tax_amount, 0)),
                Infolists\Components\TextEntry::make('total')
                    ->getStateUsing(fn ($record) => $record->formatTotal())
                    ->weight('bold')
                    ->size('lg'),
            ]),

            Infolists\Components\Section::make('Notes')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Infolists\Components\TextEntry::make('notes')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'reason'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCreditNotes::route('/'),
            'create' => Pages\CreateCreditNote::route('/create'),
            'view'   => Pages\ViewCreditNote::route('/{record}'),
            'edit'   => Pages\EditCreditNote::route('/{record}/edit'),
        ];
    }
}
