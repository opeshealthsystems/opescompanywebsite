<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Support';
    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'support']) ?? false;
    }

    protected static function staffOptions(): array
    {
        return User::whereHas('roles', fn ($q) =>
            $q->whereIn('name', ['super_admin', 'admin', 'support'])
        )->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Ticket Details')->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Customer')
                    ->options(fn () => User::role('customer')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                Forms\Components\Select::make('assigned_to')
                    ->label('Assigned To')
                    ->options(fn () => static::staffOptions())
                    ->searchable()
                    ->nullable(),

                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Select::make('type')
                    ->options(Ticket::typeOptions())
                    ->default('support')
                    ->required(),

                Forms\Components\Select::make('priority')
                    ->options(Ticket::priorityOptions())
                    ->default('medium')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options(Ticket::statusOptions())
                    ->default('open')
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('resolution')
                    ->rows(3)
                    ->columnSpanFull()
                    ->nullable(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Ref')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Ticket::typeLabel($state))
                    ->color(fn ($state) => match ($state) {
                        'billing'    => 'warning',
                        'technical'  => 'info',
                        'bug_report' => 'danger',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        'low'    => 'gray',
                        default  => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'open'             => 'danger',
                        'in_progress'      => 'warning',
                        'pending_customer' => 'info',
                        'resolved'         => 'success',
                        'closed'           => 'gray',
                        default            => 'gray',
                    }),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Opened')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Ticket::statusOptions()),
                Tables\Filters\SelectFilter::make('type')
                    ->options(Ticket::typeOptions()),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(Ticket::priorityOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            Infolists\Components\Section::make('Ticket')->schema([
                Infolists\Components\TextEntry::make('reference_number')->label('Reference')->fontFamily('mono'),
                Infolists\Components\TextEntry::make('customer.name')->label('Customer'),
                Infolists\Components\TextEntry::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Ticket::typeLabel($state))
                    ->color(fn ($state) => match ($state) {
                        'billing'    => 'warning',
                        'technical'  => 'info',
                        'bug_report' => 'danger',
                        default      => 'gray',
                    }),
                Infolists\Components\TextEntry::make('priority')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'urgent' => 'danger', 'high' => 'warning',
                        'medium' => 'info', 'low' => 'gray', default => 'gray',
                    }),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'open' => 'danger', 'in_progress' => 'warning',
                        'pending_customer' => 'info', 'resolved' => 'success',
                        'closed' => 'gray', default => 'gray',
                    }),
                Infolists\Components\TextEntry::make('assignee.name')->label('Assigned To')->placeholder('Unassigned'),
                Infolists\Components\TextEntry::make('subject')->columnSpanFull(),
                Infolists\Components\TextEntry::make('description')->columnSpanFull(),
                Infolists\Components\TextEntry::make('resolution')->placeholder('No resolution noted')->columnSpanFull(),
            ])->columns(3),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view'   => Pages\ViewTicket::route('/{record}'),
        ];
    }
}
