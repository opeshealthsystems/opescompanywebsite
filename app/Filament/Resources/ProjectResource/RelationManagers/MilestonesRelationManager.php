<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\Milestone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(250)->columnSpanFull(),
            Forms\Components\Textarea::make('description')->rows(2)->nullable()->columnSpanFull(),
            Forms\Components\DatePicker::make('due_date')->native(false)->nullable(),
            Forms\Components\Select::make('status')
                ->options(Milestone::statusOptions())
                ->default('pending')->required(),
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
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('complete')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->hidden(fn (Milestone $record) => $record->status === 'completed')
                    ->action(fn (Milestone $record) => $record->update(['status'=>'completed','completed_at'=>now()])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
