<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinalEvaluationResource\Pages;
use App\Models\CohortMember;
use App\Models\FinalEvaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FinalEvaluationResource extends Resource
{
    protected static ?string $model = FinalEvaluation::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Validation Hub';
    protected static ?int    $navigationSort  = 15;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('cohort_member_id')
                ->label('Cohort member')
                ->options(fn () => CohortMember::with(['user', 'cohort'])->get()
                    ->mapWithKeys(fn (CohortMember $m) => [$m->id => ($m->user?->name ?? 'Member #'.$m->id).' — '.($m->cohort?->name ?? '')]))
                ->searchable()->required()->disabledOn('edit'),
            Forms\Components\Textarea::make('assessment')->rows(4)->required(),
            Forms\Components\Select::make('rating')->options(FinalEvaluation::ratingOptions())->required(),
            Forms\Components\Textarea::make('recommendation')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cohortMember.user.name')->label('Member')->searchable(),
                Tables\Columns\TextColumn::make('cohortMember.cohort.name')->label('Cohort'),
                Tables\Columns\TextColumn::make('rating')->badge()
                    ->formatStateUsing(fn ($state) => FinalEvaluation::ratingOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'outstanding' => 'success', 'strong' => 'info',
                        'satisfactory' => 'gray', 'needs_improvement' => 'warning', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('evaluator.name')->label('Evaluator')->placeholder('—'),
                Tables\Columns\TextColumn::make('evaluated_at')->dateTime(),
            ])
            ->defaultSort('evaluated_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('issue_certificate')
                    ->label('Issue Certificate')->icon('heroicon-o-academic-cap')->color('success')
                    ->visible(fn (\App\Models\FinalEvaluation $r) =>
                        app(\App\Support\CertificationScore::class)->for($r)['tier'] !== 'not_certified'
                        && ! \App\Models\ValidationCertificate::where('cohort_member_id', $r->cohort_member_id)->exists())
                    ->requiresConfirmation()
                    ->action(function (\App\Models\FinalEvaluation $r) {
                        \App\Models\ValidationCertificate::issueFor($r, auth()->id());
                        \Filament\Notifications\Notification::make()->title('Certificate issued.')->success()->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Evaluation')->columns(2)->schema([
                Infolists\Components\TextEntry::make('cohortMember.user.name')->label('Member'),
                Infolists\Components\TextEntry::make('cohortMember.cohort.name')->label('Cohort'),
                Infolists\Components\TextEntry::make('rating')->badge()
                    ->formatStateUsing(fn ($state) => FinalEvaluation::ratingOptions()[$state] ?? $state),
                Infolists\Components\TextEntry::make('evaluator.name')->label('Evaluator')->placeholder('—'),
                Infolists\Components\TextEntry::make('assessment')->columnSpanFull(),
                Infolists\Components\TextEntry::make('recommendation')->columnSpanFull()->placeholder('—'),
            ]),
            Infolists\Components\Section::make('Frozen Contribution')
                ->description('Snapshot taken when this evaluation was recorded.')
                ->columns(4)
                ->schema([
                    Infolists\Components\TextEntry::make('metrics.sessions')->label('Sessions'),
                    Infolists\Components\TextEntry::make('metrics.issues_found')->label('Issues Found'),
                    Infolists\Components\TextEntry::make('metrics.issues_accepted')->label('Issues Accepted'),
                    Infolists\Components\TextEntry::make('metrics.retests')->label('Retests'),
                    Infolists\Components\TextEntry::make('metrics.as_of')->label('As of'),
                ]),
            Infolists\Components\Section::make('Certification')
                ->description('Live computed score (frozen when a certificate is issued).')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('certification_score')
                        ->label('Score')
                        ->state(fn (\App\Models\FinalEvaluation $record) => app(\App\Support\CertificationScore::class)->for($record)['score']),
                    Infolists\Components\TextEntry::make('certification_tier')
                        ->label('Tier')->badge()
                        ->state(fn (\App\Models\FinalEvaluation $record) => \App\Support\CertificationScore::tierOptions()[app(\App\Support\CertificationScore::class)->for($record)['tier']]),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFinalEvaluations::route('/'),
            'create' => Pages\CreateFinalEvaluation::route('/create'),
            'view'   => Pages\ViewFinalEvaluation::route('/{record}'),
            'edit'   => Pages\EditFinalEvaluation::route('/{record}/edit'),
        ];
    }
}
