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
            TextInput::make('reference_number')->disabled()->dehydrated(false),
            Select::make('customer_id')
                ->relationship('customer', 'name')
                ->searchable()
                ->disabled()
                ->dehydrated(false)
                ->label('Customer'),
            Select::make('type')->options(ServiceRequest::typeOptions()),
            TextInput::make('product_slug')->label('Product'),
            Textarea::make('description')->rows(3),
            DatePicker::make('preferred_date'),
            TextInput::make('preferred_time')->label('Preferred Time (HH:MM)'),
            TextInput::make('location')->maxLength(200),
            Select::make('status')->options(ServiceRequest::statusOptions())->disabled()->dehydrated(false),
            Select::make('assigned_technician_id')
                ->options(fn () => \App\Contexts\Shared\Infrastructure\AdminReferenceOptions::options('admin.references.users', ['role' => 'admin_support']))
                ->label('Assigned Technician')
                ->disabled()
                ->dehydrated(false)
                ->nullable(),
            DatePicker::make('confirmed_date')->disabled()->dehydrated(false),
            TextInput::make('confirmed_time')->label('Confirmed Time (HH:MM)')->disabled()->dehydrated(false),
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
                Tables\Actions\EditAction::make()->using(fn (ServiceRequest $record, array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.support.serviceRequests.update', $data, ['serviceRequest' => $record->id])),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel Request')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ServiceRequest $record) => in_array($record->status, ['pending', 'confirmed'], true))
                    ->requiresConfirmation()
                    ->action(fn (ServiceRequest $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run(
                        'admin.support.serviceRequests.cancel', parameters: ['serviceRequest' => $record],
                    )),
                Tables\Actions\Action::make('confirm_assign')
                    ->label('Confirm & Assign')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (ServiceRequest $record) => in_array($record->status, ['pending', 'confirmed']))
                    ->form([
                        Select::make('assigned_technician_id')
                            ->options(fn () => \App\Contexts\Shared\Infrastructure\AdminReferenceOptions::options('admin.references.users', ['role' => 'admin_support']))
                            ->label('Assign Technician')
                            ->required(),
                        DatePicker::make('confirmed_date')->required(),
                        TextInput::make('confirmed_time')->label('Confirmed Time (HH:MM)'),
                        Textarea::make('admin_notes')->rows(3)->label('Notes (optional)'),
                    ])
                    ->action(function (ServiceRequest $record, array $data) {
                        app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
                            'admin.support.serviceRequests.confirm',
                            new \App\Contexts\Shared\Application\WorkflowInput($data, ['serviceRequest' => $record], auth()->user(), app()->getLocale()),
                        );

                        Notification::make()->title('Service request confirmed.')->success()->send();
                    }),
                Tables\Actions\Action::make('complete')
                    ->label('Mark Completed')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (ServiceRequest $record) => $record->status === 'assigned')
                    ->requiresConfirmation()
                    ->action(fn (ServiceRequest $record) => app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute(
                        'admin.support.serviceRequests.complete',
                        new \App\Contexts\Shared\Application\WorkflowInput([], ['serviceRequest' => $record], auth()->user(), app()->getLocale()),
                    )),
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return app(\App\Contexts\Support\Application\Queries\AdminServiceRequestQueries::class)->query(
            new \App\Contexts\Shared\Application\WorkflowInput(actor: auth()->user(), language: app()->getLocale())
        );
    }
}
