<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CohortResource\Pages;
use App\Filament\Resources\CohortResource\RelationManagers;
use App\Models\Cohort;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CohortResource extends Resource
{
    protected static ?string $model = Cohort::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Validation Catalog';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('practitioner_program_id')
                ->label('Validation Program')
                ->options(fn () => \App\Contexts\Shared\Infrastructure\AdminReferenceOptions::options('admin.references.validationPrograms', [], 'title'))
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('specialty')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->nullable()->columnSpanFull(),
            Forms\Components\DatePicker::make('start_date')->native(false)->required(),
            Forms\Components\DatePicker::make('end_date')->native(false)->required(),
            Forms\Components\TextInput::make('max_members')->numeric(),
            Forms\Components\Select::make('status')
                ->options(Cohort::statusOptions())
                ->default('draft')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('semibold'),
                Tables\Columns\TextColumn::make('specialty'),
                Tables\Columns\TextColumn::make('practitionerProgram.title')->label('Program')->limit(40),
                Tables\Columns\TextColumn::make('start_date')->date(),
                Tables\Columns\TextColumn::make('end_date')->date(),
                Tables\Columns\TextColumn::make('members_count')->label('Members'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match ($state) {
                        'draft'     => 'gray',
                        'active'    => 'success',
                        'completed' => 'info',
                        default     => 'gray',
                    }),
            ])
            ->defaultSort('start_date', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()->using(fn (\App\Models\Cohort $record, array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.validation.cohorts.update', $data, ['cohort' => $record->id])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->action(function (\Illuminate\Support\Collection $records): void {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($records): void {
                            foreach ($records as $record) {
                                \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.validation.cohorts.delete', parameters: ['cohort' => $record->id]);
                            }
                        }, 3);
                    }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MembersRelationManager::class,
            RelationManagers\TestCasesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCohorts::route('/'),
            'create' => Pages\CreateCohort::route('/create'),
            'edit'   => Pages\EditCohort::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return app(\App\Contexts\Validation\Application\Queries\AdminCohortQueries::class)->query(
            new \App\Contexts\Shared\Application\WorkflowInput(actor: auth()->user(), language: app()->getLocale())
        );
    }
}
