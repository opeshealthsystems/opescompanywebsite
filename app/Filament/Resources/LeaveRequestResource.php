<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveRequestResource\Pages;
use App\Models\LeaveRequest;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Leave Requests';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Leave Request')->columns(2)->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Employee')
                    ->options(fn () => User::whereHas('roles', fn ($q) =>
                        $q->whereIn('name', ['super_admin', 'admin', 'support', 'staff', 'employee'])
                    )->orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('leave_type_id')
                    ->label('Leave Type')
                    ->options(fn () => \App\Models\LeaveType::activeOptions())
                    ->searchable()
                    ->nullable()
                    ->placeholder('Select configured type'),

                Forms\Components\Select::make('type')
                    ->options(LeaveRequest::typeOptions())
                    ->required(),

                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->native(false)
                    ->afterOrEqual('start_date'),

                Forms\Components\TextInput::make('total_days')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->helperText('Working days'),

                Forms\Components\Textarea::make('reason')
                    ->rows(3)
                    ->nullable()
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Status')
                ->columns(2)
                ->hidden(fn () => ! auth()->user()?->hasAnyRole(['super_admin', 'admin']))
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options(LeaveRequest::statusOptions())
                        ->default('pending'),

                    Forms\Components\Select::make('approved_by')
                        ->label('Approved By')
                        ->options(fn () => User::whereHas('roles', fn ($q) =>
                            $q->whereIn('name', ['super_admin', 'admin'])
                        )->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),

                    Forms\Components\DateTimePicker::make('approved_at')
                        ->nullable(),

                    Forms\Components\Textarea::make('notes')
                        ->label('Admin Notes')
                        ->rows(2)
                        ->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => LeaveRequest::typeOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('leaveType.name')
                    ->label('Type Config')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_days')
                    ->label('Days')
                    ->suffix(' days'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'   => 'warning',
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Approved By')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(LeaveRequest::statusOptions()),
                Tables\Filters\SelectFilter::make('type')
                    ->options(LeaveRequest::typeOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'admin', 'hr', 'manager'])),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (LeaveRequest $record) => $record->status !== 'pending')
                    ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'admin']))
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status'      => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        $record->deductFromBalance();
                        \Filament\Notifications\Notification::make()
                            ->title('Leave request approved')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->hidden(fn (LeaveRequest $record) => $record->status !== 'pending')
                    ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'admin']))
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status'      => 'rejected',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Leave request rejected')
                            ->danger()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status'      => 'approved',
                                        'approved_by' => auth()->id(),
                                        'approved_at' => now(),
                                    ]);
                                }
                            });
                            \Filament\Notifications\Notification::make()
                                ->title('Selected leave requests approved')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status'      => 'rejected',
                                        'approved_by' => auth()->id(),
                                        'approved_at' => now(),
                                    ]);
                                }
                            });
                            \Filament\Notifications\Notification::make()
                                ->title('Selected leave requests rejected')
                                ->danger()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'admin'])),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Leave Request')->columns(3)->schema([
                Infolists\Components\TextEntry::make('employee.name')
                    ->label('Employee'),

                Infolists\Components\TextEntry::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => LeaveRequest::typeOptions()[$state] ?? $state),

                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'   => 'warning',
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    }),
            ]),

            Infolists\Components\Section::make('Dates')->columns(3)->schema([
                Infolists\Components\TextEntry::make('start_date')
                    ->date('d M Y'),

                Infolists\Components\TextEntry::make('end_date')
                    ->date('d M Y'),

                Infolists\Components\TextEntry::make('total_days')
                    ->suffix(' days'),
            ]),

            Infolists\Components\Section::make('Reason')
                ->collapsible()
                ->schema([
                    Infolists\Components\TextEntry::make('reason')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Approval')->columns(3)->schema([
                Infolists\Components\TextEntry::make('approver.name')
                    ->label('Approved By')
                    ->placeholder('—'),

                Infolists\Components\TextEntry::make('approved_at')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—'),

                Infolists\Components\TextEntry::make('notes')
                    ->label('Admin Notes')
                    ->placeholder('—'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'view'   => Pages\ViewLeaveRequest::route('/{record}'),
            'edit'   => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
