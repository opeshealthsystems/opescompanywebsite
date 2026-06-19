<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IssueReportResource\Pages;
use App\Models\IssueReport;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IssueReportResource extends Resource
{
    protected static ?string $model = IssueReport::class;
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int $navigationSort = 1;

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
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('severity')
                    ->badge()
                    ->formatStateUsing(fn ($state) => IssueReport::severityOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'critical' => 'danger',
                        'high'     => 'warning',
                        'medium'   => 'gray',
                        'low'      => 'info',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('issue_type')
                    ->formatStateUsing(fn ($state) => IssueReport::issueTypeOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('cohortMember.cohort.name')
                    ->label('Cohort'),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => IssueReport::statusOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'submitted'              => 'gray',
                        'clinical_review'        => 'warning',
                        'product_review'         => 'warning',
                        'accepted'               => 'success',
                        'fixed'                  => 'success',
                        'closed'                 => 'success',
                        'rejected'               => 'danger',
                        'duplicate'              => 'danger',
                        'needs_more_information'  => 'info',
                        'sent_to_development'    => 'info',
                        default                  => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('severity')
                    ->options(IssueReport::severityOptions()),
                Tables\Filters\SelectFilter::make('issue_type')
                    ->options(IssueReport::issueTypeOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('start_clinical_review')
                    ->label('Clinical Review')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('warning')
                    ->visible(fn (IssueReport $r) => $r->status === 'submitted')
                    ->form([
                        Forms\Components\Select::make('decision')
                            ->options(\App\Models\ClinicalReview::decisionOptions())
                            ->required(),
                        Forms\Components\Textarea::make('notes')->rows(3),
                    ])
                    ->action(function (IssueReport $r, array $data) {
                        $r->recordClinicalReview(auth()->id(), $data['decision'], $data['notes'] ?? null);
                        Notification::make()->title('Clinical review recorded.')->success()->send();
                    }),
                Tables\Actions\Action::make('send_to_product_review')
                    ->label('Send to Product Review')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('info')
                    ->visible(fn (IssueReport $r) => $r->status === 'clinical_review' && $r->clinicalApproved())
                    ->requiresConfirmation()
                    ->action(function (IssueReport $r) {
                        $r->sendToProductReview();
                        Notification::make()->title('Sent to product review.')->success()->send();
                    }),
                Tables\Actions\Action::make('product_decision')
                    ->label('Product Decision')
                    ->icon('heroicon-o-cube')
                    ->color('primary')
                    ->visible(fn (IssueReport $r) => $r->status === 'product_review')
                    ->form([
                        Forms\Components\Select::make('decision')
                            ->options(\App\Models\ProductReview::decisionOptions())
                            ->required(),
                        Forms\Components\Textarea::make('notes')->rows(3),
                    ])
                    ->action(function (IssueReport $r, array $data) {
                        $r->recordProductReview(auth()->id(), $data['decision'], $data['notes'] ?? null);
                        Notification::make()->title('Product decision recorded.')->success()->send();
                    }),
                Tables\Actions\Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-o-lock-closed')
                    ->color('gray')
                    ->visible(fn (IssueReport $r) => in_array($r->status, ['accepted', 'rejected', 'duplicate']))
                    ->requiresConfirmation()
                    ->action(function (IssueReport $r) {
                        $r->closeIssue();
                        Notification::make()->title('Issue closed.')->success()->send();
                    }),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Issue')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('title')->columnSpanFull()->weight('semibold'),
                    Infolists\Components\TextEntry::make('issue_type')
                        ->formatStateUsing(fn ($state) => IssueReport::issueTypeOptions()[$state] ?? $state),
                    Infolists\Components\TextEntry::make('severity')
                        ->badge()
                        ->formatStateUsing(fn ($state) => IssueReport::severityOptions()[$state] ?? $state),
                    Infolists\Components\TextEntry::make('status')
                        ->badge()
                        ->formatStateUsing(fn ($state) => IssueReport::statusOptions()[$state] ?? $state),
                    Infolists\Components\TextEntry::make('cohortMember.cohort.name')->label('Cohort')->placeholder('—'),
                    Infolists\Components\TextEntry::make('cohortMember.user.name')->label('Practitioner')->placeholder('—'),
                    Infolists\Components\TextEntry::make('product.name')->label('Product')->placeholder('—'),
                    Infolists\Components\TextEntry::make('module.name')->label('Module')->placeholder('—'),
                    Infolists\Components\TextEntry::make('workflow.name')->label('Workflow')->placeholder('—'),
                    Infolists\Components\TextEntry::make('testCase.title')->label('Test Case')->placeholder('—'),
                ]),
            Infolists\Components\Section::make('Details')
                ->schema([
                    Infolists\Components\TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                    Infolists\Components\TextEntry::make('steps_to_reproduce')->columnSpanFull()->placeholder('—'),
                    Infolists\Components\TextEntry::make('expected_result')->columnSpanFull()->placeholder('—'),
                    Infolists\Components\TextEntry::make('actual_result')->columnSpanFull()->placeholder('—'),
                    Infolists\Components\TextEntry::make('clinical_impact')->columnSpanFull()->placeholder('—'),
                    Infolists\Components\TextEntry::make('recommendation')->columnSpanFull()->placeholder('—'),
                ]),
            Infolists\Components\Section::make('Reviews')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('clinicalReview.decision')->label('Clinical Decision')->placeholder('—'),
                    Infolists\Components\TextEntry::make('clinicalReview.notes')->label('Clinical Notes')->placeholder('—'),
                    Infolists\Components\TextEntry::make('productReview.decision')->label('Product Decision')->placeholder('—'),
                    Infolists\Components\TextEntry::make('productReview.notes')->label('Product Notes')->placeholder('—'),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIssueReports::route('/'),
            'view'  => Pages\ViewIssueReport::route('/{record}'),
        ];
    }
}
