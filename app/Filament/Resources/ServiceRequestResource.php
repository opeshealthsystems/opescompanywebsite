<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceRequestResource\Pages;
use App\Models\ServiceRequest;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Support';
    protected static ?int $navigationSort = 45;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'support']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('reference_number')->disabled(),
            Select::make('customer_id')
                ->relationship('customer', 'name')
                ->searchable()
                ->label('Customer'),
            Select::make('type')->options(ServiceRequest::typeOptions()),
            TextInput::make('product_slug')->label('Product'),
            Textarea::make('description')->rows(3),
            DatePicker::make('preferred_date'),
            TextInput::make('preferred_time')->label('Preferred Time (HH:MM)'),
            TextInput::make('location')->maxLength(200),
            Select::make('status')->options(ServiceRequest::statusOptions()),
            Select::make('assigned_technician_id')
                ->options(fn () => User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'support']))->pluck('name', 'id'))
                ->label('Assigned Technician')
                ->nullable(),
            DatePicker::make('confirmed_date'),
            TextInput::make('confirmed_time')->label('Confirmed Time (HH:MM)'),
            Textarea::make('admin_notes')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')
                    ->label('Reference')
                    ->fontFamily('mono')
                    ->searchable(),
                TextColumn::make('customer.name')->label('Customer')->searchable()->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ServiceRequest::typeOptions()[$state] ?? $state),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'   => 'warning',
                        'confirmed' => 'info',
                        'assigned'  => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ServiceRequest::statusOptions()[$state] ?? $state),
                TextColumn::make('preferred_date')->date()->sortable(),
                TextColumn::make('assignedTechnician.name')->label('Technician')->default('—'),
                TextColumn::make('confirmed_date')->date(),
            ])
            ->filters([
                SelectFilter::make('status')->options(ServiceRequest::statusOptions()),
                SelectFilter::make('type')->options(ServiceRequest::typeOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('confirm_assign')
                    ->label('Confirm & Assign')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (ServiceRequest $record) => in_array($record->status, ['pending', 'confirmed']))
                    ->form([
                        Select::make('assigned_technician_id')
                            ->options(fn () => User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'support']))->pluck('name', 'id'))
                            ->label('Assign Technician')
                            ->required(),
                        DatePicker::make('confirmed_date')->required(),
                        TextInput::make('confirmed_time')->label('Confirmed Time (HH:MM)'),
                        Textarea::make('admin_notes')->rows(3)->label('Notes (optional)'),
                    ])
                    ->action(function (ServiceRequest $record, array $data) {
                        $record->update([
                            'status'                 => 'confirmed',
                            'assigned_technician_id' => $data['assigned_technician_id'],
                            'confirmed_date'         => $data['confirmed_date'],
                            'confirmed_time'         => $data['confirmed_time'] ?? null,
                            'admin_notes'            => $data['admin_notes'] ?? null,
                        ]);

                        if (class_exists(\App\Mail\ServiceRequestConfirmed::class)) {
                            \Illuminate\Support\Facades\Mail::to($record->customer->email)
                                ->queue(new \App\Mail\ServiceRequestConfirmed($record));
                        }

                        Notification::make()->title('Service request confirmed.')->success()->send();
                    }),
                Tables\Actions\Action::make('complete')
                    ->label('Mark Completed')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (ServiceRequest $record) => $record->status === 'assigned')
                    ->requiresConfirmation()
                    ->action(fn (ServiceRequest $record) => $record->update(['status' => 'completed'])),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceRequests::route('/'),
            'view'  => Pages\ViewServiceRequest::route('/{record}'),
            'edit'  => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }
}
