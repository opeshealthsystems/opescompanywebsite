<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveTypeResource\Pages;
use App\Models\LeaveType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveTypeResource extends Resource
{
    protected static ?string $model = LeaveType::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Leave Types';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 8;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Leave Type')->columns(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(30)
                    ->unique(ignoreRecord: true)
                    ->helperText('Short identifier, e.g. annual, sick'),

                Forms\Components\TextInput::make('days_per_year')
                    ->label('Days/Year (0=unlimited)')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Forms\Components\TextInput::make('max_carry_forward')
                    ->label('Max Carry Forward (days)')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Forms\Components\ColorPicker::make('color')
                    ->default('#6366f1'),

                Forms\Components\Toggle::make('is_paid')
                    ->label('Paid Leave')
                    ->default(true),

                Forms\Components\Toggle::make('requires_approval')
                    ->label('Requires Approval')
                    ->default(true),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label(''),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('semibold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('days_per_year')
                    ->label('Days/Year')
                    ->formatStateUsing(fn ($state) => $state === 0 ? 'Unlimited' : $state)
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('max_carry_forward')
                    ->label('Carry Fwd')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Paid')
                    ->boolean(),

                Tables\Columns\IconColumn::make('requires_approval')
                    ->label('Needs Approval')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (LeaveType $record, Tables\Actions\DeleteAction $action) {
                        if ($record->leaveRequests()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Cannot delete: has existing leave requests')
                                ->danger()
                                ->send();
                            $action->cancel();
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
            Infolists\Components\Section::make()->columns(4)->schema([
                Infolists\Components\ColorEntry::make('color')
                    ->label(''),

                Infolists\Components\TextEntry::make('name')
                    ->weight('semibold'),

                Infolists\Components\TextEntry::make('code')
                    ->badge()
                    ->color('gray'),

                Infolists\Components\TextEntry::make('days_per_year')
                    ->label('Days/Year')
                    ->formatStateUsing(fn ($state) => $state === 0 ? 'Unlimited' : $state),

                Infolists\Components\IconEntry::make('is_paid')
                    ->label('Paid')
                    ->boolean(),

                Infolists\Components\IconEntry::make('requires_approval')
                    ->label('Needs Approval')
                    ->boolean(),

                Infolists\Components\IconEntry::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeaveTypes::route('/'),
            'create' => Pages\CreateLeaveType::route('/create'),
            'view'   => Pages\ViewLeaveType::route('/{record}'),
            'edit'   => Pages\EditLeaveType::route('/{record}/edit'),
        ];
    }
}
