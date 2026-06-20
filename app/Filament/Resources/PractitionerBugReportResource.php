<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PractitionerBugReportResource\Pages;
use App\Models\PractitionerBugReport;
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

class PractitionerBugReportResource extends Resource
{
    protected static ?string $model = PractitionerBugReport::class;
    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';
    protected static ?string $navigationGroup = 'Support';
    protected static ?int $navigationSort = 42;
    protected static ?string $navigationLabel = 'Bug Reports';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'support']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->disabled(),
            Select::make('severity')->options(PractitionerBugReport::severityOptions())->disabled(),
            TextInput::make('product_slug')->label('Product')->disabled(),
            Textarea::make('description')->disabled()->rows(4),
            Textarea::make('steps_to_reproduce')->label('Steps to Reproduce')->disabled()->rows(4),
            TextInput::make('screenshot_url')->label('Screenshot URL')->disabled(),
            Select::make('status')->options(PractitionerBugReport::statusOptions())->disabled(),
            Textarea::make('admin_response')->rows(3)->label('Admin Response')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('practitioner.name')->label('Reported By')->searchable()->sortable(),
                TextColumn::make('title')->limit(50)->searchable(),
                TextColumn::make('severity')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'critical' => 'danger',
                        'high'     => 'warning',
                        'medium'   => 'info',
                        'low'      => 'gray',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => PractitionerBugReport::severityOptions()[$state] ?? $state),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'open'        => 'warning',
                        'triaged'     => 'info',
                        'in_progress' => 'info',
                        'resolved'    => 'success',
                        'closed'      => 'gray',
                        'wont_fix'    => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => PractitionerBugReport::statusOptions()[$state] ?? $state),
                Tables\Columns\ImageColumn::make('screenshot_path')
                    ->label('Screenshot')
                    ->disk('public')
                    ->square()
                    ->toggleable(),
                TextColumn::make('created_at')->label('Reported')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(PractitionerBugReport::statusOptions()),
                SelectFilter::make('severity')->options(PractitionerBugReport::severityOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('respond')
                    ->label('Respond')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->visible(fn (PractitionerBugReport $record) => ! in_array($record->status, ['resolved', 'closed', 'wont_fix']))
                    ->form([
                        Select::make('status')
                            ->options([
                                'triaged'     => 'Triaged',
                                'in_progress' => 'In Progress',
                                'resolved'    => 'Resolved',
                                'closed'      => 'Closed',
                                'wont_fix'    => "Won't Fix",
                            ])
                            ->required(),
                        Textarea::make('admin_response')->required()->rows(4)->label('Response'),
                    ])
                    ->action(function (PractitionerBugReport $record, array $data) {
                        $record->update([
                            'status'         => $data['status'],
                            'admin_response' => $data['admin_response'],
                            'responded_by'   => auth()->id(),
                            'responded_at'   => now(),
                        ]);

                        if (class_exists(\App\Mail\BugReportResponded::class)) {
                            \Illuminate\Support\Facades\Mail::to($record->practitioner->email)
                                ->queue(new \App\Mail\BugReportResponded($record));
                        }
                        $record->practitioner?->notify(new \App\Notifications\FeedEntry(
                            'practitioner.bug_report_responded',
                            'Response to your bug report',
                            'Your bug report received a response.',
                            'bug-ant',
                            route('practitioner.bug-reports.show', ['locale' => 'en', 'bugReport' => $record->id]),
                        ));

                        Notification::make()->title('Response sent.')->success()->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPractitionerBugReports::route('/'),
            'view'  => Pages\ViewPractitionerBugReport::route('/{record}'),
        ];
    }
}
