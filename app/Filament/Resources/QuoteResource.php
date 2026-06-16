<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Lead;
use App\Models\Quote;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Quotes';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', ['draft', 'sent'])->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Quote')->columns(2)->schema([
                Forms\Components\TextInput::make('reference')->disabled()->placeholder('Auto-generated'),
                Forms\Components\Select::make('status')->options(Quote::statusOptions())->default('draft')->required(),
                Forms\Components\TextInput::make('title')->required()->maxLength(250)->columnSpanFull(),

                Forms\Components\Select::make('lead_id')
                    ->label('Lead')
                    ->options(fn () => Lead::orderBy('name')->pluck('name', 'id'))
                    ->searchable()->nullable()->placeholder('No lead linked'),

                Forms\Components\Select::make('created_by')
                    ->label('Created By')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()->required(),

                Forms\Components\DatePicker::make('valid_until')->label('Valid Until')->native(false)->nullable(),
                Forms\Components\Select::make('currency')->options(['XAF' => 'XAF', 'USD' => 'USD', 'EUR' => 'EUR'])->default('XAF'),
            ]),

            Forms\Components\Section::make('Line Items')->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship('items')
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('product_name')->required()->label('Product/Service')->columnSpan(2),
                        Forms\Components\TextInput::make('quantity')->numeric()->default(1)->required(),
                        Forms\Components\TextInput::make('unit_price')->numeric()->required()->label('Unit Price'),
                    ])
                    ->addActionLabel('Add Item')
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Totals')->columns(3)->schema([
                Forms\Components\TextInput::make('subtotal')->numeric()->default(0)->disabled(),
                Forms\Components\TextInput::make('tax_rate')->label('Tax Rate (%)')->numeric()->default(0)->suffix('%'),
                Forms\Components\TextInput::make('total')->numeric()->default(0)->disabled(),
            ]),

            Forms\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Forms\Components\Textarea::make('notes')->rows(3)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->fontFamily('mono')->copyable()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->weight('semibold'),
                Tables\Columns\TextColumn::make('lead.name')->label('Lead')->placeholder('—'),
                Tables\Columns\TextColumn::make('total')
                    ->getStateUsing(fn (Quote $record) => $record->formatTotal()),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match ($state) {
                        'accepted' => 'success',
                        'draft'    => 'gray',
                        'sent'     => 'info',
                        'rejected' => 'danger',
                        'expired'  => 'warning',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('valid_until')->label('Valid Until')->date('d M Y')->placeholder('—')->sortable(),
                Tables\Columns\TextColumn::make('creator.name')->label('Created By')->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Quote::statusOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_sent')
                    ->label('Mark Sent')->icon('heroicon-o-paper-airplane')->color('info')
                    ->requiresConfirmation()
                    ->hidden(fn (Quote $record) => $record->status !== 'draft')
                    ->action(fn (Quote $record) => $record->update(['status' => 'sent'])),
                Tables\Actions\Action::make('accept')
                    ->label('Accept')->icon('heroicon-o-check-circle')->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (Quote $record) => $record->status !== 'sent')
                    ->action(fn (Quote $record) => $record->update(['status' => 'accepted'])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Quote')->columns(3)->schema([
                Infolists\Components\TextEntry::make('reference')->fontFamily('mono')->copyable(),
                Infolists\Components\TextEntry::make('title')->columnSpan(2),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->color(fn ($state) => match ($state) {
                        'accepted' => 'success',
                        'draft'    => 'gray',
                        'sent'     => 'info',
                        'rejected' => 'danger',
                        'expired'  => 'warning',
                        default    => 'gray',
                    }),
                Infolists\Components\TextEntry::make('lead.name')->label('Lead')->placeholder('—'),
                Infolists\Components\TextEntry::make('valid_until')->date('d M Y')->placeholder('—'),
                Infolists\Components\TextEntry::make('creator.name')->label('Created By'),
            ]),
            Infolists\Components\Section::make('Line Items')->schema([
                Infolists\Components\RepeatableEntry::make('items')
                    ->schema([
                        Infolists\Components\TextEntry::make('product_name')->label('Product/Service'),
                        Infolists\Components\TextEntry::make('quantity'),
                        Infolists\Components\TextEntry::make('unit_price')->getStateUsing(fn ($record) => number_format((float) $record->unit_price, 0)),
                        Infolists\Components\TextEntry::make('total')->getStateUsing(fn ($record) => number_format((float) $record->total, 0)),
                    ])->columns(4)->columnSpanFull(),
            ]),
            Infolists\Components\Section::make('Totals')->columns(4)->schema([
                Infolists\Components\TextEntry::make('subtotal')
                    ->getStateUsing(fn ($record) => $record->currency . ' ' . number_format((float) $record->subtotal, 0)),
                Infolists\Components\TextEntry::make('tax_rate')->suffix('%'),
                Infolists\Components\TextEntry::make('tax_amount')
                    ->getStateUsing(fn ($record) => $record->currency . ' ' . number_format((float) $record->tax_amount, 0)),
                Infolists\Components\TextEntry::make('total')
                    ->getStateUsing(fn ($record) => $record->formatTotal())->weight('bold')->size('lg'),
            ]),
            Infolists\Components\Section::make('Notes')->collapsible()->collapsed()->schema([
                Infolists\Components\TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'title'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'view'   => Pages\ViewQuote::route('/{record}'),
            'edit'   => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
