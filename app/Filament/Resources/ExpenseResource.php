<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Expenses';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 50;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Expense Details')->columns(2)->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(200)->columnSpanFull(),

                Forms\Components\Select::make('category')
                    ->options(Expense::categoryOptions())
                    ->required(),

                Forms\Components\DatePicker::make('expense_date')
                    ->label('Date')->required()->default(now()),

                Forms\Components\TextInput::make('amount')
                    ->numeric()->required()->minValue(0),

                Forms\Components\Select::make('currency')
                    ->options(['XAF' => 'XAF', 'USD' => 'USD', 'EUR' => 'EUR'])
                    ->default('XAF')->required(),

                Forms\Components\TextInput::make('vendor')
                    ->label('Vendor / Payee')->maxLength(150)->nullable(),

                Forms\Components\FileUpload::make('receipt_path')
                    ->label('Receipt')->directory('expense-receipts')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->nullable(),
            ]),

            Forms\Components\Section::make('Approval')->columns(2)->schema([
                Forms\Components\Select::make('status')
                    ->options(Expense::statusOptions())
                    ->default('pending')->required(),

                Forms\Components\Select::make('submitted_by')
                    ->label('Submitted By')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()->required(),

                Forms\Components\Select::make('approved_by')
                    ->label('Approved By')
                    ->options(fn () => User::whereHas('roles', fn ($q) =>
                        $q->whereIn('name', ['super_admin', 'admin'])
                    )->orderBy('name')->pluck('name', 'id'))
                    ->searchable()->nullable(),

                Forms\Components\DateTimePicker::make('approved_at')
                    ->label('Approved At')->nullable(),
            ]),

            Forms\Components\Section::make('Notes')->schema([
                Forms\Components\Textarea::make('description')->rows(3)->nullable()->columnSpanFull(),
                Forms\Components\Textarea::make('notes')->label('Internal Notes')->rows(2)->nullable()->columnSpanFull(),
            ])->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref')->fontFamily('mono')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()->limit(40)->weight('semibold'),

                Tables\Columns\TextColumn::make('category')
                    ->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => Expense::categoryOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('amount')
                    ->getStateUsing(fn ($record) => $record->formatAmount())
                    ->sortable(),

                Tables\Columns\TextColumn::make('vendor')->placeholder('—')->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'approved' => 'success',
                        'paid'     => 'success',
                        'pending'  => 'warning',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('submitter.name')
                    ->label('Submitted By')->sortable(),

                Tables\Columns\TextColumn::make('expense_date')
                    ->label('Date')->date('d M Y')->sortable(),
            ])
            ->defaultSort('expense_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Expense::statusOptions()),
                Tables\Filters\SelectFilter::make('category')
                    ->options(Expense::categoryOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (Expense $record) => $record->status !== 'pending')
                    ->action(fn (Expense $record) => $record->update([
                        'status'      => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ])),
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-banknotes')
                    ->color('info')
                    ->requiresConfirmation()
                    ->hidden(fn (Expense $record) => $record->status !== 'approved')
                    ->action(fn (Expense $record) => $record->update(['status' => 'paid'])),
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
            Infolists\Components\Section::make('Expense')->columns(3)->schema([
                Infolists\Components\TextEntry::make('reference')->fontFamily('mono')->copyable(),
                Infolists\Components\TextEntry::make('title')->columnSpan(2),
                Infolists\Components\TextEntry::make('category')
                    ->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => Expense::categoryOptions()[$state] ?? $state),
                Infolists\Components\TextEntry::make('expense_date')->label('Date')->date('d M Y'),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'approved', 'paid' => 'success',
                        'pending'          => 'warning',
                        'rejected'         => 'danger',
                        default            => 'gray',
                    }),
            ]),

            Infolists\Components\Section::make('Amount & Vendor')->columns(3)->schema([
                Infolists\Components\TextEntry::make('amount')
                    ->getStateUsing(fn ($record) => $record->formatAmount())
                    ->weight('bold')->size('lg'),
                Infolists\Components\TextEntry::make('vendor')->placeholder('—'),
                Infolists\Components\TextEntry::make('submitter.name')->label('Submitted By'),
            ]),

            Infolists\Components\Section::make('Approval')->columns(3)->schema([
                Infolists\Components\TextEntry::make('approver.name')->label('Approved By')->placeholder('—'),
                Infolists\Components\TextEntry::make('approved_at')
                    ->label('Approved At')->dateTime('d M Y H:i')->placeholder('—'),
            ]),

            Infolists\Components\Section::make('Notes')
                ->collapsed()->collapsible()
                ->schema([
                    Infolists\Components\TextEntry::make('description')->label('Description')->placeholder('—')->columnSpanFull(),
                    Infolists\Components\TextEntry::make('notes')->label('Internal Notes')->placeholder('—')->columnSpanFull(),
                ]),
        ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', 'pending')->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'title', 'vendor'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'view'   => Pages\ViewExpense::route('/{record}'),
            'edit'   => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
