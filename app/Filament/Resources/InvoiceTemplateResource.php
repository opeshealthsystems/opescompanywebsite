<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceTemplateResource\Pages;
use App\Models\InvoiceTemplate;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceTemplateResource extends Resource
{
    protected static ?string $model = InvoiceTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Invoice Templates';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 42;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $due = static::getModel()::where('is_active', true)
            ->where('next_due_date', '<=', now())
            ->count();

        return $due > 0 ? (string) $due : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Templates due for generation';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Template')->columns(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(200)
                    ->columnSpanFull(),

                // customer_id links to a User with role 'customer' — mirrors InvoiceResource
                Forms\Components\Select::make('customer_id')
                    ->label('Customer (User Account)')
                    ->options(fn () => User::role('customer')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->placeholder('Select a customer account, or fill manually below'),

                Forms\Components\TextInput::make('client_name')
                    ->label('Client Name (Manual)')
                    ->nullable()
                    ->maxLength(200),

                Forms\Components\TextInput::make('client_email')
                    ->label('Client Email (Manual)')
                    ->email()
                    ->nullable()
                    ->maxLength(200),
            ]),

            Forms\Components\Section::make('Schedule')->columns(2)->schema([
                Forms\Components\Select::make('frequency')
                    ->options(InvoiceTemplate::frequencyOptions())
                    ->default('monthly')
                    ->required(),

                Forms\Components\DatePicker::make('next_due_date')
                    ->required()
                    ->native(false)
                    ->default(now()),

                Forms\Components\DatePicker::make('end_date')
                    ->label('End Date (Optional)')
                    ->nullable()
                    ->native(false),

                Forms\Components\TextInput::make('payment_terms_days')
                    ->label('Payment Terms (Days)')
                    ->numeric()
                    ->default(30)
                    ->minValue(0),

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

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                // issued_by mirrors the Invoice 'issued_by' column
                Forms\Components\Select::make('issued_by')
                    ->label('Issued By')
                    ->options(fn () => User::whereHas('roles', fn ($q) =>
                        $q->whereIn('name', ['super_admin', 'admin'])
                    )->orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->required(),
            ]),

            Forms\Components\Section::make('Line Items')->schema([
                Forms\Components\Repeater::make('line_items')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required(),
                        Forms\Components\TextInput::make('unit_price')
                            ->numeric()
                            ->required()
                            ->label('Unit Price'),
                    ])
                    ->addActionLabel('Add Item')
                    ->defaultItems(1)
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('name')
                    ->weight('semibold')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('client_display')
                    ->label('Client')
                    ->getStateUsing(fn (InvoiceTemplate $r) =>
                        $r->customer?->name ?? $r->client_name ?? '—'
                    ),

                Tables\Columns\TextColumn::make('frequency')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($s) => InvoiceTemplate::frequencyOptions()[$s] ?? $s),

                Tables\Columns\TextColumn::make('next_due_date')
                    ->label('Next Due')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn (InvoiceTemplate $r) => $r->next_due_date->isPast() ? 'danger' : null),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('currency')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Ends')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->defaultSort('next_due_date')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),

                Tables\Filters\Filter::make('due_now')
                    ->label('Due Now')
                    ->query(fn ($q) => $q->where('is_active', true)->where('next_due_date', '<=', now()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('generate_invoice')
                    ->label('Generate Now')
                    ->icon('heroicon-o-bolt')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (InvoiceTemplate $record) {
                        $invoice = $record->generateInvoice();

                        if ($invoice) {
                            Notification::make()
                                ->title('Invoice ' . $invoice->invoice_number . ' created')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Could not generate — template inactive or past end date')
                                ->warning()
                                ->send();
                        }
                    }),

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
            Infolists\Components\Section::make()->columns(3)->schema([
                Infolists\Components\TextEntry::make('name')
                    ->weight('semibold')
                    ->columnSpan(2),

                Infolists\Components\IconEntry::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Infolists\Components\TextEntry::make('frequency')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($s) => InvoiceTemplate::frequencyOptions()[$s] ?? $s),

                Infolists\Components\TextEntry::make('next_due_date')
                    ->date('d M Y')
                    ->label('Next Due'),

                Infolists\Components\TextEntry::make('end_date')
                    ->date('d M Y')
                    ->placeholder('No end')
                    ->label('Ends'),

                Infolists\Components\TextEntry::make('client_display')
                    ->label('Client')
                    ->getStateUsing(fn (InvoiceTemplate $r) =>
                        $r->customer?->name ?? $r->client_name ?? '—'
                    ),

                Infolists\Components\TextEntry::make('client_email')
                    ->label('Client Email')
                    ->placeholder('—'),

                Infolists\Components\TextEntry::make('currency'),

                Infolists\Components\TextEntry::make('tax_rate')
                    ->suffix('%'),

                Infolists\Components\TextEntry::make('payment_terms_days')
                    ->suffix(' days')
                    ->label('Payment Terms'),

                Infolists\Components\TextEntry::make('issuer.name')
                    ->label('Issued By')
                    ->placeholder('—'),
            ]),

            Infolists\Components\Section::make('Line Items')->schema([
                Infolists\Components\RepeatableEntry::make('line_items')
                    ->schema([
                        Infolists\Components\TextEntry::make('description'),
                        Infolists\Components\TextEntry::make('quantity'),
                        Infolists\Components\TextEntry::make('unit_price')
                            ->getStateUsing(fn ($r) => number_format((float) ($r['unit_price'] ?? 0), 0)),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]),

            Infolists\Components\Section::make('Notes')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Infolists\Components\TextEntry::make('notes')
                        ->placeholder('No notes.')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'client_name'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInvoiceTemplates::route('/'),
            'create' => Pages\CreateInvoiceTemplate::route('/create'),
            'view'   => Pages\ViewInvoiceTemplate::route('/{record}'),
            'edit'   => Pages\EditInvoiceTemplate::route('/{record}/edit'),
        ];
    }
}
