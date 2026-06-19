<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeveloperTaskResource\Pages;
use App\Filament\Resources\DeveloperTaskResource\RelationManagers\RetestsRelationManager;
use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\User;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeveloperTaskResource extends Resource
{
    protected static ?string $model = DeveloperTask::class;
    protected static ?string $navigationIcon  = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->weight('semibold'),
                Tables\Columns\TextColumn::make('issueReport.severity')->label('Severity')->badge()
                    ->formatStateUsing(fn ($state) => IssueReport::severityOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('assignedTo.name')->label('Assignee')->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => DeveloperTask::statusOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'open' => 'gray', 'in_progress' => 'info', 'fixed' => 'success',
                        'reopened' => 'warning', 'wont_fix' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('priority')->badge(),
                Tables\Columns\TextColumn::make('retests_count')->counts('retests')->label('Retests'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('assign')
                    ->label('Assign')->icon('heroicon-o-user')->color('gray')
                    ->form([
                        Forms\Components\Select::make('assigned_to')->label('Assignee')
                            ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()->required(),
                    ])
                    ->action(function (DeveloperTask $record, array $data) {
                        $record->update(['assigned_to' => $data['assigned_to']]);
                        Notification::make()->title('Task assigned.')->success()->send();
                    }),
                Tables\Actions\Action::make('start')
                    ->label('Start')->icon('heroicon-o-play')->color('info')
                    ->visible(fn (DeveloperTask $r) => in_array($r->status, ['open', 'reopened']))
                    ->action(function (DeveloperTask $r) {
                        $r->markInProgress();
                        Notification::make()->title('Task in progress.')->success()->send();
                    }),
                Tables\Actions\Action::make('mark_fixed')
                    ->label('Mark Fixed')->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn (DeveloperTask $r) => in_array($r->status, ['in_progress', 'reopened']))
                    ->form([Forms\Components\Textarea::make('resolution_notes')->rows(3)])
                    ->action(function (DeveloperTask $r, array $data) {
                        $r->markFixed($data['resolution_notes'] ?? null);
                        Notification::make()->title('Marked fixed — issue ready for retest.')->success()->send();
                    }),
                Tables\Actions\Action::make('reopen')
                    ->label('Reopen')->icon('heroicon-o-arrow-path')->color('warning')
                    ->visible(fn (DeveloperTask $r) => $r->status === 'fixed'
                        && ! in_array($r->issueReport->status, ['closed', 'retest_passed'], true))
                    ->requiresConfirmation()
                    ->action(function (DeveloperTask $r) {
                        $r->reopen();
                        Notification::make()->title('Task reopened.')->success()->send();
                    }),
                Tables\Actions\Action::make('wont_fix')
                    ->label("Won't Fix")->icon('heroicon-o-no-symbol')->color('danger')
                    ->visible(fn (DeveloperTask $r) => $r->status !== 'wont_fix')
                    ->form([Forms\Components\Textarea::make('notes')->rows(3)])
                    ->action(function (DeveloperTask $r, array $data) {
                        $r->markWontFix($data['notes'] ?? null);
                        Notification::make()->title("Marked won't fix — issue rejected.")->success()->send();
                    }),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Task')->columns(3)->schema([
                Infolists\Components\TextEntry::make('title')->columnSpanFull()->weight('semibold'),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->formatStateUsing(fn ($state) => DeveloperTask::statusOptions()[$state] ?? $state),
                Infolists\Components\TextEntry::make('priority')->badge(),
                Infolists\Components\TextEntry::make('assignedTo.name')->label('Assignee')->placeholder('—'),
                Infolists\Components\TextEntry::make('started_at')->dateTime()->placeholder('—'),
                Infolists\Components\TextEntry::make('fixed_at')->dateTime()->placeholder('—'),
                Infolists\Components\TextEntry::make('resolution_notes')->columnSpanFull()->placeholder('—'),
            ]),
            Infolists\Components\Section::make('Issue')->columns(2)->schema([
                Infolists\Components\TextEntry::make('issueReport.title')->label('Issue')->columnSpanFull(),
                Infolists\Components\TextEntry::make('issueReport.description')->label('Description')->columnSpanFull()->placeholder('—'),
                Infolists\Components\TextEntry::make('issueReport.steps_to_reproduce')->label('Steps to Reproduce')->columnSpanFull()->placeholder('—'),
                Infolists\Components\TextEntry::make('issueReport.expected_result')->label('Expected')->placeholder('—'),
                Infolists\Components\TextEntry::make('issueReport.actual_result')->label('Actual')->placeholder('—'),
                Infolists\Components\TextEntry::make('issueReport.clinical_impact')->label('Clinical Impact')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [RetestsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeveloperTasks::route('/'),
            'view'  => Pages\ViewDeveloperTask::route('/{record}'),
        ];
    }
}
