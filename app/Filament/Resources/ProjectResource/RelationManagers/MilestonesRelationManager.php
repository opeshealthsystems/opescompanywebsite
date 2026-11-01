<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\Milestone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    protected function getTableQuery(): Builder
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::query(
            \App\Contexts\Operations\Application\Queries\ProjectQueries::class,
            'milestoneQuery',
            [$this->getOwnerRecord()],
        );
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(250)->columnSpanFull(),
            Forms\Components\Textarea::make('description')->rows(2)->nullable()->columnSpanFull(),
            Forms\Components\DatePicker::make('due_date')->native(false)->nullable(),
            Forms\Components\Select::make('status')
                ->options(fn () => array_diff_key(Milestone::statusOptions(), ['completed' => true]))
                ->default('pending')->required()
                ->disabled(fn (?Milestone $record) => $record?->status === 'completed')
                ->dehydrated(fn (?Milestone $record) => $record?->status !== 'completed'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->weight('semibold'),
                Tables\Columns\TextColumn::make('due_date')->date('d M Y')->placeholder('—')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'completed' => 'success',
                        'in_progress' => 'info',
                        'overdue' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('due_date')
            ->headerActions([Tables\Actions\CreateAction::make()->using(fn (array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.operations.milestones.create', $data, ['project' => $this->getOwnerRecord()->id]))])
            ->actions([
                Tables\Actions\EditAction::make()->using(fn (Milestone $record, array $data) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.operations.milestones.update', $data, ['project' => $this->getOwnerRecord()->id, 'milestone' => $record->id])),
                Tables\Actions\Action::make('complete')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->hidden(fn (Milestone $record) => $record->status === 'completed')
                    ->action(fn (Milestone $record) => \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.workforce.milestones.complete', parameters: ['milestone' => $record])),
                Tables\Actions\DeleteAction::make()->using(function (Milestone $record) { \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.operations.milestones.delete', parameters: ['project' => $this->getOwnerRecord()->id, 'milestone' => $record->id]); return true; }),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()->using(function ($records) { foreach ($records as $record) \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::run('admin.operations.milestones.delete', parameters: ['project' => $this->getOwnerRecord()->id, 'milestone' => $record->id]); })])]);
    }
}
