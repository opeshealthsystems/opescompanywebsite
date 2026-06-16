<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuggestionResource\Pages;
use App\Models\Suggestion;
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

class SuggestionResource extends Resource
{
    protected static ?string $model = Suggestion::class;
    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';
    protected static ?string $navigationGroup = 'Support';
    protected static ?int $navigationSort = 40;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->disabled(),
            Select::make('category')->options(Suggestion::categoryOptions())->disabled(),
            Textarea::make('body')->disabled()->rows(4),
            Select::make('status')->options(Suggestion::statusOptions()),
            Textarea::make('admin_response')->rows(3)->label('Admin Response'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Submitted By')->searchable()->sortable(),
                TextColumn::make('title')->limit(50)->searchable(),
                TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Suggestion::categoryOptions()[$state] ?? $state),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'      => 'warning',
                        'under_review' => 'info',
                        'accepted'     => 'success',
                        'implemented'  => 'success',
                        'declined'     => 'danger',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => Suggestion::statusOptions()[$state] ?? $state),
                TextColumn::make('created_at')->label('Submitted')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(Suggestion::statusOptions()),
                SelectFilter::make('category')->options(Suggestion::categoryOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('respond')
                    ->label('Respond')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->visible(fn (Suggestion $record) => in_array($record->status, ['pending', 'under_review']))
                    ->form([
                        Select::make('status')
                            ->options([
                                'under_review' => 'Under Review',
                                'accepted'     => 'Accepted',
                                'declined'     => 'Declined',
                                'implemented'  => 'Implemented',
                            ])
                            ->required(),
                        Textarea::make('admin_response')->required()->rows(4)->label('Response'),
                    ])
                    ->action(function (Suggestion $record, array $data) {
                        $record->update([
                            'status'         => $data['status'],
                            'admin_response' => $data['admin_response'],
                            'responded_by'   => auth()->id(),
                            'responded_at'   => now(),
                        ]);

                        if (class_exists(\App\Mail\SuggestionResponded::class)) {
                            \Illuminate\Support\Facades\Mail::to($record->user->email)
                                ->queue(new \App\Mail\SuggestionResponded($record));
                        }

                        Notification::make()->title('Response sent.')->success()->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuggestions::route('/'),
            'view'  => Pages\ViewSuggestion::route('/{record}'),
        ];
    }
}
