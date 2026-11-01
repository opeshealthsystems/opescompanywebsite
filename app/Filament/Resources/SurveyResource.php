<?php
namespace App\Filament\Resources;

use App\Filament\Resources\SurveyResource\Pages;
use App\Filament\Resources\SurveyResource\RelationManagers;
use App\Models\Survey;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Surveys';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int    $navigationSort  = 10;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Survey Details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')->required()->maxLength(200)->columnSpanFull(),
                    Forms\Components\TextInput::make('title_fr')->label('Title (French)')->maxLength(200)->nullable(),
                    Forms\Components\Select::make('audience')
                        ->options(Survey::audienceOptions())
                        ->default('all')
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options(Survey::statusOptions())
                        ->default('draft')
                        ->disabled()->dehydrated(false),
                    Forms\Components\DateTimePicker::make('starts_at')->nullable(),
                    Forms\Components\DateTimePicker::make('ends_at')->nullable(),
                ]),
            Forms\Components\Section::make('Description')
                ->schema([
                    Forms\Components\Textarea::make('description')->rows(3)->nullable(),
                    Forms\Components\Textarea::make('description_fr')->label('Description (French)')->rows(3)->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('audience')->badge()
                    ->color(fn ($state) => match($state) {
                        'practitioners' => 'info',
                        'customers'     => 'warning',
                        default         => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'active' => 'success',
                        'closed' => 'danger',
                        default  => 'gray',
                    }),
                Tables\Columns\TextColumn::make('responses_count')
                    ->label('Responses'),
                Tables\Columns\TextColumn::make('starts_at')->dateTime('d M Y')->placeholder('—'),
                Tables\Columns\TextColumn::make('ends_at')->dateTime('d M Y')->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Survey::statusOptions()),
                Tables\Filters\SelectFilter::make('audience')->options(Survey::audienceOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->hidden(fn (Survey $record) => $record->status !== 'draft')
                    ->using(fn (Survey $record, array $data) => AdminWorkflowAdapter::record('admin.programmes.surveys.update', $data, ['survey' => $record->id])),
                Tables\Actions\Action::make('activate')->label('Activate')->color('success')->requiresConfirmation()
                    ->visible(fn (Survey $record) => $record->status === 'draft')
                    ->action(fn (Survey $record) => AdminWorkflowAdapter::run('admin.programmes.surveys.activate', parameters: ['survey' => $record->id])),
                Tables\Actions\Action::make('close')->label('Close')->color('gray')->requiresConfirmation()
                    ->visible(fn (Survey $record) => $record->status === 'active')
                    ->action(fn (Survey $record) => AdminWorkflowAdapter::run('admin.programmes.surveys.close', parameters: ['survey' => $record->id])),
                Tables\Actions\DeleteAction::make()->hidden(fn (Survey $record) => $record->status !== 'draft'
                    || (\App\Contexts\Shared\Infrastructure\AdminReferenceOptions::data('admin.resourceSummaries.metric', ['metric' => 'surveys.has_responses', 'id' => $record->id])['count'] ?? false))
                    ->using(function (Survey $record) { AdminWorkflowAdapter::run('admin.programmes.surveys.delete', parameters: ['survey' => $record->id]); return true; }),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Survey')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('title'),
                    Infolists\Components\TextEntry::make('audience')->badge(),
                    Infolists\Components\TextEntry::make('status')->badge(),
                    Infolists\Components\TextEntry::make('starts_at')->dateTime('d M Y H:i')->placeholder('—'),
                    Infolists\Components\TextEntry::make('ends_at')->dateTime('d M Y H:i')->placeholder('—'),
                    Infolists\Components\TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'view'   => Pages\ViewSurvey::route('/{record}'),
            'edit'   => Pages\EditSurvey::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return app(\App\Contexts\Programmes\Application\Queries\AdminSurveyQueries::class)->surveyQuery(
            new \App\Contexts\Shared\Application\WorkflowInput(actor: auth()->user(), language: app()->getLocale())
        );
    }
}
